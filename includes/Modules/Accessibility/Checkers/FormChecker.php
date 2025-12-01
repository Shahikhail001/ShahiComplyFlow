<?php
/**
 * Form Accessibility Checker
 *
 * Checks WCAG 1.3.1, 2.4.6, 3.3.1, 3.3.2 (Form labels and instructions).
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
 * Form Checker Class
 *
 * @since 1.0.0
 */
class FormChecker extends BaseChecker {
    /**
     * Check for form accessibility issues
     *
     * @param DOMDocument $dom   DOM document.
     * @param DOMXPath    $xpath XPath instance.
     * @param string      $html  Raw HTML content.
     * @return array<array> Array of issues found.
     */
    public function check(DOMDocument $dom, DOMXPath $xpath, string $html): array {
        $issues = [];

        // Check form inputs without labels
        $issues = array_merge($issues, $this->check_missing_labels($xpath));

        // Check empty labels
        $issues = array_merge($issues, $this->check_empty_labels($xpath));

        // Check required fields without indication
        $issues = array_merge($issues, $this->check_required_fields($xpath));

        // Check fieldset without legend
        $issues = array_merge($issues, $this->check_fieldset_legend($xpath));

        // Check button accessibility
        $issues = array_merge($issues, $this->check_buttons($xpath));

        return $issues;
    }

    /**
     * Check for form inputs without labels
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_missing_labels(DOMXPath $xpath): array {
        $issues = [];
        
        // Get all inputs that should have labels (exclude hidden, submit, button, reset, image)
        $inputs = $xpath->query('//input[not(@type="hidden") and not(@type="submit") and not(@type="button") and not(@type="reset") and not(@type="image")] | //select | //textarea');

        foreach ($inputs as $input) {
            if (!$this->is_visible($input)) {
                continue;
            }

            $has_label = false;
            $input_id = $input->getAttribute('id');

            // Check for associated label
            if (!empty($input_id)) {
                $labels = $xpath->query("//label[@for='{$input_id}']");
                if ($labels->length > 0) {
                    $has_label = true;
                }
            }

            // Check for wrapping label
            if (!$has_label) {
                $parent = $input->parentNode;
                while ($parent) {
                    if ($parent->nodeName === 'label') {
                        $has_label = true;
                        break;
                    }
                    $parent = $parent->parentNode;
                }
            }

            // Check for aria-label or aria-labelledby
            if (!$has_label) {
                $aria_label = $input->getAttribute('aria-label');
                $aria_labelledby = $input->getAttribute('aria-labelledby');
                
                if (!empty($aria_label) || !empty($aria_labelledby)) {
                    $has_label = true;
                }
            }

            // Check for title attribute (less preferred but acceptable)
            if (!$has_label) {
                $title = $input->getAttribute('title');
                if (!empty($title)) {
                    $has_label = true;
                }
            }

            if (!$has_label) {
                $type = $input->getAttribute('type') ?: $input->nodeName;
                
                $issues[] = $this->create_issue([
                    'type' => 'missing_form_label',
                    'severity' => 'critical',
                    'wcag' => '1.3.1',
                    'category' => 'forms',
                    'message' => sprintf(__('%s input missing label', 'complyflow'), ucfirst($type)),
                    'element' => $this->get_element_html($input),
                    'selector' => $this->get_selector($input),
                    'fix' => __('Add <label> element or aria-label attribute', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/labels-or-instructions',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check for empty labels
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_empty_labels(DOMXPath $xpath): array {
        $issues = [];
        $labels = $xpath->query('//label');

        foreach ($labels as $label) {
            if (!$this->is_visible($label)) {
                continue;
            }

            $text = $this->get_text_content($label);

            if ($this->is_empty_string($text)) {
                $issues[] = $this->create_issue([
                    'type' => 'empty_label',
                    'severity' => 'serious',
                    'wcag' => '2.4.6',
                    'category' => 'forms',
                    'message' => __('Form label is empty', 'complyflow'),
                    'element' => $this->get_element_html($label),
                    'selector' => $this->get_selector($label),
                    'fix' => __('Add descriptive text to the label', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/headings-and-labels',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check required fields without indication
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_required_fields(DOMXPath $xpath): array {
        $issues = [];
        $required_inputs = $xpath->query('//*[@required]');

        foreach ($required_inputs as $input) {
            if (!$this->is_visible($input)) {
                continue;
            }

            // Check if required is indicated via aria-required
            $aria_required = $input->getAttribute('aria-required');
            
            if ($aria_required !== 'true') {
                $issues[] = $this->create_issue([
                    'type' => 'required_no_aria',
                    'severity' => 'minor',
                    'wcag' => '3.3.2',
                    'category' => 'forms',
                    'message' => __('Required field should have aria-required="true"', 'complyflow'),
                    'element' => $this->get_element_html($input),
                    'selector' => $this->get_selector($input),
                    'fix' => __('Add aria-required="true" to the input element', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/labels-or-instructions',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check fieldset without legend
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_fieldset_legend(DOMXPath $xpath): array {
        $issues = [];
        $fieldsets = $xpath->query('//fieldset');

        foreach ($fieldsets as $fieldset) {
            if (!$this->is_visible($fieldset)) {
                continue;
            }

            $legends = $xpath->query('.//legend', $fieldset);

            if ($legends->length === 0) {
                $issues[] = $this->create_issue([
                    'type' => 'fieldset_no_legend',
                    'severity' => 'serious',
                    'wcag' => '1.3.1',
                    'category' => 'forms',
                    'message' => __('Fieldset missing legend', 'complyflow'),
                    'element' => $this->get_element_html($fieldset, 150),
                    'selector' => $this->get_selector($fieldset),
                    'fix' => __('Add <legend> element as first child of fieldset', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/tutorials/forms/grouping/',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check button accessibility
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_buttons(DOMXPath $xpath): array {
        $issues = [];
        
        // Check buttons without text
        $buttons = $xpath->query('//button | //input[@type="button"] | //input[@type="submit"] | //input[@type="reset"]');

        foreach ($buttons as $button) {
            if (!$this->is_visible($button)) {
                continue;
            }

            $text = '';

            if ($button->nodeName === 'button') {
                $text = $this->get_text_content($button);
            } elseif ($button->nodeName === 'input') {
                $text = $button->getAttribute('value');
            }

            // Check aria-label if no text
            if ($this->is_empty_string($text)) {
                $aria_label = $button->getAttribute('aria-label');
                $aria_labelledby = $button->getAttribute('aria-labelledby');

                if (empty($aria_label) && empty($aria_labelledby)) {
                    $issues[] = $this->create_issue([
                        'type' => 'button_no_text',
                        'severity' => 'critical',
                        'wcag' => '4.1.2',
                        'category' => 'forms',
                        'message' => __('Button has no accessible text', 'complyflow'),
                        'element' => $this->get_element_html($button),
                        'selector' => $this->get_selector($button),
                        'fix' => __('Add text content or aria-label to describe button purpose', 'complyflow'),
                        'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/name-role-value',
                    ]);
                }
            }
        }

        return $issues;
    }
}
