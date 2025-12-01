<?php
/**
 * Base Issue Checker Class
 *
 * Abstract base class for all accessibility issue checkers.
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
 * Base Checker Class
 *
 * @since 1.0.0
 */
abstract class BaseChecker {
    /**
     * Check for accessibility issues
     *
     * @param DOMDocument $dom   DOM document.
     * @param DOMXPath    $xpath XPath instance.
     * @param string      $html  Raw HTML content.
     * @return array<array> Array of issues found.
     */
    abstract public function check(DOMDocument $dom, DOMXPath $xpath, string $html): array;

    /**
     * Create issue array
     *
     * @param array $data Issue data.
     * @return array Formatted issue.
     */
    protected function create_issue(array $data): array {
        return array_merge([
            'type' => '',
            'severity' => 'moderate',
            'wcag' => '',
            'category' => 'other',
            'message' => '',
            'element' => '',
            'selector' => '',
            'fix' => '',
            'learn_more' => '',
        ], $data);
    }

    /**
     * Get element selector (CSS-like)
     *
     * @param \DOMNode $node DOM node.
     * @return string CSS selector.
     */
    protected function get_selector(\DOMNode $node): string {
        $selector = $node->nodeName;

        if ($node->hasAttributes()) {
            // Add ID if present
            $id = $node->getAttribute('id');
            if ($id) {
                $selector .= '#' . $id;
            }

            // Add first class if present
            $class = $node->getAttribute('class');
            if ($class) {
                $classes = explode(' ', trim($class));
                if (!empty($classes[0])) {
                    $selector .= '.' . $classes[0];
                }
            }
        }

        return $selector;
    }

    /**
     * Get element HTML (truncated)
     *
     * @param \DOMNode $node     DOM node.
     * @param int      $max_length Maximum length.
     * @return string HTML string.
     */
    protected function get_element_html(\DOMNode $node, int $max_length = 200): string {
        if (!$node->ownerDocument) {
            return '';
        }

        $html = $node->ownerDocument->saveHTML($node);
        
        if (strlen($html) > $max_length) {
            $html = substr($html, 0, $max_length) . '...';
        }

        return $html;
    }

    /**
     * Check if element is visible
     *
     * @param \DOMElement $element DOM element.
     * @return bool True if visible.
     */
    protected function is_visible(\DOMElement $element): bool {
        // Check for hidden attributes
        if ($element->hasAttribute('hidden')) {
            return false;
        }

        // Check aria-hidden
        if ($element->getAttribute('aria-hidden') === 'true') {
            return false;
        }

        // Check type="hidden" for inputs
        if ($element->nodeName === 'input' && $element->getAttribute('type') === 'hidden') {
            return false;
        }

        return true;
    }

    /**
     * Get text content from element
     *
     * @param \DOMNode $node DOM node.
     * @return string Text content.
     */
    protected function get_text_content(\DOMNode $node): string {
        return trim($node->textContent ?? '');
    }

    /**
     * Check if string is empty or whitespace only
     *
     * @param string $string String to check.
     * @return bool True if empty.
     */
    protected function is_empty_string(string $string): bool {
        return empty(trim($string));
    }
}
