<?php
/**
 * Plugin Name: Nakama Instagram Feed
 * Plugin URI: https://imperiodev.com
 * Description: Conecta tu cuenta de Instagram para mostrar el feed en tiempo real. Optimizado y personalizado para Nakama Bordados.
 * Version: 1.0.0
 * Author: ImperioDev
 * Author URI: https://imperiodev.com
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nakama_Instagram_Feed {

    private $option_name = 'nakama_instagram_settings';

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_shortcode( 'nakama_instagram_feed', array( $this, 'render_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    public function enqueue_scripts() {
        // Enqueue Tailwind if not present (optional, user provided generic script in request)
        // For production plugins, it's better to rely on theme's CSS or compile your own.
        // Given existing HTML uses a CDN, we can conditionally add it or assume theme has it.
        // To be safe and self-contained for the "test" environment, we'll leave it to the theme/header.
    }

    public function add_admin_menu() {
        add_menu_page(
            'Nakama Instagram',
            'Nakama Instagram',
            'manage_options',
            'nakama-instagram-feed',
            array( $this, 'settings_page_html' ),
            'dashicons-camera',
            100
        );
    }

    public function register_settings() {
        register_setting( $this->option_name, $this->option_name );

        add_settings_section(
            'nakama_api_section',
            'Configuración de API',
            null,
            'nakama-instagram-feed'
        );

        add_settings_field(
            'access_token',
            'Instagram Access Token',
            array( $this, 'access_token_callback' ),
            'nakama-instagram-feed',
            'nakama_api_section'
        );

        add_settings_section(
            'nakama_display_section',
            'Configuración Visual',
            null,
            'nakama-instagram-feed'
        );

        add_settings_field(
            'header_text',
            'Texto del Encabezado',
            array( $this, 'header_text_callback' ),
            'nakama-instagram-feed',
            'nakama_display_section'
        );

        add_settings_field(
            'show_credit',
            'Mostrar "Desarrollado por ImperioDev"',
            array( $this, 'show_credit_callback' ),
            'nakama-instagram-feed',
            'nakama_display_section'
        );
    }

    public function settings_page_html() {
        ?>
        <div class="wrap">
            <h1>Configuración Nakama Instagram Feed</h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( $this->option_name );
                do_settings_sections( 'nakama-instagram-feed' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function access_token_callback() {
        $options = get_option( $this->option_name );
        $value = isset( $options['access_token'] ) ? $options['access_token'] : '';
        echo '<input type="text" name="' . $this->option_name . '[access_token]" value="' . esc_attr( $value ) . '" class="regular-text" />';
        echo '<p class="description">Introduce tu Token de Acceso Básico de Instagram. <a href="https://developers.facebook.com/docs/instagram-basic-display-api/getting-started" target="_blank">Cómo obtenerlo</a>.</p>';
    }

    public function header_text_callback() {
        $options = get_option( $this->option_name );
        $value = isset( $options['header_text'] ) ? $options['header_text'] : 'SÍGUENOS EN INSTAGRAM';
        echo '<input type="text" name="' . $this->option_name . '[header_text]" value="' . esc_attr( $value ) . '" class="regular-text" />';
    }

    public function show_credit_callback() {
        $options = get_option( $this->option_name );
        $value = isset( $options['show_credit'] ) ? $options['show_credit'] : '0';
        ?>
        <label>
            <input type="checkbox" name="<?php echo $this->option_name; ?>[show_credit]" value="1" <?php checked( 1, $value, true ); ?> />
            Mostrar leyenda en el pie del feed.
        </label>
        <?php
    }

    private function get_instagram_posts( $limit = 8 ) {
        $options = get_option( $this->option_name );
        $access_token = isset( $options['access_token'] ) ? $options['access_token'] : '';

        if ( empty( $access_token ) ) {
            return new WP_Error( 'no_token', 'No Access Token configured.' );
        }

        $transient_key = 'nakama_insta_feed_' . md5( $access_token );
        $cached_data = get_transient( $transient_key );

        if ( false !== $cached_data ) {
            return $cached_data;
        }

        $url = "https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp&access_token={$access_token}&limit={$limit}";
        
        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( isset( $data['error'] ) ) {
            return new WP_Error( 'api_error', $data['error']['message'] );
        }

        if ( isset( $data['data'] ) ) {
            $posts = $data['data'];
            // Cache for 15 minutes (900 seconds)
            set_transient( $transient_key, $posts, 900 );
            return $posts;
        }

        return array();
    }

    public function render_shortcode( $atts ) {
        $options = get_option( $this->option_name );
        $header_text = isset( $options['header_text'] ) ? $options['header_text'] : 'SÍGUENOS EN INSTAGRAM';
        $show_credit = isset( $options['show_credit'] ) && $options['show_credit'] == '1';

        // Fetch posts
        $posts = $this->get_instagram_posts();

        // If error or empty (and no mock data requested for testing/fallback logic in real plugin)
        // For this task, we will gracefully handle errors.
        if ( is_wp_error( $posts ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                return '<div class="text-red-500 p-4 border border-red-500">Error: ' . $posts->get_error_message() . '</div>';
            }
            return ''; 
        }

        ob_start();
        ?>
        <section class="w-full py-16 border-t border-gray-100 dark:border-gray-800" id="nakama-instagram-feed">
            <div class="px-4 max-w-screen-2xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-center md:items-end mb-8 text-center md:text-left">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-4xl font-display font-bold mb-2 uppercase section-title force-light-text">
                            <?php echo esc_html( $header_text ); ?>
                        </h2>
                        <span class="pulse-red-text block h-1 w-20 bg-primary mt-2 mx-auto md:mx-0"></span>
                    </div>
                    <?php if ( $show_credit ): ?>
                         <div class="text-sm text-gray-500 font-body">
                            Desarrollado por <span class="text-primary font-bold">ImperioDev</span>
                         </div>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php 
                    if ( ! empty( $posts ) ) :
                        foreach ( $posts as $post ) : 
                            $image_url = ($post['media_type'] == 'VIDEO' && isset($post['thumbnail_url'])) ? $post['thumbnail_url'] : $post['media_url'];
                            $caption = isset($post['caption']) ? $post['caption'] : '';
                            $permalink = $post['permalink'];
                            ?>
                            <a href="<?php echo esc_url( $permalink ); ?>" target="_blank" class="group relative block overflow-hidden rounded-sm aspect-square shadow-lg">
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( substr($caption, 0, 50) ); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="material-icons-outlined text-white text-4xl">visibility</span>
                                </div>
                            </a>
                        <?php endforeach; 
                    else: ?>
                        <!-- Placeholder/Empty state if token valid but no posts -->
                         <div class="col-span-full text-center py-10 dark:text-white">
                            No recent posts found.
                         </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}

new Nakama_Instagram_Feed();
