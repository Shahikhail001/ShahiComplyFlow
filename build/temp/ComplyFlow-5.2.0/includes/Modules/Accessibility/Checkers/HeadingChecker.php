<?php
/**
 * Heading Structure Checker
 *
 * Checks WCAG 1.3.1 (Info and Relationships) and 2.4.6 (Headings and Labels).
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
 * Heading Checker Class
 *
 * @since 1.0.0
 */
class HeadingChecker extends BaseChecker {
    /**
     * Check for heading structure issues
     *
     * @param DOMDocument $dom   DOM document.
     * @param DOMXPath    $xpath XPath instance.
     * @param string      $html  Raw HTML content.
     * @return array<array> Array of issues found.
     */
    public function check(DOMDocument $dom, DOMXPath $xpath, string $html): array {
        $issues = [];

        // Get all headings
        $headings = $this->get_all_headings($xpath);

        // Check for missing H1
        $issues = array_merge($issues, $this->check_missing_h1($headings));

        // Check for multiple H1s
        $issues = array_merge($issues, $this->check_multiple_h1($headings));

        // Check for skipped heading levels
        $issues = array_merge($issues, $this->check_skipped_levels($headings));

        // Check for empty headings
        $issues = array_merge($issues, $this->check_empty_headings($xpath));

        return $issues;
    }

    /**
     * Get all headings in order
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Array of heading data.
     */
    private function get_all_headings(DOMXPath $xpath): array {
        $headings = [];
        $heading_nodes = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');

        foreach ($heading_nodes as $node) {
            if (!$this->is_visible($node)) {
                continue;
            }

            $level = (int) substr($node->nodeName, 1); // Extract number from h1, h2, etc.
            
            $headings[] = [
                'node' => $node,
                'level' => $level,
                'text' => $this->get_text_content($node),
            ];
        }

        return $headings;
    }

    /**
     * Check for missing H1
     *
     * @param array $headings Array of heading data.
     * @return array<array> Issues found.
     */
    private function check_missing_h1(array $headings): array {
        $issues = [];
        $has_h1 = false;

        foreach ($headings as $heading) {
            if ($heading['level'] === 1) {
                $has_h1 = true;
                break;
            }
        }

        if (!$has_h1 && !empty($headings)) {
            $issues[] = $this->create_issue([
                'type' => 'missing_h1',
                'severity' => 'serious',
                'wcag' => '1.3.1',
                'category' => 'structure',
                'message' => __('Page is missing an H1 heading', 'complyflow'),
                'element' => '',
                'selector' => 'body',
                'fix' => __('Add an H1 heading that describes the main content of the page', 'complyflow'),
                'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships',
            ]);
        }

        return $issues;
    }

    /**
     * Check for multiple H1 headings
     *
     * @param array $headings Array of heading data.
     * @return array<array> Issues found.
     */
    private function check_multiple_h1(array $headings): array {
        $issues = [];
        $h1_count = 0;
        $h1_nodes = [];

        foreach ($headings as $heading) {
            if ($heading['level'] === 1) {
                $h1_count++;
                $h1_nodes[] = $heading['node'];
            }
        }

        if ($h1_count > 1) {
            foreach ($h1_nodes as $index => $node) {
                if ($index > 0) { // Skip first H1
                    $issues[] = $this->create_issue([
                        'type' => 'multiple_h1',
                        'severity' => 'moderate',
                        'wcag' => '1.3.1',
                        'category' => 'structure',
                        'message' => sprintf(__('Page has %d H1 headings (should have only one)', 'complyflow'), $h1_count),
                        'element' => $this->get_element_html($node),
                        'selector' => $this->get_selector($node),
                        'fix' => __('Use only one H1 per page for the main heading', 'complyflow'),
                        'learn_more' => 'https://www.w3.org/WAI/tutorials/page-structure/headings/',
                    ]);
                }
            }
        }

        return $issues;
    }

    /**
     * Check for skipped heading levels
     *
     * @param array $headings Array of heading data.
     * @return array<array> Issues found.
     */
    private function check_skipped_levels(array $headings): array {
        $issues = [];
        $previous_level = 0;

        foreach ($headings as $heading) {
            $current_level = $heading['level'];

            // Check if level is skipped (e.g., H2 to H4)
            if ($previous_level > 0 && $current_level > $previous_level + 1) {
                $issues[] = $this->create_issue([
                    'type' => 'skipped_heading_level',
                    'severity' => 'moderate',
                    'wcag' => '1.3.1',
                    'category' => 'structure',
                    'message' => sprintf(
                        __('Heading level skipped from H%d to H%d', 'complyflow'),
                        $previous_level,
                        $current_level
                    ),
                    'element' => $this->get_element_html($heading['node']),
                    'selector' => $this->get_selector($heading['node']),
                    'fix' => sprintf(
                        __('Use H%d instead, or add missing H%d heading(s)', 'complyflow'),
                        $previous_level + 1,
                        $previous_level + 1
                    ),
                    'learn_more' => 'https://www.w3.org/WAI/tutorials/page-structure/headings/',
                ]);
            }

            $previous_level = $current_level;
        }

        return $issues;
    }

    /**
     * Check for empty headings
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_empty_headings(DOMXPath $xpath): array {
        $issues = [];
        $all_headings = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');

        foreach ($all_headings as $heading) {
            if (!$this->is_visible($heading)) {
                continue;
            }

            $text = $this->get_text_content($heading);

            if ($this->is_empty_string($text)) {
                $issues[] = $this->create_issue([
                    'type' => 'empty_heading',
                    'severity' => 'serious',
                    'wcag' => '2.4.6',
                    'category' => 'structure',
                    'message' => sprintf(__('%s heading is empty', 'complyflow'), strtoupper($heading->nodeName)),
                    'element' => $this->get_element_html($heading),
                    'selector' => $this->get_selector($heading),
                    'fix' => __('Add descriptive text to the heading or remove it', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/headings-and-labels',
                ]);
            }
        }

        return $issues;
    }
}
