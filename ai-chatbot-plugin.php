<?php
/*
Plugin Name: AI Chatbot Plugin
Description: A plugin to integrate an AI-powered chatbot into your WordPress website.
Version: 1.0
Author: Hemal Mondal
*/

// Add admin menu for chatbot settings
function chatbot_admin_menu() {
    add_menu_page(
        'Chatbot Settings', // Page title
        'Chatbot',          // Menu title
        'manage_options',   // Capability
        'chatbot-settings', // Menu slug
        'chatbot_settings_page', // Callback function
        'dashicons-format-chat', // Icon
        6                    // Position
    );
}
add_action('admin_menu', 'chatbot_admin_menu');

// Create the settings page
function chatbot_settings_page() {
    // Check if the API key is being saved
    if (isset($_POST['chatbot_api_key'])) {
        $api_key = sanitize_text_field($_POST['chatbot_api_key']);

        // Validate the API key
        $is_valid = chatbot_validate_api_key($api_key);

        if ($is_valid) {
            update_option('chatbot_api_key', $api_key);
            echo '<div class="notice notice-success"><p>API connected successfully!</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Invalid API key. Please try again.</p></div>';
        }
    }

    // Get the saved API key
    $api_key = get_option('chatbot_api_key', '');

    ?>
    <div class="wrap">
        <h1>Chatbot Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="chatbot_api_key">API Key</label></th>
                    <td>
                        <input name="chatbot_api_key" type="text" id="chatbot_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text">
                        <p class="description">Enter your API key to connect the chatbot to your backend.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save API Key'); ?>
        </form>
    </div>
    <?php
}

// Validate the API key
function chatbot_validate_api_key($api_key) {
    // Replace this with your actual API validation logic
    $response = wp_remote_get('https://your-api.com/validate', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key
        )
    ));

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    return isset($data['valid']) && $data['valid'] === true;
}

// Crawl the website
function chatbot_crawl_website() {
    $site_url = get_site_url();
    $response = wp_remote_get($site_url . '/wp-json/wp/v2/posts');
    if (is_array($response)) {
        $posts = json_decode($response['body'], true);
        foreach ($posts as $post) {
            // Process each post
            $title = $post['title']['rendered'];
            $content = $post['content']['rendered'];
            // Save data for training
        }
    }
}
add_action('init', 'chatbot_crawl_website');

// Enqueue scripts and styles
function chatbot_enqueue_scripts() {
    // Enqueue CSS
    wp_enqueue_style('chatbot-style', plugins_url('css/chatbot.css', __FILE__));

    // Enqueue JavaScript
    wp_enqueue_script('chatbot-script', plugins_url('js/chatbot.js', __FILE__), array('jquery'), null, true);

    // Localize script to pass data from PHP to JavaScript
    wp_localize_script('chatbot-script', 'chatbotData', array(
        'apiUrl' => 'https://your-api.com/chatbot', // Replace with your API endpoint
        'nonce' => wp_create_nonce('chatbot_nonce') // Security nonce
    ));
}
add_action('wp_enqueue_scripts', 'chatbot_enqueue_scripts');

// Add chatbot HTML to the website footer
function chatbot_display() {
    include plugin_dir_path(__FILE__) . 'chatbot.php';
}
add_action('wp_footer', 'chatbot_display');

// Shortcode to display chatbot
function chatbot_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'chatbot.php';
    return ob_get_clean();
}
add_shortcode('chatbot', 'chatbot_shortcode');