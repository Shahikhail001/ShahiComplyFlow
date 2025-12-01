<?php
/**
 * Legal Documents Admin View
 *
 * @package ComplyFlow\Admin\Views
 * @since   3.0.1
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check questionnaire completion using the actual saved data
$questionnaire_answers = get_option('complyflow_questionnaire_answers', []);
$completed = !empty($questionnaire_answers) && isset($questionnaire_answers['company_name']);

// Get all generated policies
$privacy_policy = get_option('complyflow_generated_privacy_policy', '');
$terms = get_option('complyflow_generated_terms_of_service', '');
$cookie_policy = get_option('complyflow_generated_cookie_policy', '');
$data_protection = get_option('complyflow_generated_data_protection', '');
$consent_management = get_option('complyflow_generated_consent_management', '');
$user_rights_notice = get_option('complyflow_generated_user_rights_notice', '');
$third_party_services = get_option('complyflow_generated_third_party_services', '');
$cookie_categories = get_option('complyflow_generated_cookie_categories', '');

// Get last updated dates from individual policy metadata
$privacy_updated = get_option('complyflow_privacy_policy_updated', 0);
$terms_updated = get_option('complyflow_terms_updated', 0);
$cookie_updated = get_option('complyflow_cookie_policy_updated', 0);
$data_protection_updated = get_option('complyflow_data_protection_updated', 0);
$consent_management_updated = get_option('complyflow_consent_management_updated', 0);
$user_rights_notice_updated = get_option('complyflow_user_rights_notice_updated', 0);
$third_party_services_updated = get_option('complyflow_third_party_services_updated', 0);
$cookie_categories_updated = get_option('complyflow_cookie_categories_updated', 0);

// Check for success message
$show_success = isset($_GET['questionnaire_saved']) && $_GET['questionnaire_saved'] === '1';
?>

<style>
:root {
  --cf-dash-bg: #f3f8fe;
  --cf-dash-gradient: linear-gradient(135deg,#1e3a8a 0%,#2563eb 50%,#0ea5e9 100%);
  --cf-dash-surface: #ffffff;
  --cf-dash-surface-alt: #f0f5fb;
  --cf-dash-border: #dbe4f3;
  --cf-dash-text: #1e293b;
  --cf-dash-muted: #64748b;
  --cf-dash-primary: #2563eb;
  --cf-dash-primary-soft: #3b82f6;
  --cf-dash-accent: #0ea5e9;
  --cf-dash-success: #16a34a;
  --cf-dash-warning: #f97316;
  --cf-dash-critical: #dc2626;
  --cf-radius-sm: 6px;
  --cf-radius-md: 12px;
  --cf-radius-lg: 18px;
  --cf-shadow-sm: 0 2px 4px -2px rgba(30,58,138,.12),0 4px 6px -1px rgba(30,58,138,.08);
  --cf-shadow-lg: 0 12px 24px -6px rgba(30,58,138,.18);
}

.complyflow-legal-documents {
  background: var(--cf-dash-bg);
  padding: 0 0 40px;
  margin: 0 -20px 0 -10px;
  font-family: system-ui,-apple-system,"Segoe UI",Roboto,Ubuntu,"Helvetica Neue",sans-serif;
}

/* Hide default WP title */
.complyflow-legal-documents > h1:first-of-type,
.complyflow-legal-documents > .description:first-of-type {
  display: none;
}

/* Header with gradient */
.legal-documents-header {
  background: var(--cf-dash-gradient);
  padding: 32px 40px 80px;
  margin: 0;
  position: relative;
  overflow: hidden;
  border-radius: 0 0 var(--cf-radius-lg) var(--cf-radius-lg);
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.15);
}

.legal-documents-header h1 {
  color: #fff;
  font-size: 28px;
  font-weight: 600;
  letter-spacing: .5px;
  margin: 0 0 12px;
}

.legal-documents-header .page-description {
  color: rgba(255,255,255,0.9);
  font-size: 15px;
  margin: 0;
}

/* Meta info bar */
.legal-documents-meta {
  margin: -60px 20px 32px;
  background: var(--cf-dash-surface);
  border: 1px solid var(--cf-dash-border);
  box-shadow: var(--cf-shadow-sm);
  border-radius: var(--cf-radius-lg);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 28px;
  flex-wrap: wrap;
  gap: 16px;
}

.legal-documents-meta .meta-item {
  margin: 0;
  font-size: 14px;
  color: var(--cf-dash-muted);
}

.legal-documents-meta .meta-item strong {
  color: var(--cf-dash-text);
  font-weight: 600;
  margin-right: 6px;
}

/* Grid layout */
.complyflow-legal-grid {
  margin: 0 20px;
}

.complyflow-legal-main {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 28px;
  margin-bottom: 32px;
}

/* Policy Cards */
.complyflow-policy-card {
  background: var(--cf-dash-surface);
  border: 1px solid var(--cf-dash-border);
  border-radius: var(--cf-radius-lg);
  box-shadow: var(--cf-shadow-sm);
  position: relative;
  overflow: hidden;
  transition: box-shadow .35s, transform .35s;
}

.complyflow-policy-card:before {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg,rgba(59,130,246,.06),rgba(14,165,233,.04));
  opacity: 0;
  transition: opacity .35s;
  pointer-events: none;
}

.complyflow-policy-card:hover {
  box-shadow: var(--cf-shadow-lg);
  transform: translateY(-4px);
}

.complyflow-policy-card:hover:before {
  opacity: 1;
}

.complyflow-policy-card .postbox-header {
  background: none;
  border: none;
  padding: 20px 24px 12px;
  position: relative;
  z-index: 1;
}

.complyflow-policy-card .postbox-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: var(--cf-dash-text);
  margin: 0;
}

.complyflow-policy-card .inside {
  padding: 0 24px 24px;
  position: relative;
  z-index: 1;
}

/* Status badges */
.policy-status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 24px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 12px;
}

.policy-status-generated {
  background: rgba(22,163,74,0.1);
  color: var(--cf-dash-success);
}

.policy-status-not-generated {
  background: rgba(220,38,38,0.1);
  color: var(--cf-dash-critical);
}

.policy-status .dashicons {
  font-size: 16px;
  width: 16px;
  height: 16px;
}

.policy-updated {
  font-size: 13px;
  color: var(--cf-dash-muted);
  margin: 0 0 16px;
}

/* Policy Actions */
.policy-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 16px;
}

.policy-actions .button {
  font-size: 13px;
  font-weight: 500;
  padding: 8px 14px;
  border-radius: var(--cf-radius-sm);
  border: 1px solid var(--cf-dash-border);
  background: var(--cf-dash-surface-alt);
  color: var(--cf-dash-text);
  transition: all .25s;
  box-shadow: none;
}

.policy-actions .button:hover {
  background: var(--cf-dash-primary);
  border-color: var(--cf-dash-primary);
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px -2px rgba(37,99,235,.3);
}

.policy-actions .button-primary,
.generate-policy {
  background: var(--cf-dash-primary);
  border-color: var(--cf-dash-primary);
  color: #fff;
  box-shadow: 0 4px 12px -4px rgba(37,99,235,.4);
  width: 100%;
  padding: 12px 20px;
  font-size: 14px;
  font-weight: 600;
  justify-content: center;
}

.policy-actions .button-primary:hover,
.generate-policy:hover:not(:disabled) {
  background: var(--cf-dash-primary-soft);
  box-shadow: 0 6px 16px -4px rgba(37,99,235,.5);
  transform: translateY(-2px);
}

.generate-policy:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Shortcode box */
.policy-shortcode {
  background: var(--cf-dash-surface-alt);
  padding: 12px;
  border-radius: var(--cf-radius-sm);
  border: 1px solid var(--cf-dash-border);
}

.policy-shortcode label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: var(--cf-dash-muted);
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.policy-shortcode input {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid var(--cf-dash-border);
  border-radius: var(--cf-radius-sm);
  font-family: 'Courier New', monospace;
  font-size: 13px;
  background: var(--cf-dash-surface);
  color: var(--cf-dash-primary);
  font-weight: 500;
}

/* Sidebar widgets */
.complyflow-legal-sidebar {
  display: grid;
  gap: 28px;
  grid-column: 1 / -1;
}

@media (min-width: 1200px) {
  .complyflow-legal-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 28px;
  }
  
  .complyflow-legal-main {
    margin-bottom: 0;
  }
  
  .complyflow-legal-sidebar {
    grid-column: auto;
  }
}

.complyflow-legal-sidebar .postbox {
  background: var(--cf-dash-surface);
  border: 1px solid var(--cf-dash-border);
  border-radius: var(--cf-radius-lg);
  box-shadow: var(--cf-shadow-sm);
  transition: box-shadow .35s, transform .35s;
}

.complyflow-legal-sidebar .postbox:hover {
  box-shadow: var(--cf-shadow-lg);
  transform: translateY(-4px);
}

.complyflow-legal-sidebar .postbox-header {
  background: none;
  border: none;
  padding: 20px 24px 12px;
}

.complyflow-legal-sidebar .postbox-header h2 {
  font-size: 16px;
  font-weight: 600;
  color: var(--cf-dash-text);
  margin: 0;
}

.complyflow-legal-sidebar .inside {
  padding: 0 24px 24px;
}

/* Quick Actions */
.complyflow-quick-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.complyflow-quick-actions .button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 18px;
  font-size: 14px;
  font-weight: 600;
  border-radius: var(--cf-radius-md);
  transition: all .3s;
  text-decoration: none;
}

.complyflow-quick-actions .button-large,
.complyflow-quick-actions .button-primary {
  background: var(--cf-dash-primary);
  border-color: var(--cf-dash-primary);
  color: #fff;
  box-shadow: 0 6px 16px -6px rgba(37,99,235,.4);
}

.complyflow-quick-actions .button-large:hover,
.complyflow-quick-actions .button-primary:hover:not(:disabled) {
  background: var(--cf-dash-primary-soft);
  transform: translateY(-3px);
  box-shadow: 0 10px 24px -8px rgba(37,99,235,.5);
}

.complyflow-quick-actions .button:not(.button-primary):not(.button-large) {
  background: var(--cf-dash-surface-alt);
  border-color: var(--cf-dash-border);
  color: var(--cf-dash-text);
}

.complyflow-quick-actions .button:not(.button-primary):not(.button-large):hover {
  background: var(--cf-dash-primary);
  border-color: var(--cf-dash-primary);
  color: #fff;
  transform: translateY(-2px);
}

.complyflow-quick-actions .dashicons {
  font-size: 18px;
  width: 18px;
  height: 18px;
}

/* Policy Stats */
.complyflow-policy-stats {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.stat-item {
  text-align: center;
  padding: 16px 12px;
  background: var(--cf-dash-surface-alt);
  border-radius: var(--cf-radius-md);
  border: 1px solid var(--cf-dash-border);
  transition: all .25s;
}

.stat-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px -2px rgba(30,58,138,.15);
}

.stat-value {
  display: block;
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 4px;
  color: var(--cf-dash-success);
}

.stat-label {
  display: block;
  font-size: 12px;
  color: var(--cf-dash-muted);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Resources */
.complyflow-resources-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.complyflow-resources-list li {
  padding: 0;
  margin: 0;
}

.complyflow-resources-list a {
  display: flex;
  align-items: center;
  padding: 10px 12px;
  background: var(--cf-dash-surface-alt);
  color: var(--cf-dash-primary);
  text-decoration: none;
  border-radius: var(--cf-radius-sm);
  font-size: 14px;
  font-weight: 500;
  transition: all .25s;
  border: 1px solid var(--cf-dash-border);
}

.complyflow-resources-list a:before {
  content: "→";
  margin-right: 8px;
  font-weight: 700;
  transition: transform .25s;
}

.complyflow-resources-list a:hover {
  background: var(--cf-dash-primary);
  border-color: var(--cf-dash-primary);
  color: #fff;
  transform: translateX(4px);
}

.complyflow-resources-list a:hover:before {
  transform: translateX(4px);
}

/* Notice styling */
.notice {
  background: var(--cf-dash-surface);
  border-left: 4px solid var(--cf-dash-warning);
  border-radius: var(--cf-radius-md);
  padding: 16px 20px;
  margin: 0 20px 24px;
  box-shadow: var(--cf-shadow-sm);
  border-right: 1px solid var(--cf-dash-border);
  border-top: 1px solid var(--cf-dash-border);
  border-bottom: 1px solid var(--cf-dash-border);
}

.notice strong {
  color: var(--cf-dash-text);
}

.notice .button {
  margin-top: 8px;
}

/* Modal Styles */
.complyflow-modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 999999;
}

.complyflow-modal.complyflow-modal-show {
  display: block !important;
}

.complyflow-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  -webkit-backdrop-filter: blur(4px);
  backdrop-filter: blur(4px);
  z-index: 999998;
}

.complyflow-modal-content {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: var(--cf-dash-surface);
  border-radius: var(--cf-radius-lg);
  box-shadow: var(--cf-shadow-lg);
  z-index: 999999;
  max-width: 1200px;
  width: 95%;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  border: 1px solid var(--cf-dash-border);
  resize: both;
  overflow: hidden;
}

.complyflow-modal-large .complyflow-modal-content {
  max-width: 1400px;
}

/* Preview modal specific sizing */
.complyflow-preview-modal .complyflow-modal-content {
  max-width: 1200px;
  width: 95%;
  height: 90vh;
  max-height: 90vh;
}

.complyflow-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 28px;
  border-bottom: 1px solid var(--cf-dash-border);
  background: var(--cf-dash-gradient);
  border-radius: var(--cf-radius-lg) var(--cf-radius-lg) 0 0;
}

.complyflow-modal-header h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #fff;
}

.complyflow-modal-close {
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.3);
  font-size: 24px;
  line-height: 1;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  border-radius: var(--cf-radius-sm);
  transition: all .25s;
}

.complyflow-modal-close:hover {
  background: rgba(255,255,255,0.25);
  border-color: rgba(255,255,255,0.5);
  transform: scale(1.1);
}

.complyflow-modal-body {
  padding: 24px 28px;
  overflow-y: auto;
  flex: 1;
}

.complyflow-modal-footer {
  padding: 20px 28px;
  border-top: 1px solid var(--cf-dash-border);
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
  background: var(--cf-dash-surface-alt);
  border-radius: 0 0 var(--cf-radius-lg) var(--cf-radius-lg);
  flex-shrink: 0;
}

.complyflow-modal-footer .button {
  font-weight: 600;
  padding: 10px 20px;
  border-radius: var(--cf-radius-sm);
  height: auto;
  line-height: 1.4;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  white-space: nowrap;
}

.complyflow-modal-footer .button-primary {
  background: var(--cf-dash-primary);
  border-color: var(--cf-dash-primary);
  color: #fff;
  box-shadow: 0 4px 12px -4px rgba(37,99,235,.4);
}

.complyflow-modal-footer .button-primary:hover {
  background: var(--cf-dash-primary-soft);
  box-shadow: 0 6px 16px -4px rgba(37,99,235,.5);
}

/* Edit modal specific styles */
.complyflow-edit-modal .complyflow-modal-content {
  max-width: 1400px;
  width: 95%;
  height: 90vh;
}

.complyflow-edit-modal .complyflow-modal-body {
  padding: 20px 28px;
  overflow-y: auto;
  flex: 1;
  min-height: 0;
}

.complyflow-edit-warning {
  background: #fff3cd;
  border: 1px solid #ffc107;
  border-radius: var(--cf-radius-sm);
  padding: 12px 16px;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #856404;
}

.complyflow-edit-warning .dashicons {
  flex-shrink: 0;
}

/* Button dashicons alignment fix */
.complyflow-modal-footer .button .dashicons {
  margin-right: 6px;
  vertical-align: middle;
  line-height: 1;
}

/* Modal tabs */
.complyflow-modal-tabs {
  display: flex;
  gap: 8px;
  margin-left: auto;
  margin-right: 20px;
}

.complyflow-tab-btn {
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  color: rgba(255,255,255,0.8);
  padding: 6px 14px;
  border-radius: var(--cf-radius-sm);
  cursor: pointer;
  font-size: 13px;
  font-weight: 500;
  transition: all .25s;
}

.complyflow-tab-btn:hover {
  background: rgba(255,255,255,0.15);
  border-color: rgba(255,255,255,0.3);
  color: #fff;
}

.complyflow-tab-btn.active {
  background: rgba(255,255,255,0.95);
  border-color: rgba(255,255,255,0.95);
  color: var(--cf-dash-primary);
}

.complyflow-tab-content {
  display: none;
}

.complyflow-tab-content.active {
  display: block;
}

/* Version history modal */
.complyflow-history-modal .complyflow-modal-content {
  max-width: 900px;
}

.complyflow-version-timeline {
  max-height: 500px;
  overflow-y: auto;
  padding: 8px;
}

.complyflow-version-item {
  border-left: 3px solid var(--cf-dash-primary);
  padding-left: 20px;
  margin-bottom: 24px;
  position: relative;
  transition: all .25s;
}

.complyflow-version-item:hover {
  border-left-color: var(--cf-dash-accent);
}

.complyflow-version-badge {
  position: absolute;
  left: -10px;
  top: 0;
  width: 16px;
  height: 16px;
  background: var(--cf-dash-primary);
  border-radius: 50%;
  border: 3px solid var(--cf-dash-surface);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
  transition: all .25s;
}

.complyflow-version-item:hover .complyflow-version-badge {
  background: var(--cf-dash-accent);
  box-shadow: 0 0 0 6px rgba(14, 165, 233, 0.2);
  transform: scale(1.2);
}

.complyflow-version-header {
  margin-bottom: 10px;
}

.complyflow-version-header strong {
  font-size: 14px;
  color: var(--cf-dash-text);
  font-weight: 600;
}

.complyflow-version-meta {
  font-size: 13px;
  color: var(--cf-dash-muted);
  margin-bottom: 10px;
}

.complyflow-version-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.complyflow-version-actions .button {
  font-size: 13px;
  padding: 6px 12px;
  border-radius: var(--cf-radius-sm);
  height: auto;
  line-height: 1.4;
}

.complyflow-version-actions .dashicons {
  font-size: 16px;
  width: 16px;
  height: 16px;
  vertical-align: middle;
  margin-right: 4px;
  margin-top: -2px;
}

/* Diff viewer */
.complyflow-diff-legend {
  margin-bottom: 15px;
  padding: 12px 16px;
  background: var(--cf-dash-surface-alt);
  border-radius: var(--cf-radius-sm);
  border: 1px solid var(--cf-dash-border);
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
}

.complyflow-diff-legend span {
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 500;
  font-family: monospace;
}

.complyflow-diff-view {
  max-height: 600px;
  overflow-y: auto;
  font-family: 'Courier New', Courier, monospace;
  font-size: 12px;
  border: 1px solid var(--cf-dash-border);
  padding: 16px;
  background: var(--cf-dash-surface);
  border-radius: var(--cf-radius-sm);
  line-height: 1.6;
}

.diff-line-equal {
  padding: 4px 8px;
  margin: 2px 0;
}

.diff-line-added {
  padding: 4px 8px;
  margin: 2px 0;
  background: #d4edda;
  border-left: 3px solid #28a745;
}

.diff-line-removed {
  padding: 4px 8px;
  margin: 2px 0;
  background: #f8d7da;
  border-left: 3px solid #dc3545;
}

/* Badge styles */
.complyflow-badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.complyflow-badge-success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

/* Spin animation for loading states */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.spin {
  animation: spin 1s linear infinite;
  display: inline-block;
}

.complyflow-edit-warning .dashicons {
  color: #ffc107;
  flex-shrink: 0;
}

#complyflow-policy-editor {
  border: 1px solid var(--cf-dash-border);
  border-radius: var(--cf-radius-sm);
  padding: 12px;
  line-height: 1.6;
  resize: vertical;
}

#complyflow-policy-editor:focus {
  outline: 2px solid var(--cf-dash-primary);
  outline-offset: -2px;
  border-color: var(--cf-dash-primary);
}

/* Copy button feedback */
.copy-policy.copied {
  background: var(--cf-dash-success) !important;
  border-color: var(--cf-dash-success) !important;
  color: white !important;
}

body.modal-open {
  overflow: hidden;
}

/* Responsive */
@media (max-width: 1200px) {
  .complyflow-legal-main {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  }
}

@media (max-width: 782px) {
  .legal-documents-header {
    padding: 24px 20px 60px;
  }
  
  .legal-documents-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  
  .complyflow-legal-main {
    grid-template-columns: 1fr;
  }
  
  .complyflow-policy-stats {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>

<div class="legal-documents-header">
  <h1><?php esc_html_e('Legal Documents', 'complyflow'); ?></h1>
  <p class="page-description">
    <?php esc_html_e('Generate and manage legal documents for your website based on your compliance questionnaire.', 'complyflow'); ?>
  </p>
</div>

<div class="legal-documents-meta">
  <div class="meta-item">
    <strong><?php esc_html_e('Questionnaire Status:', 'complyflow'); ?></strong>
    <?php echo $completed ? '<span style="color: var(--cf-dash-success);">✓ ' . esc_html__('Completed', 'complyflow') . '</span>' : '<span style="color: var(--cf-dash-warning);">⚠ ' . esc_html__('Incomplete', 'complyflow') . '</span>'; ?>
  </div>
  <div class="meta-item">
    <strong><?php esc_html_e('Documents Generated:', 'complyflow'); ?></strong>
    <?php 
    $count = 0;
    if (!empty($privacy_policy)) $count++;
    if (!empty($terms)) $count++;
    if (!empty($cookie_policy)) $count++;
    if (!empty($data_protection)) $count++;
    echo esc_html($count . ' / 4');
    ?>
  </div>
</div>

<div class="wrap complyflow-legal-documents">
    <?php if ($show_success) : ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong><?php esc_html_e('Questionnaire Saved!', 'complyflow'); ?></strong><br>
                <?php esc_html_e('Your questionnaire answers have been saved successfully. You can now generate your legal documents.', 'complyflow'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if (!$completed) : ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php esc_html_e('Questionnaire Not Completed', 'complyflow'); ?></strong><br>
                <?php esc_html_e('Please complete the policy questionnaire first to generate your legal documents.', 'complyflow'); ?>
            </p>
            <p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-questionnaire')); ?>" class="button button-primary">
                    <?php esc_html_e('Start Questionnaire', 'complyflow'); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>

    <div class="complyflow-legal-grid">
        <div class="complyflow-legal-main">
            
            <!-- Privacy Policy -->
            <div class="complyflow-policy-card postbox">
                <div class="postbox-header">
                    <h2>Privacy Policy</h2>
                </div>
                <div class="inside">
                    <?php if (!empty($privacy_policy)) : ?>
                        <span class="policy-status policy-status-generated">
                            <span class="dashicons dashicons-yes-alt"></span>
                            Generated
                        </span>
                        <p class="policy-updated">
                            Last updated: <?php echo $privacy_updated ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $privacy_updated) : esc_html__('Never', 'complyflow'); ?>
                        </p>
                        <div class="policy-actions">
                            <button type="button" class="button view-policy" data-type="privacy_policy">
                                <span class="dashicons dashicons-visibility"></span>
                                View
                            </button>
                            <button type="button" class="button edit-policy" data-type="privacy_policy">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </button>
                            <button type="button" class="button copy-policy" data-type="privacy_policy">
                                <span class="dashicons dashicons-admin-page"></span>
                                Copy
                            </button>
                            <button type="button" class="button version-history-btn" data-type="privacy_policy">
                                <span class="dashicons dashicons-backup"></span>
                                Version History
                            </button>
                            <button type="button" class="button export-pdf" data-type="privacy_policy">
                                <span class="dashicons dashicons-pdf"></span>
                                Export PDF
                            </button>
                            <button type="button" class="button button-primary regenerate-policy" data-type="privacy_policy">
                                <span class="dashicons dashicons-update"></span>
                                Regenerate
                            </button>
                        </div>
                        <div class="policy-shortcode">
                            <label>Shortcode:</label>
                            <input type="text" readonly value="[complyflow_policy type=&quot;privacy_policy&quot;]" onclick="this.select()">
                        </div>
                    <?php else : ?>
                        <span class="policy-status policy-status-not-generated">
                            <span class="dashicons dashicons-warning"></span>
                            Not Generated
                        </span>
                        <p>No privacy policy has been generated yet.</p>
                        <button type="button" class="button button-primary generate-policy" data-type="privacy_policy" <?php disabled(!$completed); ?>>
                            <span class="dashicons dashicons-plus-alt"></span>
                            Generate Privacy Policy
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Terms of Service -->
            <div class="complyflow-policy-card postbox">
                <div class="postbox-header">
                    <h2>Terms of Service</h2>
                </div>
                <div class="inside">
                    <?php if (!empty($terms)) : ?>
                        <span class="policy-status policy-status-generated">
                            <span class="dashicons dashicons-yes-alt"></span>
                            Generated
                        </span>
                        <p class="policy-updated">
                            Last updated: <?php echo $terms_updated ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $terms_updated) : esc_html__('Never', 'complyflow'); ?>
                        </p>
                        <div class="policy-actions">
                            <button type="button" class="button view-policy" data-type="terms_of_service">
                                <span class="dashicons dashicons-visibility"></span>
                                View
                            </button>
                            <button type="button" class="button edit-policy" data-type="terms_of_service">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </button>
                            <button type="button" class="button copy-policy" data-type="terms_of_service">
                                <span class="dashicons dashicons-admin-page"></span>
                                Copy
                            </button>
                            <button type="button" class="button version-history-btn" data-type="terms_of_service">
                                <span class="dashicons dashicons-backup"></span>
                                Version History
                            </button>
                            <button type="button" class="button export-pdf" data-type="terms_of_service">
                                <span class="dashicons dashicons-pdf"></span>
                                Export PDF
                            </button>
                            <button type="button" class="button button-primary regenerate-policy" data-type="terms_of_service">
                                <span class="dashicons dashicons-update"></span>
                                Regenerate
                            </button>
                        </div>
                        <div class="policy-shortcode">
                            <label>Shortcode:</label>
                            <input type="text" readonly value="[complyflow_policy type=&quot;terms_of_service&quot;]" onclick="this.select()">
                        </div>
                    <?php else : ?>
                        <span class="policy-status policy-status-not-generated">
                            <span class="dashicons dashicons-warning"></span>
                            Not Generated
                        </span>
                        <p>No terms of service have been generated yet.</p>
                        <button type="button" class="button button-primary generate-policy" data-type="terms_of_service" <?php disabled(!$completed); ?>>
                            <span class="dashicons dashicons-plus-alt"></span>
                            Generate Terms
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cookie Policy -->
            <div class="complyflow-policy-card postbox">
                <div class="postbox-header">
                    <h2>Cookie Policy</h2>
                </div>
                <div class="inside">
                    <?php if (!empty($cookie_policy)) : ?>
                        <span class="policy-status policy-status-generated">
                            <span class="dashicons dashicons-yes-alt"></span>
                            Generated
                        </span>
                        <p class="policy-updated">
                            Last updated: <?php echo $cookie_updated ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $cookie_updated) : esc_html__('Never', 'complyflow'); ?>
                        </p>
                        <div class="policy-actions">
                            <button type="button" class="button view-policy" data-type="cookie_policy">
                                <span class="dashicons dashicons-visibility"></span>
                                View
                            </button>
                            <button type="button" class="button edit-policy" data-type="cookie_policy">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </button>
                            <button type="button" class="button copy-policy" data-type="cookie_policy">
                                <span class="dashicons dashicons-admin-page"></span>
                                Copy
                            </button>
                            <button type="button" class="button version-history-btn" data-type="cookie_policy">
                                <span class="dashicons dashicons-backup"></span>
                                Version History
                            </button>
                            <button type="button" class="button export-pdf" data-type="cookie_policy">
                                <span class="dashicons dashicons-pdf"></span>
                                Export PDF
                            </button>
                            <button type="button" class="button button-primary regenerate-policy" data-type="cookie_policy">
                                <span class="dashicons dashicons-update"></span>
                                Regenerate
                            </button>
                        </div>
                        <div class="policy-shortcode">
                            <label>Shortcode:</label>
                            <input type="text" readonly value="[complyflow_policy type=&quot;cookie_policy&quot;]" onclick="this.select()">
                        </div>
                    <?php else : ?>
                        <span class="policy-status policy-status-not-generated">
                            <span class="dashicons dashicons-warning"></span>
                            Not Generated
                        </span>
                        <p>No cookie policy has been generated yet.</p>
                        <button type="button" class="button button-primary generate-policy" data-type="cookie_policy" <?php disabled(!$completed); ?>>
                            <span class="dashicons dashicons-plus-alt"></span>
                            Generate Cookie Policy
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Data Protection Policy -->
            <div class="complyflow-policy-card postbox">
                <div class="postbox-header">
                    <h2>Data Protection Policy</h2>
                </div>
                <div class="inside">
                    <?php if (!empty($data_protection)) : ?>
                        <span class="policy-status policy-status-generated">
                            <span class="dashicons dashicons-yes-alt"></span>
                            Generated
                        </span>
                        <p class="policy-updated">
                            Last updated: <?php echo $data_protection_updated ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $data_protection_updated) : esc_html__('Never', 'complyflow'); ?>
                        </p>
                        <div class="policy-actions">
                            <button type="button" class="button view-policy" data-type="data_protection">
                                <span class="dashicons dashicons-visibility"></span>
                                View
                            </button>
                            <button type="button" class="button edit-policy" data-type="data_protection">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </button>
                            <button type="button" class="button copy-policy" data-type="data_protection">
                                <span class="dashicons dashicons-admin-page"></span>
                                Copy
                            </button>
                            <button type="button" class="button version-history-btn" data-type="data_protection">
                                <span class="dashicons dashicons-backup"></span>
                                Version History
                            </button>
                            <button type="button" class="button export-pdf" data-type="data_protection">
                                <span class="dashicons dashicons-pdf"></span>
                                Export PDF
                            </button>
                            <button type="button" class="button button-primary regenerate-policy" data-type="data_protection">
                                <span class="dashicons dashicons-update"></span>
                                Regenerate
                            </button>
                        </div>
                        <div class="policy-shortcode">
                            <label>Shortcode:</label>
                            <input type="text" readonly value='[complyflow_policy type="data_protection"]' onclick="this.select()">
                        </div>
                    <?php else : ?>
                        <span class="policy-status policy-status-not-generated">
                            <span class="dashicons dashicons-warning"></span>
                            Not Generated
                        </span>
                        <p>No data protection policy has been generated yet.</p>
                        <button type="button" class="button button-primary generate-policy" data-type="data_protection" <?php disabled(!$completed); ?>>
                            <span class="dashicons dashicons-plus-alt"></span>
                            Generate Data Protection Policy
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Consent Management Policy -->
            <div class="complyflow-policy-card postbox">
                <div class="postbox-header">
                    <h2>Consent Management Policy</h2>
                </div>
                <div class="inside">
                    <?php if (!empty($consent_management)) : ?>
                        <span class="policy-status policy-status-generated">
                            <span class="dashicons dashicons-yes-alt"></span>
                            Generated
                        </span>
                        <p class="policy-updated">
                            Last updated: <?php echo $consent_management_updated ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $consent_management_updated) : esc_html__('Never', 'complyflow'); ?>
                        </p>
                        <div class="policy-actions">
                            <button type="button" class="button view-policy" data-type="consent_management">
                                <span class="dashicons dashicons-visibility"></span>
                                View
                            </button>
                            <button type="button" class="button edit-policy" data-type="consent_management">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </button>
                            <button type="button" class="button copy-policy" data-type="consent_management">
                                <span class="dashicons dashicons-admin-page"></span>
                                Copy
                            </button>
                            <button type="button" class="button version-history-btn" data-type="consent_management">
                                <span class="dashicons dashicons-backup"></span>
                                Version History
                            </button>
                            <button type="button" class="button export-pdf" data-type="consent_management">
                                <span class="dashicons dashicons-pdf"></span>
                                Export PDF
                            </button>
                            <button type="button" class="button button-primary regenerate-policy" data-type="consent_management">
                                <span class="dashicons dashicons-update"></span>
                                Regenerate
                            </button>
                        </div>
                        <div class="policy-shortcode">
                            <label>Shortcode:</label>
                            <input type="text" readonly value='[complyflow_policy type="consent_management"]' onclick="this.select()">
                        </div>
                    <?php else : ?>
                        <span class="policy-status policy-status-not-generated">
                            <span class="dashicons dashicons-warning"></span>
                            Not Generated
                        </span>
                        <p>No consent management policy has been generated yet.</p>
                        <button type="button" class="button button-primary generate-policy" data-type="consent_management" <?php disabled(!$completed); ?>>
                            <span class="dashicons dashicons-plus-alt"></span>
                            Generate Consent Management Policy
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Rights Notice -->
            <div class="complyflow-policy-card postbox">
                <div class="postbox-header">
                    <h2>User Rights Notice</h2>
                </div>
                <div class="inside">
                    <?php if (!empty($user_rights_notice)) : ?>
                        <span class="policy-status policy-status-generated">
                            <span class="dashicons dashicons-yes-alt"></span>
                            Generated
                        </span>
                        <p class="policy-updated">
                            Last updated: <?php echo $user_rights_notice_updated ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $user_rights_notice_updated) : esc_html__('Never', 'complyflow'); ?>
                        </p>
                        <div class="policy-actions">
                            <button type="button" class="button view-policy" data-type="user_rights_notice">
                                <span class="dashicons dashicons-visibility"></span>
                                View
                            </button>
                            <button type="button" class="button edit-policy" data-type="user_rights_notice">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </button>
                            <button type="button" class="button copy-policy" data-type="user_rights_notice">
                                <span class="dashicons dashicons-admin-page"></span>
                                Copy
                            </button>
                            <button type="button" class="button version-history-btn" data-type="user_rights_notice">
                                <span class="dashicons dashicons-backup"></span>
                                Version History
                            </button>
                            <button type="button" class="button export-pdf" data-type="user_rights_notice">
                                <span class="dashicons dashicons-pdf"></span>
                                Export PDF
                            </button>
                            <button type="button" class="button button-primary regenerate-policy" data-type="user_rights_notice">
                                <span class="dashicons dashicons-update"></span>
                                Regenerate
                            </button>
                        </div>
                        <div class="policy-shortcode">
                            <label>Shortcode:</label>
                            <input type="text" readonly value='[complyflow_policy type="user_rights_notice"]' onclick="this.select()">
                        </div>
                    <?php else : ?>
                        <span class="policy-status policy-status-not-generated">
                            <span class="dashicons dashicons-warning"></span>
                            Not Generated
                        </span>
                        <p>No user rights notice has been generated yet.</p>
                        <button type="button" class="button button-primary generate-policy" data-type="user_rights_notice" <?php disabled(!$completed); ?>>
                            <span class="dashicons dashicons-plus-alt"></span>
                            Generate User Rights Notice
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Third-Party Services Disclosure -->
            <div class="complyflow-policy-card postbox">
                <div class="postbox-header">
                    <h2>Third-Party Services Disclosure</h2>
                </div>
                <div class="inside">
                    <?php if (!empty($third_party_services)) : ?>
                        <span class="policy-status policy-status-generated">
                            <span class="dashicons dashicons-yes-alt"></span>
                            Generated
                        </span>
                        <p class="policy-updated">
                            Last updated: <?php echo $third_party_services_updated ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $third_party_services_updated) : esc_html__('Never', 'complyflow'); ?>
                        </p>
                        <div class="policy-actions">
                            <button type="button" class="button view-policy" data-type="third_party_services">
                                <span class="dashicons dashicons-visibility"></span>
                                View
                            </button>
                            <button type="button" class="button edit-policy" data-type="third_party_services">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </button>
                            <button type="button" class="button copy-policy" data-type="third_party_services">
                                <span class="dashicons dashicons-admin-page"></span>
                                Copy
                            </button>
                            <button type="button" class="button version-history-btn" data-type="third_party_services">
                                <span class="dashicons dashicons-backup"></span>
                                Version History
                            </button>
                            <button type="button" class="button export-pdf" data-type="third_party_services">
                                <span class="dashicons dashicons-pdf"></span>
                                Export PDF
                            </button>
                            <button type="button" class="button button-primary regenerate-policy" data-type="third_party_services">
                                <span class="dashicons dashicons-update"></span>
                                Regenerate
                            </button>
                        </div>
                        <div class="policy-shortcode">
                            <label>Shortcode:</label>
                            <input type="text" readonly value='[complyflow_policy type="third_party_services"]' onclick="this.select()">
                        </div>
                    <?php else : ?>
                        <span class="policy-status policy-status-not-generated">
                            <span class="dashicons dashicons-warning"></span>
                            Not Generated
                        </span>
                        <p>No third-party services disclosure has been generated yet.</p>
                        <button type="button" class="button button-primary generate-policy" data-type="third_party_services" <?php disabled(!$completed); ?>>
                            <span class="dashicons dashicons-plus-alt"></span>
                            Generate Third-Party Services Disclosure
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cookie Categories Reference -->
            <div class="complyflow-policy-card postbox">
                <div class="postbox-header">
                    <h2>Cookie Categories Reference</h2>
                </div>
                <div class="inside">
                    <?php if (!empty($cookie_categories)) : ?>
                        <span class="policy-status policy-status-generated">
                            <span class="dashicons dashicons-yes-alt"></span>
                            Generated
                        </span>
                        <p class="policy-updated">
                            Last updated: <?php echo $cookie_categories_updated ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $cookie_categories_updated) : esc_html__('Never', 'complyflow'); ?>
                        </p>
                        <div class="policy-actions">
                            <button type="button" class="button view-policy" data-type="cookie_categories">
                                <span class="dashicons dashicons-visibility"></span>
                                View
                            </button>
                            <button type="button" class="button edit-policy" data-type="cookie_categories">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </button>
                            <button type="button" class="button copy-policy" data-type="cookie_categories">
                                <span class="dashicons dashicons-admin-page"></span>
                                Copy
                            </button>
                            <button type="button" class="button version-history-btn" data-type="cookie_categories">
                                <span class="dashicons dashicons-backup"></span>
                                Version History
                            </button>
                            <button type="button" class="button export-pdf" data-type="cookie_categories">
                                <span class="dashicons dashicons-pdf"></span>
                                Export PDF
                            </button>
                            <button type="button" class="button button-primary regenerate-policy" data-type="cookie_categories">
                                <span class="dashicons dashicons-update"></span>
                                Regenerate
                            </button>
                        </div>
                        <div class="policy-shortcode">
                            <label>Shortcode:</label>
                            <input type="text" readonly value='[complyflow_policy type="cookie_categories"]' onclick="this.select()">
                        </div>
                    <?php else : ?>
                        <span class="policy-status policy-status-not-generated">
                            <span class="dashicons dashicons-warning"></span>
                            Not Generated
                        </span>
                        <p>No cookie categories reference has been generated yet.</p>
                        <button type="button" class="button button-primary generate-policy" data-type="cookie_categories" <?php disabled(!$completed); ?>>
                            <span class="dashicons dashicons-plus-alt"></span>
                            Generate Cookie Categories Reference
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="complyflow-legal-sidebar">
            <!-- Quick Actions -->
            <div class="postbox">
                <div class="postbox-header">
                    <h2>Quick Actions</h2>
                </div>
                <div class="inside complyflow-quick-actions">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-questionnaire')); ?>" class="button button-large">
                        <span class="dashicons dashicons-edit"></span>
                        Edit Questionnaire
                    </a>
                    <button type="button" id="generate-all-policies" class="button button-primary button-large" <?php disabled(!$completed); ?>>
                        <span class="dashicons dashicons-update"></span>
                        Generate All Policies
                    </button>
                </div>
            </div>

            <!-- Policy Status -->
            <div class="postbox">
                <div class="postbox-header">
                    <h2>Policy Status</h2>
                </div>
                <div class="inside">
                    <div class="complyflow-policy-stats">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo !empty($privacy_policy) ? '✓' : '✗'; ?></span>
                            <span class="stat-label">Privacy</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo !empty($terms) ? '✓' : '✗'; ?></span>
                            <span class="stat-label">Terms</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo !empty($cookie_policy) ? '✓' : '✗'; ?></span>
                            <span class="stat-label">Cookie</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo !empty($data_protection) ? '✓' : '✗'; ?></span>
                            <span class="stat-label">Data Protection</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo !empty($consent_management) ? '✓' : '✗'; ?></span>
                            <span class="stat-label">Consent</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo !empty($user_rights_notice) ? '✓' : '✗'; ?></span>
                            <span class="stat-label">User Rights</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo !empty($third_party_services) ? '✓' : '✗'; ?></span>
                            <span class="stat-label">3rd Party</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo !empty($cookie_categories) ? '✓' : '✗'; ?></span>
                            <span class="stat-label">Categories</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resources -->
            <div class="postbox">
                <div class="postbox-header">
                    <h2>Resources</h2>
                </div>
                <div class="inside">
                    <ul class="complyflow-resources-list">
                        <li><a href="https://gdpr.eu/" target="_blank">GDPR Official Site</a></li>
                        <li><a href="https://oag.ca.gov/privacy/ccpa" target="_blank">CCPA Information</a></li>
                        <li><a href="https://www.gov.br/cidadania/pt-br/acesso-a-informacao/lgpd" target="_blank">LGPD Official Site</a></li>
                        <li><a href="https://www.ftc.gov/enforcement/rules/rulemaking-regulatory-reform-proceedings/childrens-online-privacy-protection-rule" target="_blank">COPPA Information</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="complyflow-policy-modal" class="complyflow-modal">
    <div class="complyflow-modal-overlay"></div>
    <div class="complyflow-modal-content">
        <div class="complyflow-modal-header">
            <h2 id="policy-modal-title"></h2>
            <button type="button" class="complyflow-modal-close">&times;</button>
        </div>
        <div class="complyflow-modal-body">
            <div id="policy-preview-content"></div>
        </div>
        <div class="complyflow-modal-footer">
            <button type="button" class="button button-secondary complyflow-modal-close">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="complyflow-policy-edit-modal" class="complyflow-modal complyflow-modal-large">
    <div class="complyflow-modal-overlay"></div>
    <div class="complyflow-modal-content">
        <div class="complyflow-modal-header">
            <h2>Edit Policy</h2>
            <button type="button" class="complyflow-modal-close">&times;</button>
        </div>
        <div class="complyflow-modal-body">
            <?php
            wp_editor('', 'policy_editor', [
                'textarea_rows' => 20,
                'media_buttons' => false,
                'tinymce' => [
                    'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,undo,redo',
                ],
            ]);
            ?>
            <input type="hidden" id="editing-policy-type">
        </div>
        <div class="complyflow-modal-footer">
            <button type="button" id="save-edited-policy" class="button button-primary">
                Save Changes
            </button>
            <button type="button" class="button button-secondary complyflow-modal-close">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Close modal functionality
    function closeModal(modalId) {
        $('#' + modalId).removeClass('complyflow-modal-show').hide();
        $('body').removeClass('modal-open');
    }

    // Open modal functionality
    function openModal(modalId) {
        $('#' + modalId).addClass('complyflow-modal-show').show();
        $('body').addClass('modal-open');
    }

    // Close button click handler
    $(document).on('click', '.complyflow-modal-close', function() {
        var modal = $(this).closest('.complyflow-modal');
        closeModal(modal.attr('id'));
    });

    // Overlay click handler
    $(document).on('click', '.complyflow-modal-overlay', function() {
        var modal = $(this).closest('.complyflow-modal');
        closeModal(modal.attr('id'));
    });

    // ESC key handler
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            $('.complyflow-modal.complyflow-modal-show').each(function() {
                closeModal($(this).attr('id'));
            });
        }
    });

    // Edit policy button
    $(document).on('click', '.edit-policy', function() {
        var policyType = $(this).data('type');
        $('#editing-policy-type').val(policyType);
        
        // Load policy content into editor
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_get_policy',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType
            },
            success: function(response) {
                if (response.success && response.data.content) {
                    if (typeof tinymce !== 'undefined') {
                        var editor = tinymce.get('policy_editor');
                        if (editor) {
                            editor.setContent(response.data.content);
                        } else {
                            $('#policy_editor').val(response.data.content);
                        }
                    } else {
                        $('#policy_editor').val(response.data.content);
                    }
                    openModal('complyflow-policy-edit-modal');
                }
            },
            error: function() {
                alert('Failed to load policy content.');
            }
        });
    });

    // Preview policy button
    $(document).on('click', '.preview-policy', function() {
        var policyType = $(this).data('type');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_get_policy',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType
            },
            success: function(response) {
                if (response.success && response.data.content) {
                    var title = policyType.replace('_', ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); });
                    $('#policy-modal-title').text(title);
                    $('#policy-preview-content').html(response.data.content);
                    openModal('complyflow-policy-modal');
                }
            },
            error: function() {
                alert('Failed to load policy content.');
            }
        });
    });

    // Save edited policy
    $('#save-edited-policy').on('click', function() {
        var policyType = $('#editing-policy-type').val();
        var content = '';
        
        if (typeof tinymce !== 'undefined') {
            var editor = tinymce.get('policy_editor');
            if (editor) {
                content = editor.getContent();
            } else {
                content = $('#policy_editor').val();
            }
        } else {
            content = $('#policy_editor').val();
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_save_policy',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType,
                content: content
            },
            success: function(response) {
                if (response.success) {
                    alert('Policy saved successfully!');
                    closeModal('complyflow-policy-edit-modal');
                    location.reload();
                } else {
                    alert('Failed to save policy: ' + (response.data.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('Failed to save policy.');
            }
        });
    });

    // View policy button
    $(document).on('click', '.view-policy', function() {
        var policyType = $(this).data('type');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_get_policy',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType
            },
            success: function(response) {
                if (response.success && response.data.content) {
                    // Create modal
                    var modal = $('<div class="complyflow-modal-overlay complyflow-preview-modal"></div>');
                    var modalContent = $('<div class="complyflow-modal-content">' +
                        '<div class="complyflow-modal-header">' +
                        '<h2>Policy Preview</h2>' +
                        '<button class="complyflow-modal-close">&times;</button>' +
                        '</div>' +
                        '<div class="complyflow-modal-body" style="padding: 0; overflow: hidden;">' +
                        '<iframe style="width: 100%; height: 100%; border: none; background: white;"></iframe>' +
                        '</div>' +
                        '<div class="complyflow-modal-footer">' +
                        '<button class="button button-secondary complyflow-modal-close">Close</button>' +
                        '<button class="button button-primary complyflow-print-policy">Print</button>' +
                        '</div>' +
                        '</div>');
                    
                    modal.append(modalContent);
                    $('body').append(modal);
                    
                    // Write content to iframe
                    var iframe = modal.find('iframe')[0];
                    iframe.contentWindow.document.open();
                    iframe.contentWindow.document.write(response.data.content);
                    iframe.contentWindow.document.close();
                    
                    // Show modal
                    modal.fadeIn(200);
                    
                    // Close modal handlers
                    modal.find('.complyflow-modal-close').on('click', function() {
                        modal.fadeOut(200, function() {
                            modal.remove();
                        });
                    });
                    
                    modal.on('click', function(e) {
                        if ($(e.target).hasClass('complyflow-modal-overlay')) {
                            modal.fadeOut(200, function() {
                                modal.remove();
                            });
                        }
                    });
                    
                    // Print handler
                    modal.find('.complyflow-print-policy').on('click', function() {
                        iframe.contentWindow.print();
                    });
                } else {
                    alert('Failed to load policy: ' + (response.data.message || 'Policy not found'));
                }
            },
            error: function() {
                alert('Failed to load policy.');
            }
        });
    });

    // Edit policy button
    $(document).on('click', '.edit-policy', function() {
        var policyType = $(this).data('type');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_get_policy',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType
            },
            success: function(response) {
                if (response.success && response.data.content) {
                    // Create edit modal with TinyMCE
                    var modal = $('<div class="complyflow-modal-overlay complyflow-edit-modal"></div>');
                    var modalContent = $('<div class="complyflow-modal-content">' +
                        '<div class="complyflow-modal-header">' +
                        '<h2>Edit Policy</h2>' +
                        '<div class="complyflow-modal-tabs">' +
                        '<button class="complyflow-tab-btn active" data-tab="editor">✏️ Editor</button>' +
                        '<button class="complyflow-tab-btn" data-tab="html">📝 HTML</button>' +
                        '<button class="complyflow-tab-btn" data-tab="preview">👁️ Preview</button>' +
                        '</div>' +
                        '<button class="complyflow-modal-close">&times;</button>' +
                        '</div>' +
                        '<div class="complyflow-modal-body">' +
                        '<div class="complyflow-edit-warning">' +
                        '<span class="dashicons dashicons-warning"></span> ' +
                        'Warning: Manual edits will be preserved unless you regenerate this policy.' +
                        '</div>' +
                        '<div class="complyflow-tab-content active" data-tab="editor">' +
                        '<textarea id="complyflow-policy-editor"></textarea>' +
                        '</div>' +
                        '<div class="complyflow-tab-content" data-tab="html">' +
                        '<textarea id="complyflow-policy-html" style="width: 100%; min-height: 600px; height: calc(90vh - 280px); font-family: monospace; font-size: 13px; resize: vertical;"></textarea>' +
                        '</div>' +
                        '<div class="complyflow-tab-content" data-tab="preview">' +
                        '<iframe id="complyflow-policy-preview" style="width: 100%; min-height: 600px; height: calc(90vh - 280px); border: 1px solid #ddd; background: white;"></iframe>' +
                        '</div>' +
                        '</div>' +
                        '<div class="complyflow-modal-footer">' +
                        '<button class="button button-secondary complyflow-modal-close">Cancel</button>' +
                        '<button class="button complyflow-view-history" data-type="' + policyType + '">' +
                        '<span class="dashicons dashicons-backup"></span> Version History' +
                        '</button>' +
                        '<button class="button button-primary complyflow-save-edited-policy" data-type="' + policyType + '">Save Changes</button>' +
                        '</div>' +
                        '</div>');
                    
                    modal.append(modalContent);
                    $('body').append(modal);
                    
                    // Tab switching
                    modal.find('.complyflow-tab-btn').on('click', function() {
                        var tab = $(this).data('tab');
                        modal.find('.complyflow-tab-btn').removeClass('active');
                        $(this).addClass('active');
                        modal.find('.complyflow-tab-content').removeClass('active');
                        modal.find('.complyflow-tab-content[data-tab="' + tab + '"]').addClass('active');
                        
                        // Update HTML view when switching
                        if (tab === 'html' && typeof tinymce !== 'undefined') {
                            var editor = tinymce.get('complyflow-policy-editor');
                            if (editor) {
                                $('#complyflow-policy-html').val(editor.getContent());
                            }
                        }
                        
                        // Update preview when switching
                        if (tab === 'preview') {
                            var content = '';
                            if (typeof tinymce !== 'undefined') {
                                var editor = tinymce.get('complyflow-policy-editor');
                                content = editor ? editor.getContent() : $('#complyflow-policy-html').val();
                            } else {
                                content = $('#complyflow-policy-html').val();
                            }
                            var iframe = document.getElementById('complyflow-policy-preview');
                            iframe.contentWindow.document.open();
                            iframe.contentWindow.document.write(content);
                            iframe.contentWindow.document.close();
                        }
                        
                        // Update editor from HTML when switching back
                        if (tab === 'editor' && typeof tinymce !== 'undefined') {
                            var editor = tinymce.get('complyflow-policy-editor');
                            if (editor) {
                                editor.setContent($('#complyflow-policy-html').val());
                            }
                        }
                    });
                    
                    // Show modal
                    modal.fadeIn(200, function() {
                        // Initialize TinyMCE after modal is visible
                        if (typeof tinymce !== 'undefined') {
                            wp.editor.initialize('complyflow-policy-editor', {
                                tinymce: {
                                    wpautop: false,
                                    height: 'calc(90vh - 300px)',
                                    min_height: 500,
                                    plugins: 'lists link code fullscreen paste autoresize',
                                    autoresize_min_height: 500,
                                    autoresize_max_height: 'calc(90vh - 300px)',
                                    toolbar1: 'formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link unlink | code fullscreen',
                                    menubar: false,
                                    branding: false,
                                    statusbar: true,
                                    resize: true,
                                    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; line-height: 1.6; }'
                                },
                                quicktags: true,
                                mediaButtons: false
                            });
                            
                            // Wait for TinyMCE to initialize
                            setTimeout(function() {
                                var editor = tinymce.get('complyflow-policy-editor');
                                if (editor) {
                                    editor.setContent(response.data.content);
                                } else {
                                    $('#complyflow-policy-editor').val(response.data.content);
                                }
                                $('#complyflow-policy-html').val(response.data.content);
                            }, 500);
                        } else {
                            // Fallback to textarea
                            $('#complyflow-policy-editor').val(response.data.content);
                            $('#complyflow-policy-html').val(response.data.content);
                        }
                    });
                    
                    // Close modal handlers
                    modal.find('.complyflow-modal-close').on('click', function() {
                        if (confirm('Are you sure you want to close without saving?')) {
                            // Remove TinyMCE instance
                            if (typeof tinymce !== 'undefined' && tinymce.get('complyflow-policy-editor')) {
                                wp.editor.remove('complyflow-policy-editor');
                            }
                            modal.fadeOut(200, function() {
                                modal.remove();
                            });
                        }
                    });
                    
                    // Version history handler
                    modal.find('.complyflow-view-history').on('click', function() {
                        showVersionHistory(policyType);
                    });
                    
                    // Save handler
                    modal.find('.complyflow-save-edited-policy').on('click', function() {
                        var saveBtn = $(this);
                        var originalText = saveBtn.text();
                        saveBtn.prop('disabled', true).text('Saving...');
                        
                        // Get content from TinyMCE or fallback to textarea
                        var editedContent = '';
                        if (typeof tinymce !== 'undefined' && tinymce.get('complyflow-policy-editor')) {
                            editedContent = tinymce.get('complyflow-policy-editor').getContent();
                        } else {
                            editedContent = modal.find('#complyflow-policy-editor').val();
                        }
                        
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'complyflow_save_policy',
                                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                                policy_type: policyType,
                                content: editedContent
                            },
                            success: function(saveResponse) {
                                if (saveResponse.success) {
                                    alert('Policy saved successfully!');
                                    
                                    // Remove TinyMCE instance
                                    if (typeof tinymce !== 'undefined' && tinymce.get('complyflow-policy-editor')) {
                                        wp.editor.remove('complyflow-policy-editor');
                                    }
                                    
                                    modal.fadeOut(200, function() {
                                        modal.remove();
                                    });
                                    location.reload();
                                } else {
                                    alert('Failed to save: ' + (saveResponse.data.message || 'Unknown error'));
                                    saveBtn.prop('disabled', false).text(originalText);
                                }
                            },
                            error: function() {
                                alert('Failed to save policy.');
                                saveBtn.prop('disabled', false).text(originalText);
                            }
                        });
                    });
                } else {
                    alert('Failed to load policy: ' + (response.data.message || 'Policy not found'));
                }
            },
            error: function() {
                alert('Failed to load policy.');
            }
        });
    });

    // Copy policy button
    $(document).on('click', '.copy-policy', function() {
        var policyType = $(this).data('type');
        var button = $(this);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_get_policy',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType
            },
            success: function(response) {
                if (response.success && response.data.content) {
                    // Create temporary textarea
                    var tempTextarea = $('<textarea>');
                    tempTextarea.val(response.data.content);
                    tempTextarea.css({
                        position: 'fixed',
                        top: '-9999px',
                        left: '-9999px'
                    });
                    $('body').append(tempTextarea);
                    
                    // Select and copy
                    tempTextarea[0].select();
                    tempTextarea[0].setSelectionRange(0, 99999);
                    
                    try {
                        document.execCommand('copy');
                        
                        // Show success feedback
                        var originalHtml = button.html();
                        button.html('<span class="dashicons dashicons-yes"></span> Copied!');
                        button.addClass('copied');
                        
                        setTimeout(function() {
                            button.html(originalHtml);
                            button.removeClass('copied');
                        }, 2000);
                    } catch (err) {
                        alert('Failed to copy to clipboard. Please try manually.');
                    }
                    
                    // Remove temporary textarea
                    tempTextarea.remove();
                } else {
                    alert('Failed to load policy: ' + (response.data.message || 'Policy not found'));
                }
            },
            error: function() {
                alert('Failed to load policy.');
            }
        });
    });

    // Generate policy button
    $(document).on('click', '.generate-policy', function() {
        var policyType = $(this).data('type');
        var button = $(this);
        
        button.prop('disabled', true).text('Generating...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_generate_policy',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType
            },
            success: function(response) {
                if (response.success) {
                    alert('Policy generated successfully!');
                    location.reload();
                } else {
                    alert('Failed to generate policy: ' + (response.data.message || 'Unknown error'));
                    button.prop('disabled', false).text('Generate ' + policyType.replace('_', ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); }));
                }
            },
            error: function() {
                alert('Failed to generate policy.');
                button.prop('disabled', false).text('Generate ' + policyType.replace('_', ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); }));
            }
        });
    });

    // Version History button
    $(document).on('click', '.version-history-btn', function() {
        var policyType = $(this).data('type');
        showVersionHistory(policyType);
    });

    // Export policy button (HTML)
    $(document).on('click', '.export-policy', function() {
        var policyType = $(this).data('type');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_get_policy',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType
            },
            success: function(response) {
                if (response.success && response.data.content) {
                    var blob = new Blob([response.data.content], { type: 'text/html' });
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = policyType + '.html';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                }
            },
            error: function() {
                alert('Failed to export policy.');
            }
        });
    });

    // Export PDF button
    $(document).on('click', '.export-pdf', function() {
        var policyType = $(this).data('type');
        var button = $(this);
        var originalHtml = button.html();
        
        button.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Generating PDF...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_get_policy',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType
            },
            success: function(response) {
                if (response.success && response.data.content) {
                    // Create a printable window with the policy content
                    var printWindow = window.open('', '_blank');
                    var policyTitle = policyType.replace(/_/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); });
                    
                    printWindow.document.write('<!DOCTYPE html><html><head>');
                    printWindow.document.write('<title>' + policyTitle + '</title>');
                    printWindow.document.write('<style>');
                    printWindow.document.write('body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 40px auto; padding: 0 20px; }');
                    printWindow.document.write('h1, h2, h3 { color: #2563eb; page-break-after: avoid; }');
                    printWindow.document.write('p { margin-bottom: 1em; page-break-inside: avoid; }');
                    printWindow.document.write('ul, ol { margin-bottom: 1em; page-break-inside: avoid; }');
                    printWindow.document.write('@media print { body { margin: 0; } @page { margin: 2cm; } }');
                    printWindow.document.write('</style>');
                    printWindow.document.write('</head><body>');
                    printWindow.document.write(response.data.content);
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    
                    // Wait for content to load then trigger print
                    setTimeout(function() {
                        printWindow.focus();
                        printWindow.print();
                        // Note: User will need to "Save as PDF" in the print dialog
                    }, 500);
                    
                    button.prop('disabled', false).html(originalHtml);
                } else {
                    alert('Failed to load policy content.');
                    button.prop('disabled', false).html(originalHtml);
                }
            },
            error: function() {
                alert('Failed to export policy.');
                button.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Regenerate policy button
    $(document).on('click', '.regenerate-policy', function() {
        if (!confirm('Are you sure you want to regenerate this policy? This will overwrite the current version.')) {
            return;
        }
        
        var policyType = $(this).data('type');
        var button = $(this);
        
        button.prop('disabled', true).text('Regenerating...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_generate_policy',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType
            },
            success: function(response) {
                if (response.success) {
                    alert('Policy regenerated successfully!');
                    location.reload();
                } else {
                    alert('Failed to regenerate policy: ' + (response.data.message || 'Unknown error'));
                    button.prop('disabled', false).text('Regenerate');
                }
            },
            error: function() {
                alert('Failed to regenerate policy.');
                button.prop('disabled', false).text('Regenerate');
            }
        });
    });

    // Generate all policies button
    $('#generate-all-policies').on('click', function() {
        if (!confirm('Are you sure you want to generate all policies? This may take a moment.')) {
            return;
        }
        
        var button = $(this);
        button.prop('disabled', true).text('Generating All Policies...');
        
        var policies = ['privacy_policy', 'terms_of_service', 'cookie_policy', 'data_protection', 'consent_management', 'user_rights_notice', 'third_party_services', 'cookie_categories'];
        var completed = 0;
        
        function generateNext() {
            if (completed >= policies.length) {
                alert('All policies generated successfully!');
                location.reload();
                return;
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'complyflow_generate_policy',
                    nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                    policy_type: policies[completed]
                },
                success: function(response) {
                    completed++;
                    generateNext();
                },
                error: function() {
                    alert('Failed to generate ' + policies[completed] + ' policy.');
                    button.prop('disabled', false).text('Generate All Policies');
                }
            });
        }
        
        generateNext();
    });

    // Version History Viewer
    function showVersionHistory(policyType) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_get_version_history',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType
            },
            success: function(response) {
                if (response.success && response.data.versions) {
                    var versions = response.data.versions;
                    
                    var historyModal = $('<div class="complyflow-modal-overlay"></div>');
                    var historyContent = $('<div class="complyflow-modal-content complyflow-history-modal" style="max-width: 900px;">' +
                        '<div class="complyflow-modal-header">' +
                        '<h2><span class="dashicons dashicons-backup"></span> Version History</h2>' +
                        '<button class="complyflow-modal-close">&times;</button>' +
                        '</div>' +
                        '<div class="complyflow-modal-body">' +
                        '<div class="complyflow-version-timeline"></div>' +
                        '</div>' +
                        '<div class="complyflow-modal-footer">' +
                        '<button class="button button-secondary complyflow-modal-close">Close</button>' +
                        '</div>' +
                        '</div>');
                    
                    historyModal.append(historyContent);
                    var timeline = historyContent.find('.complyflow-version-timeline');
                    
                    if (versions.length === 0) {
                        timeline.html('<p style="text-align: center; color: #666; padding: 40px;">No version history available.</p>');
                    } else {
                        versions.forEach(function(version, index) {
                            var versionItem = $('<div class="complyflow-version-item" style="border-left: 3px solid #2271b1; padding-left: 20px; margin-bottom: 20px; position: relative;">' +
                                '<div class="complyflow-version-badge" style="position: absolute; left: -10px; top: 0; width: 16px; height: 16px; background: #2271b1; border-radius: 50%; border: 3px solid #fff;"></div>' +
                                '<div class="complyflow-version-header" style="margin-bottom: 10px;">' +
                                '<strong style="font-size: 14px;">Version ' + version.version + '</strong> ' +
                                '<span style="color: #666; font-size: 13px;">' + version.timestamp + '</span>' +
                                (version.is_current ? ' <span class="complyflow-badge complyflow-badge-success" style="margin-left: 10px;">Current</span>' : '') +
                                '</div>' +
                                '<div class="complyflow-version-meta" style="font-size: 13px; color: #666; margin-bottom: 10px;">' +
                                '<span><strong>Size:</strong> ' + version.size + '</span> | ' +
                                '<span><strong>Modified by:</strong> ' + version.user + '</span>' +
                                (version.changes_summary ? ' | <span><strong>Changes:</strong> ' + version.changes_summary + '</span>' : '') +
                                '</div>' +
                                '<div class="complyflow-version-actions">' +
                                '<button class="button button-small complyflow-view-version" data-version="' + version.version + '" data-type="' + policyType + '">' +
                                '<span class="dashicons dashicons-visibility"></span> View' +
                                '</button> ' +
                                (index > 0 ? '<button class="button button-small complyflow-compare-versions" data-version="' + version.version + '" data-prev-version="' + versions[index - 1].version + '" data-type="' + policyType + '">' +
                                '<span class="dashicons dashicons-leftright"></span> Compare with Previous' +
                                '</button> ' : '') +
                                (!version.is_current ? '<button class="button button-small button-primary complyflow-restore-version" data-version="' + version.version + '" data-type="' + policyType + '">' +
                                '<span class="dashicons dashicons-backup"></span> Restore' +
                                '</button>' : '') +
                                '</div>' +
                                '</div>');
                            
                            timeline.append(versionItem);
                        });
                    }
                    
                    $('body').append(historyModal);
                    historyModal.fadeIn(200);
                    
                    historyModal.find('.complyflow-modal-close').on('click', function() {
                        historyModal.fadeOut(200, function() {
                            historyModal.remove();
                        });
                    });
                    
                    historyModal.on('click', '.complyflow-view-version', function() {
                        var version = $(this).data('version');
                        viewVersion(policyType, version);
                    });
                    
                    historyModal.on('click', '.complyflow-compare-versions', function() {
                        var version = $(this).data('version');
                        var prevVersion = $(this).data('prev-version');
                        compareVersions(policyType, prevVersion, version);
                    });
                    
                    historyModal.on('click', '.complyflow-restore-version', function() {
                        var version = $(this).data('version');
                        if (confirm('Are you sure you want to restore version ' + version + '? This will replace the current version.')) {
                            restoreVersion(policyType, version);
                        }
                    });
                } else {
                    alert('Failed to load version history: ' + (response.data && response.data.message ? response.data.message : 'Unknown error'));
                }
            },
            error: function() {
                alert('Failed to load version history.');
            }
        });
    }

    function viewVersion(policyType, version) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_get_version',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType,
                version: version
            },
            success: function(response) {
                if (response.success && response.data.content) {
                    var viewModal = $('<div class="complyflow-modal-overlay"></div>');
                    var viewContent = $('<div class="complyflow-modal-content" style="max-width: 1000px;">' +
                        '<div class="complyflow-modal-header">' +
                        '<h2>Version ' + version + ' - Preview</h2>' +
                        '<button class="complyflow-modal-close">&times;</button>' +
                        '</div>' +
                        '<div class="complyflow-modal-body">' +
                        '<iframe style="width: 100%; height: 600px; border: 1px solid #ddd; background: white;"></iframe>' +
                        '</div>' +
                        '<div class="complyflow-modal-footer">' +
                        '<button class="button button-secondary complyflow-modal-close">Close</button>' +
                        '</div>' +
                        '</div>');
                    
                    viewModal.append(viewContent);
                    $('body').append(viewModal);
                    
                    var iframe = viewContent.find('iframe')[0];
                    iframe.contentWindow.document.open();
                    iframe.contentWindow.document.write(response.data.content);
                    iframe.contentWindow.document.close();
                    
                    viewModal.fadeIn(200);
                    
                    viewModal.find('.complyflow-modal-close').on('click', function() {
                        viewModal.fadeOut(200, function() {
                            viewModal.remove();
                        });
                    });
                } else {
                    alert('Failed to load version: ' + (response.data && response.data.message ? response.data.message : 'Unknown error'));
                }
            },
            error: function() {
                alert('Failed to load version.');
            }
        });
    }

    function compareVersions(policyType, version1, version2) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_compare_versions',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType,
                version1: version1,
                version2: version2
            },
            success: function(response) {
                if (response.success && response.data.diff) {
                    var diffModal = $('<div class="complyflow-modal-overlay"></div>');
                    var diffContent = $('<div class="complyflow-modal-content" style="max-width: 1200px;">' +
                        '<div class="complyflow-modal-header">' +
                        '<h2><span class="dashicons dashicons-leftright"></span> Compare Versions</h2>' +
                        '<div style="font-size: 13px; color: #666; margin-top: 5px;">' +
                        'Version ' + version1 + ' vs Version ' + version2 +
                        '</div>' +
                        '<button class="complyflow-modal-close">&times;</button>' +
                        '</div>' +
                        '<div class="complyflow-modal-body">' +
                        '<div class="complyflow-diff-legend" style="margin-bottom: 15px; padding: 10px; background: #f5f5f5; border-radius: 4px;">' +
                        '<span style="background: #d4edda; padding: 2px 8px; margin-right: 10px; border-radius: 3px;">+ Added</span>' +
                        '<span style="background: #f8d7da; padding: 2px 8px; margin-right: 10px; border-radius: 3px;">- Removed</span>' +
                        '<span style="background: #fff3cd; padding: 2px 8px; border-radius: 3px;">~ Modified</span>' +
                        '</div>' +
                        '<div class="complyflow-diff-view" style="max-height: 600px; overflow-y: auto; font-family: monospace; font-size: 12px; border: 1px solid #ddd; padding: 15px; background: white;">' +
                        response.data.diff +
                        '</div>' +
                        '</div>' +
                        '<div class="complyflow-modal-footer">' +
                        '<button class="button button-secondary complyflow-modal-close">Close</button>' +
                        '</div>' +
                        '</div>');
                    
                    diffModal.append(diffContent);
                    $('body').append(diffModal);
                    diffModal.fadeIn(200);
                    
                    diffModal.find('.complyflow-modal-close').on('click', function() {
                        diffModal.fadeOut(200, function() {
                            diffModal.remove();
                        });
                    });
                } else {
                    alert('Failed to compare versions: ' + (response.data && response.data.message ? response.data.message : 'Unknown error'));
                }
            },
            error: function() {
                alert('Failed to compare versions.');
            }
        });
    }

    function restoreVersion(policyType, version) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_restore_version',
                nonce: '<?php echo wp_create_nonce('complyflow_generate_policy_nonce'); ?>',
                policy_type: policyType,
                version: version
            },
            success: function(response) {
                if (response.success) {
                    alert('Version ' + version + ' restored successfully!');
                    location.reload();
                } else {
                    alert('Failed to restore version: ' + (response.data && response.data.message ? response.data.message : 'Unknown error'));
                }
            },
            error: function() {
                alert('Failed to restore version.');
            }
        });
    }
});
</script>
