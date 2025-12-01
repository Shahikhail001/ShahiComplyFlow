<?php
/**
 * Keyboard Accessibility Checker
 *
 * Checks WCAG 2.1.1, 2.1.2 (Keyboard Accessible).
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
 * Keyboard Checker Class
 *
 * @since 1.0.0
 */
class KeyboardChecker extends BaseChecker {
    /**
     * Check for keyboard accessibility issues
     *
     * @param DOMDocument $dom   DOM document.
     * @param DOMXPath    $xpath XPath instance.
     * @param string      $html  Raw HTML content.
     * @return array<array> Array of issues found.
     */
    public function check(DOMDocument $dom, DOMXPath $xpath, string $html): array {
        $issues = [];

        // Check for positive tabindex
        $issues = array_merge($issues, $this->check_tabindex($xpath));

        return $issues;
    }

    /**
     * Check for positive tabindex values
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_tabindex(DOMXPath $xpath): array {
        $issues = [];
        $elements = $xpath->query('//*[@tabindex]');

        foreach ($elements as $element) {
            $tabindex = $element->getAttribute('tabindex');
            
            if (is_numeric($tabindex) && (int)$tabindex > 0) {
                $issues[] = $this->create_issue([
                    'type' => 'positive_tabindex',
                    'severity' => 'moderate',
                    'wcag' => '2.4.3',
                    'category' => 'keyboard',
                    'message' => __('Positive tabindex disrupts natural tab order', 'complyflow'),
                    'element' => $this->get_element_html($element),
                    'selector' => $this->get_selector($element),
                    'fix' => __('Use tabindex="0" or "-1", avoid positive values', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/focus-order',
                ]);
            }
        }

        return $issues;
    }
}
