<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SACG_Settings {

    const OPT_API_KEY      = 'content_generator_api_key';
    const OPT_MODEL        = 'content_generator_model';
    const OPT_PROMPT_BASE  = 'content_generator_prompt_'; // + post_type slug

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Returns the option key for a given post type.
     */
    public static function prompt_option_key( $post_type ) {
        return self::OPT_PROMPT_BASE . sanitize_key( $post_type );
    }

    /**
     * Returns all post types that should appear in settings:
     * built-in 'post' and 'page' + publicly registered CPTs,
     * excluding internal WordPress types.
     */
    public static function get_supported_post_types() {
        $builtin = [ 'post', 'page' ];

        $public_cpts = get_post_types( [
            'public'   => true,
            '_builtin' => false,
        ], 'objects' );

        $cpt_slugs = array_keys( $public_cpts );

        return array_merge( $builtin, $cpt_slugs );
    }

    /**
     * Returns the WP_Post_Type object (or a minimal stdClass for built-ins)
     * so the view always has a ->label to display.
     */
    public static function get_post_type_label( $post_type ) {
        $obj = get_post_type_object( $post_type );
        return $obj ? $obj->label : $post_type;
    }

    public function add_settings_page() {
        add_options_page(
            __( 'Super AI Content Generator', 'super-ai-content-generator' ),
            __( 'Super AI Content Generator', 'super-ai-content-generator' ),
            'manage_options',
            'super-ai-content-generator',
            [ $this, 'render_settings_page' ]
        );
    }

    public function register_settings() {
        // API key
        register_setting( 'content_generator_settings', self::OPT_API_KEY, [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ] );

        // Model
        register_setting( 'content_generator_settings', self::OPT_MODEL, [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'llama-3.3-70b-versatile',
        ] );

        // One prompt option per supported post type
        foreach ( self::get_supported_post_types() as $post_type ) {
            register_setting( 'content_generator_settings', self::prompt_option_key( $post_type ), [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default'           => '',
            ] );
        }
    }

    public function render_settings_page() {
        require_once SACG_DIR . 'admin/views/settings-page.php';
    }
}