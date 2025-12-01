<?php
/**
 * Base contract for ComplyFlow modules.
 *
 * @package ComplyFlow\Core\Interfaces
 */

namespace ComplyFlow\Core\Interfaces;

if (!defined('ABSPATH')) {
    exit;
}

interface ModuleInterface {
    /**
     * Return metadata describing the module.
     *
     * @return array<string, mixed>
     */
    public static function get_info(): array;

    /**
     * Bootstraps the module (register hooks, enqueue assets, etc.).
     */
    public function init(): void;
}
