<?php
/**
 * Table Accessibility Checker
 *
 * Checks WCAG 1.3.1 (Info and Relationships) - table structure.
 *
 * @package ComplyFlow\Modules\Accessibility\Checkers
 * @since 1.0.0
 */

namespace ComplyFlow\Modules\Accessibility\Checkers;

use DOMDocument;
use DOMXPath;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Table Checker Class
 *
 * @since 1.0.0
 */
class TableChecker extends BaseChecker {
    /**
     * Check for table accessibility issues
     *
     * @param DOMDocument $dom   DOM document.
     * @param DOMXPath    $xpath XPath instance.
     * @param string      $html  Raw HTML content.
     * @return array<array> Array of issues found.
     */
    public function check(DOMDocument $dom, DOMXPath $xpath, string $html): array {
        $issues = [];

        // Check tables missing headers
        $issues = array_merge($issues, $this->check_table_headers($xpath));

        // Check tables missing captions
        $issues = array_merge($issues, $this->check_table_captions($xpath));

        return $issues;
    }

    /**
     * Check tables for proper headers
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_table_headers(DOMXPath $xpath): array {
        $issues = [];
        $tables = $xpath->query('//table[not(@role="presentation")]');

        foreach ($tables as $table) {
            $headers = $xpath->query('.//th', $table);

            if ($headers->length === 0) {
                $issues[] = $this->create_issue([
                    'type' => 'table_no_headers',
                    'severity' => 'serious',
                    'wcag' => '1.3.1',
                    'category' => 'tables',
                    'message' => __('Data table missing header cells', 'complyflow'),
                    'element' => $this->get_element_html($table, 150),
                    'selector' => $this->get_selector($table),
                    'fix' => __('Use <th> elements for table headers', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/tutorials/tables/',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check tables for captions
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_table_captions(DOMXPath $xpath): array {
        $issues = [];
        $tables = $xpath->query('//table[not(@role="presentation")]');

        foreach ($tables as $table) {
            $captions = $xpath->query('.//caption', $table);
            $aria_label = $table->getAttribute('aria-label');
            $aria_labelledby = $table->getAttribute('aria-labelledby');

            if ($captions->length === 0 && empty($aria_label) && empty($aria_labelledby)) {
                $issues[] = $this->create_issue([
                    'type' => 'table_no_caption',
                    'severity' => 'moderate',
                    'wcag' => '1.3.1',
                    'category' => 'tables',
                    'message' => __('Data table missing caption or accessible name', 'complyflow'),
                    'element' => $this->get_element_html($table, 150),
                    'selector' => $this->get_selector($table),
                    'fix' => __('Add <caption> element or aria-label to describe the table', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/tutorials/tables/caption-summary/',
                ]);
            }
        }

        return $issues;
    }
}
