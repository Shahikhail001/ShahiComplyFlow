<?php
/**
 * Link Accessibility Checker
 *
 * Checks WCAG 2.4.4, 2.4.9 (Link Purpose).
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
 * Link Checker Class
 *
 * @since 1.0.0
 */
class LinkChecker extends BaseChecker {
    /**
     * Check for link accessibility issues
     *
     * @param DOMDocument $dom   DOM document.
     * @param DOMXPath    $xpath XPath instance.
     * @param string      $html  Raw HTML content.
     * @return array<array> Array of issues found.
     */
    public function check(DOMDocument $dom, DOMXPath $xpath, string $html): array {
        $issues = [];

        // Check empty links
        $issues = array_merge($issues, $this->check_empty_links($xpath));

        // Check ambiguous link text
        $issues = array_merge($issues, $this->check_ambiguous_links($xpath));

        // Check links without href
        $issues = array_merge($issues, $this->check_missing_href($xpath));

        return $issues;
    }

    /**
     * Check for empty links
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_empty_links(DOMXPath $xpath): array {
        $issues = [];
        $links = $xpath->query('//a[@href]');

        foreach ($links as $link) {
            if (!$this->is_visible($link)) {
                continue;
            }

            $text = $this->get_text_content($link);
            
            // Check for images with alt text
            $images = $xpath->query('.//img[@alt]', $link);
            $has_image_alt = false;
            
            foreach ($images as $img) {
                if (!empty(trim($img->getAttribute('alt')))) {
                    $has_image_alt = true;
                    break;
                }
            }

            // Check aria-label
            $aria_label = $link->getAttribute('aria-label');
            $aria_labelledby = $link->getAttribute('aria-labelledby');

            if ($this->is_empty_string($text) && !$has_image_alt && empty($aria_label) && empty($aria_labelledby)) {
                $issues[] = $this->create_issue([
                    'type' => 'empty_link',
                    'severity' => 'critical',
                    'wcag' => '2.4.4',
                    'category' => 'links',
                    'message' => __('Link has no text or accessible name', 'complyflow'),
                    'element' => $this->get_element_html($link),
                    'selector' => $this->get_selector($link),
                    'fix' => __('Add descriptive text or aria-label to describe link purpose', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/link-purpose-in-context',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check for ambiguous link text
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_ambiguous_links(DOMXPath $xpath): array {
        $issues = [];
        $ambiguous_texts = [
            'click here',
            'read more',
            'more',
            'here',
            'link',
            'this',
            'continue',
            'go',
        ];

        $links = $xpath->query('//a[@href]');

        foreach ($links as $link) {
            if (!$this->is_visible($link)) {
                continue;
            }

            $text = strtolower(trim($this->get_text_content($link)));

            if (in_array($text, $ambiguous_texts, true)) {
                $issues[] = $this->create_issue([
                    'type' => 'ambiguous_link_text',
                    'severity' => 'moderate',
                    'wcag' => '2.4.4',
                    'category' => 'links',
                    'message' => sprintf(__('Link text "%s" is not descriptive', 'complyflow'), $text),
                    'element' => $this->get_element_html($link),
                    'selector' => $this->get_selector($link),
                    'fix' => __('Use descriptive text that makes sense out of context', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/link-purpose-link-only',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check for links without href
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_missing_href(DOMXPath $xpath): array {
        $issues = [];
        $links = $xpath->query('//a[not(@href)]');

        foreach ($links as $link) {
            if (!$this->is_visible($link)) {
                continue;
            }

            $issues[] = $this->create_issue([
                'type' => 'link_no_href',
                'severity' => 'serious',
                'wcag' => '4.1.2',
                'category' => 'links',
                'message' => __('Link element missing href attribute', 'complyflow'),
                'element' => $this->get_element_html($link),
                'selector' => $this->get_selector($link),
                'fix' => __('Add href attribute or use <button> element instead', 'complyflow'),
                'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/name-role-value',
            ]);
        }

        return $issues;
    }
}
