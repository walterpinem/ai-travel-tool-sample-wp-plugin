<?php

/**
 * AI Travel Tool Handler
 *
 * @package AITRAVELTOOL
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * AITRAVELTOOL_Handler Class
 */
class AITRAVELTOOL_Handler
{

    /**
     * Constructor
     */
    public function __construct()
    {
        // Register AJAX actions.
        add_action('wp_ajax_aitraveltool_generate_itinerary', array($this, 'generate_itinerary'));
        add_action('wp_ajax_nopriv_aitraveltool_generate_itinerary', array($this, 'generate_itinerary'));
    }

    /**
     * Generate travel itinerary.
     */
    public function generate_itinerary()
    {
        // Check nonce for security.
        if (! isset($_POST['nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'aitraveltool_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'ai-travel-tool-sample')));
        }

        // Get and sanitize input data.
        $destination = isset($_POST['destination']) ? sanitize_text_field(wp_unslash($_POST['destination'])) : '';
        $trip_type   = isset($_POST['trip_type']) ? sanitize_text_field(wp_unslash($_POST['trip_type'])) : '';

        // Debug received parameters.
        error_log('Received destination: ' . $destination);
        error_log('Received trip_type: ' . $trip_type);

        // Validate required fields.
        if (empty($destination) || empty($trip_type)) {
            wp_send_json_error(array('message' => __('Destination and trip type are required.', 'ai-travel-tool-sample')));
        }

        // Get API key based on selected service.
        $api_service = isset($_POST['api_service']) ? sanitize_text_field(wp_unslash($_POST['api_service'])) : 'openai';
        $api_key     = $this->get_api_key($api_service);

        error_log('Selected API service: ' . $api_service);

        // Check if API key is available.
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('API key not configured. Please check the plugin settings.', 'ai-travel-tool-sample')));
        }

        // Get prompt template from the options.
        $prompt_template = get_option('aitraveltool_prompt_template');

        // Use the default prompt template if the retrieved value is false or an empty string.
        if (false === $prompt_template || trim($prompt_template) === '') {
            $prompt_template = $this->get_default_prompt_template();
            error_log('Using default prompt template.');
        } else {
            error_log('Retrieved prompt template: ' . $prompt_template);
        }

        // Replace the placeholders with actual values.
        $prompt = str_replace(
            array('{destination}', '{trip_type}'),
            array(ucfirst($destination), $trip_type),
            $prompt_template
        );

        // Verify the prompt is not empty after replacement.
        if (empty(trim($prompt))) {
            $prompt = "Create a detailed $trip_type itinerary for $destination.";
            error_log('Empty prompt after replacement, using direct fallback.');
        }

        error_log('Final prompt after replacement: ' . $prompt);

        // Call the appropriate API based on the selected service.
        $response = $this->call_ai_api($api_service, $api_key, $prompt);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }

        wp_send_json_success(array('itinerary' => $response));
    }


    /**
     * Get API key based on service.
     */
    private function get_api_key($service)
    {
        switch ($service) {
            case 'openai':
                return get_option('aitraveltool_openai_api_key', '');
            case 'openrouter':
                return get_option('aitraveltool_openrouter_api_key', '');
            case 'groq':
                return get_option('aitraveltool_groq_api_key', '');
            default:
                return '';
        }
    }

    /**
     * Call AI API based on service.
     */
    private function call_ai_api($service, $api_key, $prompt)
    {
        switch ($service) {
            case 'openai':
                return $this->call_openai_api($api_key, $prompt);
            case 'openrouter':
                return $this->call_openrouter_api($api_key, $prompt);
            case 'groq':
                return $this->call_groq_api($api_key, $prompt);
            default:
                return new WP_Error('invalid_service', __('Invalid AI service specified.', 'ai-travel-tool-sample'));
        }
    }

    /**
     * Call OpenAI API.
     */
    private function call_openai_api($api_key, $prompt)
    {
        $url = 'https://api.openai.com/v1/chat/completions';

        // Retrieve the dynamic model setting.
        $model = get_option('aitraveltool_openai_model', 'gpt-4o-mini');

        $headers = array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        );

        $body = array(
            'model'       => $model,
            'messages'    => array(
                array(
                    'role'    => 'user',
                    'content' => $prompt,
                ),
            ),
            'temperature' => 0.7,
            'max_tokens'  => 2000,
        );

        $args = array(
            'headers' => $headers,
            'body'    => wp_json_encode($body),
            'method'  => 'POST',
            'timeout' => 60,
        );

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $response_data = json_decode($response_body, true);

        if (200 !== $response_code) {
            $error_message = isset($response_data['error']['message'])
                ? $response_data['error']['message']
                : __('Unknown API error', 'ai-travel-tool-sample');
            return new WP_Error('api_error', $error_message);
        }

        return isset($response_data['choices'][0]['message']['content'])
            ? $response_data['choices'][0]['message']['content']
            : '';
    }

    /**
     * Call OpenRouter API.
     */
    /**
     * Call OpenRouter API.
     */
    private function call_openrouter_api($api_key, $prompt)
    {
        $url = 'https://openrouter.ai/api/v1/chat/completions';

        // Retrieve the dynamic model setting.
        $model = get_option('aitraveltool_openrouter_model', 'meta-llama/llama-3.3-70b-instruct:free');

        // Debug prompt at start of function
        error_log('OpenRouter received prompt: "' . $prompt . '"');

        $headers = array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
            'HTTP-Referer'  => get_site_url(),
            'X-Title'       => get_bloginfo('name'), // Optional but helpful for rankings
        );

        // Ensure prompt is not empty
        if (empty(trim($prompt))) {
            $prompt = "Please create a travel itinerary.";
            error_log('OpenRouter using fallback prompt due to empty input');
        }

        $body = array(
            'model'       => $model,
            'messages'    => array(
                array(
                    'role'    => 'user',
                    'content' => $prompt,
                ),
            ),
            'temperature' => 0.7,
            'max_tokens'  => 2000,
        );

        // Debug full request body
        error_log('OpenRouter request body: ' . wp_json_encode($body));

        $args = array(
            'headers' => $headers,
            'body'    => wp_json_encode($body),
            'method'  => 'POST',
            'timeout' => 120, // Change this value as needed
        );

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            error_log('OpenRouter error: ' . $response->get_error_message());
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        error_log('OpenRouter response code: ' . $response_code);

        // Only log first part of response to avoid huge logs
        error_log('OpenRouter response preview: ' . substr($response_body, 0, 200) . '...');

        $response_data = json_decode($response_body, true);

        if (200 !== $response_code) {
            $error_message = isset($response_data['error']['message'])
                ? $response_data['error']['message']
                : __('Unknown API error', 'ai-travel-tool-sample');
            error_log('OpenRouter error message: ' . $error_message);
            return new WP_Error('api_error', $error_message);
        }

        return isset($response_data['choices'][0]['message']['content'])
            ? $response_data['choices'][0]['message']['content']
            : '';
    }

    /**
     * Call Groq API.
     */
    private function call_groq_api($api_key, $prompt)
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        // Retrieve the dynamic model setting.
        $model = get_option('aitraveltool_groq_model', 'llama-3.3-70b-versatile');

        // Debug prompt at start of function
        error_log('Groq received prompt: "' . $prompt . '"');

        $headers = array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        );

        // Ensure prompt is not empty
        if (empty(trim($prompt))) {
            $prompt = "Please create a travel itinerary.";
            error_log('Groq using fallback prompt due to empty input');
        }

        $body = array(
            'model'       => $model,
            'messages'    => array(
                array(
                    'role'    => 'user',
                    'content' => $prompt,
                ),
            ),
            'temperature' => 1,
            'max_tokens'  => 8000,
            'top_p'       => 1,
            'stream'      => false,
            'stop'        => null,
        );

        // Debug full request body
        error_log('Groq request body: ' . wp_json_encode($body));

        $args = array(
            'headers' => $headers,
            'body'    => wp_json_encode($body),
            'method'  => 'POST',
            'timeout' => 120,
        );

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            error_log('Groq error: ' . $response->get_error_message());
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        error_log('Groq response code: ' . $response_code);

        // Only log first part of response to avoid huge logs
        error_log('Groq response preview: ' . substr($response_body, 0, 200) . '...');

        $response_data = json_decode($response_body, true);

        if (200 !== $response_code) {
            $error_message = isset($response_data['error']['message'])
                ? $response_data['error']['message']
                : __('Unknown API error', 'ai-travel-tool-sample');
            error_log('Groq error message: ' . $error_message);
            return new WP_Error('api_error', $error_message);
        }

        return isset($response_data['choices'][0]['message']['content'])
            ? $response_data['choices'][0]['message']['content']
            : '';
    }
}

// Initialize the handler.
$aitraveltool_handler = new AITRAVELTOOL_Handler();
