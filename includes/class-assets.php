<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SACG_Assets {

    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
    }

    public function enqueue( $hook ) {
        if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'cg-admin',
            SACG_URL . 'assets/css/admin.css',
            [],
            SACG_VERSION
        );

        $is_gutenberg = $this->is_gutenberg_active();
        $has_prompt   = $this->current_post_type_has_prompt();

        if ( $is_gutenberg ) {
            wp_enqueue_script(
                'cg-gutenberg',
                SACG_URL . 'assets/js/gutenberg-editor.js',
                [ 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-blocks' ],
                SACG_VERSION,
                true
            );
            wp_localize_script( 'cg-gutenberg', 'cgData', $this->get_js_data( $has_prompt ) );
        } else {
            wp_enqueue_script(
                'cg-classic',
                SACG_URL . 'assets/js/classic-editor.js',
                [],
                SACG_VERSION,
                true
            );
            wp_localize_script( 'cg-classic', 'cgData', $this->get_js_data( $has_prompt ) );
        }
    }

    /**
     * Check whether the current post type has a non-empty prompt configured.
     */
    private function current_post_type_has_prompt() {
        $post_type = $this->get_current_post_type();
        if ( ! $post_type ) {
            return false;
        }

        // Must be a supported post type
        if ( ! in_array( $post_type, SACG_Settings::get_supported_post_types(), true ) ) {
            return false;
        }

        $prompt = get_option( SACG_Settings::prompt_option_key( $post_type ), '' );
        return ! empty( trim( $prompt ) );
    }

    private function get_current_post_type() {
        $post_type = get_post_type();
        if ( ! $post_type ) {
            $screen = get_current_screen();
            if ( $screen && ! empty( $screen->post_type ) ) {
                return $screen->post_type;
            }
            return 'post';
        }
        return $post_type;
    }    

    private function get_js_data( $has_prompt ) {
        return [
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'sacg_generate_nonce' ),
            'hasPrompt' => $has_prompt,
            'postType'  => $this->get_current_post_type(),
            'i18n'      => [
                // Shared
                'connecting'      => __( 'Connecting to Groq to generate content…', 'super-ai-content-generator' ),
                'generated'       => __( 'Content generated successfully.', 'super-ai-content-generator' ),
                'errorUnknown'    => __( 'Unknown error.', 'super-ai-content-generator' ),
                'errorResponse'   => __( 'Error processing the response.', 'super-ai-content-generator' ),
                'errorConnection' => __( 'Connection error.', 'super-ai-content-generator' ),
                'generating'      => __( 'Generating…', 'super-ai-content-generator' ),
                'btnLabel'        => __( 'Generate content with AI', 'super-ai-content-generator' ),
                'confirmReplace'  => __( 'This post already has content. Do you want to replace it?', 'super-ai-content-generator' ),
                // Classic editor
                'alertNoTitle'    => __( 'Please write a title before generating content.', 'super-ai-content-generator' ),
                // Gutenberg
                'panelTitle'      => __( '✨ AI Content Generator', 'super-ai-content-generator' ),
                'errorNoTitle'    => __( 'Please write a title before generating content.', 'super-ai-content-generator' ),
            ],
        ];
    }

    private function is_gutenberg_active() {
        // Detecting by the current screen, more reliable than use_block_editor_for_post_type
        // when there are themes/plugins that modify that filter
        if ( ! function_exists( 'get_current_screen' ) ) {
            return false;
        }
        $screen = get_current_screen();
        if ( ! $screen ) {
            return false;
        }
        return method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor();
    }    

}