<?php
/**
 * Accessibility Scanner Class
 *
 * WCAG 2.2 Level AA compliance scanner with 50+ automated checks.
 *
 * @package ComplyFlow\Modules\Accessibility
 * @since 1.0.0
 */

namespace ComplyFlow\Modules\Accessibility;

use DOMDocument;
use DOMXPath;
use ComplyFlow\Database\ScanRepository;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Scanner Class
 *
 * @since 1.0.0
 */
class Scanner {
    /**
     * DOM Document
     *
     * @var DOMDocument
     */
    private DOMDocument $dom;

    /**
     * XPath instance
     *
     * @var DOMXPath
     */
    private DOMXPath $xpath;

    /**
     * Issues found during scan
     *
     * @var array<array>
     */
    private array $issues = [];

    /**
     * Scan URL
     *
     * @var string
     */
    private string $url;

    /**
     * HTML content
     *
     * @var string
     */
    private string $html;

    /**
     * Scan repository
     *
     * @var ScanRepository
     */
    private ScanRepository $repository;

    /**
     * Issue checkers
     *
     * @var array<object>
     */
    private array $checkers = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->repository = new ScanRepository();
        $this->initialize_checkers();
    }

    /**
     * Initialize issue checkers
     *
     * @return void
     */
    private function initialize_checkers(): void {
        $this->checkers = [
            new Checkers\ImageChecker(),
            new Checkers\HeadingChecker(),
            new Checkers\ColorContrastChecker(),
            new Checkers\FormChecker(),
            new Checkers\AriaChecker(),
            new Checkers\LinkChecker(),
            new Checkers\KeyboardChecker(),
            new Checkers\SemanticChecker(),
            new Checkers\MultimediaChecker(),
            new Checkers\TableChecker(),
        ];
    }

    /**
     * Scan a URL for accessibility issues
     *
     * @param string $url     URL to scan.
     * @param array  $options Scan options.
     * @return array{success: bool, scan_id: int|false, issues: array, summary: array}
     */
    public function scan_url(string $url, array $options = []): array {
        $this->url = $url;
        $this->issues = [];

        try {
            // Fetch HTML
            $this->html = $this->fetch_html($url);

            if (empty($this->html)) {
                return [
                    'success' => false,
                    'scan_id' => false,
                    'issues' => [],
                    'summary' => [],
                    'error' => __('Failed to fetch URL content', 'complyflow'),
                ];
            }

            // Parse HTML
            $this->parse_html($this->html);

            // Run all checks
            $this->run_checks();

            // Calculate score
            $score = $this->calculate_score();

            // Generate summary
            $summary = $this->generate_summary();

            // Save scan results
            $scan_id = $this->save_scan($url, $score, $summary);

            return [
                'success' => true,
                'scan_id' => $scan_id,
                'issues' => $this->issues,
                'summary' => $summary,
                'score' => $score,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'scan_id' => false,
                'issues' => [],
                'summary' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch HTML content from URL
     *
     * @param string $url URL to fetch.
     * @return string HTML content.
     */
    private function fetch_html(string $url): string {
        $sslverify = apply_filters('complyflow_scanner_sslverify', true, $url);
        // Auto-disable sslverify for localhost/self-signed during development
        if (strpos($url, 'localhost') !== false || strpos($url, '127.0.0.1') !== false) {
            $sslverify = false;
        }
        $response = wp_remote_get($url, [
            'timeout' => 30,
            'user-agent' => 'ComplyFlow Accessibility Scanner/1.0',
            'sslverify' => $sslverify,
        ]);

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            throw new \Exception(sprintf(__('HTTP %d error', 'complyflow'), $status_code));
        }

        return wp_remote_retrieve_body($response);
    }

    /**
     * Parse HTML content into DOMDocument
     *
     * @param string $html HTML content.
     * @return void
     */
    private function parse_html(string $html): void {
        $this->dom = new DOMDocument();
        
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        
        // Load HTML with UTF-8 encoding
        $this->dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        // Clear errors
        libxml_clear_errors();

        $this->xpath = new DOMXPath($this->dom);
    }

    /**
     * Run all accessibility checks
     *
     * @return void
     */
    private function run_checks(): void {
        foreach ($this->checkers as $checker) {
            $checker_issues = $checker->check($this->dom, $this->xpath, $this->html);
            
            if (!empty($checker_issues)) {
                $this->issues = array_merge($this->issues, $checker_issues);
            }
        }

        /**
         * Filters issues found during scan
         *
         * @since 1.0.0
         *
         * @param array  $issues Issues found.
         * @param string $url    Scanned URL.
         */
        $this->issues = apply_filters('complyflow_scan_issues', $this->issues, $this->url);
    }

    /**
     * Calculate accessibility score
     *
     * @return float Score from 0-100.
     */
    private function calculate_score(): float {
        if (empty($this->issues)) {
            return 100.0;
        }

        $severity_weights = [
            'critical' => 10,
            'serious' => 5,
            'moderate' => 2,
            'minor' => 1,
        ];

        $total_impact = 0;
        foreach ($this->issues as $issue) {
            $severity = $issue['severity'] ?? 'minor';
            $total_impact += $severity_weights[$severity] ?? 1;
        }

        // Cap at 100 impact points to avoid negative scores
        $total_impact = min($total_impact, 100);

        // Calculate score (100 - impact)
        $score = max(0, 100 - $total_impact);

        return round($score, 2);
    }

    /**
     * Generate scan summary
     *
     * @return array<string, mixed> Summary data.
     */
    private function generate_summary(): array {
        $summary = [
            'total_issues' => count($this->issues),
            'by_severity' => [
                'critical' => 0,
                'serious' => 0,
                'moderate' => 0,
                'minor' => 0,
            ],
            'by_wcag' => [],
            'by_category' => [],
        ];

        foreach ($this->issues as $issue) {
            // Count by severity
            $severity = $issue['severity'] ?? 'minor';
            if (isset($summary['by_severity'][$severity])) {
                $summary['by_severity'][$severity]++;
            }

            // Count by WCAG criterion
            $wcag = $issue['wcag'] ?? 'unknown';
            if (!isset($summary['by_wcag'][$wcag])) {
                $summary['by_wcag'][$wcag] = 0;
            }
            $summary['by_wcag'][$wcag]++;

            // Count by category
            $category = $issue['category'] ?? 'other';
            if (!isset($summary['by_category'][$category])) {
                $summary['by_category'][$category] = 0;
            }
            $summary['by_category'][$category]++;
        }

        return $summary;
    }

    /**
     * Save scan results to database
     *
     * @param string $url     Scanned URL.
     * @param float  $score   Accessibility score.
     * @param array  $summary Scan summary.
     * @return int|false Scan ID or false on failure.
     */
    private function save_scan(string $url, float $score, array $summary) {
        // Match database schema: url, scan_type, total_issues, critical_issues, warning_issues, notice_issues, results
        $data = [
            'url' => $url,
            'scan_type' => 'accessibility',
            'total_issues' => count($this->issues),
            'critical_issues' => $summary['by_severity']['critical'] ?? 0,
            'warning_issues' => $summary['by_severity']['serious'] ?? 0,
            'notice_issues' => $summary['by_severity']['moderate'] ?? 0,
            'results' => [
                'issues' => $this->issues,
                'summary' => $summary,
                'score' => $score,
                'scanned_at' => current_time('mysql', true),
            ],
        ];

        $scan_id = $this->repository->create_scan($data);
        
        return $scan_id;
    }

    /**
     * Get scan results by ID
     *
     * @param int $scan_id Scan ID.
     * @return object|null Scan record.
     */
    public function get_scan(int $scan_id): ?object {
        return $this->repository->find($scan_id);
    }

    /**
     * Get latest scan for URL
     *
     * @param string $url URL to check.
     * @return object|null Latest scan record.
     */
    public function get_latest_scan(string $url): ?object {
        return $this->repository->get_latest_scan($url);
    }

    /**
     * Get scan statistics
     *
     * @return array<string, mixed> Statistics.
     */
    public function get_statistics(): array {
        return $this->repository->get_statistics();
    }

    /**
     * Delete scan by ID
     *
     * @param int $scan_id Scan ID.
     * @return bool True on success.
     */
    public function delete_scan(int $scan_id): bool {
        return $this->repository->delete($scan_id);
    }

    /**
     * Get all scans with pagination
     *
     * @param array $args Query arguments.
     * @return array<object> Scan records.
     */
    public function get_scans(array $args = []): array {
        return $this->repository->find_all($args);
    }

    /**
     * Export scan results to array
     *
     * @param int $scan_id Scan ID.
     * @return array|false Scan data or false.
     */
    public function export_scan(int $scan_id) {
        $scan = $this->get_scan($scan_id);

        if (!$scan) {
            return false;
        }

        $results = json_decode($scan->results, true);

        return [
            'id' => $scan->id,
            'url' => $scan->url,
            'score' => $results['score'] ?? 0,
            'scanned_at' => $scan->created_at,
            'issues' => $results['issues'] ?? [],
            'summary' => $results['summary'] ?? [],
        ];
    }
}
