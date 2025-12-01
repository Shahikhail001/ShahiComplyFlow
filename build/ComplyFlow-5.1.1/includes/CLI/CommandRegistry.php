<?php
/**
 * Main WP-CLI Command
 *
 * Registers all ComplyFlow WP-CLI commands.
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
 * ComplyFlow CLI Command Registration
 *
 * @since 1.0.0
 */
class CommandRegistry {
    /**
     * Register all WP-CLI commands
     *
     * @return void
     */
    public static function register(): void {
        if (!class_exists('WP_CLI')) {
            return;
        }

        // Register main command
        WP_CLI::add_command('complyflow', __CLASS__);

        // Register subcommands
        WP_CLI::add_command('complyflow scan', ScanCommand::class);
        WP_CLI::add_command('complyflow consent', ConsentCommand::class);
        WP_CLI::add_command('complyflow dsr', DSRCommand::class);
        WP_CLI::add_command('complyflow settings', SettingsCommand::class);
        WP_CLI::add_command('complyflow cache', CacheCommand::class);
    }

    /**
     * Display ComplyFlow information.
     *
     * ## EXAMPLES
     *
     *     wp complyflow
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void {
        WP_CLI::log(WP_CLI::colorize('%BComplyFlow - WordPress Compliance & Accessibility Suite%n'));
        WP_CLI::log('');
        WP_CLI::log('Version: ' . COMPLYFLOW_VERSION);
        WP_CLI::log('');
        WP_CLI::log('Available commands:');
        WP_CLI::log('  wp complyflow scan        - Manage accessibility scans');
        WP_CLI::log('  wp complyflow consent     - Manage consent logs');
        WP_CLI::log('  wp complyflow dsr         - Manage DSR requests');
        WP_CLI::log('  wp complyflow settings    - Manage plugin settings');
        WP_CLI::log('  wp complyflow cache       - Manage cache');
        WP_CLI::log('');
        WP_CLI::log('For help on a specific command, use:');
        WP_CLI::log('  wp complyflow <command> --help');
    }
}
