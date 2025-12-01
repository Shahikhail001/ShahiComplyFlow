<?php
namespace ComplyFlow\Modules\DevTools;

use ComplyFlow\Modules\DevTools\JS_SDK;
use ComplyFlow\Modules\DevTools\Hooks;
use ComplyFlow\Modules\DevTools\CodeExamples;

if (!defined('ABSPATH')) {
    exit;
}

class DevToolsModule {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu'], 70);
        add_action('wp_footer', [$this, 'output_sdk']);
        Hooks::register_hooks();
    }

    public function add_admin_menu() {
        add_submenu_page(
            'complyflow',
            __('Developer Tools', 'complyflow'),
            __('Developer Tools', 'complyflow'),
            'manage_options',
            'complyflow-dev-tools',
            [$this, 'render_dev_tools_page']
        );
    }

    public function render_dev_tools_page() {
        echo '<h2>' . esc_html__('ComplyFlow Developer Tools', 'complyflow') . '</h2>';
        echo '<h3>' . esc_html__('JavaScript SDK', 'complyflow') . '</h3>';
        JS_SDK::output_sdk();
        echo '<pre><code>' . esc_html(file_get_contents(COMPLYFLOW_PATH . 'includes/Modules/DevTools/JS_SDK.php')) . '</code></pre>';
        echo '<h3>' . esc_html__('Hooks & Filters', 'complyflow') . '</h3>';
        $examples = CodeExamples::get_examples();
        foreach ($examples as $title => $code) {
            echo '<strong>' . esc_html($title) . '</strong><br><pre><code>' . esc_html($code) . '</code></pre>';
        }
    }
}
