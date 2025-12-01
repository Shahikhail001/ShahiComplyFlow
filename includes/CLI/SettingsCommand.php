<?php
/**
 * Settings WP-CLI Command
 *
 * WP-CLI commands for settings management.
 *
 * @package ComplyFlow\CLI
 * @since 1.0.0
 */

namespace ComplyFlow\CLI;

use ComplyFlow\Admin\Settings;
use WP_CLI;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manage ComplyFlow settings.
 *
 * ## EXAMPLES
 *
 *     # Get a setting value
 *     $ wp complyflow settings get site_name
 *
 *     # Set a setting value
 *     $ wp complyflow settings set data_retention_days 365
 *
 *     # Export settings
 *     $ wp complyflow settings export
 *
 * @since 1.0.0
 */
class SettingsCommand extends BaseCommand {
    /**
     * Settings instance
     *
     * @var Settings
     */
    private Settings $settings;

    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = new Settings();
    }

    /**
     * Get a setting value.
     *
     * ## OPTIONS
     *
     * <key>
     * : Setting key.
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: plaintext
     * options:
     *   - plaintext
     *   - json
     * ---
     *
     * ## EXAMPLES
     *
     *     wp complyflow settings get site_name
     *     wp complyflow settings get data_retention_days
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function get(array $args, array $assoc_args): void {
        list($key) = $args;
        $value = $this->settings->get($key);

        if ($value === null) {
            $this->warning("Setting '$key' not found.");
            return;
        }

        $format = $assoc_args['format'] ?? 'plaintext';

        if ($format === 'json') {
            WP_CLI::print_value([$key => $value], ['format' => 'json']);
        } else {
            if (is_array($value) || is_object($value)) {
                WP_CLI::print_value($value, ['format' => 'json']);
            } else {
                $this->log($value);
            }
        }
    }

    /**
     * Set a setting value.
     *
     * ## OPTIONS
     *
     * <key>
     * : Setting key.
     *
     * <value>
     * : Setting value.
     *
     * ## EXAMPLES
     *
     *     wp complyflow settings set site_name "My Site"
     *     wp complyflow settings set data_retention_days 365
     *     wp complyflow settings set module_consent_enabled true
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function set(array $args, array $assoc_args): void {
        list($key, $value) = $args;

        // Convert boolean strings
        if (strtolower($value) === 'true') {
            $value = true;
        } elseif (strtolower($value) === 'false') {
            $value = false;
        }

        // Convert numeric strings
        if (is_numeric($value)) {
            $value = strpos($value, '.') !== false ? (float) $value : (int) $value;
        }

        $this->settings->set($key, $value);

        $this->success("Setting '$key' updated successfully.");
    }

    /**
     * List all settings.
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: table
     * options:
     *   - table
     *   - json
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     wp complyflow settings list
     *     wp complyflow settings list --format=json
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function list(array $args, array $assoc_args): void {
        $all_settings = $this->settings->get_all();
        $format = $assoc_args['format'] ?? 'table';

        if ($format === 'json' || $format === 'yaml') {
            WP_CLI::print_value($all_settings, ['format' => $format]);
        } else {
            $items = [];
            foreach ($all_settings as $key => $value) {
                $display_value = $value;
                if (is_array($value) || is_object($value)) {
                    $display_value = wp_json_encode($value);
                } elseif (is_bool($value)) {
                    $display_value = $value ? 'true' : 'false';
                }
                
                $items[] = [
                    'Key' => $key,
                    'Value' => substr($display_value, 0, 50) . (strlen($display_value) > 50 ? '...' : ''),
                ];
            }

            $this->format_items($items, ['Key', 'Value'], 'table');
        }
    }

    /**
     * Export settings to JSON.
     *
     * ## OPTIONS
     *
     * [--file=<path>]
     * : Output file path. If not specified, outputs to stdout.
     *
     * ## EXAMPLES
     *
     *     wp complyflow settings export
     *     wp complyflow settings export --file=settings.json
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function export(array $args, array $assoc_args): void {
        $export = $this->settings->export();
        $json = wp_json_encode($export, JSON_PRETTY_PRINT);

        if (isset($assoc_args['file'])) {
            $file = $assoc_args['file'];
            $written = file_put_contents($file, $json);
            
            if ($written === false) {
                $this->error("Failed to write to file: $file");
            }
            
            $this->success("Settings exported to: $file");
        } else {
            $this->log($json);
        }
    }

    /**
     * Import settings from JSON.
     *
     * ## OPTIONS
     *
     * <file>
     * : JSON file path to import.
     *
     * [--yes]
     * : Skip confirmation.
     *
     * ## EXAMPLES
     *
     *     wp complyflow settings import settings.json
     *     wp complyflow settings import settings.json --yes
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function import(array $args, array $assoc_args): void {
        list($file) = $args;

        if (!file_exists($file)) {
            $this->error("File not found: $file");
        }

        $json = file_get_contents($file);
        
        if ($json === false) {
            $this->error("Failed to read file: $file");
        }

        if (!isset($assoc_args['yes'])) {
            $this->confirm("Import settings from $file? This will overwrite existing settings.");
        }

        $result = $this->settings->import($json);

        if ($result) {
            $this->success("Settings imported successfully from: $file");
        } else {
            $this->error("Failed to import settings. Invalid JSON format.");
        }
    }

    /**
     * Reset settings to defaults.
     *
     * ## OPTIONS
     *
     * [--yes]
     * : Skip confirmation.
     *
     * ## EXAMPLES
     *
     *     wp complyflow settings reset
     *     wp complyflow settings reset --yes
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function reset(array $args, array $assoc_args): void {
        if (!isset($assoc_args['yes'])) {
            $this->confirm("Reset all settings to defaults? This cannot be undone.");
        }

        $this->settings->reset();

        $this->success("Settings reset to defaults.");
    }
}
