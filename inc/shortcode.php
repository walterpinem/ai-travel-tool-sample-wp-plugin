<?php

/**
 * AI Travel Tool Shortcode
 *
 * @package AITRAVELTOOL
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) exit;

/**
 * AITRAVELTOOL_Shortcode Class
 */
class AITRAVELTOOL_Shortcode
{

    /**
     * Constructor
     */
    public function __construct()
    {
        // Register shortcode
        add_shortcode('ai_travel_tool', array($this, 'render_shortcode'));

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_assets()
    {
        // Only enqueue if shortcode is present
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'ai_travel_tool')) {
            // Enqueue CSS
            wp_enqueue_style(
                'aitraveltool-style',
                AITRAVELTOOL_PLUGIN_URL . 'css/style.css',
                array(),
                AITRAVELTOOL_VERSION
            );

            // Enqueue JS
            wp_enqueue_script(
                'aitraveltool-script',
                AITRAVELTOOL_PLUGIN_URL . 'js/main.js',
                array('jquery'),
                AITRAVELTOOL_VERSION,
                true
            );

            // Localize script for AJAX
            wp_localize_script(
                'aitraveltool-script',
                'aitraveltool_params',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'    => wp_create_nonce('aitraveltool_nonce'),
                    'loading'  => __('Generating your personalized travel itinerary...', 'ai-travel-tool-sample'),
                    'error'    => __('An error occurred. Please try again.', 'ai-travel-tool-sample'),
                )
            );
        }
    }

    /**
     * Render shortcode
     */
    public function render_shortcode($atts)
    {
        // Check if any API key is configured
        $openai_api_key = get_option('aitraveltool_openai_api_key', '');
        $openrouter_api_key = get_option('aitraveltool_openrouter_api_key', '');
        $groq_api_key = get_option('aitraveltool_groq_api_key', '');

        if (empty($openai_api_key) && empty($openrouter_api_key) && empty($groq_api_key)) {
            if (current_user_can('manage_options')) {
                return '<div class="aitraveltool-error">' .
                    __('AI Travel Tool: Please configure at least one API key in the plugin settings.', 'ai-travel-tool-sample') .
                    ' <a href="' . admin_url('admin.php?page=ai-travel-tool') . '">' .
                    __('Go to Settings', 'ai-travel-tool-sample') .
                    '</a></div>';
            } else {
                return '<div class="aitraveltool-error">' .
                    __('AI Travel Tool is not properly configured. Please contact the site administrator.', 'ai-travel-tool-sample') .
                    '</div>';
            }
        }

        // Parse attributes
        $atts = shortcode_atts(
            array(
                'title'              => __('AI Travel Itinerary Generator', 'ai-travel-tool-sample'),
                'destination_label'  => __('Destination', 'ai-travel-tool-sample'),
                'trip_type_label'    => __('Trip Type', 'ai-travel-tool-sample'),
                'button_text'        => __('Generate Itinerary', 'ai-travel-tool-sample'),
                'trip_types'         => 'Adventure,Family,Romantic,Solo,Business,Cultural,Luxury,Budget,Foodie,Wellness',
                'default_api'        => 'openai',
                'show_api_selector'  => 'yes',
            ),
            $atts,
            'ai_travel_tool'
        );

        // Convert trip types string to array
        $trip_types = explode(',', $atts['trip_types']);

        // Start output buffering
        ob_start();
?>
        <div class="aitraveltool-container">
            <h2 class="aitraveltool-title"><?php echo esc_html($atts['title']); ?></h2>

            <div class="aitraveltool-form">
                <div class="aitraveltool-field">
                    <label for="aitraveltool-destination"><?php echo esc_html($atts['destination_label']); ?></label>
                    <input type="text" id="aitraveltool-destination" placeholder="<?php esc_attr_e('e.g. Tokyo, Paris, Bali', 'ai-travel-tool-sample'); ?>" required>
                </div>

                <div class="aitraveltool-field">
                    <label for="aitraveltool-trip-type"><?php echo esc_html($atts['trip_type_label']); ?></label>
                    <select id="aitraveltool-trip-type" required>
                        <option value=""><?php esc_html_e('Select trip type', 'ai-travel-tool-sample'); ?></option>
                        <?php foreach ($trip_types as $trip_type) : ?>
                            <option value="<?php echo esc_attr(trim($trip_type)); ?>"><?php echo esc_html(trim($trip_type)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($atts['show_api_selector'] === 'yes') : ?>
                    <div class="aitraveltool-field">
                        <label for="aitraveltool-api-service"><?php esc_html_e('AI Service', 'ai-travel-tool-sample'); ?></label>
                        <select id="aitraveltool-api-service">
                            <?php if (! empty($openai_api_key)) : ?>
                                <option value="openai" <?php selected($atts['default_api'], 'openai'); ?>>OpenAI</option>
                            <?php endif; ?>
                            <?php if (! empty($openrouter_api_key)) : ?>
                                <option value="openrouter" <?php selected($atts['default_api'], 'openrouter'); ?>>OpenRouter</option>
                            <?php endif; ?>
                            <?php if (! empty($groq_api_key)) : ?>
                                <option value="groq" <?php selected($atts['default_api'], 'groq'); ?>>Groq</option>
                            <?php endif; ?>
                        </select>
                    </div>
                <?php else : ?>
                    <input type="hidden" id="aitraveltool-api-service" value="<?php echo esc_attr($atts['default_api']); ?>">
                <?php endif; ?>

                <div class="aitraveltool-button-container">
                    <button id="aitraveltool-generate-btn" class="aitraveltool-button"><?php echo esc_html($atts['button_text']); ?></button>
                </div>
            </div>

            <div id="aitraveltool-loading" class="aitraveltool-loading" style="display: none;">
                <div class="aitraveltool-spinner"></div>
                <p><?php esc_html_e('Generating your personalized travel itinerary...', 'ai-travel-tool-sample'); ?></p>
            </div>

            <div id="aitraveltool-result" class="aitraveltool-result" style="display: none;"></div>
        </div>
<?php

        // Return the buffered content
        return ob_get_clean();
    }
}

// Initialize the shortcode
$aitraveltool_shortcode = new AITRAVELTOOL_Shortcode();
