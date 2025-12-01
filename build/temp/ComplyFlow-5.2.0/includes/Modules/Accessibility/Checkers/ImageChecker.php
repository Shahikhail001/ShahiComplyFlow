<?php
/**
 * Image Accessibility Checker
 *
 * Checks WCAG 1.1.1 (Non-text Content) compliance for images.
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
 * Image Checker Class
 *
 * @since 1.0.0
 */
class ImageChecker extends BaseChecker {
    /**
     * Check for image accessibility issues
     *
     * @param DOMDocument $dom   DOM document.
     * @param DOMXPath    $xpath XPath instance.
     * @param string      $html  Raw HTML content.
     * @return array<array> Array of issues found.
     */
    public function check(DOMDocument $dom, DOMXPath $xpath, string $html): array {
        $issues = [];

        // Check images missing alt attributes
        $issues = array_merge($issues, $this->check_missing_alt($xpath));

        // Check images with empty alt on non-decorative images
        $issues = array_merge($issues, $this->check_empty_alt($xpath));

        // Check images with redundant alt text
        $issues = array_merge($issues, $this->check_redundant_alt($xpath));

        // Check images with filename as alt text
        $issues = array_merge($issues, $this->check_filename_as_alt($xpath));

        // Check linked images
        $issues = array_merge($issues, $this->check_linked_images($xpath));

        // Check image maps
        $issues = array_merge($issues, $this->check_image_maps($xpath));

        // Check SVG accessibility
        $issues = array_merge($issues, $this->check_svg_accessibility($xpath));

        return $issues;
    }

    /**
     * Check for images missing alt attributes
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_missing_alt(DOMXPath $xpath): array {
        $issues = [];
        $images = $xpath->query('//img[not(@alt)]');

        foreach ($images as $img) {
            if (!$this->is_visible($img)) {
                continue;
            }

            $issues[] = $this->create_issue([
                'type' => 'missing_alt',
                'severity' => 'critical',
                'wcag' => '1.1.1',
                'category' => 'images',
                'message' => __('Image missing alt attribute', 'complyflow'),
                'element' => $this->get_element_html($img),
                'selector' => $this->get_selector($img),
                'fix' => __('Add descriptive alt text: <img src="..." alt="Description of image">', 'complyflow'),
                'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/non-text-content',
            ]);
        }

        return $issues;
    }

    /**
     * Check images with empty alt on non-decorative images
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_empty_alt(DOMXPath $xpath): array {
        $issues = [];
        $images = $xpath->query('//img[@alt=""]');

        foreach ($images as $img) {
            if (!$this->is_visible($img)) {
                continue;
            }

            // Empty alt is OK for decorative images, but check if this might be informative
            $src = $img->getAttribute('src');
            $title = $img->getAttribute('title');
            
            // If image has title or is inside a link, it's likely informative
            $parent = $img->parentNode;
            $in_link = $parent && $parent->nodeName === 'a';

            if (!empty($title) || $in_link) {
                $issues[] = $this->create_issue([
                    'type' => 'empty_alt_informative',
                    'severity' => 'serious',
                    'wcag' => '1.1.1',
                    'category' => 'images',
                    'message' => __('Informative image has empty alt text', 'complyflow'),
                    'element' => $this->get_element_html($img),
                    'selector' => $this->get_selector($img),
                    'fix' => __('Add descriptive alt text or use role="presentation" if truly decorative', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/non-text-content',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check for redundant alt text (e.g., "image of", "picture of")
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_redundant_alt(DOMXPath $xpath): array {
        $issues = [];
        $images = $xpath->query('//img[@alt]');
        
        $redundant_patterns = [
            'image of',
            'picture of',
            'photo of',
            'graphic of',
            'icon of',
        ];

        foreach ($images as $img) {
            if (!$this->is_visible($img)) {
                continue;
            }

            $alt = strtolower($img->getAttribute('alt'));

            foreach ($redundant_patterns as $pattern) {
                if (strpos($alt, $pattern) === 0) {
                    $issues[] = $this->create_issue([
                        'type' => 'redundant_alt',
                        'severity' => 'minor',
                        'wcag' => '1.1.1',
                        'category' => 'images',
                        'message' => __('Alt text contains redundant phrase', 'complyflow'),
                        'element' => $this->get_element_html($img),
                        'selector' => $this->get_selector($img),
                        'fix' => __('Remove "' . $pattern . '" from alt text - screen readers already announce "image"', 'complyflow'),
                        'learn_more' => 'https://www.w3.org/WAI/tutorials/images/tips/',
                    ]);
                    break;
                }
            }
        }

        return $issues;
    }

    /**
     * Check for filename used as alt text
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_filename_as_alt(DOMXPath $xpath): array {
        $issues = [];
        $images = $xpath->query('//img[@alt]');

        foreach ($images as $img) {
            if (!$this->is_visible($img)) {
                continue;
            }

            $alt = $img->getAttribute('alt');
            $src = $img->getAttribute('src');

            // Extract filename from src
            $filename = basename($src);
            $filename_no_ext = pathinfo($filename, PATHINFO_FILENAME);

            // Check if alt text looks like a filename
            if (
                stripos($alt, $filename) !== false ||
                stripos($alt, $filename_no_ext) !== false ||
                preg_match('/\.(jpg|jpeg|png|gif|svg|webp)$/i', $alt)
            ) {
                $issues[] = $this->create_issue([
                    'type' => 'filename_as_alt',
                    'severity' => 'serious',
                    'wcag' => '1.1.1',
                    'category' => 'images',
                    'message' => __('Alt text appears to be a filename', 'complyflow'),
                    'element' => $this->get_element_html($img),
                    'selector' => $this->get_selector($img),
                    'fix' => __('Replace filename with descriptive text about the image content', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/tutorials/images/',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check linked images
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_linked_images(DOMXPath $xpath): array {
        $issues = [];
        $linked_images = $xpath->query('//a[img]');

        foreach ($linked_images as $link) {
            $images = $xpath->query('.//img', $link);
            
            if ($images->length === 0) {
                continue;
            }

            $has_alt = false;
            $has_text = false;

            // Check if any image has alt text
            foreach ($images as $img) {
                $alt = trim($img->getAttribute('alt'));
                if (!empty($alt)) {
                    $has_alt = true;
                    break;
                }
            }

            // Check if link has text content
            $link_text = $this->get_text_content($link);
            if (!empty($link_text)) {
                $has_text = true;
            }

            // Issue if no alt and no text
            if (!$has_alt && !$has_text) {
                $issues[] = $this->create_issue([
                    'type' => 'linked_image_no_alt',
                    'severity' => 'critical',
                    'wcag' => '2.4.4',
                    'category' => 'images',
                    'message' => __('Linked image has no alt text or link text', 'complyflow'),
                    'element' => $this->get_element_html($link),
                    'selector' => $this->get_selector($link),
                    'fix' => __('Add alt text to image describing the link destination', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/link-purpose-in-context',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check image maps
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_image_maps(DOMXPath $xpath): array {
        $issues = [];
        $maps = $xpath->query('//map');

        foreach ($maps as $map) {
            $areas = $xpath->query('.//area', $map);

            foreach ($areas as $area) {
                $alt = $area->getAttribute('alt');

                if (empty($alt)) {
                    $issues[] = $this->create_issue([
                        'type' => 'image_map_no_alt',
                        'severity' => 'critical',
                        'wcag' => '1.1.1',
                        'category' => 'images',
                        'message' => __('Image map area missing alt text', 'complyflow'),
                        'element' => $this->get_element_html($area),
                        'selector' => $this->get_selector($area),
                        'fix' => __('Add alt attribute to <area> element: <area alt="Description" ...>', 'complyflow'),
                        'learn_more' => 'https://www.w3.org/WAI/tutorials/images/imagemap/',
                    ]);
                }
            }
        }

        return $issues;
    }

    /**
     * Check SVG accessibility
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_svg_accessibility(DOMXPath $xpath): array {
        $issues = [];
        $svgs = $xpath->query('//svg[not(@aria-hidden="true") and not(@role="presentation")]');

        foreach ($svgs as $svg) {
            if (!$this->is_visible($svg)) {
                continue;
            }

            // Check for title or aria-label
            $has_title = $xpath->query('.//title', $svg)->length > 0;
            $has_aria_label = $svg->hasAttribute('aria-label') && !empty($svg->getAttribute('aria-label'));
            $has_aria_labelledby = $svg->hasAttribute('aria-labelledby');

            if (!$has_title && !$has_aria_label && !$has_aria_labelledby) {
                $issues[] = $this->create_issue([
                    'type' => 'svg_no_label',
                    'severity' => 'serious',
                    'wcag' => '1.1.1',
                    'category' => 'images',
                    'message' => __('SVG has no accessible name', 'complyflow'),
                    'element' => $this->get_element_html($svg, 150),
                    'selector' => $this->get_selector($svg),
                    'fix' => __('Add <title> element inside SVG or use aria-label attribute', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/tutorials/images/decorative/',
                ]);
            }
        }

        return $issues;
    }
}
