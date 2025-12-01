<?php
namespace ComplyFlow\Modules\Forms;

if (!defined('ABSPATH')) {
    exit;
}

class EncryptionManager {
    /**
     * Encrypt form data before saving
     * @param string $data
     * @return string
     */
    public static function encrypt($data) {
        $key = self::get_key();
        return openssl_encrypt($data, 'AES-256-CBC', $key, 0, substr($key, 0, 16));
    }

    /**
     * Decrypt form data
     * @param string $data
     * @return string
     */
    public static function decrypt($data) {
        $key = self::get_key();
        return openssl_decrypt($data, 'AES-256-CBC', $key, 0, substr($key, 0, 16));
    }

    /**
     * Get encryption key
     * @return string
     */
    private static function get_key() {
        $key = get_option('complyflow_encryption_key');
        if (!$key) {
            $key = wp_generate_password(32, true, true);
            update_option('complyflow_encryption_key', $key);
        }
        return $key;
    }
}
