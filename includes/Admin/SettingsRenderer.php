<?php
/**
 * Settings Renderer Class
 *
 * Renders settings fields in the admin interface.
 *
 * @package ComplyFlow\Admin
 * @since 1.0.0
 */

namespace ComplyFlow\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings Renderer Class
 *
 * @since 1.0.0
 */
class SettingsRenderer {
    /**
     * Settings instance
     *
     * @var Settings
     */
    private Settings $settings;

    /**
     * Constructor
     *
     * @param Settings $settings Settings instance.
     */
    public function __construct(Settings $settings) {
        $this->settings = $settings;
    }

    /**
     * Render field based on type
     *
     * @param string               $key   Field key.
     * @param array<string, mixed> $field Field configuration.
     * @return void
     */
    public function render_field(string $key, array $field): void {
        $value = $this->settings->get($key, $field['default'] ?? '');
        $type = $field['type'] ?? 'text';

        echo '<div class="complyflow-form-group">';

        match ($type) {
            'text' => $this->render_text($key, $field, $value),
            'email' => $this->render_email($key, $field, $value),
            'url' => $this->render_url($key, $field, $value),
            'number' => $this->render_number($key, $field, $value),
            'textarea' => $this->render_textarea($key, $field, $value),
            'toggle' => $this->render_toggle($key, $field, $value),
            'select' => $this->render_select($key, $field, $value),
            'radio' => $this->render_radio($key, $field, $value),
            'checkbox' => $this->render_checkbox($key, $field, $value),
            'color' => $this->render_color($key, $field, $value),
            'file' => $this->render_file($key, $field, $value),
            default => $this->render_text($key, $field, $value),
        };

        if (!empty($field['description'])) {
            printf(
                '<p class="description">%s</p>',
                esc_html($field['description'])
            );
        }

        echo '</div>';
    }

    /**
     * Render text field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_text(string $key, array $field, mixed $value): void {
        printf(
            '<input type="text" id="%1$s" name="complyflow_settings[%1$s]" value="%2$s" class="regular-text" %3$s>',
            esc_attr($key),
            esc_attr($value),
            !empty($field['required']) ? 'required' : ''
        );
    }

    /**
     * Render email field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_email(string $key, array $field, mixed $value): void {
        printf(
            '<input type="email" id="%1$s" name="complyflow_settings[%1$s]" value="%2$s" class="regular-text" %3$s>',
            esc_attr($key),
            esc_attr($value),
            !empty($field['required']) ? 'required' : ''
        );
    }

    /**
     * Render URL field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_url(string $key, array $field, mixed $value): void {
        printf(
            '<input type="url" id="%1$s" name="complyflow_settings[%1$s]" value="%2$s" class="regular-text" %3$s>',
            esc_attr($key),
            esc_attr($value),
            !empty($field['required']) ? 'required' : ''
        );
    }

    /**
     * Render number field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_number(string $key, array $field, mixed $value): void {
        $min = isset($field['min']) ? 'min="' . esc_attr($field['min']) . '"' : '';
        $max = isset($field['max']) ? 'max="' . esc_attr($field['max']) . '"' : '';
        $step = isset($field['step']) ? 'step="' . esc_attr($field['step']) . '"' : '';

        printf(
            '<div style="display: flex; align-items: center; gap: 0.5rem;">' .
            '<input type="number" id="%1$s" name="complyflow_settings[%1$s]" value="%2$s" class="small-text" %3$s %4$s %5$s %6$s>' .
            '%7$s' .
            '</div>',
            esc_attr($key),
            esc_attr($value),
            $min,
            $max,
            $step,
            !empty($field['required']) ? 'required' : '',
            isset($field['suffix']) ? '<span>' . esc_html($field['suffix']) . '</span>' : ''
        );
    }

    /**
     * Render textarea field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_textarea(string $key, array $field, mixed $value): void {
        $rows = $field['rows'] ?? 5;

        printf(
            '<textarea id="%1$s" name="complyflow_settings[%1$s]" class="large-text" rows="%2$d" %3$s>%4$s</textarea>',
            esc_attr($key),
            (int) $rows,
            !empty($field['required']) ? 'required' : '',
            esc_textarea($value)
        );
    }

    /**
     * Render toggle field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_toggle(string $key, array $field, mixed $value): void {
        printf(
            '<label class="complyflow-toggle" style="display: inline-flex; align-items: center;">' .
            '<input type="checkbox" name="complyflow_settings[%1$s]" value="1" %2$s>' .
            '<span class="complyflow-toggle-slider"></span>' .
            '</label>',
            esc_attr($key),
            checked($value, true, false)
        );

        // Add CSS for toggle if not already present
        echo '<style>.complyflow-toggle{position:relative;width:44px;height:24px}.complyflow-toggle input{opacity:0;width:0;height:0}.complyflow-toggle-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#ccc;transition:.3s;border-radius:24px}.complyflow-toggle-slider:before{position:absolute;content:"";height:16px;width:16px;left:4px;bottom:4px;background:#fff;transition:.3s;border-radius:50%}.complyflow-toggle input:checked+.complyflow-toggle-slider{background:#2271b1}.complyflow-toggle input:checked+.complyflow-toggle-slider:before{transform:translateX(20px)}</style>';
    }

    /**
     * Render select field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_select(string $key, array $field, mixed $value): void {
        printf(
            '<select id="%1$s" name="complyflow_settings[%1$s]" class="regular-text" %2$s>',
            esc_attr($key),
            !empty($field['required']) ? 'required' : ''
        );

        if (!empty($field['placeholder'])) {
            printf(
                '<option value="">%s</option>',
                esc_html($field['placeholder'])
            );
        }

        foreach ($field['options'] as $option_value => $option_label) {
            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_attr($option_value),
                selected($value, $option_value, false),
                esc_html($option_label)
            );
        }

        echo '</select>';
    }

    /**
     * Render radio field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_radio(string $key, array $field, mixed $value): void {
        echo '<fieldset><div style="display: flex; flex-direction: column; gap: 0.5rem;">';

        foreach ($field['options'] as $option_value => $option_label) {
            printf(
                '<label style="display: flex; align-items: center; gap: 0.5rem;">' .
                '<input type="radio" name="complyflow_settings[%1$s]" value="%2$s" %3$s>' .
                '<span>%4$s</span>' .
                '</label>',
                esc_attr($key),
                esc_attr($option_value),
                checked($value, $option_value, false),
                esc_html($option_label)
            );
        }

        echo '</div></fieldset>';
    }

    /**
     * Render checkbox field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_checkbox(string $key, array $field, mixed $value): void {
        echo '<fieldset><div style="display: flex; flex-direction: column; gap: 0.5rem;">';

        foreach ($field['options'] as $option_value => $option_label) {
            $is_checked = is_array($value) && in_array($option_value, $value, true);

            printf(
                '<label style="display: flex; align-items: center; gap: 0.5rem;">' .
                '<input type="checkbox" name="complyflow_settings[%1$s][]" value="%2$s" %3$s>' .
                '<span>%4$s</span>' .
                '</label>',
                esc_attr($key),
                esc_attr($option_value),
                checked($is_checked, true, false),
                esc_html($option_label)
            );
        }

        echo '</div></fieldset>';
    }

    /**
     * Render color field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_color(string $key, array $field, mixed $value): void {
        printf(
            '<input type="color" id="%1$s" name="complyflow_settings[%1$s]" value="%2$s" %3$s>',
            esc_attr($key),
            esc_attr($value),
            !empty($field['required']) ? 'required' : ''
        );
    }

    /**
     * Render file field
     *
     * @param string $key   Field key.
     * @param array  $field Field configuration.
     * @param mixed  $value Current value.
     * @return void
     */
    private function render_file(string $key, array $field, mixed $value): void {
        printf(
            '<input type="file" id="%1$s" name="%1$s" class="regular-text" %2$s %3$s>',
            esc_attr($key),
            !empty($field['accept']) ? 'accept="' . esc_attr($field['accept']) . '"' : '',
            !empty($field['required']) ? 'required' : ''
        );

        if (!empty($value)) {
            printf(
                '<p class="description">%s: <a href="%s" target="_blank">%s</a></p>',
                esc_html__('Current file', 'complyflow'),
                esc_url($value),
                esc_html(basename($value))
            );
        }
    }
}
