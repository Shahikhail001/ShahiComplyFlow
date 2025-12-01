<?php
/**
 * Quick database check for questionnaire answers
 */

// Load WordPress
require_once dirname(dirname(dirname(__DIR__))) . '/wp-load.php';

if (!current_user_can('manage_options')) {
    die('Access denied');
}

header('Content-Type: text/plain');

echo "=== COMPLYFLOW QUESTIONNAIRE DATABASE CHECK ===\n\n";

// Get the saved answers
$answers = get_option('complyflow_questionnaire_answers', []);

echo "Total saved answers: " . count($answers) . "\n\n";
echo "Saved data:\n";
print_r($answers);

echo "\n\n=== CHECKING REQUIRED FIELDS ===\n\n";

// Load questionnaire
require_once COMPLYFLOW_PATH . 'includes/Modules/Documents/Questionnaire.php';
$questionnaire = new \ComplyFlow\Modules\Documents\Questionnaire();
$questions = $questionnaire->get_questions();

$required_fields = [];
foreach ($questions as $q) {
    if ($q['required']) {
        $required_fields[] = $q['id'];
    }
}

echo "Total required fields: " . count($required_fields) . "\n";
echo "Required fields:\n";
foreach ($required_fields as $field) {
    $has_answer = isset($answers[$field]);
    $is_empty = empty($answers[$field]);
    $status = ($has_answer && !$is_empty) ? '✓' : '✗';
    $value = $has_answer ? json_encode($answers[$field]) : 'NOT SET';
    
    echo sprintf(
        "  %s %-30s %s\n",
        $status,
        $field,
        $value
    );
}

echo "\n\n=== VALIDATION RESULT ===\n\n";
$is_complete = $questionnaire->is_complete();
echo "is_complete(): " . ($is_complete ? "TRUE ✓" : "FALSE ✗") . "\n";
echo "completion_percentage(): " . $questionnaire->get_completion_percentage() . "%\n";
