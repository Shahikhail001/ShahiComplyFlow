<?php
/**
 * WP-CLI Base Command Class
 *
 * Abstract base class for all WP-CLI commands.
 *
 * @package ComplyFlow\CLI
 * @since 1.0.0
 */

namespace ComplyFlow\CLI;

use WP_CLI;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Abstract CLI Command Class
 *
 * @since 1.0.0
 */
abstract class BaseCommand {
    /**
     * Output success message
     *
     * @param string $message Success message.
     * @return void
     */
    protected function success(string $message): void {
        WP_CLI::success($message);
    }

    /**
     * Output error message and halt
     *
     * @param string $message Error message.
     * @return void
     */
    protected function error(string $message): void {
        WP_CLI::error($message);
    }

    /**
     * Output warning message
     *
     * @param string $message Warning message.
     * @return void
     */
    protected function warning(string $message): void {
        WP_CLI::warning($message);
    }

    /**
     * Output log message
     *
     * @param string $message Log message.
     * @return void
     */
    protected function log(string $message): void {
        WP_CLI::log($message);
    }

    /**
     * Display progress bar
     *
     * @param int $count Total count.
     * @return \cli\progress\Bar
     */
    protected function progress(int $count) {
        return WP_CLI\Utils\make_progress_bar('Processing', $count);
    }

    /**
     * Format items as table
     *
     * @param array  $items  Items to display.
     * @param array  $fields Fields to show.
     * @param string $format Output format.
     * @return void
     */
    protected function format_items(array $items, array $fields, string $format = 'table'): void {
        WP_CLI\Utils\format_items($format, $items, $fields);
    }

    /**
     * Confirm action
     *
     * @param string $message Confirmation message.
     * @return void
     */
    protected function confirm(string $message): void {
        WP_CLI::confirm($message);
    }

    /**
     * Colorize output
     *
     * @param string $text  Text to colorize.
     * @param string $color Color code.
     * @return string
     */
    protected function colorize(string $text, string $color = '%G'): string {
        return WP_CLI::colorize($color . $text . '%n');
    }
}
