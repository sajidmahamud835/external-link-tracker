<?php
global $wpdb;
$destination = isset($_GET['destination']) ? esc_url_raw(urldecode($_GET['destination'])) : '';
$referrer = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : 'direct';
$timestamp = time();

// Insert data into the tracking table
if ($destination) {
    $wpdb->insert(
        $wpdb->prefix . 'elt_link_tracking',
        array(
            'destination' => $destination,
            'referrer' => $referrer,
            'timestamp' => $timestamp,
        )
    );
}

// Get custom settings
$warning_message = get_option('elt_warning_message', 'You are about to leave our site.');
$countdown_time = get_option('elt_countdown_time', 30);
$custom_head = get_option('elt_custom_head', '');
$custom_body = get_option('elt_custom_body', '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>External Link Warning</title>
    <?php echo esc_html($custom_head); // Custom head code ?>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            max-width: 400px;
        }
        h2 {
            color: #333;
            font-size: 1.6em;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            font-size: 1.1em;
            margin-bottom: 20px;
        }
        #countdown {
            font-weight: bold;
            color: #e74c3c;
        }
        .progress-bar {
            width: 100%;
            height: 15px;
            background: #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 15px;
        }
        .progress {
            height: 100%;
            width: 0;
            background: #4caf50;
            transition: width <?php echo esc_attr($countdown_time); ?>s linear;
        }
        .redirect-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1em;
            transition: background-color 0.3s;
        }
        .redirect-btn:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        let countdown = <?php echo esc_attr($countdown_time); ?>;
        function updateCountdown() {
            if (countdown > 0) {
                document.getElementById("countdown").innerText = countdown;
                countdown--;
                setTimeout(updateCountdown, 1000);
            } else {
                window.location.href = "<?php echo esc_url($destination); ?>";
            }
        }
        window.onload = function() {
            document.getElementById("progress").style.width = "100%";
            updateCountdown();
        };
    </script>
</head>
<body>
    <div class="container">
        <h2><?php echo esc_html($warning_message); ?></h2>
        <p>Redirecting in <span id="countdown"><?php echo esc_html($countdown_time); ?></span> seconds...</p>
        <div class="progress-bar">
            <div id="progress" class="progress"></div>
        </div>
        <a href="<?php echo esc_url($destination); ?>" class="redirect-btn">Continue Now</a>
    </div>
    <?php echo esc_html($custom_body); // Custom body code ?>
</body>
</html>
