<?php
/*
Plugin Name: AI Chatbot Plugin
Description: A plugin to integrate an AI-powered chatbot into your WordPress website.
Version: 1.0
Author: Your Name
*/

// Hook to display notice on plugin activation
register_activation_hook(__FILE__, 'chatbot_activation_notice');

function chatbot_activation_notice() {
    // Set a transient to show the notice
    set_transient('chatbot_activation_notice', true, 5);
}

// Display the notice
function chatbot_admin_notice() {
    if (get_transient('chatbot_activation_notice')) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Welcome to the AI Chatbot Plugin! Please <a href="<?php echo admin_url('admin.php?page=chatbot-settings'); ?>">enter your API key</a> to get started.</p>
        </div>
        <?php
        delete_transient('chatbot_activation_notice');
    }
}
add_action('admin_notices', 'chatbot_admin_notice');

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
    ?>
    <div class="wrap">
        <h1>Chatbot Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('chatbot_options_group');
            do_settings_sections('chatbot-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Initialize settings
function chatbot_settings_init() {
    register_setting('chatbot_options_group', 'chatbot_options', 'chatbot_options_validate');

    add_settings_section('chatbot_main_section', 'Main Settings', 'chatbot_section_text', 'chatbot-settings');

    add_settings_field('chatbot_api_key', 'API Key', 'chatbot_api_key_input', 'chatbot-settings', 'chatbot_main_section');
}
add_action('admin_init', 'chatbot_settings_init');

// Section text
function chatbot_section_text() {
    echo '<p>Enter your API key to connect the chatbot to your backend.</p>';
}

// API key input field
function chatbot_api_key_input() {
    $options = get_option('chatbot_options');
    echo "<input id='chatbot_api_key' name='chatbot_options[api_key]' size='40' type='text' value='{$options['api_key']}' />";
}

// Validate input
function chatbot_options_validate($input) {
    $newinput['api_key'] = trim($input['api_key']);
    return $newinput;
}

// Shortcode to display chatbot
function chatbot_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'chatbot.php';
    return ob_get_clean();
}
add_shortcode('chatbot', 'chatbot_shortcode');