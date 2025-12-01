<?php
/**
 * Settings Page Template
 *
 * @package ComplyFlow\Admin
 * @since 1.0.0
 */

use ComplyFlow\Admin\Settings;
use ComplyFlow\Admin\SettingsRenderer;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get settings instance from global scope (set by Plugin class)
global $complyflow_settings;
if (!$complyflow_settings instanceof Settings) {
    wp_die(esc_html__('Settings not initialized.', 'complyflow'));
}

$renderer = new SettingsRenderer($complyflow_settings);
$tabs = $complyflow_settings->get_tabs();
$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : array_key_first($tabs);

// Handle form submission
if (isset($_POST['complyflow_save_settings']) && check_admin_referer('complyflow_settings_save')) {
    $new_settings = isset($_POST['complyflow_settings']) ? 
        array_map('sanitize_text_field', wp_unslash($_POST['complyflow_settings'])) : [];
    
    // Validate settings
    $validation = $complyflow_settings->validate($new_settings);
    
    if ($validation['valid']) {
        $complyflow_settings->save($new_settings);
        add_settings_error(
            'complyflow_messages',
            'complyflow_message',
            __('Settings saved successfully.', 'complyflow'),
            'updated'
        );
    } else {
        foreach ($validation['errors'] as $error) {
            add_settings_error(
                'complyflow_messages',
                'complyflow_error',
                $error,
                'error'
            );
        }
    }
}

// Handle reset to defaults
if (isset($_POST['complyflow_reset_settings']) && check_admin_referer('complyflow_settings_reset')) {
    $complyflow_settings->reset();
    add_settings_error(
        'complyflow_messages',
        'complyflow_message',
        __('Settings reset to defaults.', 'complyflow'),
        'updated'
    );
}
?>

<style>
    .complyflow-save-indicator {
        display: none;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border: 1px solid var(--cf-border, #d0d7e3);
        border-radius: 8px;
        background: var(--cf-surface, #f7f9fc);
        margin: 16px 0;
        font-weight: 600;
        color: var(--cf-text, #0f172a);
    }

    .complyflow-save-indicator .spinner {
        float: none;
        margin: 0;
    }

    .complyflow-save-indicator.is-active {
        display: flex;
    }

    .complyflow-settings-notices .notice {
        margin: 16px 0;
    }
</style>

<div class="complyflow-admin-page">
    <!-- Page Header with Gradient -->
    <div class="complyflow-page-header">
        <h1><?php esc_html_e('Settings', 'complyflow'); ?></h1>
        <p class="page-subtitle"><?php esc_html_e('Configure plugin modules and compliance settings', 'complyflow'); ?></p>
    </div>

    <div id="complyflow-save-indicator" class="complyflow-save-indicator" role="status" aria-live="assertive" aria-hidden="true">
        <span class="spinner"></span>
        <span class="save-text"><?php esc_html_e('Saving changes...', 'complyflow'); ?></span>
    </div>

    <div id="complyflow-settings-notices" class="complyflow-settings-notices" aria-live="polite"></div>

    <div class="complyflow-page-content">
        <?php settings_errors('complyflow_messages'); ?>

        <div class="complyflow-card">
            <!-- Tabs Navigation -->
        <h2 class="nav-tab-wrapper">
            <?php foreach ($tabs as $tab_key => $tab_data) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-settings&tab=' . $tab_key)); ?>" 
                   class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                    <?php if (!empty($tab_data['icon'])) : ?>
                        <span class="dashicons <?php echo esc_attr($tab_data['icon']); ?>"></span>
                    <?php endif; ?>
                    <?php echo esc_html($tab_data['title']); ?>
                </a>
            <?php endforeach; ?>
        </h2>

        <form method="post" action="" class="complyflow-settings-form">
            <?php wp_nonce_field('complyflow_settings_save'); ?>
            
            <div class="complyflow-tab-content active">
                <?php
                $sections = $complyflow_settings->get_sections($active_tab);
                
                foreach ($sections as $section_key => $section_config) :
                    ?>
                    <div class="complyflow-settings-section">
                        <h2><?php echo esc_html($section_config['title']); ?></h2>
                        <?php if (!empty($section_config['description'])) : ?>
                            <p class="description"><?php echo esc_html($section_config['description']); ?></p>
                        <?php endif; ?>

                        <table class="form-table" role="presentation">
                            <tbody>
                                <?php
                                $fields = $complyflow_settings->get_fields($section_key);
                                foreach ($fields as $field_key => $field_config) :
                                    ?>
                                    <tr>
                                        <th scope="row">
                                            <label for="<?php echo esc_attr($field_key); ?>">
                                                <?php echo esc_html($field_config['label']); ?>
                                                <?php if (!empty($field_config['required'])) : ?>
                                                    <span class="required">*</span>
                                                <?php endif; ?>
                                            </label>
                                        </th>
                                        <td>
                                            <?php $renderer->render_field($field_key, $field_config); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 2px solid var(--cf-surface-alt);">
                <?php submit_button(
                    __('Save Settings', 'complyflow'),
                    'primary complyflow-button-primary',
                    'complyflow_save_settings',
                    false
                ); ?>

                <?php submit_button(
                    __('Reset to Defaults', 'complyflow'),
                    'secondary complyflow-button-secondary',
                    'complyflow_reset_settings',
                    false,
                    [
                        'onclick' => 'return confirm("' . esc_js(__('Are you sure you want to reset all settings to defaults?', 'complyflow')) . '");'
                    ]
                ); ?>

                <button type="button" id="complyflow-export-settings" class="button complyflow-button-secondary">
                    <?php esc_html_e('Export Settings', 'complyflow'); ?>
                </button>

                <button type="button" id="complyflow-import-settings" class="button complyflow-button-secondary">
                    <?php esc_html_e('Import Settings', 'complyflow'); ?>
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Export settings
    $('#complyflow-export-settings').on('click', function() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_export_settings',
                nonce: '<?php echo wp_create_nonce('complyflow_export_settings'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    const blob = new Blob([JSON.stringify(response.data, null, 2)], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'complyflow-settings-' + new Date().toISOString().split('T')[0] + '.json';
                    a.click();
                    URL.revokeObjectURL(url);
                } else {
                    alert(response.data.message || '<?php esc_html_e('Export failed.', 'complyflow'); ?>');
                }
            }
        });
    });

    // Import settings - show modal
    $('#complyflow-import-settings').on('click', function() {
        $('#complyflow-import-modal').show();
    });

    // Import settings - cancel
    $('#complyflow-import-cancel').on('click', function() {
        $('#complyflow-import-modal').hide();
        $('#complyflow-import-json').val('');
    });

    // Import settings - confirm
    $('#complyflow-import-confirm').on('click', function() {
        const json = $('#complyflow-import-json').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_import_settings',
                nonce: '<?php echo wp_create_nonce('complyflow_import_settings'); ?>',
                json: json
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php esc_html_e('Settings imported successfully. Reloading page...', 'complyflow'); ?>');
                    location.reload();
                } else {
                    alert(response.data.message || '<?php esc_html_e('Import failed.', 'complyflow'); ?>');
                }
            }
        });
    });
});
</script>
