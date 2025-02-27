<?php

/**
 * AI Travel Tool Settings
 *
 * @package AITRAVELTOOL
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) exit;

/**
 * AITRAVELTOOL_Settings Class
 */
class AITRAVELTOOL_Settings
{

    /**
     * Constructor
     */
    public function __construct()
    {
        // Add admin menu.
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Register settings.
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add admin menu.
     */
    public function add_admin_menu()
    {
        add_menu_page(
            __('AI Travel Tool', 'ai-travel-tool-sample'),
            __('AI Travel Tool', 'ai-travel-tool-sample'),
            'manage_options',
            'ai-travel-tool',
            array($this, 'settings_page'),
            'dashicons-airplane',
            25
        );
    }

    /**
     * Register settings.
     */
    public function register_settings()
    {
        // Register API Keys settings section.
        add_settings_section(
            'aitraveltool_api_keys_section',
            __('API Keys', 'ai-travel-tool-sample'),
            array($this, 'api_keys_section_callback'),
            'ai-travel-tool'
        );

        // Register API Keys fields.
        // OpenAI API Key.
        register_setting(
            'aitraveltool_settings',
            'aitraveltool_openai_api_key',
            array(
                'type'              => 'string',
                'sanitize_callback' => array($this, 'sanitize_api_key'),
                'default'           => '',
            )
        );
        add_settings_field(
            'aitraveltool_openai_api_key',
            __('OpenAI API Key', 'ai-travel-tool-sample'),
            array($this, 'openai_api_key_callback'),
            'ai-travel-tool',
            'aitraveltool_api_keys_section'
        );

        // OpenRouter API Key.
        register_setting(
            'aitraveltool_settings',
            'aitraveltool_openrouter_api_key',
            array(
                'type'              => 'string',
                'sanitize_callback' => array($this, 'sanitize_api_key'),
                'default'           => '',
            )
        );
        add_settings_field(
            'aitraveltool_openrouter_api_key',
            __('OpenRouter API Key', 'ai-travel-tool-sample'),
            array($this, 'openrouter_api_key_callback'),
            'ai-travel-tool',
            'aitraveltool_api_keys_section'
        );

        // Groq API Key.
        register_setting(
            'aitraveltool_settings',
            'aitraveltool_groq_api_key',
            array(
                'type'              => 'string',
                'sanitize_callback' => array($this, 'sanitize_api_key'),
                'default'           => '',
            )
        );
        add_settings_field(
            'aitraveltool_groq_api_key',
            __('Groq API Key', 'ai-travel-tool-sample'),
            array($this, 'groq_api_key_callback'),
            'ai-travel-tool',
            'aitraveltool_api_keys_section'
        );

        // Register AI Models settings section.
        add_settings_section(
            'aitraveltool_ai_models_section',
            __('AI Models', 'ai-travel-tool-sample'),
            array($this, 'ai_models_section_callback'),
            'ai-travel-tool'
        );

        // Register OpenAI Model field.
        register_setting(
            'aitraveltool_settings',
            'aitraveltool_openai_model',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'gpt-4o-mini',
            )
        );
        add_settings_field(
            'aitraveltool_openai_model',
            __('OpenAI Model', 'ai-travel-tool-sample'),
            array($this, 'openai_model_callback'),
            'ai-travel-tool',
            'aitraveltool_ai_models_section'
        );

        // Register OpenRouter Model field.
        register_setting(
            'aitraveltool_settings',
            'aitraveltool_openrouter_model',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'meta-llama/llama-3.3-70b-instruct:free',
            )
        );
        add_settings_field(
            'aitraveltool_openrouter_model',
            __('OpenRouter Model', 'ai-travel-tool-sample'),
            array($this, 'openrouter_model_callback'),
            'ai-travel-tool',
            'aitraveltool_ai_models_section'
        );

        // Register Groq Model field.
        register_setting(
            'aitraveltool_settings',
            'aitraveltool_groq_model',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'llama-3.3-70b-versatile',
            )
        );
        add_settings_field(
            'aitraveltool_groq_model',
            __('Groq Model', 'ai-travel-tool-sample'),
            array($this, 'groq_model_callback'),
            'ai-travel-tool',
            'aitraveltool_ai_models_section'
        );

        // Register Prompt Template settings section.
        add_settings_section(
            'aitraveltool_prompt_section',
            __('Prompt Template', 'ai-travel-tool-sample'),
            array($this, 'prompt_section_callback'),
            'ai-travel-tool'
        );

        // Register Prompt Template field.
        register_setting(
            'aitraveltool_settings',
            'aitraveltool_prompt_template',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default'           => $this->get_default_prompt_template(),
            )
        );
        add_settings_field(
            'aitraveltool_prompt_template',
            __('Prompt Template', 'ai-travel-tool-sample'),
            array($this, 'prompt_template_callback'),
            'ai-travel-tool',
            'aitraveltool_prompt_section'
        );
    }

    /**
     * API Keys section callback.
     */
    public function api_keys_section_callback()
    {
        echo '<p>' . __('Enter your API keys to connect with AI services. These keys will be securely stored.', 'ai-travel-tool-sample') . '</p>';
    }

    /**
     * AI Models section callback.
     */
    public function ai_models_section_callback()
    {
        echo '<p>' . __('Enter your desired AI models for travel itinerary generation. These models will be used for processing AI requests.', 'ai-travel-tool-sample') . '</p>';
    }

    /**
     * Prompt section callback.
     */
    public function prompt_section_callback()
    {
        echo '<p>' . __('Customize the prompt template used to generate travel itineraries. You can use the following variables: {trip_type}, {destination}', 'ai-travel-tool-sample') . '</p>';
    }

    /**
     * OpenAI API Key field callback.
     */
    public function openai_api_key_callback()
    {
        $openai_api_key = get_option('aitraveltool_openai_api_key');
?>
        <input type="password"
            id="aitraveltool_openai_api_key"
            name="aitraveltool_openai_api_key"
            value="<?php echo esc_attr($openai_api_key); ?>"
            class="regular-text"
            autocomplete="new-password" />
        <p class="description">
            <?php _e('Enter your OpenAI API Key', 'ai-travel-tool-sample'); ?>
        </p>
    <?php
    }

    /**
     * OpenRouter API Key field callback.
     */
    public function openrouter_api_key_callback()
    {
        $openrouter_api_key = get_option('aitraveltool_openrouter_api_key');
    ?>
        <input type="password"
            id="aitraveltool_openrouter_api_key"
            name="aitraveltool_openrouter_api_key"
            value="<?php echo esc_attr($openrouter_api_key); ?>"
            class="regular-text"
            autocomplete="new-password" />
        <p class="description">
            <?php _e('Enter your OpenRouter API Key', 'ai-travel-tool-sample'); ?>
        </p>
    <?php
    }

    /**
     * Groq API Key field callback.
     */
    public function groq_api_key_callback()
    {
        $groq_api_key = get_option('aitraveltool_groq_api_key');
    ?>
        <input type="password"
            id="aitraveltool_groq_api_key"
            name="aitraveltool_groq_api_key"
            value="<?php echo esc_attr($groq_api_key); ?>"
            class="regular-text"
            autocomplete="new-password" />
        <p class="description">
            <?php _e('Enter your Groq API Key', 'ai-travel-tool-sample'); ?>
        </p>
    <?php
    }

    /**
     * OpenAI Model field callback.
     */
    public function openai_model_callback()
    {
        $openai_model = get_option('aitraveltool_openai_model', 'gpt-4o-mini');
    ?>
        <input type="text"
            id="aitraveltool_openai_model"
            name="aitraveltool_openai_model"
            value="<?php echo esc_attr($openai_model); ?>"
            class="regular-text" />
        <p class="description">
            <?php
            echo __('Enter the OpenAI model ID to be used. ', 'ai-travel-tool-sample') .
                '<a href="https://platform.openai.com/docs/models" target="_blank" rel="noopener noreferrer">' .
                __('More models', 'ai-travel-tool-sample') . '</a>';
            ?>
        </p>
        <p class="description">
            <?php _e('Default: gpt-4o-mini', 'ai-travel-tool-sample'); ?>
        </p>
    <?php
    }

    /**
     * OpenRouter Model field callback.
     */
    public function openrouter_model_callback()
    {
        $openrouter_model = get_option('aitraveltool_openrouter_model', 'meta-llama/llama-3.3-70b-instruct:free');
    ?>
        <input type="text"
            id="aitraveltool_openrouter_model"
            name="aitraveltool_openrouter_model"
            value="<?php echo esc_attr($openrouter_model); ?>"
            class="regular-text" />
        <p class="description">
            <?php
            echo __('Enter the OpenRouter model ID to be used. ', 'ai-travel-tool-sample') .
                '<a href="https://openrouter.ai/models?max_price=0" target="_blank" rel="noopener noreferrer">' .
                __('More models', 'ai-travel-tool-sample') . '</a>';
            ?>
        </p>
        <p class="description">
            <?php _e('Default: meta-llama/llama-3.3-70b-instruct:free', 'ai-travel-tool-sample'); ?>
        </p>
    <?php
    }

    /**
     * Groq Model field callback.
     */
    public function groq_model_callback()
    {
        $groq_model = get_option('aitraveltool_groq_model', 'llama-3.3-70b-versatile');
    ?>
        <input type="text"
            id="aitraveltool_groq_model"
            name="aitraveltool_groq_model"
            value="<?php echo esc_attr($groq_model); ?>"
            class="regular-text" />
        <p class="description">
            <?php
            echo __('Enter the Groq model ID to be used. ', 'ai-travel-tool-sample') .
                '<a href="https://console.groq.com/docs/models" target="_blank" rel="noopener noreferrer">' .
                __('More models', 'ai-travel-tool-sample') . '</a>';
            ?>
        </p>
        <p class="description">
            <?php _e('Default: llama-3.3-70b-versatile', 'ai-travel-tool-sample'); ?>
        </p>
    <?php
    }

    /**
     * Prompt Template field callback.
     */
    public function prompt_template_callback()
    {
        $prompt_template = get_option('aitraveltool_prompt_template', $this->get_default_prompt_template());
    ?>
        <textarea id="aitraveltool_prompt_template"
            name="aitraveltool_prompt_template"
            rows="10"
            class="large-text code"><?php echo esc_textarea($prompt_template); ?></textarea>
        <p class="description">
            <?php _e('Customize the prompt used for generating travel itineraries. Use {trip_type} and {destination} as placeholders.', 'ai-travel-tool-sample'); ?>
        </p>
    <?php
    }

    /**
     * Settings page.
     */
    public function settings_page()
    {
        // Check user capabilities.
        if (! current_user_can('manage_options')) {
            return;
        }

        // Add settings error/update messages.
        settings_errors('aitraveltool_messages');
    ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p class="description">
                <?php
                /* translators: %s is the URL of the author */
                printf(
                    __('Crafted by <a href="%s" target="_blank" title="Walter Pinem" rel="noopener noreferrer">Walter Pinem</a>.', 'ai-travel-tool-sample'),
                    esc_url('https://walterpinem.com/')
                );
                ?>
            </p>
            <hr />
            <form action="options.php" method="post">
                <?php
                // Output security fields.
                settings_fields('aitraveltool_settings');

                // Output setting sections and fields.
                do_settings_sections('ai-travel-tool');

                // Output save settings button.
                submit_button(__('Save Settings', 'ai-travel-tool-sample'));
                ?>
            </form>
        </div>
<?php
    }

    /**
     * Sanitize API Key.
     */
    public function sanitize_api_key($key)
    {
        // Basic sanitization for API keys.
        return sanitize_text_field($key);
    }

    /**
     * Get default prompt template.
     */
    public function get_default_prompt_template()
    {
        return 'You are a world-class travel expert. Craft a vivid, detailed itinerary for a {trip_type} trip to {destination} that blends iconic landmarks with off-the-beaten-path discoveries. Highlight must-see attractions, authentic local experiences, unique cuisine, and seasonal events to inspire an unforgettable journey.';
    }
}

// Initialize the settings.
$aitraveltool_settings = new AITRAVELTOOL_Settings();
