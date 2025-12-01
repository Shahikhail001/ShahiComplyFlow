<?php
/**
 * Backwards-compatible alias that exposes the core SettingsRepository
 * under the legacy ComplyFlow\Core\Repositories namespace.
 *
 * @package ComplyFlow\Core\Repositories
 */

namespace ComplyFlow\Core\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class SettingsRepository extends \ComplyFlow\Core\SettingsRepository {}
