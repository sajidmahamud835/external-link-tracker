<?php

// Function to create a custom table for storing link tracking data
function elt_create_link_tracking_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'elt_link_tracking';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        destination varchar(255) NOT NULL,
        referrer varchar(255),
        timestamp bigint(11) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Register settings
function elt_register_settings() {
    register_setting('elt_options', 'elt_warning_message');
    register_setting('elt_options', 'elt_custom_head');
    register_setting('elt_options', 'elt_custom_body');
    register_setting('elt_options', 'elt_countdown_time');
}
add_action('admin_init', 'elt_register_settings');

// Retrieve click data with optional pagination
function elt_get_click_data($limit = 20, $offset = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'elt_link_tracking';
    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT %d OFFSET %d", $limit, $offset),
        ARRAY_A
    );
    return $results;
}

// Count total clicks for pagination
function elt_get_total_clicks() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'elt_link_tracking';
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
}

// Render the dashboard HTML with pagination
function elt_render_dashboard() {
    // Pagination setup
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $limit = 20;
    $offset = ($current_page - 1) * $limit;
    $click_data = elt_get_click_data($limit, $offset);
    $total_clicks = elt_get_total_clicks();
    $total_pages = ceil($total_clicks / $limit);

    ?>

    <div class="wrap">
        <h1>Link Tracker Dashboard</h1>

        <!-- Navigation Tabs -->
        <h2 class="nav-tab-wrapper">
            <a href="?page=elt-dashboard&tab=dashboard" class="nav-tab <?php echo !isset($_GET['tab']) || $_GET['tab'] == 'dashboard' ? 'nav-tab-active' : ''; ?>">Dashboard</a>
            <a href="?page=elt-dashboard&tab=settings" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
        </h2>

        <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'dashboard') : ?>
            <!-- Click Data Dashboard -->        
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th>Destination URL</th>
                    <th>Referrer</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($click_data)) : ?>
                    <tr>
                        <td colspan="3">No data available.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($click_data as $click) : ?>
                        <tr>
                            <td><?php echo esc_html($click['destination']); ?></td>
                            <td><?php echo esc_html($click['referrer'] ?: 'Direct'); ?></td>
                            <td><?php echo esc_html(date("Y-m-d H:i:s", $click['timestamp'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if ($total_pages > 1) : ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php
                    $pagination_args = array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'current' => $current_page,
                        'total' => $total_pages,
                    );
                    echo paginate_links($pagination_args);
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>


        <?php elseif ($_GET['tab'] == 'settings') : ?>
            <!-- Settings Form -->
            <form method="post" action="options.php">
                <?php
                settings_fields('elt_options');
                do_settings_sections('elt_options');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Warning Message</th>
                        <td><textarea name="elt_warning_message" rows="3" class="large-text"><?php echo esc_textarea(get_option('elt_warning_message', 'You are about to leave our site.')); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row">Countdown Time (seconds)</th>
                        <td><input type="number" name="elt_countdown_time" value="<?php echo esc_attr(get_option('elt_countdown_time', 30)); ?>" min="1" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Custom &lt;head&gt; Code</th>
                        <td><textarea name="elt_custom_head" rows="5" class="large-text" placeholder="Add GTM or tracking code..."><?php echo esc_textarea(get_option('elt_custom_head', '')); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row">Custom &lt;body&gt; Code</th>
                        <td><textarea name="elt_custom_body" rows="5" class="large-text" placeholder="Add tracking or ad scripts..."><?php echo esc_textarea(get_option('elt_custom_body', '')); ?></textarea></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

// Modify external links in content
function elt_modify_external_links($content) {
    // Get only the site's hostname (e.g., "yoursite.com")
    $site_host = parse_url(get_home_url(), PHP_URL_HOST);

    // Use regular expression to modify external links, excluding internal links by hostname
    $content = preg_replace_callback('/<a\s+href="(https?:\/\/(?!' . preg_quote($site_host, '/') . ')[^"]+)"/i', function ($matches) {
        // Encode the URL for safe redirection
        $encoded_url = urlencode($matches[1]);
        return '<a href="' . esc_url(home_url("/link?destination={$encoded_url}")) . '"';
    }, $content);

    return $content;
}
add_filter('the_content', 'elt_modify_external_links');


// Handle redirect and display warning page
function elt_link_redirect() {
    if (isset($_GET['destination'])) {
        include ELT_PLUGIN_DIR . 'templates/warning-page.php';
        exit;
    }
}
add_action('template_redirect', 'elt_link_redirect');

?>
