<?php
/**
 * Plugin Name: Remote Media Replacer
 * Description: Replace local media URLs with remote ones if available. Falls back to local if remote image doesn't exist.
 * Version: 1.0
 * Author: Your Name
 */

// Register setting in WP Admin
add_action('admin_menu', function () {
    add_options_page('Remote Media Replacer', 'Remote Media Replacer', 'manage_options', 'remote-media-replacer', 'rmr_settings_page');
});

add_action('admin_init', function () {
    register_setting('rmr_settings', 'rmr_remote_upload_url');
});

// Settings page HTML
function rmr_settings_page() {
    ?>
    <div class="wrap">
        <h1>Remote Media Replacer Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('rmr_settings');
            do_settings_sections('rmr_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Remote Uploads Base URL</th>
                    <td>
                        <input type="url" name="rmr_remote_upload_url" value="<?php echo esc_attr(get_option('rmr_remote_upload_url')); ?>" style="width: 400px;" placeholder="https://example.com/wp-content/uploads" />
                        <p class="description">Set the base URL of your main site's uploads directory.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Get remote upload base URL
function rmr_get_main_upload_url() {
    $url = trim(get_option('rmr_remote_upload_url'));
    return $url ? rtrim($url, '/') : false;
}

// Check if we're on local environment
function rmr_is_local() {
    return strpos(home_url(), 'localhost') !== false || strpos(home_url(), '127.0.0.1') !== false;
}

// Start output buffer for replacement
add_action('template_redirect', function () {
    if (!rmr_is_local() || !rmr_get_main_upload_url()) return;
    ob_start('rmr_replace_final_output');
});

// Replace <img> src with remote version if remote image exists
function rmr_replace_final_output($html) {
    $remote = rmr_get_main_upload_url();
    if (!$remote) return $html;

    $local = content_url('uploads');

    return preg_replace_callback('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', function ($matches) use ($remote, $local) {
        $originalTag = $matches[0];
        $src = $matches[1];

        // Only modify if it's a local upload image
        if (strpos($src, $local) === false) return $originalTag;

        $remoteSrc = str_replace($local, $remote, $src);

        // Use remote only if image exists
        if (rmr_remote_image_exists($remoteSrc)) {
            return str_replace($src, $remoteSrc, $originalTag);
        }

        return $originalTag; // fallback to local
    }, $html);
}

// Check if image exists on remote server using HEAD request
function rmr_remote_image_exists($url) {
    $response = wp_remote_head($url, ['timeout' => 2]);
    return is_array($response) && !is_wp_error($response) && intval(wp_remote_retrieve_response_code($response)) === 200;
}
