<?php
/**
 * Module Manager Class
 *
 * Manages plugin modules - loading, initialization, and dependencies.
 *
 * @package ComplyFlow\Core
 * @since 1.0.0
 */

namespace ComplyFlow\Core;

use ComplyFlow\Admin\Settings;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Module Manager Class
 *
 * @since 1.0.0
 */
class ModuleManager {
    /**
     * Settings instance
     *
     * @var Settings
     */
    private Settings $settings;

    /**
     * Registered modules
     *
     * @var array<string, array<string, mixed>>
     */
    private array $modules = [];

    /**
     * Loaded module instances
     *
     * @var array<string, object>
     */
    private array $loaded_modules = [];

    /**
     * Constructor
     *
     * @param Settings $settings Settings instance.
     */
    public function __construct(Settings $settings) {
        $this->settings = $settings;
        $this->register_core_modules();
    }

    /**
     * Register core modules
     *
     * @return void
     */
    private function register_core_modules(): void {
        // Consent Management Module
        $this->register_module('consent', [
            'name' => __('Consent Management', 'complyflow'),
            'description' => __('GDPR/CCPA compliant consent banners and cookie management', 'complyflow'),
            'class' => 'ComplyFlow\Modules\Consent\ConsentModule',
            'enabled_by_default' => true,
            'dependencies' => [],
            'required_capability' => 'manage_options',
            'version' => '1.0.0',
        ]);

        // Accessibility Scanner Module
        $this->register_module('accessibility', [
            'name' => __('Accessibility Scanner', 'complyflow'),
            'description' => __('WCAG 2.2 Level AA compliance scanning and reporting', 'complyflow'),
            'class' => 'ComplyFlow\Modules\Accessibility\AccessibilityModule',
            'enabled_by_default' => true,
            'dependencies' => [],
            'required_capability' => 'manage_options',
            'version' => '1.0.0',
        ]);

        // Data Subject Rights Module
        $this->register_module('dsr', [
            'name' => __('Data Subject Rights Portal', 'complyflow'),
            'description' => __('GDPR Article 15-20 compliance portal for data requests', 'complyflow'),
            'class' => 'ComplyFlow\Modules\DSR\DSRModule',
            'enabled_by_default' => true,
            'dependencies' => [],
            'required_capability' => 'manage_options',
            'version' => '1.0.0',
        ]);

        // Document Manager Module
        $this->register_module('documents', [
            'name' => __('Document Manager', 'complyflow'),
            'description' => __('Privacy policy, terms of service, and cookie policy management', 'complyflow'),
            'class' => 'ComplyFlow\Modules\Documents\DocumentsModule',
            'enabled_by_default' => true,
            'dependencies' => [],
            'required_capability' => 'manage_options',
            'version' => '1.0.0',
        ]);

        // Cookie Inventory Module
        $this->register_module('inventory', [
            'name' => __('Cookie Inventory', 'complyflow'),
            'description' => __('Automatic detection and cataloging of cookies and trackers', 'complyflow'),
            'class' => 'ComplyFlow\Modules\Cookie\CookieModule',
            'enabled_by_default' => true,
            'dependencies' => [],
            'required_capability' => 'manage_options',
            'version' => '1.0.0',
        ]);

        // Dashboard Module
        $this->register_module('dashboard', [
            'name' => __('Dashboard', 'complyflow'),
            'description' => __('Overview dashboard with compliance metrics and quick actions', 'complyflow'),
            'class' => 'ComplyFlow\Modules\Dashboard\DashboardModule',
            'enabled_by_default' => true,
            'dependencies' => [],
            'required_capability' => 'manage_options',
            'version' => '1.0.0',
        ]);

        /**
         * Fires after core modules are registered
         *
         * @since 1.0.0
         *
         * @param ModuleManager $this Module manager instance.
         */
        do_action('complyflow_modules_registered', $this);
    }

    /**
     * Register a module
     *
     * @param string               $id     Module ID (unique identifier).
     * @param array<string, mixed> $config Module configuration.
     * @return bool True if registered, false if already exists.
     */
    public function register_module(string $id, array $config): bool {
        if (isset($this->modules[$id])) {
            return false;
        }

        $defaults = [
            'name' => '',
            'description' => '',
            'class' => '',
            'enabled_by_default' => true,
            'dependencies' => [],
            'required_capability' => 'manage_options',
            'version' => '1.0.0',
        ];

        $this->modules[$id] = wp_parse_args($config, $defaults);

        return true;
    }

    /**
     * Unregister a module
     *
     * @param string $id Module ID.
     * @return bool True if unregistered, false if doesn't exist.
     */
    public function unregister_module(string $id): bool {
        if (!isset($this->modules[$id])) {
            return false;
        }

        unset($this->modules[$id]);
        unset($this->loaded_modules[$id]);

        return true;
    }

    /**
     * Initialize all enabled modules
     *
     * @return void
     */
    public function init_modules(): void {
        foreach ($this->modules as $id => $config) {
            if ($this->is_module_enabled($id)) {
                $this->load_module($id);
            }
        }

        /**
         * Fires after all modules are initialized
         *
         * @since 1.0.0
         *
         * @param ModuleManager $this Module manager instance.
         */
        do_action('complyflow_modules_initialized', $this);
    }

    /**
     * Load a specific module
     *
     * @param string $id Module ID.
     * @return bool True if loaded, false on failure.
     */
    public function load_module(string $id): bool {
        // Already loaded
        if (isset($this->loaded_modules[$id])) {
            return true;
        }

        // Module doesn't exist
        if (!isset($this->modules[$id])) {
            return false;
        }

        $config = $this->modules[$id];

        // Check dependencies
        foreach ($config['dependencies'] as $dependency) {
            if (!$this->is_module_loaded($dependency)) {
                if (!$this->load_module($dependency)) {
                    // Dependency not available
                    error_log(sprintf(
                        'ComplyFlow: Module "%s" requires dependency "%s" which is not available.',
                        $id,
                        $dependency
                    ));
                    return false;
                }
            }
        }

        // Check if class exists
        if (!class_exists($config['class'])) {
            error_log(sprintf(
                'ComplyFlow: Module class "%s" not found for module "%s".',
                $config['class'],
                $id
            ));
            return false;
        }

        // Instantiate module
        try {
            $module = new $config['class']();
            $this->loaded_modules[$id] = $module;

            // Initialize module if it has an init method
            if (method_exists($module, 'init')) {
                $module->init();
            }

            /**
             * Fires when a module is loaded
             *
             * @since 1.0.0
             *
             * @param string $id     Module ID.
             * @param object $module Module instance.
             */
            do_action('complyflow_module_loaded', $id, $module);

            return true;
        } catch (\Exception $e) {
            error_log(sprintf(
                'ComplyFlow: Failed to load module "%s": %s',
                $id,
                $e->getMessage()
            ));
            return false;
        }
    }

    /**
     * Check if a module is enabled
     *
     * @param string $id Module ID.
     * @return bool True if enabled, false otherwise.
     */
    public function is_module_enabled(string $id): bool {
        if (!isset($this->modules[$id])) {
            return false;
        }

        $setting_key = 'module_' . $id . '_enabled';
        $enabled = $this->settings->get($setting_key, $this->modules[$id]['enabled_by_default']);

        /**
         * Filters whether a module is enabled
         *
         * @since 1.0.0
         *
         * @param bool   $enabled Whether module is enabled.
         * @param string $id      Module ID.
         */
        return apply_filters('complyflow_module_enabled', (bool) $enabled, $id);
    }

    /**
     * Check if a module is loaded
     *
     * @param string $id Module ID.
     * @return bool True if loaded, false otherwise.
     */
    public function is_module_loaded(string $id): bool {
        return isset($this->loaded_modules[$id]);
    }

    /**
     * Get a loaded module instance
     *
     * @param string $id Module ID.
     * @return object|null Module instance or null if not loaded.
     */
    public function get_module(string $id): ?object {
        return $this->loaded_modules[$id] ?? null;
    }

    /**
     * Get all registered modules
     *
     * @return array<string, array<string, mixed>> Registered modules.
     */
    public function get_modules(): array {
        return $this->modules;
    }

    /**
     * Get all loaded module instances
     *
     * @return array<string, object> Loaded module instances.
     */
    public function get_loaded_modules(): array {
        return $this->loaded_modules;
    }

    /**
     * Enable a module
     *
     * @param string $id Module ID.
     * @return bool True if enabled, false on failure.
     */
    public function enable_module(string $id): bool {
        if (!isset($this->modules[$id])) {
            return false;
        }

        $setting_key = 'module_' . $id . '_enabled';
        $this->settings->set($setting_key, true);

        // Load the module if not already loaded
        if (!$this->is_module_loaded($id)) {
            return $this->load_module($id);
        }

        return true;
    }

    /**
     * Disable a module
     *
     * @param string $id Module ID.
     * @return bool True if disabled, false on failure.
     */
    public function disable_module(string $id): bool {
        if (!isset($this->modules[$id])) {
            return false;
        }

        $setting_key = 'module_' . $id . '_enabled';
        $this->settings->set($setting_key, false);

        // Unload the module if loaded
        if (isset($this->loaded_modules[$id])) {
            unset($this->loaded_modules[$id]);
        }

        return true;
    }

    /**
     * Get module information
     *
     * @param string $id Module ID.
     * @return array<string, mixed>|null Module info or null if not found.
     */
    public function get_module_info(string $id): ?array {
        if (!isset($this->modules[$id])) {
            return null;
        }

        $info = $this->modules[$id];
        $info['enabled'] = $this->is_module_enabled($id);
        $info['loaded'] = $this->is_module_loaded($id);

        return $info;
    }
}
