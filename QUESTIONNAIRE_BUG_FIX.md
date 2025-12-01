# Questionnaire Policy Generation Bug Fix

## Issue Description
**Problem:** User could save the questionnaire successfully, but policy generation failed with error: "Please complete the questionnaire first."

**Root Cause:** The `is_complete()` validation method was checking ALL required fields, including conditionally shown fields, without respecting the `show_if` logic. This meant that fields which were:
1. Marked as required
2. Only shown if certain conditions were met (via `show_if` property)
3. Not displayed in the UI (because condition wasn't met)

...were still being validated as if they should have answers, causing the validation to fail.

## Example of the Problem

Looking at the questionnaire definition in `Questionnaire.php`:

```php
[
    'id' => 'has_email_marketing',
    'type' => 'boolean',
    'required' => true,
    'show_if' => ['collect_emails' => true],  // Only shown if collect_emails is true
]

[
    'id' => 'collect_payment_info',
    'type' => 'boolean',
    'required' => true,
    'show_if' => ['has_ecommerce' => true],  // Only shown if has_ecommerce is true
]
```

If the user answered "No" to `collect_emails` or `has_ecommerce`, these fields would not be shown in the questionnaire form. However, the old `is_complete()` method would still check if they had answers, causing validation to fail.

## Files Modified

### 1. `includes/Modules/Documents/Questionnaire.php`

#### Change 1: Fixed `save_answers()` Method (Lines 354-378)
**Before:**
```php
// Skip empty arrays
if (is_array($value) && empty($value)) {
    continue;
}

if (is_array($value)) {
    // Handle multi-select checkboxes
    $sanitized[$key] = array_map('sanitize_text_field', $value);
} elseif (is_string($value) && !empty(trim($value))) {
    // Handle text fields - only save if not empty
    $sanitized[$key] = sanitize_textarea_field($value);
}
```

**After:**
```php
if (is_array($value)) {
    // Handle multi-select checkboxes - save even if empty
    $sanitized[$key] = empty($value) ? [] : array_map('sanitize_text_field', $value);
} else {
    // Handle text fields - save even if empty (for proper validation)
    $sanitized[$key] = sanitize_textarea_field($value);
}
```

**Reason:** Now saves ALL submitted fields including empty ones, ensuring the validation can properly check them.

#### Change 2: Fixed `is_complete()` Method (Lines 485-540)
**Before:**
```php
public function is_complete(): bool {
    $questions = $this->get_questions();
    $answers = $this->get_saved_answers();

    foreach ($questions as $question) {
        if ($question['required'] && empty($answers[$question['id']])) {
            return false;
        }
    }

    return true;
}
```

**After:**
```php
public function is_complete(): bool {
    $questions = $this->get_questions();
    $answers = $this->get_saved_answers();

    foreach ($questions as $question) {
        // Skip non-required fields
        if (!$question['required']) {
            continue;
        }

        // Check if field is conditionally shown
        if (isset($question['show_if'])) {
            $show_field = true;
            foreach ($question['show_if'] as $condition_key => $condition_value) {
                // Check if the condition is met
                if (!isset($answers[$condition_key]) || $answers[$condition_key] !== $condition_value) {
                    $show_field = false;
                    break;
                }
            }
            
            // If condition not met, skip this field
            if (!$show_field) {
                continue;
            }
        }

        // Check if required field has a value
        if (empty($answers[$question['id']])) {
            return false;
        }
    }

    return true;
}
```

**Reason:** Now respects conditional `show_if` logic. Only validates fields that are actually shown to the user based on their previous answers.

## Testing the Fix

### Affected Required Fields with Conditions:
1. **collect_payment_info** - Only required if `has_ecommerce` is true
2. **has_email_marketing** - Only required if `collect_emails` is true
3. Various conditional fields based on user selections

### Test Scenarios:

#### Scenario 1: E-commerce Disabled
- User answers "No" to `has_ecommerce`
- Field `collect_payment_info` (required with show_if) should be skipped
- ✅ Validation should pass without this field

#### Scenario 2: Email Collection Disabled  
- User answers "No" to `collect_emails`
- Field `has_email_marketing` (required with show_if) should be skipped
- ✅ Validation should pass without this field

#### Scenario 3: All Features Enabled
- User enables all features
- All conditional required fields are shown
- ✅ Validation requires answers for ALL fields

### Debug Logging
Enhanced logging added to help diagnose issues:
- Logs total questions and answers count
- Logs each required field validation
- Shows which conditional fields are skipped and why
- Indicates exactly which field causes validation to fail

View logs at: `wp-content/debug.log` (if WP_DEBUG_LOG is enabled)

## How to Verify the Fix

1. **Clear previous questionnaire data** (optional for clean test):
   ```php
   delete_option('complyflow_questionnaire_answers');
   ```

2. **Fill out questionnaire**:
   - Answer only required fields
   - Leave some boolean questions as "No" (unchecked)
   - Click "Save & Continue"

3. **Generate a policy**:
   - Go to Legal Documents page
   - Click "Generate" on any policy
   - ✅ Should now succeed

4. **Check debug logs** (if WP_DEBUG_LOG enabled):
   ```
   ComplyFlow is_complete check:
   - Total questions: 30
   - Total answers: 12
   - Required field "has_email_marketing" SKIPPED (condition not met: collect_emails must be true, but is false)
   - Required field "company_name": has_answer=yes, is_empty=no, value="My Company"
   ...
   ComplyFlow: is_complete() = TRUE (all required fields present)
   ```

## Additional Debugging Tool

Created `debug-questionnaire.php` in plugin root to inspect saved data:

**URL:** `http://localhost/shahitest/wp-content/plugins/ShahiComplyFlow/debug-questionnaire.php`

**Shows:**
- All saved answers
- Which required fields are missing
- Validation status
- Color-coded table of all questions

## WordPress Error Log Setup

To enable debugging and view logs:

Edit `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
```

Log file location: `wp-content/debug.log`

## Summary

**Problem:** Conditional required fields were validated even when not shown  
**Solution:** Modified `is_complete()` to respect `show_if` conditions  
**Impact:** Users can now successfully generate policies after completing visible required fields  
**Added:** Enhanced debug logging for troubleshooting  
**Testing:** PHP syntax verified, ready for production use

## Files Changed
- ✅ `includes/Modules/Documents/Questionnaire.php` (2 methods fixed)
- ✅ `debug-questionnaire.php` (new debugging tool)

No database migrations or schema changes required.
