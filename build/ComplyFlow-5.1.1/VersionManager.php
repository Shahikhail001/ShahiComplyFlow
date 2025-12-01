<?php
namespace ComplyFlow;

// VersionManager.php - Tracks policy revisions, supports diff viewing, rollback, and manual edit tracking
// Stores versions in WordPress options table with timestamps and user info

if (!defined('ABSPATH')) {
    exit;
}

class VersionManager {
    const OPTION_KEY = 'complyflow_policy_versions';

    /**
     * Save a new version of a document
     * @param string $type Policy type (privacy, terms, cookie)
     * @param string $content Document content
     * @param string $user User who made the change
     */
    public static function save_version($type, $content, $user) {
        $versions = get_option(self::OPTION_KEY, []);
        $versions[$type][] = [
            'content' => $content,
            'timestamp' => current_time('mysql'),
            'user' => $user,
        ];
        update_option(self::OPTION_KEY, $versions);
    }

    /**
     * Get all versions for a document type
     * @param string $type
     * @return array
     */
    public static function get_versions($type) {
        $versions = get_option(self::OPTION_KEY, []);
        return isset($versions[$type]) ? $versions[$type] : [];
    }

    /**
     * Get a specific version
     * @param string $type
     * @param int $index
     * @return array|null
     */
    public static function get_version($type, $index) {
        $versions = self::get_versions($type);
        return isset($versions[$index]) ? $versions[$index] : null;
    }

    /**
     * Rollback to a specific version
     * @param string $type
     * @param int $index
     * @return bool
     */
    public static function rollback($type, $index) {
        $versions = self::get_versions($type);
        if (!isset($versions[$index])) return false;
        // Overwrite current document with selected version
        // This assumes a set_policy_content($type, $content) function exists
        if (function_exists('ComplyFlow\\set_policy_content')) {
            call_user_func('ComplyFlow\\set_policy_content', $type, $versions[$index]['content']);
            return true;
        }
        return false;
    }

    /**
     * Get diff between two versions
     * @param string $type
     * @param int $indexA
     * @param int $indexB
     * @return string HTML diff
     */
    public static function get_diff($type, $indexA, $indexB) {
        $versions = self::get_versions($type);
        if (!isset($versions[$indexA]) || !isset($versions[$indexB])) return '';
        $a = $versions[$indexA]['content'];
        $b = $versions[$indexB]['content'];
        // Simple diff (line by line)
        $diff = self::simple_diff($a, $b);
        return $diff;
    }

    /**
     * Simple line-by-line diff (returns HTML)
     */
    private static function simple_diff($old, $new) {
        $oldLines = explode("\n", $old);
        $newLines = explode("\n", $new);
        $diff = '<pre style="background:#f9f9f9;padding:10px;">';
        foreach ($oldLines as $i => $line) {
            if (!isset($newLines[$i])) {
                $diff .= '<span style="background:#ffecec;">- ' . htmlspecialchars($line) . "</span>\n";
            } elseif ($line !== $newLines[$i]) {
                $diff .= '<span style="background:#fffbe5;">~ ' . htmlspecialchars($line) . "</span>\n";
            } else {
                $diff .= '  ' . htmlspecialchars($line) . "\n";
            }
        }
        foreach (array_slice($newLines, count($oldLines)) as $line) {
            $diff .= '<span style="background:#eaffea;">+ ' . htmlspecialchars($line) . "</span>\n";
        }
        $diff .= '</pre>';
        return $diff;
    }
}
