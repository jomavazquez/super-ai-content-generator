<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sacg_render_settings_page_content() {
    $sacg_current_model = get_option( SACG_Settings::OPT_MODEL, 'llama-3.3-70b-versatile' );
    $sacg_models = [
        'llama-3.3-70b-versatile' => __( 'Llama 3.3 70B (recommended, most capable)', 'super-ai-content-generator' ),
        'llama-3.1-8b-instant'    => __( 'Llama 3.1 8B (fast and lightweight)', 'super-ai-content-generator' ),
        'mixtral-8x7b-32768'      => __( 'Mixtral 8x7B (good quality)', 'super-ai-content-generator' ),
        'gemma2-9b-it'            => __( 'Gemma 2 9B (by Google, compact)', 'super-ai-content-generator' ),
    ];

    $sacg_post_types = SACG_Settings::get_supported_post_types();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Super AI Content Generator — Settings', 'super-ai-content-generator' ); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'content_generator_settings' ); ?>

            <!-- ============================================================ -->
            <!-- Section: API & Model                                         -->
            <!-- ============================================================ -->
            <h2 class="title"><?php esc_html_e( 'API & Model', 'super-ai-content-generator' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( SACG_Settings::OPT_API_KEY ); ?>">
                            <?php esc_html_e( 'Groq API Key', 'super-ai-content-generator' ); ?>
                        </label>
                    </th>
                    <td>
                        <input
                            type="password"
                            id="<?php echo esc_attr( SACG_Settings::OPT_API_KEY ); ?>"
                            name="<?php echo esc_attr( SACG_Settings::OPT_API_KEY ); ?>"
                            value="<?php echo esc_attr( get_option( SACG_Settings::OPT_API_KEY, '' ) ); ?>"
                            class="regular-text"
                            autocomplete="new-password"
                        />
                        <p class="description">
                            <?php esc_html_e( 'Get your free API Key at https://console.groq.com/keys', 'super-ai-content-generator' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( SACG_Settings::OPT_MODEL ); ?>">
                            <?php esc_html_e( 'AI Model', 'super-ai-content-generator' ); ?>
                        </label>
                    </th>
                    <td>
                        <select
                            id="<?php echo esc_attr( SACG_Settings::OPT_MODEL ); ?>"
                            name="<?php echo esc_attr( SACG_Settings::OPT_MODEL ); ?>"
                            class="regular-text"
                        >
                            <?php foreach ( $sacg_models as $sacg_value => $sacg_label ) : ?>
                                <option value="<?php echo esc_attr( $sacg_value ); ?>" <?php selected( $sacg_current_model, $sacg_value ); ?>>
                                    <?php echo esc_html( $sacg_label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php esc_html_e( 'All models are free on Groq. Llama 3.3 70B offers the best quality.', 'super-ai-content-generator' ); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <!-- ============================================================ -->
            <!-- Section: Prompts per post type                               -->
            <!-- ============================================================ -->
            <h2 class="title"><?php esc_html_e( 'Prompts by post type', 'super-ai-content-generator' ); ?></h2>
            <p>
                <?php esc_html_e( 'Configure a prompt for each post type. Leave a field empty to hide the generation button for that type.', 'super-ai-content-generator' ); ?>
                <?php
                    /* translators: %s: placeholder that will be replaced with the post title */
                    esc_html_e( 'Use %s where you want the post title to be inserted.', 'super-ai-content-generator' );
                ?>
            </p>
            <table class="form-table" role="presentation">
                <?php foreach ( $sacg_post_types as $sacg_post_type ) :
                    $sacg_option_key   = SACG_Settings::prompt_option_key( $sacg_post_type );
                    $sacg_label        = SACG_Settings::get_post_type_label( $sacg_post_type );
                    $sacg_saved_prompt = get_option( $sacg_option_key, '' );
                ?>
                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $sacg_option_key ); ?>">
                            <?php echo esc_html( $sacg_label ); ?>
                            <code style="font-weight:normal;font-size:11px;display:block;margin-top:3px;color:#666;">
                                <?php echo esc_html( $sacg_post_type ); ?>
                            </code>
                        </label>
                    </th>
                    <td>
                        <textarea
                            id="<?php echo esc_attr( $sacg_option_key ); ?>"
                            name="<?php echo esc_attr( $sacg_option_key ); ?>"
                            class="large-text"
                            rows="3"
                            placeholder="<?php esc_attr_e( 'Leave empty to disable the button for this post type.', 'super-ai-content-generator' ); ?>"
                        ><?php echo esc_textarea( $sacg_saved_prompt ); ?></textarea>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <?php submit_button( __( 'Save changes', 'super-ai-content-generator' ) ); ?>
        </form>
    </div>
    <?php
}

sacg_render_settings_page_content();