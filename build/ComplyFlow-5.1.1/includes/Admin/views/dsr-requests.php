<?php
/**
 * DSR Requests Admin View
 *
 * @package ComplyFlow\Modules\DSR
 * @since   3.2.2
 */

if (!defined('ABSPATH')) {
    exit;
}

use ComplyFlow\Modules\DSR\RequestHandler;

$settings = ComplyFlow\Core\SettingsRepository::get_instance();
$request_handler = new RequestHandler($settings);

// Check if viewing single request
$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;

if ($request_id) {
    $request_data = $request_handler->get_request_data($request_id);
    if (is_wp_error($request_data)) {
        echo '<div class="notice notice-error"><p>' . esc_html($request_data->get_error_message()) . '</p></div>';
        $request_id = 0;
    }
}
?>

<style>
    /* Modern DSR Admin Styles */
    .complyflow-dsr-admin {
        background: #f5f7fa;
        margin: -10px -20px 0;
        padding: 20px;
    }
    
    .complyflow-dsr-detail {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 30px;
        margin-top: 20px;
    }
    
    .dsr-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e8edf2;
    }
    
    .dsr-detail-header h2 {
        margin: 0;
        font-size: 28px;
        color: #1e293b;
        font-weight: 600;
    }
    
    /* Tab Styles */
    .nav-tab-wrapper {
        border-bottom: 2px solid #e8edf2;
        margin: 0 0 30px 0;
    }
    
    .nav-tab {
        border: none;
        background: transparent;
        color: #64748b;
        padding: 12px 24px;
        font-weight: 500;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
    }
    
    .nav-tab:hover {
        background: #f8fafc;
        color: #3b82f6;
    }
    
    .nav-tab-active {
        color: #3b82f6 !important;
        border-bottom-color: #3b82f6 !important;
        background: #f8fafc;
    }
    
    .tab-content { 
        display: none; 
        margin-top: 30px;
        animation: fadeIn 0.3s ease-in;
    }
    
    .tab-content.active { 
        display: block; 
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Status Badges */
    .dsr-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .status-dsr_pending { 
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #fff;
    }
    
    .status-dsr_verified { 
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: #fff;
    }
    
    .status-dsr_in_progress { 
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
    }
    
    .status-dsr_completed { 
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
    }
    
    .status-dsr_rejected { 
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
    }
    
    /* Type Badges */
    .dsr-type-badge {
        display: inline-block;
        padding: 4px 12px;
        background: #e0e7ff;
        color: #4f46e5;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    
    /* Action Groups */
    .dsr-actions-panel .action-group { 
        margin-bottom: 24px;
        padding: 24px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .dsr-actions-panel .action-group:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .dsr-actions-panel .action-group h3 { 
        margin: 0 0 12px 0;
        color: #1e293b;
        font-size: 18px;
        font-weight: 600;
    }
    
    .dsr-actions-panel .action-group p {
        color: #64748b;
        margin: 0 0 16px 0;
        line-height: 1.6;
    }
    
    /* Danger Zone */
    .danger-zone {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border-color: #fca5a5;
    }
    
    .danger-zone h3 {
        color: #dc2626;
    }
    
    /* Form Table */
    .form-table th {
        color: #475569;
        font-weight: 600;
        padding: 16px 12px;
        width: 200px;
    }
    
    .form-table td {
        padding: 16px 12px;
        color: #1e293b;
    }
    
    .form-table tr {
        border-bottom: 1px solid #f1f5f9;
    }
    
    /* Table Styles */
    .wp-list-table {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .wp-list-table thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        color: #475569;
        font-weight: 600;
        padding: 16px;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }
    
    .wp-list-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .wp-list-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .wp-list-table tbody td {
        padding: 16px;
        vertical-align: middle;
    }
    
    /* Buttons */
    .button-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
    }
    
    .button-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
        transform: translateY(-1px);
    }
    
    .button-secondary {
        background: #fff;
        border: 2px solid #e2e8f0;
        color: #475569;
        transition: all 0.3s ease;
    }
    
    .button-secondary:hover {
        border-color: #3b82f6;
        color: #3b82f6;
        transform: translateY(-1px);
    }
    
    /* Page Title */
    .wp-heading-inline {
        color: #1e293b;
        font-weight: 600;
    }
    
    .page-title-action {
        background: #fff !important;
        border: 2px solid #e2e8f0 !important;
        color: #475569 !important;
        transition: all 0.3s ease;
        border-radius: 6px;
    }
    
    .page-title-action:hover {
        border-color: #3b82f6 !important;
        color: #3b82f6 !important;
        transform: translateX(-2px);
    }
    
    /* Filters */
    select.complyflow-filter {
        padding: 8px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        color: #475569;
        font-size: 14px;
    }
    
    .button.filter-button {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        margin-left: 8px;
    }
    
    .button.filter-button:hover {
        background: #2563eb;
    }
</style>

<div class="wrap complyflow-dsr-admin">
    <h1 class="wp-heading-inline"><?php esc_html_e('Data Subject Rights Requests', 'complyflow'); ?></h1>
    
    <?php if ($request_id && !is_wp_error($request_data)): ?>
        <!-- Single Request Detail View -->
        <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-dsr')); ?>" class="page-title-action">
            <?php esc_html_e('← Back to All Requests', 'complyflow'); ?>
        </a>
        
        <hr class="wp-header-end">

        <div class="complyflow-dsr-detail">
            <?php
            // Show verification URL if email wasn't sent (for localhost testing)
            $verification_url = get_post_meta($request_id, '_dsr_verification_url_for_testing', true);
            if ($verification_url && $request_data['status'] === 'dsr_pending'):
            ?>
            <div class="notice notice-warning">
                <p>
                    <strong><?php esc_html_e('Email Not Sent (Mail Server Not Configured)', 'complyflow'); ?></strong><br>
                    <?php esc_html_e('For testing purposes, you can manually verify this request using the link below:', 'complyflow'); ?>
                </p>
                <p>
                    <a href="<?php echo esc_url($verification_url); ?>" target="_blank" class="button button-primary">
                        <?php esc_html_e('Verify Request Manually', 'complyflow'); ?>
                    </a>
                    <button type="button" class="button" onclick="navigator.clipboard.writeText('<?php echo esc_js($verification_url); ?>'); this.innerText='<?php esc_attr_e('Copied!', 'complyflow'); ?>'">
                        <?php esc_html_e('Copy Verification URL', 'complyflow'); ?>
                    </button>
                </p>
            </div>
            <?php endif; ?>
            
            <div class="dsr-detail-header">
                <h2><?php echo esc_html(sprintf(__('Request #%d', 'complyflow'), $request_id)); ?></h2>
                <span class="dsr-status-badge status-<?php echo esc_attr($request_data['status']); ?>">
                    <?php echo esc_html(ucwords(str_replace(['dsr_', '_'], ['', ' '], $request_data['status']))); ?>
                </span>
            </div>

            <!-- Tabs Navigation -->
            <h2 class="nav-tab-wrapper">
                <a href="#tab-details" class="nav-tab nav-tab-active" data-tab="details">
                    <?php esc_html_e('Details', 'complyflow'); ?>
                </a>
                <a href="#tab-history" class="nav-tab" data-tab="history">
                    <?php esc_html_e('History & Notes', 'complyflow'); ?>
                </a>
                <a href="#tab-data" class="nav-tab" data-tab="data">
                    <?php esc_html_e('User Data Preview', 'complyflow'); ?>
                </a>
                <a href="#tab-actions" class="nav-tab" data-tab="actions">
                    <?php esc_html_e('Actions', 'complyflow'); ?>
                </a>
            </h2>

            <style>
                /* Modern DSR Admin Styles */
                .complyflow-dsr-admin {
                    background: #f5f7fa;
                    margin: -10px -20px 0;
                    padding: 20px;
                }
                
                .complyflow-dsr-detail {
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                    padding: 30px;
                    margin-top: 20px;
                }
                
                .dsr-detail-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #e8edf2;
                }
                
                .dsr-detail-header h2 {
                    margin: 0;
                    font-size: 28px;
                    color: #1e293b;
                    font-weight: 600;
                }
                
                /* Tab Styles */
                .nav-tab-wrapper {
                    border-bottom: 2px solid #e8edf2;
                    margin: 0 0 30px 0;
                }
                
                .nav-tab {
                    border: none;
                    background: transparent;
                    color: #64748b;
                    padding: 12px 24px;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    border-bottom: 3px solid transparent;
                    margin-bottom: -2px;
                }
                
                .nav-tab:hover {
                    background: #f8fafc;
                    color: #3b82f6;
                }
                
                .nav-tab-active {
                    color: #3b82f6;
                    border-bottom-color: #3b82f6;
                    background: #f8fafc;
                }
                
                .tab-content { 
                    display: none; 
                    margin-top: 30px;
                    animation: fadeIn 0.3s ease-in;
                }
                
                .tab-content.active { 
                    display: block; 
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                
                /* Status Badges */
                .dsr-status-badge {
                    display: inline-flex;
                    align-items: center;
                    padding: 6px 16px;
                    border-radius: 20px;
                    font-size: 13px;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                
                .status-dsr_pending { 
                    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
                    color: #fff;
                }
                
                .status-dsr_verified { 
                    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
                    color: #fff;
                }
                
                .status-dsr_in_progress { 
                    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                    color: #fff;
                }
                
                .status-dsr_completed { 
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    color: #fff;
                }
                
                .status-dsr_rejected { 
                    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                    color: #fff;
                }
                
                /* Type Badges */
                .dsr-type-badge {
                    display: inline-block;
                    padding: 4px 12px;
                    background: #e0e7ff;
                    color: #4f46e5;
                    border-radius: 6px;
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: capitalize;
                }
                
                /* Action Groups */
                .dsr-actions-panel .action-group { 
                    margin-bottom: 24px;
                    padding: 24px;
                    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                    transition: all 0.3s ease;
                }
                
                .dsr-actions-panel .action-group:hover {
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    transform: translateY(-2px);
                }
                
                .dsr-actions-panel .action-group h3 { 
                    margin: 0 0 12px 0;
                    color: #1e293b;
                    font-size: 18px;
                    font-weight: 600;
                }
                
                .dsr-actions-panel .action-group p {
                    color: #64748b;
                    margin: 0 0 16px 0;
                    line-height: 1.6;
                }
                
                /* Danger Zone */
                .danger-zone {
                    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
                    border-color: #fca5a5;
                }
                
                .danger-zone h3 {
                    color: #dc2626;
                }
                
                /* Form Table */
                .form-table th {
                    color: #475569;
                    font-weight: 600;
                    padding: 16px 12px;
                    width: 200px;
                }
                
                .form-table td {
                    padding: 16px 12px;
                    color: #1e293b;
                }
                
                .form-table tr {
                    border-bottom: 1px solid #f1f5f9;
                }
                
                /* Notes List */
                .dsr-notes-list {
                    max-height: 500px;
                    overflow-y: auto;
                }
                
                .dsr-note {
                    background: #f8fafc;
                    border-left: 4px solid #3b82f6;
                    padding: 16px;
                    margin-bottom: 16px;
                    border-radius: 6px;
                }
                
                .dsr-note .note-header {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 8px;
                    font-size: 13px;
                }
                
                .dsr-note .note-header strong {
                    color: #1e293b;
                }
                
                .dsr-note .note-date {
                    color: #94a3b8;
                }
                
                .dsr-note .note-content {
                    color: #475569;
                    line-height: 1.6;
                }
                
                /* Data Summary */
                .dsr-data-summary {
                    background: #f8fafc;
                    padding: 24px;
                    border-radius: 8px;
                    border: 1px solid #e2e8f0;
                }
                
                .dsr-data-summary h3 {
                    margin-top: 0;
                    color: #1e293b;
                    font-size: 18px;
                }
                
                .dsr-data-summary ul {
                    list-style: none;
                    padding: 0;
                    margin: 16px 0;
                }
                
                .dsr-data-summary li {
                    padding: 12px 0;
                    border-bottom: 1px solid #e2e8f0;
                    color: #475569;
                }
                
                .dsr-data-summary li:last-child {
                    border-bottom: none;
                }
                
                .dsr-data-summary li strong {
                    color: #1e293b;
                    display: inline-block;
                    min-width: 180px;
                }
                
                /* Buttons */
                .button-primary {
                    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                    border: none;
                    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
                    transition: all 0.3s ease;
                }
                
                .button-primary:hover {
                    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
                    transform: translateY(-1px);
                }
                
                .button-secondary {
                    background: #fff;
                    border: 2px solid #e2e8f0;
                    color: #475569;
                    transition: all 0.3s ease;
                }
                
                .button-secondary:hover {
                    border-color: #3b82f6;
                    color: #3b82f6;
                    transform: translateY(-1px);
                }
                
                .button-link-delete {
                    color: #dc2626;
                    text-decoration: none;
                    transition: all 0.3s ease;
                }
                
                .button-link-delete:hover {
                    color: #991b1b;
                    text-decoration: underline;
                }
                
                /* Table Styles */
                .wp-list-table {
                    background: #fff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                }
                
                .wp-list-table thead th {
                    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                    color: #475569;
                    font-weight: 600;
                    padding: 16px;
                    text-transform: uppercase;
                    font-size: 12px;
                    letter-spacing: 0.5px;
                }
                
                .wp-list-table tbody tr {
                    transition: all 0.2s ease;
                }
                
                .wp-list-table tbody tr:hover {
                    background: #f8fafc;
                }
                
                .wp-list-table tbody td {
                    padding: 16px;
                    vertical-align: middle;
                }
                
                /* Inputs */
                .regular-text, .large-text, .complyflow-input, textarea {
                    border: 2px solid #e2e8f0;
                    border-radius: 6px;
                    padding: 10px 14px;
                    transition: all 0.3s ease;
                }
                
                .regular-text:focus, .large-text:focus, .complyflow-input:focus, textarea:focus {
                    border-color: #3b82f6;
                    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                    outline: none;
                }
                
                /* Page Title Actions */
                .page-title-action {
                    background: #fff;
                    border: 2px solid #e2e8f0;
                    color: #475569;
                    transition: all 0.3s ease;
                    border-radius: 6px;
                }
                
                .page-title-action:hover {
                    border-color: #3b82f6;
                    color: #3b82f6;
                    transform: translateX(-2px);
                }
            </style>

            <!-- Tab: Details -->
            <div id="tab-details" class="tab-content active">
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e('Request Type', 'complyflow'); ?></th>
                        <td><strong><?php echo esc_html(ucfirst($request_data['type'])); ?></strong></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Requester Name', 'complyflow'); ?></th>
                        <td><?php echo esc_html($request_data['full_name']); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Email Address', 'complyflow'); ?></th>
                        <td><a href="mailto:<?php echo esc_attr($request_data['email']); ?>"><?php echo esc_html($request_data['email']); ?></a></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Status', 'complyflow'); ?></th>
                        <td>
                            <span class="dsr-status-badge status-<?php echo esc_attr($request_data['status']); ?>">
                                <?php echo esc_html(ucwords(str_replace(['dsr_', '_'], ['', ' '], $request_data['status']))); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Submitted Date', 'complyflow'); ?></th>
                        <td><?php echo esc_html($request_data['submitted_date']); ?></td>
                    </tr>
                    <?php if ($request_data['verified_date']): ?>
                    <tr>
                        <th><?php esc_html_e('Verified Date', 'complyflow'); ?></th>
                        <td><?php echo esc_html($request_data['verified_date']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($request_data['completed_date']): ?>
                    <tr>
                        <th><?php esc_html_e('Completed Date', 'complyflow'); ?></th>
                        <td><?php echo esc_html($request_data['completed_date']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?php esc_html_e('IP Address', 'complyflow'); ?></th>
                        <td><?php echo esc_html($request_data['ip_address']); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('User Agent', 'complyflow'); ?></th>
                        <td><code><?php echo esc_html($request_data['user_agent']); ?></code></td>
                    </tr>
                    <?php if ($request_data['additional_info']): ?>
                    <tr>
                        <th><?php esc_html_e('Additional Information', 'complyflow'); ?></th>
                        <td><?php echo wp_kses_post(nl2br($request_data['additional_info'])); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>

            <!-- Tab: History & Notes -->
            <div id="tab-history" class="tab-content">
                <div class="dsr-notes-list">
                    <?php if (!empty($request_data['notes'])): ?>
                        <?php foreach (array_reverse($request_data['notes']) as $note): ?>
                        <div class="dsr-note">
                            <div class="note-header">
                                <strong><?php echo esc_html($note['user']); ?></strong>
                                <span class="note-date"><?php echo esc_html($note['date']); ?></span>
                            </div>
                            <div class="note-content">
                                <?php echo esc_html($note['note']); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="description"><?php esc_html_e('No notes yet.', 'complyflow'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab: User Data Preview -->
            <div id="tab-data" class="tab-content">
                <?php
                $user = get_user_by('email', $request_data['email']);
                $comments_count = count(get_comments(['author_email' => $request_data['email']]));
                $posts_count = 0;
                if ($user) {
                    $posts_count = count(get_posts(['author' => $user->ID, 'post_type' => 'any', 'posts_per_page' => -1]));
                }
                $orders_count = 0;
                if (class_exists('WooCommerce')) {
                    $orders_count = count(wc_get_orders(['customer' => $request_data['email'], 'limit' => -1]));
                }
                ?>
                <div class="dsr-data-summary">
                    <h3><?php esc_html_e('Data Summary', 'complyflow'); ?></h3>
                    <ul>
                        <li>
                            <strong><?php esc_html_e('User Account:', 'complyflow'); ?></strong>
                            <?php if ($user): ?>
                                <?php echo esc_html(sprintf(__('Yes (ID: %d, Username: %s)', 'complyflow'), $user->ID, $user->user_login)); ?>
                            <?php else: ?>
                                <?php esc_html_e('No registered account', 'complyflow'); ?>
                            <?php endif; ?>
                        </li>
                        <li>
                            <strong><?php esc_html_e('Comments:', 'complyflow'); ?></strong>
                            <?php echo esc_html(sprintf(_n('%d comment', '%d comments', $comments_count, 'complyflow'), $comments_count)); ?>
                        </li>
                        <?php if ($user): ?>
                        <li>
                            <strong><?php esc_html_e('Authored Posts:', 'complyflow'); ?></strong>
                            <?php echo esc_html(sprintf(_n('%d post', '%d posts', $posts_count, 'complyflow'), $posts_count)); ?>
                        </li>
                        <?php endif; ?>
                        <?php if (class_exists('WooCommerce')): ?>
                        <li>
                            <strong><?php esc_html_e('Orders:', 'complyflow'); ?></strong>
                            <?php echo esc_html(sprintf(_n('%d order', '%d orders', $orders_count, 'complyflow'), $orders_count)); ?>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <p class="description">
                        <?php esc_html_e('This is a summary of data found for this email address. Use the Export function to generate a complete data package.', 'complyflow'); ?>
                    </p>
                </div>
            </div>

            <!-- Tab: Actions -->
            <div id="tab-actions" class="tab-content">
                <div class="dsr-actions-panel">
                    
                    <?php if ($request_data['status'] === 'dsr_pending'): ?>
                    <div class="action-group">
                        <h3><?php esc_html_e('Manually Verify Request', 'complyflow'); ?></h3>
                        <p><?php esc_html_e('Skip email verification and manually approve this request to proceed.', 'complyflow'); ?></p>
                        <button type="button" class="button button-primary dsr-action-btn" data-action="verify" data-request-id="<?php echo esc_attr($request_id); ?>">
                            <?php esc_html_e('Verify Request', 'complyflow'); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($request_data['status'] === 'dsr_verified'): ?>
                    <div class="action-group">
                        <h3><?php esc_html_e('Approve Request', 'complyflow'); ?></h3>
                        <p><?php esc_html_e('Mark this request as approved and in progress.', 'complyflow'); ?></p>
                        <button type="button" class="button button-primary dsr-action-btn" data-action="approve" data-request-id="<?php echo esc_attr($request_id); ?>">
                            <?php esc_html_e('Approve Request', 'complyflow'); ?>
                        </button>
                    </div>
                    <?php endif; ?>

                    <?php if ($request_data['status'] === 'dsr_in_progress'): ?>
                    <div class="action-group">
                        <h3><?php esc_html_e('Mark as Completed', 'complyflow'); ?></h3>
                        <p><?php esc_html_e('Mark this request as completed. The requester will be notified.', 'complyflow'); ?></p>
                        <button type="button" class="button button-primary dsr-action-btn" data-action="complete" data-request-id="<?php echo esc_attr($request_id); ?>">
                            <?php esc_html_e('Mark Completed', 'complyflow'); ?>
                        </button>
                    </div>
                    <?php endif; ?>

                    <?php if (in_array($request_data['status'], ['dsr_verified', 'dsr_in_progress'])): ?>
                    <div class="action-group">
                        <h3><?php esc_html_e('Reject Request', 'complyflow'); ?></h3>
                        <p><?php esc_html_e('Reject this request with a reason.', 'complyflow'); ?></p>
                        <textarea id="reject-reason" class="large-text" rows="3" placeholder="<?php esc_attr_e('Enter rejection reason...', 'complyflow'); ?>"></textarea>
                        <br><br>
                        <button type="button" class="button dsr-action-btn" data-action="reject" data-request-id="<?php echo esc_attr($request_id); ?>">
                            <?php esc_html_e('Reject Request', 'complyflow'); ?>
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="action-group">
                        <h3><?php esc_html_e('Export User Data', 'complyflow'); ?></h3>
                        <p><?php esc_html_e('Generate a data export package for this user.', 'complyflow'); ?></p>
                        <select id="export-format" class="regular-text">
                            <option value="json"><?php esc_html_e('JSON (Machine-readable)', 'complyflow'); ?></option>
                            <option value="html"><?php esc_html_e('HTML (Human-readable)', 'complyflow'); ?></option>
                            <option value="csv"><?php esc_html_e('CSV (Spreadsheet)', 'complyflow'); ?></option>
                        </select>
                        <br><br>
                        <button type="button" class="button button-secondary dsr-export-btn" data-request-id="<?php echo esc_attr($request_id); ?>">
                            <?php esc_html_e('Export Data', 'complyflow'); ?>
                        </button>
                    </div>

                    <div class="action-group danger-zone">
                        <h3><?php esc_html_e('Delete Request', 'complyflow'); ?></h3>
                        <p class="description"><?php esc_html_e('Permanently delete this request. This action cannot be undone.', 'complyflow'); ?></p>
                        <button type="button" class="button button-link-delete dsr-delete-btn" data-request-id="<?php echo esc_attr($request_id); ?>">
                            <?php esc_html_e('Delete Request', 'complyflow'); ?>
                        </button>
                    </div>

                </div>
            </div>

        </div>

    <?php else: ?>
        <!-- Requests List View -->
        <hr class="wp-header-end">

        <!-- Filters -->
        <div class="tablenav top">
            <div class="alignleft actions">
                <select name="filter_status" id="filter-status">
                    <option value=""><?php esc_html_e('All Statuses', 'complyflow'); ?></option>
                    <option value="dsr_pending"><?php esc_html_e('Pending Verification', 'complyflow'); ?></option>
                    <option value="dsr_verified"><?php esc_html_e('Verified', 'complyflow'); ?></option>
                    <option value="dsr_in_progress"><?php esc_html_e('In Progress', 'complyflow'); ?></option>
                    <option value="dsr_completed"><?php esc_html_e('Completed', 'complyflow'); ?></option>
                    <option value="dsr_rejected"><?php esc_html_e('Rejected', 'complyflow'); ?></option>
                </select>
                
                <select name="filter_type" id="filter-type">
                    <option value=""><?php esc_html_e('All Request Types', 'complyflow'); ?></option>
                    <option value="access"><?php esc_html_e('Access', 'complyflow'); ?></option>
                    <option value="delete"><?php esc_html_e('Delete', 'complyflow'); ?></option>
                    <option value="portability"><?php esc_html_e('Portability', 'complyflow'); ?></option>
                    <option value="rectification"><?php esc_html_e('Rectification', 'complyflow'); ?></option>
                    <option value="restriction"><?php esc_html_e('Restriction', 'complyflow'); ?></option>
                </select>
                
                <button type="button" id="filter-apply" class="button"><?php esc_html_e('Filter', 'complyflow'); ?></button>
            </div>
        </div>

        <?php
        // Get filtered requests
        $filter_status = isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : '';
        $filter_type = isset($_GET['filter_type']) ? sanitize_text_field($_GET['filter_type']) : '';
        
        $args = [
            'post_type' => 'complyflow_dsr',
            'post_status' => $filter_status ?: ['dsr_pending', 'dsr_verified', 'dsr_in_progress', 'dsr_completed', 'dsr_rejected'],
            'posts_per_page' => 20,
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        if ($filter_type) {
            $args['meta_query'] = [
                [
                    'key' => '_dsr_type',
                    'value' => $filter_type,
                ],
            ];
        }

        $requests = get_posts($args);
        ?>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="column-id"><?php esc_html_e('ID', 'complyflow'); ?></th>
                    <th class="column-name"><?php esc_html_e('Requester', 'complyflow'); ?></th>
                    <th class="column-email"><?php esc_html_e('Email', 'complyflow'); ?></th>
                    <th class="column-type"><?php esc_html_e('Request Type', 'complyflow'); ?></th>
                    <th class="column-status"><?php esc_html_e('Status', 'complyflow'); ?></th>
                    <th class="column-date"><?php esc_html_e('Date', 'complyflow'); ?></th>
                    <th class="column-actions"><?php esc_html_e('Actions', 'complyflow'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">
                        <p><?php esc_html_e('No requests found.', 'complyflow'); ?></p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($requests as $request): ?>
                    <?php
                    $req_data = $request_handler->get_request_data($request->ID);
                    if (is_wp_error($req_data)) continue;
                    ?>
                    <tr>
                        <td><strong>#<?php echo esc_html($request->ID); ?></strong></td>
                        <td><?php echo esc_html($req_data['full_name']); ?></td>
                        <td><a href="mailto:<?php echo esc_attr($req_data['email']); ?>"><?php echo esc_html($req_data['email']); ?></a></td>
                        <td>
                            <span class="dsr-type-badge">
                                <?php echo esc_html(ucfirst($req_data['type'])); ?>
                            </span>
                        </td>
                        <td>
                            <span class="dsr-status-badge status-<?php echo esc_attr($req_data['status']); ?>">
                                <?php echo esc_html(ucwords(str_replace(['dsr_', '_'], ['', ' '], $req_data['status']))); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html(mysql2date(get_option('date_format'), $req_data['submitted_date'])); ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-dsr&request_id=' . $request->ID)); ?>" class="button button-small">
                                <?php esc_html_e('View', 'complyflow'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    <?php endif; ?>

</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Tab switching functionality
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        var targetTab = $(this).data('tab');
        
        // Remove active class from all tabs and content
        $('.nav-tab').removeClass('nav-tab-active');
        $('.tab-content').removeClass('active').hide();
        
        // Add active class to clicked tab and show content
        $(this).addClass('nav-tab-active');
        $('#tab-' + targetTab).addClass('active').fadeIn(200);
    });
    
    // DSR action buttons
    $('.dsr-action-btn').on('click', function() {
        var $btn = $(this);
        var action = $btn.data('action');
        var requestId = $btn.data('request-id');
        
        if (!confirm('<?php echo esc_js(__('Are you sure you want to perform this action?', 'complyflow')); ?>')) {
            return;
        }
        
        $btn.prop('disabled', true).text('<?php echo esc_js(__('Processing...', 'complyflow')); ?>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_process_dsr',
                nonce: '<?php echo wp_create_nonce('complyflow_dsr_nonce'); ?>',
                request_id: requestId,
                dsr_action: action
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || '<?php echo esc_js(__('An error occurred', 'complyflow')); ?>');
                    $btn.prop('disabled', false).text($btn.data('original-text'));
                }
            },
            error: function() {
                alert('<?php echo esc_js(__('Network error occurred', 'complyflow')); ?>');
                $btn.prop('disabled', false).text($btn.data('original-text'));
            }
        });
    });
    
    // Export data button
    $('.dsr-export-btn').on('click', function() {
        var $btn = $(this);
        var requestId = $btn.data('request-id');
        var format = $('#export-format').val();
        
        $btn.prop('disabled', true).text('<?php echo esc_js(__('Generating Export...', 'complyflow')); ?>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_export_dsr_data',
                nonce: '<?php echo wp_create_nonce('complyflow_dsr_nonce'); ?>',
                request_id: requestId,
                format: format
            },
            success: function(response) {
                $btn.prop('disabled', false).text('<?php echo esc_js(__('Export Data', 'complyflow')); ?>');
                
                if (response.success && response.data.download_url) {
                    // Trigger download
                    window.location.href = response.data.download_url;
                } else {
                    alert(response.data.message || '<?php echo esc_js(__('Export failed', 'complyflow')); ?>');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('<?php echo esc_js(__('Export Data', 'complyflow')); ?>');
                alert('<?php echo esc_js(__('Network error occurred', 'complyflow')); ?>');
            }
        });
    });
    
    // Delete request button
    $('.dsr-delete-btn').on('click', function() {
        var $btn = $(this);
        var requestId = $btn.data('request-id');
        
        if (!confirm('<?php echo esc_js(__('Are you sure you want to permanently delete this request? This action cannot be undone.', 'complyflow')); ?>')) {
            return;
        }
        
        $btn.prop('disabled', true).text('<?php echo esc_js(__('Deleting...', 'complyflow')); ?>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_delete_dsr_request',
                nonce: '<?php echo wp_create_nonce('complyflow_dsr_nonce'); ?>',
                request_id: requestId
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = '<?php echo admin_url('admin.php?page=complyflow-dsr'); ?>';
                } else {
                    alert(response.data.message || '<?php echo esc_js(__('Delete failed', 'complyflow')); ?>');
                    $btn.prop('disabled', false).text('<?php echo esc_js(__('Delete Request', 'complyflow')); ?>');
                }
            },
            error: function() {
                alert('<?php echo esc_js(__('Network error occurred', 'complyflow')); ?>');
                $btn.prop('disabled', false).text('<?php echo esc_js(__('Delete Request', 'complyflow')); ?>');
            }
        });
    });
    
    // Store original button text
    $('.dsr-action-btn').each(function() {
        $(this).data('original-text', $(this).text());
    });
});
</script>
