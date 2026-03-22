<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SACG_Ajax {

    public function __construct() {
        add_action( 'wp_ajax_sacg_generate_content', [ $this, 'handle' ] );
    }

    public function handle() {
        check_ajax_referer( 'sacg_generate_nonce', 'nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( [ 'message' => __( 'You do not have sufficient permissions.', 'super-ai-content-generator' ) ] );
        }

        $title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
        if ( empty( $title ) ) {
            wp_send_json_error( [ 'message' => __( 'The title is empty.', 'super-ai-content-generator' ) ] );
        }

        $post_type = isset( $_POST['post_type'] ) ? sanitize_key( wp_unslash( $_POST['post_type'] ) ) : '';
        if ( empty( $post_type ) ) {
            wp_send_json_error( [ 'message' => __( 'Could not determine the post type.', 'super-ai-content-generator' ) ] );
        }

        // Verify the post type is supported and has a prompt
        $supported = SACG_Settings::get_supported_post_types();
        if ( ! in_array( $post_type, $supported, true ) ) {
            wp_send_json_error( [ 'message' => __( 'This post type does not support content generation.', 'super-ai-content-generator' ) ] );
        }

        $prompt_template = get_option( SACG_Settings::prompt_option_key( $post_type ), '' );
        if ( empty( trim( $prompt_template ) ) ) {
            wp_send_json_error( [ 'message' => __( 'No prompt configured for this post type.', 'super-ai-content-generator' ) ] );
        }

        $api_key = get_option( SACG_Settings::OPT_API_KEY, '' );
        if ( empty( $api_key ) ) {
            wp_send_json_error( [ 'message' => __( 'Groq API Key is not configured. Go to Settings → Content Generator.', 'super-ai-content-generator' ) ] );
        }

        $model  = get_option( SACG_Settings::OPT_MODEL, 'llama-3.3-70b-versatile' );
        $prompt = sprintf( $prompt_template, $title )
                . __( 
                    '. Reply directly with the content in HTML format using p, h2, h3, strong, em, ul and li tags. Do not use html, head or body tags. Do not include code blocks or backticks.', 
                    'super-ai-content-generator' 
                );

        $body = wp_json_encode( [
            'model'       => $model,
            'messages'    => [ [ 'role' => 'user', 'content' => $prompt ] ],
            'max_tokens'  => 1500,
            'temperature' => 0.7,
        ] );

        $response = wp_remote_post( 'https://api.groq.com/openai/v1/chat/completions', [
            'timeout' => 60,
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ],
            'body' => $body,
        ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( [
                'message' => sprintf(
                    /* translators: %s: error message from WordPress HTTP API */
                    __( 'Connection error: %s', 'super-ai-content-generator' ),
                    $response->get_error_message()
                ),
            ] );
        }

        $status = wp_remote_retrieve_response_code( $response );
        $data   = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $status !== 200 ) {
            $err = isset( $data['error']['message'] ) ? $data['error']['message'] : __( 'Unknown API error.', 'super-ai-content-generator' );
            wp_send_json_error( [
                'message' => sprintf(
                    /* translators: 1: HTTP status code, 2: error message from Groq */
                    __( 'Groq (%1$s): %2$s', 'super-ai-content-generator' ),
                    $status,
                    $err
                ),
            ] );
        }

        $content = isset( $data['choices'][0]['message']['content'] )
            ? wp_kses_post( trim( $data['choices'][0]['message']['content'] ) )
            : '';

        if ( empty( $content ) ) {
            wp_send_json_error( [ 'message' => __( 'The API returned no content.', 'super-ai-content-generator' ) ] );
        }

        wp_send_json_success( [ 'content' => $content ] );
    }
}