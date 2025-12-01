<?php
/**
 * Multimedia Accessibility Checker
 *
 * Checks WCAG 1.2.1, 1.2.2, 1.2.3 (Captions, Audio Description).
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
 * Multimedia Checker Class
 *
 * @since 1.0.0
 */
class MultimediaChecker extends BaseChecker {
    /**
     * Check for multimedia accessibility issues
     *
     * @param DOMDocument $dom   DOM document.
     * @param DOMXPath    $xpath XPath instance.
     * @param string      $html  Raw HTML content.
     * @return array<array> Array of issues found.
     */
    public function check(DOMDocument $dom, DOMXPath $xpath, string $html): array {
        $issues = [];

        // Check videos without captions
        $issues = array_merge($issues, $this->check_video_captions($xpath));

        // Check audio elements
        $issues = array_merge($issues, $this->check_audio_elements($xpath));

        return $issues;
    }

    /**
     * Check videos for caption tracks
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_video_captions(DOMXPath $xpath): array {
        $issues = [];
        $videos = $xpath->query('//video');

        foreach ($videos as $video) {
            // Check for track elements
            $tracks = $xpath->query('.//track[@kind="captions" or @kind="subtitles"]', $video);

            if ($tracks->length === 0) {
                $issues[] = $this->create_issue([
                    'type' => 'video_no_captions',
                    'severity' => 'serious',
                    'wcag' => '1.2.2',
                    'category' => 'multimedia',
                    'message' => __('Video missing captions', 'complyflow'),
                    'element' => $this->get_element_html($video, 150),
                    'selector' => $this->get_selector($video),
                    'fix' => __('Add <track kind="captions"> element to video', 'complyflow'),
                    'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/captions-prerecorded',
                ]);
            }
        }

        return $issues;
    }

    /**
     * Check audio elements
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array<array> Issues found.
     */
    private function check_audio_elements(DOMXPath $xpath): array {
        $issues = [];
        $audios = $xpath->query('//audio');

        foreach ($audios as $audio) {
            // Basic check - audio should have transcript link nearby or in description
            $issues[] = $this->create_issue([
                'type' => 'audio_check_transcript',
                'severity' => 'minor',
                'wcag' => '1.2.1',
                'category' => 'multimedia',
                'message' => __('Audio element detected - verify transcript is provided', 'complyflow'),
                'element' => $this->get_element_html($audio, 150),
                'selector' => $this->get_selector($audio),
                'fix' => __('Provide a text transcript or captions for audio content', 'complyflow'),
                'learn_more' => 'https://www.w3.org/WAI/WCAG22/Understanding/audio-only-and-video-only-prerecorded',
            ]);
        }

        return $issues;
    }
}
