<?php
/**
 * ARIA Accessibility Checker
 *
 * Checks WCAG 4.1.2 (Name, Role, Value) - ARIA usage.
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
 * ARIA Checker Class
 *
 * @since 1.0.0
 */
class AriaChecker extends BaseChecker {
    /**
     * Valid ARIA roles
     *
     * @var array<string>
     */
    private array $valid_roles = [
        'alert', 'alertdialog', 'application', 'article', 'banner', 'button',
        'cell', 'checkbox', 'columnheader', 'combobox', 'complementary',
        'contentinfo', 'definition', 'dialog', 'directory', 'document', 'feed',
        'figure', 'form', 'grid', 'gridcell', 'group', 'heading', 'img',
        'link', 'list', 'listbox', 'listitem', 'log', 'main', 'marquee',
        'math', 'menu', 'menubar', 'menuitem', 'menuitemcheckbox',
        'menuitemradio', 'navigation', 'none', 'note', 'option', 'presentation',
        'progressbar', 'radio', 'radiogroup', 'region', 'row', 'rowgroup',
        'rowheader', 'scrollbar', 'search', 'searchbox', 'separator', 'slider',
        'spinbutton', 'status', 'switch', 'tab', 'table', 'tablist', 'tabpanel',
        'term', 'textbox', 'timer', 'toolbar', 'tooltip', 'tree', 'treegrid',
        'treeitem',
    ];

    /**
     * Check for ARIA accessibility issues
     *
     * @param DOMDocument $dom   DOM document.
     * @param DOMXPath    $xpath XPath instance.
     * @param string      $html  Raw HTML content.
     * @return array<array> Array of issues found.
     */
    public function check(DOMDocument $dom, DOMXPath $xpath, string $html): array {
        $issues = [];

        // Check invalid ARIA roles
        $issues = array_merge($issues, $this->check_invalid_roles($xpath));

        // Check aria-labelledby references
        $issues = array_merge($issues, $this->check_aria_labelledby($xpath, $dom));

        return $issues;
    }

    /**
     * Check for invalid ARIA roles
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_invalid_roles(DOMXPath $xpath): array {
        $issues = [];
        $elements = $xpath->query('//*[@role]');

        foreach ($elements as $element) {
            $role = $element->getAttribute('role');
            
            if (!in_array($role, $this->valid_roles, true)) {
                $issues[] = $this->create_issue([
                    'type' => 'invalid_aria_role',
                    'severity' => 'serious',
                    'wcag' => '4.1.2',
                    'category' => 'aria',
                    'message' => sprintf(__('Invalid ARIA role: %s', 'complyflow'), $role),
                    'element' => $this->get_element_html($element),
                    'selector' => $this->get_selector($element),
                    'fix' => __('Use a valid ARIA role from the ARIA specification', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/TR/wai-aria-1.2/#role_definitions',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check aria-labelledby references
     *
     * @param DOMXPath    $xpath XPath instance.
     * @param DOMDocument $dom   DOM document.
     * @return array<array> Issues found.
     */
    private function check_aria_labelledby(DOMXPath $xpath, DOMDocument $dom): array {
        $issues = [];
        $elements = $xpath->query('//*[@aria-labelledby]');

        foreach ($elements as $element) {
            $labelledby = $element->getAttribute('aria-labelledby');
            $ids = explode(' ', trim($labelledby));

            foreach ($ids as $id) {
                if (empty($id)) {
                    continue;
                }

                // Check if referenced element exists
                $referenced = $dom->getElementById($id);

                if (!$referenced) {
                    $issues[] = $this->create_issue([
                        'type' => 'aria_labelledby_missing',
                        'severity' => 'serious',
                        'wcag' => '4.1.2',
                        'category' => 'aria',
                        'message' => sprintf(__('aria-labelledby references non-existent ID: %s', 'complyflow'), $id),
                        'element' => $this->get_element_html($element),
                        'selector' => $this->get_selector($element),
                        'fix' => __('Ensure the referenced element exists with the correct ID', 'complyflow'),
                        'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/name-role-value',
                    ]);
                }
            }
        }

        return $issues;
    }
}
