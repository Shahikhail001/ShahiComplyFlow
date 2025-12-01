<?php
/**
 * Settings Repository Wrapper
 *
 * Provides a shared interface for retrieving and storing plugin settings
 * across modules, CLI commands, and admin views.
 *
 * @package ComplyFlow\Core
 */

namespace ComplyFlow\Core;

use ComplyFlow\Admin\Settings;

if (!defined('ABSPATH')) {
    exit;
}

class SettingsRepository {
    private static ?self $instance = null;

    private Settings $settings;

    public function __construct(?Settings $settings = null) {
        $this->settings = $settings ?? new Settings();
    }

    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(string $key, mixed $default = null): mixed {
        return $this->settings->get($key, $default);
    }

    public function set(string $key, mixed $value): bool {
        return $this->settings->set($key, $value);
    }

    public function get_all(): array {
        return $this->settings->get_all();
    }

    public function save(array $values): bool {
        return $this->settings->save($values);
    }

    public function export(): string {
        return $this->settings->export();
    }

    public function import(string $json): array {
        return $this->settings->import($json);
    }

    public function reset(): bool {
        return $this->settings->reset();
    }

    public function get_settings(): Settings {
        return $this->settings;
    }
}
