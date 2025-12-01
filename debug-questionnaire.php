<?php
/**
 * Debug script for questionnaire data
 *
 * Usage: Load in browser as: /wp-content/plugins/ShahiComplyFlow/debug-questionnaire.php
 */

// Load WordPress
require_once dirname(dirname(dirname(__DIR__))) . '/wp-load.php';

if (!current_user_can('manage_options')) {
    die('Access denied');
}

echo '<h1>ComplyFlow Questionnaire Debug</h1>';
echo '<style>body { font-family: monospace; padding: 20px; } pre { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; } h2 { color: #2271b1; margin-top: 30px; }</style>';

// Get saved answers
$saved_answers = get_option('complyflow_questionnaire_answers', []);

echo '<h2>Saved Answers in Database</h2>';
echo '<pre>';
print_r($saved_answers);
echo '</pre>';

echo '<p><strong>Answer Count:</strong> ' . count($saved_answers) . '</p>';

// Load questionnaire class
require_once COMPLYFLOW_PATH . 'includes/Modules/Documents/Questionnaire.php';
$questionnaire = new \ComplyFlow\Modules\Documents\Questionnaire();

// Get all questions
$questions = $questionnaire->get_questions();

echo '<h2>Required Questions</h2>';
echo '<table border="1" cellpadding="10" style="border-collapse: collapse; width: 100%;">';
echo '<tr><th>Question ID</th><th>Type</th><th>Required</th><th>Has Answer?</th><th>Answer Value</th></tr>';

foreach ($questions as $question) {
    if ($question['required']) {
        $has_answer = isset($saved_answers[$question['id']]);
        $answer_value = $has_answer ? $saved_answers[$question['id']] : 'MISSING';
        $is_empty = empty($saved_answers[$question['id']]);
        
        $row_style = $is_empty ? 'background-color: #ffcccc;' : 'background-color: #ccffcc;';
        
        echo '<tr style="' . $row_style . '">';
        echo '<td><strong>' . esc_html($question['id']) . '</strong></td>';
        echo '<td>' . esc_html($question['type']) . '</td>';
        echo '<td>YES</td>';
        echo '<td>' . ($has_answer ? '✓' : '✗') . '</td>';
        echo '<td><pre>' . print_r($answer_value, true) . '</pre></td>';
        echo '</tr>';
    }
}

echo '</table>';

// Check if complete
$is_complete = $questionnaire->is_complete();
$completion = $questionnaire->get_completion_percentage();

echo '<h2>Validation Status</h2>';
echo '<p><strong>Is Complete:</strong> <span style="font-size: 20px; color: ' . ($is_complete ? 'green' : 'red') . ';">' . ($is_complete ? '✓ YES' : '✗ NO') . '</span></p>';
echo '<p><strong>Completion Percentage:</strong> ' . $completion . '%</p>';

// Find which required fields are missing
echo '<h2>Missing Required Fields</h2>';
$missing = [];
foreach ($questions as $question) {
    if ($question['required'] && empty($saved_answers[$question['id']])) {
        $missing[] = $question['id'] . ' (' . $question['text'] . ')';
    }
}

if (empty($missing)) {
    echo '<p style="color: green;">✓ All required fields have answers!</p>';
} else {
    echo '<ul style="color: red;">';
    foreach ($missing as $field) {
        echo '<li>' . esc_html($field) . '</li>';
    }
    echo '</ul>';
}

echo '<h2>All Questions (Required and Optional)</h2>';
echo '<table border="1" cellpadding="10" style="border-collapse: collapse; width: 100%;">';
echo '<tr><th>Section</th><th>Question ID</th><th>Type</th><th>Required</th><th>Has Answer?</th></tr>';

$current_section = '';
foreach ($questions as $question) {
    if ($current_section !== $question['section']) {
        $current_section = $question['section'];
        echo '<tr style="background-color: #e0e0e0; font-weight: bold;"><td colspan="5">' . esc_html(strtoupper($current_section)) . '</td></tr>';
    }
    
    $has_answer = isset($saved_answers[$question['id']]);
    $row_style = '';
    if ($question['required']) {
        $row_style = empty($saved_answers[$question['id']]) ? 'background-color: #ffcccc;' : 'background-color: #ccffcc;';
    }
    
    echo '<tr style="' . $row_style . '">';
    echo '<td>' . esc_html($question['section']) . '</td>';
    echo '<td><strong>' . esc_html($question['id']) . '</strong></td>';
    echo '<td>' . esc_html($question['type']) . '</td>';
    echo '<td>' . ($question['required'] ? '<strong>YES</strong>' : 'no') . '</td>';
    echo '<td>' . ($has_answer ? '✓' : '✗') . '</td>';
    echo '</tr>';
}

echo '</table>';
