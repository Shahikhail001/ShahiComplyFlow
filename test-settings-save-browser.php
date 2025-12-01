<!DOCTYPE html>
<html>
<head>
    <title>ComplyFlow Settings Save Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>ComplyFlow Settings Save Test</h1>
    <button id="test-save">Test Settings Save</button>
    <div id="result"></div>

    <script>
    jQuery(document).ready(function($) {
        $('#test-save').on('click', function() {
            var $result = $('#result');
            $result.html('<p>Testing...</p>');
            
            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: {
                    action: 'complyflow_save_settings',
                    nonce: '<?php echo wp_create_nonce("complyflow_admin_nonce"); ?>',
                    settings: 'complyflow_settings[test_field]=test_value'
                },
                success: function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        $result.html('<p style="color: green;">✓ SUCCESS: ' + response.data.message + '</p>');
                    } else {
                        $result.html('<p style="color: red;">✗ FAILED: ' + (response.data.message || 'Unknown error') + '</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $result.html('<p style="color: red;">✗ AJAX ERROR: ' + error + '</p>');
                }
            });
        });
    });
    </script>

<?php
require_once '../../../wp-load.php';
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    echo '<p style="color: red;">You must be logged in as an administrator to test this.</p>';
}
?>
</body>
</html>
