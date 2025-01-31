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
        update_option('chatbot_api_key', $api_key);
        echo '<div class="notice notice-success"><p>API key saved successfully!</p></div>';
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