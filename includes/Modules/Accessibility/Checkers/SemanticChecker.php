<?php
/**
 * Semantic HTML Checker
 *
 * Checks WCAG 1.3.1 (Info and Relationships) - semantic HTML usage.
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
 * Semantic Checker Class
 *
 * @since 1.0.0
 */
class SemanticChecker extends BaseChecker {
    /**
     * Check for semantic HTML issues
     *
     * @param DOMDocument $dom   DOM document.
     * @param DOMXPath    $xpath XPath instance.
     * @param string      $html  Raw HTML content.
     * @return array<array> Array of issues found.
     */
    public function check(DOMDocument $dom, DOMXPath $xpath, string $html): array {
        $issues = [];

        // Check for missing language attribute
        $issues = array_merge($issues, $this->check_language_attribute($xpath));

        // Check for missing page title
        $issues = array_merge($issues, $this->check_page_title($xpath));

        return $issues;
    }

    /**
     * Check for missing lang attribute
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_language_attribute(DOMXPath $xpath): array {
        $issues = [];
        $html_elements = $xpath->query('//html');

        if ($html_elements->length > 0) {
            $html = $html_elements->item(0);
            $lang = $html->getAttribute('lang');

            if (empty($lang)) {
                $issues[] = $this->create_issue([
                    'type' => 'missing_lang',
                    'severity' => 'serious',
                    'wcag' => '3.1.1',
                    'category' => 'structure',
                    'message' => __('HTML element missing lang attribute', 'complyflow'),
                    'element' => '<html>',
                    'selector' => 'html',
                    'fix' => __('Add lang attribute: <html lang="en">', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/language-of-page',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check for missing page title
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_page_title(DOMXPath $xpath): array {
        $issues = [];
        $titles = $xpath->query('//title');

        if ($titles->length === 0) {
            $issues[] = $this->create_issue([
                'type' => 'missing_title',
                'severity' => 'critical',
                'wcag' => '2.4.2',
                'category' => 'structure',
                'message' => __('Page missing title element', 'complyflow'),
                'element' => '<head>',
                'selector' => 'head',
                'fix' => __('Add <title> element in <head>: <title>Page Title</title>', 'complyflow'),
                'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/page-titled',
            ]);
        } else {
            $title = $titles->item(0);
            $title_text = $this->get_text_content($title);

            if ($this->is_empty_string($title_text)) {
                $issues[] = $this->create_issue([
                    'type' => 'empty_title',
                    'severity' => 'critical',
                    'wcag' => '2.4.2',
                    'category' => 'structure',
                    'message' => __('Page title is empty', 'complyflow'),
                    'element' => $this->get_element_html($title),
                    'selector' => 'title',
                    'fix' => __('Add descriptive text to page title', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/page-titled',
                ]);
            }
        }

        return $issues;
    }
}
