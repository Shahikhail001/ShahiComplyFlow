<?php
/**
 * DSR Data Exporter
 *
 * @package ComplyFlow\Modules\DSR
 * @since   3.1.3
 */

namespace ComplyFlow\Modules\DSR;

use ComplyFlow\Core\SettingsRepository;
use WP_Error;

class DataExporter {
    
    private SettingsRepository $settings;

    public function __construct(SettingsRepository $settings) {
        $this->settings = $settings;
    }

    public function init(): void {
        // Initialization logic if needed
    }

    public function export_user_data(int $request_id, string $format = 'json'): array|WP_Error {
        $request_handler = new RequestHandler($this->settings);
        $request_data = $request_handler->get_request_data($request_id);

        if (is_wp_error($request_data)) {
            return $request_data;
        }

        $email = $request_data['email'];
        $user_data = $this->collect_user_data($email);

        if (class_exists('WooCommerce')) {
            $user_data['woocommerce'] = $this->collect_woocommerce_data($email);
        }

        $user_data = apply_filters('complyflow_export_user_data', $user_data, $email);

        $formatted_data = match($format) {
            'json' => $this->format_as_json($user_data),
            'csv' => $this->format_as_csv($user_data),
            'html' => $this->format_as_html($user_data),
            default => new WP_Error('invalid_format', __('Invalid export format.', 'complyflow')),
        };

        if (is_wp_error($formatted_data)) {
            return $formatted_data;
        }

        return $this->create_download_package($formatted_data, $format, $request_id);
    }

    private function collect_user_data(string $email): array {
        $data = [
            'user_profile' => [],
            'comments' => [],
            'posts' => [],
            'user_meta' => [],
        ];

        $user = get_user_by('email', $email);

        if ($user) {
            $data['user_profile'] = [
                'ID' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'display_name' => $user->display_name,
                'registered' => $user->user_registered,
                'roles' => $user->roles,
            ];

            $all_meta = get_user_meta($user->ID);
            foreach ($all_meta as $key => $values) {
                if (!str_starts_with($key, '_')) {
                    $data['user_meta'][$key] = maybe_unserialize($values[0] ?? '');
                }
            }
        }

        $comments = get_comments([
            'author_email' => $email,
            'status' => 'all',
        ]);

        foreach ($comments as $comment) {
            $data['comments'][] = [
                'post_id' => $comment->comment_post_ID,
                'post_title' => get_the_title($comment->comment_post_ID),
                'date' => $comment->comment_date,
                'content' => $comment->comment_content,
                'author_name' => $comment->comment_author,
            ];
        }

        if ($user) {
            $posts = get_posts([
                'author' => $user->ID,
                'post_type' => 'any',
                'post_status' => 'any',
                'posts_per_page' => -1,
            ]);

            foreach ($posts as $post) {
                $data['posts'][] = [
                    'ID' => $post->ID,
                    'title' => $post->post_title,
                    'type' => $post->post_type,
                    'date' => $post->post_date,
                    'status' => $post->post_status,
                    'excerpt' => wp_trim_words($post->post_content, 50),
                ];
            }
        }

        return $data;
    }

    private function collect_woocommerce_data(string $email): array {
        $data = [
            'orders' => [],
            'customer_data' => [],
        ];

        $customer = new \WC_Customer(0);
        $customer->set_email($email);

        $orders = wc_get_orders([
            'customer' => $email,
            'limit' => -1,
        ]);

        foreach ($orders as $order) {
            $items = [];
            foreach ($order->get_items() as $item) {
                $items[] = [
                    'name' => $item->get_name(),
                    'quantity' => $item->get_quantity(),
                    'total' => $item->get_total(),
                ];
            }

            $data['orders'][] = [
                'order_id' => $order->get_id(),
                'date' => $order->get_date_created()->format('Y-m-d H:i:s'),
                'status' => $order->get_status(),
                'total' => $order->get_total(),
                'items' => $items,
            ];
        }

        $user = get_user_by('email', $email);
        if ($user) {
            $customer = new \WC_Customer($user->ID);

            $data['customer_data'] = [
                'billing' => [
                    'first_name' => $customer->get_billing_first_name(),
                    'last_name' => $customer->get_billing_last_name(),
                    'company' => $customer->get_billing_company(),
                    'address_1' => $customer->get_billing_address_1(),
                    'address_2' => $customer->get_billing_address_2(),
                    'city' => $customer->get_billing_city(),
                    'state' => $customer->get_billing_state(),
                    'postcode' => $customer->get_billing_postcode(),
                    'country' => $customer->get_billing_country(),
                    'phone' => $customer->get_billing_phone(),
                ],
                'shipping' => [
                    'first_name' => $customer->get_shipping_first_name(),
                    'last_name' => $customer->get_shipping_last_name(),
                    'company' => $customer->get_shipping_company(),
                    'address_1' => $customer->get_shipping_address_1(),
                    'address_2' => $customer->get_shipping_address_2(),
                    'city' => $customer->get_shipping_city(),
                    'state' => $customer->get_shipping_state(),
                    'postcode' => $customer->get_shipping_postcode(),
                    'country' => $customer->get_shipping_country(),
                ],
            ];
        }

        return $data;
    }

    private function format_as_json(array $data): string {
        return wp_json_encode($data, JSON_PRETTY_PRINT);
    }

    private function format_as_csv(array $data): string {
        $output = fopen('php://temp', 'r+');
        
        fputcsv($output, ['Section', 'Key', 'Value']);

        foreach ($data as $section => $section_data) {
            if (is_array($section_data)) {
                $this->write_csv_recursive($output, $section, $section_data);
            }
        }

        rewind($output);
        $csv_data = stream_get_contents($output);
        fclose($output);

        return $csv_data;
    }

    private function write_csv_recursive($handle, string $prefix, array $data): void {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->write_csv_recursive($handle, "$prefix.$key", $value);
            } else {
                fputcsv($handle, [$prefix, $key, $value]);
            }
        }
    }

    private function format_as_html(array $data): string {
        $html = '<html><head><meta charset="UTF-8"><title>Data Export</title>';
        $html .= '<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #333; }
            h2 { color: #666; margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
            table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
            th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
            th { background-color: #f4f4f4; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
        </style></head><body>';
        
        $html .= '<h1>' . esc_html__('Personal Data Export', 'complyflow') . '</h1>';
        $html .= '<p>' . esc_html__('Generated on', 'complyflow') . ': ' . date('Y-m-d H:i:s') . '</p>';

        foreach ($data as $section => $section_data) {
            $html .= '<h2>' . esc_html(ucwords(str_replace('_', ' ', $section))) . '</h2>';
            
            if (is_array($section_data) && !empty($section_data)) {
                $html .= $this->render_html_table($section_data);
            } else {
                $html .= '<p>' . esc_html__('No data available.', 'complyflow') . '</p>';
            }
        }

        $html .= '</body></html>';

        return $html;
    }

    private function render_html_table(array $data): string {
        if (empty($data)) {
            return '<p>' . esc_html__('No data available.', 'complyflow') . '</p>';
        }

        if (!is_array(reset($data))) {
            $html = '<table><thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>';
            foreach ($data as $key => $value) {
                $html .= '<tr><td>' . esc_html($key) . '</td><td>' . esc_html((string)$value) . '</td></tr>';
            }
            $html .= '</tbody></table>';
            return $html;
        }

        $keys = array_keys(reset($data));
        $html = '<table><thead><tr>';
        foreach ($keys as $key) {
            $html .= '<th>' . esc_html(ucwords(str_replace('_', ' ', (string)$key))) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($keys as $key) {
                $value = $row[$key] ?? '';
                if (is_array($value)) {
                    $value = wp_json_encode($value);
                }
                $html .= '<td>' . esc_html((string)$value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    private function create_download_package(string $data, string $format, int $request_id): array {
        $upload_dir = wp_upload_dir();
        $export_dir = $upload_dir['basedir'] . '/complyflow-exports';

        if (!file_exists($export_dir)) {
            wp_mkdir_p($export_dir);
        }

        $filename = sprintf('export-%d-%s.%s', $request_id, time(), $format);
        $filepath = $export_dir . '/' . $filename;

        file_put_contents($filepath, $data);

        $download_url = $upload_dir['baseurl'] . '/complyflow-exports/' . $filename;

        update_post_meta($request_id, '_dsr_export_file', $filepath);
        update_post_meta($request_id, '_dsr_export_url', $download_url);

        return [
            'download_url' => $download_url,
            'file_path' => $filepath,
            'filename' => $filename,
        ];
    }
}
