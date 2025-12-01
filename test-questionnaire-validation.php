<?php
/**
 * Test Questionnaire Validation
 * 
 * This script tests the is_complete() logic with sample data
 */

// Load WordPress
require_once dirname(dirname(dirname(__DIR__))) . '/wp-load.php';

if (!current_user_can('manage_options')) {
    die('Access denied');
}

// Load classes
require_once COMPLYFLOW_PATH . 'includes/Modules/Documents/Questionnaire.php';
$questionnaire = new \ComplyFlow\Modules\Documents\Questionnaire();

echo '<h1>ComplyFlow Questionnaire Validation Test</h1>';
echo '<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .test { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .test h2 { color: #2271b1; margin-top: 0; }
    .pass { color: #00a32a; font-weight: bold; }
    .fail { color: #d63638; font-weight: bold; }
    pre { background: #f0f0f1; padding: 15px; border-left: 4px solid #2271b1; overflow-x: auto; }
    .result { padding: 15px; margin: 10px 0; border-radius: 4px; }
    .result.pass { background: #d7f0db; border: 2px solid #00a32a; }
    .result.fail { background: #f8d7da; border: 2px solid #d63638; }
</style>';

// Test Case 1: Minimal required fields only
echo '<div class="test">';
echo '<h2>Test Case 1: Minimal Required Fields</h2>';
echo '<p>Testing with only the basic required fields filled, no e-commerce, no email collection.</p>';

$test1_data = [
    'company_name' => 'Test Company',
    'contact_email' => 'test@example.com',
    'target_countries' => ['US'],
    'has_ecommerce' => false,           // No e-commerce
    'collect_emails' => false,          // No email collection (so has_email_marketing not required)
    'has_user_accounts' => false,
    'has_analytics' => false,
    'has_advertising' => false,
    'has_social_sharing' => false,
    'allow_data_export' => true,
    'allow_data_deletion' => true,
    'data_retention_period' => 24,
    'allows_children' => false,
];

update_option('complyflow_questionnaire_answers', $test1_data);
$result1 = $questionnaire->is_complete();

echo '<pre>' . print_r($test1_data, true) . '</pre>';
echo '<div class="result ' . ($result1 ? 'pass' : 'fail') . '">';
echo '<strong>Result:</strong> ' . ($result1 ? '<span class="pass">✓ PASS</span>' : '<span class="fail">✗ FAIL</span>');
echo '<br><strong>Expected:</strong> PASS (all visible required fields filled)';
echo '</div>';
echo '</div>';

// Test Case 2: E-commerce enabled
echo '<div class="test">';
echo '<h2>Test Case 2: E-commerce Enabled (Missing conditional field)</h2>';
echo '<p>Testing with e-commerce enabled but missing collect_payment_info (should fail).</p>';

$test2_data = [
    'company_name' => 'Test Company',
    'contact_email' => 'test@example.com',
    'target_countries' => ['US'],
    'has_ecommerce' => true,            // E-commerce enabled
    // 'collect_payment_info' => missing! // This is required when has_ecommerce is true
    'collect_emails' => false,
    'has_user_accounts' => false,
    'has_analytics' => false,
    'has_advertising' => false,
    'has_social_sharing' => false,
    'allow_data_export' => true,
    'allow_data_deletion' => true,
    'data_retention_period' => 24,
    'allows_children' => false,
];

update_option('complyflow_questionnaire_answers', $test2_data);
$result2 = $questionnaire->is_complete();

echo '<pre>' . print_r($test2_data, true) . '</pre>';
echo '<div class="result ' . ($result2 ? 'fail' : 'pass') . '">';
echo '<strong>Result:</strong> ' . ($result2 ? '<span class="fail">✗ FAIL</span>' : '<span class="pass">✓ PASS</span>');
echo '<br><strong>Expected:</strong> FAIL (missing required conditional field)';
echo '<br><strong>Note:</strong> Result should be FAIL because collect_payment_info is required when has_ecommerce=true';
echo '</div>';
echo '</div>';

// Test Case 3: E-commerce enabled with all required fields
echo '<div class="test">';
echo '<h2>Test Case 3: E-commerce Enabled (All fields present)</h2>';
echo '<p>Testing with e-commerce enabled and all conditional fields filled.</p>';

$test3_data = [
    'company_name' => 'Test Company',
    'contact_email' => 'test@example.com',
    'target_countries' => ['US'],
    'has_ecommerce' => true,            // E-commerce enabled
    'collect_payment_info' => true,     // Required conditional field present
    'collect_emails' => false,
    'has_user_accounts' => false,
    'has_analytics' => false,
    'has_advertising' => false,
    'has_social_sharing' => false,
    'allow_data_export' => true,
    'allow_data_deletion' => true,
    'data_retention_period' => 24,
    'allows_children' => false,
];

update_option('complyflow_questionnaire_answers', $test3_data);
$result3 = $questionnaire->is_complete();

echo '<pre>' . print_r($test3_data, true) . '</pre>';
echo '<div class="result ' . ($result3 ? 'pass' : 'fail') . '">';
echo '<strong>Result:</strong> ' . ($result3 ? '<span class="pass">✓ PASS</span>' : '<span class="fail">✗ FAIL</span>');
echo '<br><strong>Expected:</strong> PASS (all required fields including conditional ones are filled)';
echo '</div>';
echo '</div>';

// Test Case 4: Email collection enabled
echo '<div class="test">';
echo '<h2>Test Case 4: Email Collection Enabled (Missing has_email_marketing)</h2>';
echo '<p>Testing with email collection enabled but missing has_email_marketing.</p>';

$test4_data = [
    'company_name' => 'Test Company',
    'contact_email' => 'test@example.com',
    'target_countries' => ['US'],
    'has_ecommerce' => false,
    'collect_emails' => true,           // Email collection enabled
    // 'has_email_marketing' => missing! // This is required when collect_emails is true
    'has_user_accounts' => false,
    'has_analytics' => false,
    'has_advertising' => false,
    'has_social_sharing' => false,
    'allow_data_export' => true,
    'allow_data_deletion' => true,
    'data_retention_period' => 24,
    'allows_children' => false,
];

update_option('complyflow_questionnaire_answers', $test4_data);
$result4 = $questionnaire->is_complete();

echo '<pre>' . print_r($test4_data, true) . '</pre>';
echo '<div class="result ' . ($result4 ? 'fail' : 'pass') . '">';
echo '<strong>Result:</strong> ' . ($result4 ? '<span class="fail">✗ FAIL</span>' : '<span class="pass">✓ PASS</span>');
echo '<br><strong>Expected:</strong> FAIL (missing required conditional field)';
echo '<br><strong>Note:</strong> Result should be FAIL because has_email_marketing is required when collect_emails=true';
echo '</div>';
echo '</div>';

// Restore actual saved data
$actual_data = get_option('complyflow_questionnaire_answers', []);
echo '<div class="test">';
echo '<h2>Current Saved Questionnaire Data</h2>';
echo '<p>This is the actual data saved in your database.</p>';
echo '<pre>' . print_r($actual_data, true) . '</pre>';

update_option('complyflow_questionnaire_answers', $actual_data);
$actual_result = $questionnaire->is_complete();

echo '<div class="result ' . ($actual_result ? 'pass' : 'fail') . '">';
echo '<strong>Current Validation Result:</strong> ' . ($actual_result ? '<span class="pass">✓ COMPLETE</span>' : '<span class="fail">✗ INCOMPLETE</span>');
echo '<br><strong>Completion Percentage:</strong> ' . $questionnaire->get_completion_percentage() . '%';
echo '</div>';
echo '</div>';

echo '<div class="test">';
echo '<h2>Summary</h2>';
echo '<ul>';
echo '<li>Test 1 (minimal): ' . ($result1 ? '<span class="pass">✓ PASS</span>' : '<span class="fail">✗ FAIL</span>') . '</li>';
echo '<li>Test 2 (e-commerce missing field): ' . (!$result2 ? '<span class="pass">✓ PASS</span>' : '<span class="fail">✗ FAIL</span>') . ' (should fail)</li>';
echo '<li>Test 3 (e-commerce complete): ' . ($result3 ? '<span class="pass">✓ PASS</span>' : '<span class="fail">✗ FAIL</span>') . '</li>';
echo '<li>Test 4 (email missing field): ' . (!$result4 ? '<span class="pass">✓ PASS</span>' : '<span class="fail">✗ FAIL</span>') . ' (should fail)</li>';
echo '<li>Current data validation: ' . ($actual_result ? '<span class="pass">✓ COMPLETE</span>' : '<span class="fail">✗ INCOMPLETE</span>') . '</li>';
echo '</ul>';
echo '</div>';

echo '<p><a href="' . admin_url('admin.php?page=complyflow-documents') . '">← Back to Legal Documents</a></p>';
