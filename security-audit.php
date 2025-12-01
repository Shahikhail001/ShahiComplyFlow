<?php
/**
 * Security Audit Script
 *
 * Scans ComplyFlow codebase for common security issues.
 * Run from command line: php security-audit.php
 *
 * @package ComplyFlow
 * @since 4.3.0
 */

class SecurityAuditor {
    private $issues = [];
    private $stats = [
        'files_scanned' => 0,
        'lines_scanned' => 0,
        'critical' => 0,
        'high' => 0,
        'medium' => 0,
        'low' => 0,
    ];

    public function run() {
        echo "ComplyFlow Security Audit\n";
        echo "========================\n\n";

        // Scan PHP files
        $this->scanDirectory(__DIR__ . '/includes');
        
        // Display results
        $this->displayResults();
    }

    private function scanDirectory($dir) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $this->scanFile($file->getPathname());
            }
        }
    }

    private function scanFile($filepath) {
        $this->stats['files_scanned']++;
        $content = file_get_contents($filepath);
        $lines = explode("\n", $content);
        $this->stats['lines_scanned'] += count($lines);

        $relpath = str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $filepath);

        // Check 1: Direct $_POST/$_GET usage without sanitization
        $this->checkDirectInput($content, $lines, $relpath);

        // Check 2: echo/print without escaping
        $this->checkDirectOutput($content, $lines, $relpath);

        // Check 3: SQL queries without prepare
        $this->checkSQLInjection($content, $lines, $relpath);

        // Check 4: Missing nonce verification in AJAX handlers
        $this->checkNonceVerification($content, $lines, $relpath);

        // Check 5: Missing capability checks
        $this->checkCapabilityChecks($content, $lines, $relpath);

        // Check 6: eval() usage
        $this->checkEvalUsage($content, $lines, $relpath);

        // Check 7: Unsafe file operations
        $this->checkFileOperations($content, $lines, $relpath);
    }

    private function checkDirectInput($content, $lines, $filepath) {
        // Check for $_POST, $_GET, $_REQUEST without sanitization
        $patterns = [
            '/\$_POST\[[\'"]\w+[\'"]\](?!\s*\))/i',
            '/\$_GET\[[\'"]\w+[\'"]\](?!\s*\))/i',
            '/\$_REQUEST\[[\'"]\w+[\'"]\](?!\s*\))/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $line_num = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                    $line_content = trim($lines[$line_num - 1]);

                    // Skip if inside sanitize function
                    if (stripos($line_content, 'sanitize_') !== false ||
                        stripos($line_content, 'esc_') !== false ||
                        stripos($line_content, 'wp_kses') !== false ||
                        stripos($line_content, 'intval') !== false ||
                        stripos($line_content, 'absint') !== false) {
                        continue;
                    }

                    $this->addIssue('high', $filepath, $line_num, 
                        "Potentially unsanitized input: {$match[0]}", 
                        $line_content);
                }
            }
        }
    }

    private function checkDirectOutput($content, $lines, $filepath) {
        // Check for echo/print with variables
        $pattern = '/(echo|print)\s+\$\w+/i';
        
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line_num = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $line_content = trim($lines[$line_num - 1]);

                // Skip if escaped
                if (stripos($line_content, 'esc_html') !== false ||
                    stripos($line_content, 'esc_attr') !== false ||
                    stripos($line_content, 'esc_url') !== false ||
                    stripos($line_content, 'wp_kses') !== false ||
                    stripos($line_content, 'sanitize_') !== false) {
                    continue;
                }

                $this->addIssue('high', $filepath, $line_num,
                    "Potentially unescaped output: {$match[0]}",
                    $line_content);
            }
        }
    }

    private function checkSQLInjection($content, $lines, $filepath) {
        // Check for $wpdb queries without prepare
        if (stripos($content, '$wpdb->') !== false) {
            $patterns = [
                '/\$wpdb->query\s*\(\s*["\'](?!SELECT|INSERT|UPDATE|DELETE)/i',
                '/\$wpdb->get_results\s*\(\s*["\'].*\$\w+/i',
                '/\$wpdb->get_var\s*\(\s*["\'].*\$\w+/i',
                '/\$wpdb->get_row\s*\(\s*["\'].*\$\w+/i',
            ];

            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    foreach ($matches[0] as $match) {
                        $line_num = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                        $line_content = trim($lines[$line_num - 1]);

                        // Skip if using prepare
                        if (stripos($line_content, '->prepare(') !== false) {
                            continue;
                        }

                        $this->addIssue('critical', $filepath, $line_num,
                            "Potential SQL injection vulnerability",
                            $line_content);
                    }
                }
            }
        }
    }

    private function checkNonceVerification($content, $lines, $filepath) {
        // Check AJAX actions for nonce verification
        if (preg_match('/add_action\s*\(\s*[\'"]wp_ajax_(\w+)[\'"]/', $content, $match)) {
            $action_name = $match[1];
            
            // Check if nonce verification exists
            if (stripos($content, 'wp_verify_nonce') === false &&
                stripos($content, 'check_ajax_referer') === false) {
                $line_num = substr_count(substr($content, 0, strpos($content, $match[0])), "\n") + 1;
                
                $this->addIssue('high', $filepath, $line_num,
                    "AJAX action '{$action_name}' missing nonce verification",
                    $match[0]);
            }
        }
    }

    private function checkCapabilityChecks($content, $lines, $filepath) {
        // Check admin pages for capability checks
        if (preg_match('/add_menu_page|add_submenu_page/', $content) ||
            strpos($filepath, 'Admin') !== false) {
            
            if (stripos($content, 'current_user_can') === false &&
                stripos($content, 'is_admin') === false &&
                stripos($filepath, 'views') === false) { // Views called by checked pages
                
                $this->addIssue('medium', $filepath, 0,
                    "Admin file may be missing capability checks",
                    "Check if current_user_can() is used");
            }
        }
    }

    private function checkEvalUsage($content, $lines, $filepath) {
        if (preg_match_all('/\beval\s*\(/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line_num = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $line_content = trim($lines[$line_num - 1]);

                $this->addIssue('critical', $filepath, $line_num,
                    "Dangerous eval() usage detected",
                    $line_content);
            }
        }
    }

    private function checkFileOperations($content, $lines, $filepath) {
        $dangerous_functions = [
            'unlink', 'file_put_contents', 'fopen', 'fwrite',
            'move_uploaded_file', 'copy', 'rename'
        ];

        foreach ($dangerous_functions as $func) {
            if (preg_match_all('/\b' . $func . '\s*\(/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $line_num = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                    $line_content = trim($lines[$line_num - 1]);

                    $this->addIssue('medium', $filepath, $line_num,
                        "File operation '{$func}' detected - verify input validation",
                        $line_content);
                }
            }
        }
    }

    private function addIssue($severity, $file, $line, $message, $code) {
        $this->issues[] = [
            'severity' => $severity,
            'file' => $file,
            'line' => $line,
            'message' => $message,
            'code' => $code,
        ];
        $this->stats[$severity]++;
    }

    private function displayResults() {
        echo "\nScan Statistics:\n";
        echo "================\n";
        echo "Files scanned: {$this->stats['files_scanned']}\n";
        echo "Lines scanned: {$this->stats['lines_scanned']}\n\n";

        echo "Issues Found:\n";
        echo "=============\n";
        echo "🔴 Critical: {$this->stats['critical']}\n";
        echo "🟠 High:     {$this->stats['high']}\n";
        echo "🟡 Medium:   {$this->stats['medium']}\n";
        echo "⚪ Low:      {$this->stats['low']}\n\n";

        if (empty($this->issues)) {
            echo "✅ No security issues detected!\n\n";
            return;
        }

        // Sort by severity
        $severity_order = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
        usort($this->issues, function($a, $b) use ($severity_order) {
            return $severity_order[$a['severity']] <=> $severity_order[$b['severity']];
        });

        echo "Detailed Issues:\n";
        echo "================\n\n";

        foreach ($this->issues as $issue) {
            $emoji = match($issue['severity']) {
                'critical' => '🔴',
                'high' => '🟠',
                'medium' => '🟡',
                'low' => '⚪',
            };

            echo "{$emoji} [{$issue['severity']}] {$issue['file']}";
            if ($issue['line'] > 0) {
                echo ":{$issue['line']}";
            }
            echo "\n";
            echo "   {$issue['message']}\n";
            echo "   Code: {$issue['code']}\n\n";
        }

        // Summary
        echo "\nRecommendations:\n";
        echo "================\n";
        if ($this->stats['critical'] > 0) {
            echo "🔴 Fix critical issues immediately - these are security vulnerabilities\n";
        }
        if ($this->stats['high'] > 0) {
            echo "🟠 Review high priority issues - verify proper sanitization/escaping\n";
        }
        if ($this->stats['medium'] > 0) {
            echo "🟡 Review medium priority issues - add validation where needed\n";
        }
        echo "\n";
    }
}

// Run the audit
$auditor = new SecurityAuditor();
$auditor->run();
