<?php
/**
 * Plugin Name:       ComplyFlow
 * Plugin URI:        https://shahisoft.gec5.com/complyflow
 * Description:       Ultimate WordPress Compliance & Accessibility Suite - Comprehensive WCAG 2.2, GDPR, CCPA, and data privacy compliance automation
 * Version:           5.1.1
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Author:            ShahiSoft Team
 * Author URI:        https://shahisoft.gec5.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       complyflow
 * Domain Path:       /languages
 *
 * @package ComplyFlow
 * @since 1.0.0
 */

namespace ComplyFlow;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin Constants
 */
define('COMPLYFLOW_VERSION', '5.1.1');
define('COMPLYFLOW_PATH', plugin_dir_path(__FILE__));
define('COMPLYFLOW_URL', plugin_dir_url(__FILE__));
define('COMPLYFLOW_BASENAME', plugin_basename(__FILE__));
define('COMPLYFLOW_FILE', __FILE__);

/**
 * Minimum Requirements
 */
define('COMPLYFLOW_MIN_PHP_VERSION', '8.0.0');
define('COMPLYFLOW_MIN_WP_VERSION', '6.4.0');

/**
 * Composer autoloader
 */
if (file_exists(COMPLYFLOW_PATH . 'vendor/autoload.php')) {
    require_once COMPLYFLOW_PATH . 'vendor/autoload.php';
}

/**
 * Check system requirements before activation
 *
 * @return void
 */
function complyflow_check_requirements(): void {
    $errors = [];

    // Check PHP version
    if (version_compare(PHP_VERSION, COMPLYFLOW_MIN_PHP_VERSION, '<')) {
        $errors[] = sprintf(
            /* translators: 1: Required PHP version, 2: Current PHP version */
            __('ComplyFlow requires PHP version %1$s or higher. You are running PHP version %2$s.', 'complyflow'),
            COMPLYFLOW_MIN_PHP_VERSION,
            PHP_VERSION
        );
    }

    // Check WordPress version
    global $wp_version;
    if (version_compare($wp_version, COMPLYFLOW_MIN_WP_VERSION, '<')) {
        $errors[] = sprintf(
            /* translators: 1: Required WordPress version, 2: Current WordPress version */
            __('ComplyFlow requires WordPress version %1$s or higher. You are running WordPress version %2$s.', 'complyflow'),
            COMPLYFLOW_MIN_WP_VERSION,
            $wp_version
        );
    }

    // Display errors if any
    if (!empty($errors)) {
        deactivate_plugins(COMPLYFLOW_BASENAME);
        wp_die(
            '<h1>' . esc_html__('Plugin Activation Error', 'complyflow') . '</h1>' .
            '<p>' . implode('</p><p>', array_map('esc_html', $errors)) . '</p>',
            esc_html__('Plugin Activation Error', 'complyflow'),
            ['back_link' => true]
        );
    }
}

/**
 * Initialize the plugin
 *
 * @return void
 */
function complyflow_init(): void {
    // Initialize the main plugin class
    if (class_exists('ComplyFlow\Core\Plugin')) {
        Core\Plugin::instance()->init();
    }
}

/**
 * Plugin activation hook
 *
 * @return void
 */
function complyflow_activate(): void {
    complyflow_check_requirements();
    
    if (class_exists('ComplyFlow\Core\Activator')) {
        Core\Activator::activate();
    }
}

/**
 * Plugin deactivation hook
 *
 * @return void
 */
function complyflow_deactivate(): void {
    if (class_exists('ComplyFlow\Core\Deactivator')) {
        Core\Deactivator::deactivate();
    }
}

/**
 * Check requirements on admin init (for manual checks)
 */
add_action('admin_init', __NAMESPACE__ . '\complyflow_check_requirements');

/**
 * Initialize plugin after WordPress is loaded
 */
add_action('plugins_loaded', __NAMESPACE__ . '\complyflow_init', 10);

/**
 * Register activation and deactivation hooks
 */
register_activation_hook(__FILE__, __NAMESPACE__ . '\complyflow_activate');
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\complyflow_deactivate');
