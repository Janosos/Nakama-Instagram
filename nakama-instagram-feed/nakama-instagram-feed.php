<?php
/**
 * Plugin Name: Nakama Instagram Feed
 * Plugin URI: https://imperiodev.com
 * Description: Conecta tu cuenta de Instagram para mostrar el feed en tiempo real. Optimizado y personalizado para Nakama Bordados.
 * Version: 1.2.0
 * Author: ImperioDev
 * Author URI: https://imperiodev.com
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nakama_Instagram_Feed
{

    private $option_name = 'nakama_instagram_settings';

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_shortcode('nakama_instagram_feed', array($this, 'render_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_notices', array($this, 'check_token_errors'));
    }

    public function enqueue_scripts()
    {
        // Enqueue fonts/styles if needed. Assuming theme handles Tailwind handling.
    }

    public function check_token_errors()
    {
        $options = get_option($this->option_name);
        if (!empty($options['nakama_token_error_msg'])) {
            ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>Actualización Requerida (Nakama Instagram):</strong> <?php echo esc_html($options['nakama_token_error_msg']); ?></p>
                        </div>
                        <?php
        }
    }

    public function add_admin_menu()
    {
        add_menu_page(
            'Nakama Instagram',
            'Nakama Instagram',
            'manage_options',
            'nakama-instagram-feed',
            array($this, 'settings_page_html'),
            'dashicons-camera',
            100
        );
    }

    public function register_settings()
    {
        register_setting($this->option_name, $this->option_name, array($this, 'sanitize_settings'));

        add_settings_section('nakama_api_section', 'Configuración de API', null, 'nakama-instagram-feed');

        add_settings_field('access_token', 'Instagram Access Token', array($this, 'access_token_callback'), 'nakama-instagram-feed', 'nakama_api_section');

        // New fields for Auto-Refresh (Graph API)
        add_settings_field('app_id', 'App ID (Opcional para Auto-fresh)', array($this, 'app_id_callback'), 'nakama-instagram-feed', 'nakama_api_section');
        add_settings_field('app_secret', 'App Secret (Opcional para Auto-fresh)', array($this, 'app_secret_callback'), 'nakama-instagram-feed', 'nakama_api_section');

        add_settings_section('nakama_display_section', 'Configuración Visual', null, 'nakama-instagram-feed');
        add_settings_field('header_text', 'Título del Encabezado', array($this, 'header_text_callback'), 'nakama-instagram-feed', 'nakama_display_section');
        add_settings_field('header_subtitle', 'Subtítulo (Descripción Corta)', array($this, 'header_subtitle_callback'), 'nakama-instagram-feed', 'nakama_display_section');
        add_settings_field('header_alignment', 'Alineación del Encabezado', array($this, 'header_alignment_callback'), 'nakama-instagram-feed', 'nakama_display_section');
    }

    public function sanitize_settings($input)
    {
        $old_options = get_option($this->option_name);
        $new_input = array();

        // Token logic: Clean and Stamp
        $new_token = isset($input['access_token']) ? trim($input['access_token']) : '';
        $new_input['access_token'] = $new_token;

        // If token changed or no timestamp exists, update timestamp
        if (empty($old_options['token_timestamp']) || ($old_options['access_token'] !== $new_token)) {
            $new_input['token_timestamp'] = time();
        } else {
            $new_input['token_timestamp'] = $old_options['token_timestamp'];
        }

        $new_input['app_id'] = isset($input['app_id']) ? sanitize_text_field($input['app_id']) : '';
        $new_input['app_secret'] = isset($input['app_secret']) ? sanitize_text_field($input['app_secret']) : '';

        $new_input['header_text'] = isset($input['header_text']) ? sanitize_text_field($input['header_text']) : '';
        $new_input['header_subtitle'] = isset($input['header_subtitle']) ? sanitize_text_field($input['header_subtitle']) : '';
        $new_input['header_alignment'] = isset($input['header_alignment']) ? sanitize_text_field($input['header_alignment']) : 'center';

        return $new_input;
    }

    public function settings_page_html()
    {
        ?>
        <div class="wrap">
            <h1>Configuración Nakama Instagram Feed</h1>

            <div class="card" style="max-width: 100%; margin-bottom: 20px;">
                <h2>Ayuda rápida / Instrucciones</h2>
                <ol>
                    <li>
                        <strong>1. Obtener Token:</strong> Genera tu "User Access Token" en la
                        <a href="https://developers.facebook.com/docs/instagram-basic-display-api/getting-started"
                            target="_blank">API de Instagram Basic Display</a>.
                    </li>
                    <li>
                        <strong>2. Configurar:</strong> Pega el token en el campo de abajo y guarda los cambios.
                    </li>
                    <li>
                        <strong>3. Implementar:</strong> Copia y pega el siguiente shortcode en cualquier página:
                        <code>[nakama_instagram_feed]</code>
                    </li>
                </ol>
            </div>

            <form action="options.php" method="post">
                <?php
                settings_fields($this->option_name);
                do_settings_sections('nakama-instagram-feed');
                submit_button();
                ?>
            </form>
            <hr>
            <p style="text-align: right; color: #666; font-size: 11px;">
                Desarrollado por <strong>ImperioDev</strong>
            </p>
        </div>
        <?php
    }

    public function access_token_callback()
    {
        $options = get_option($this->option_name);
        $value = isset($options['access_token']) ? $options['access_token'] : '';
        echo '<input type="text" name="' . $this->option_name . '[access_token]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">Token de acceso de Instagram Basic Display API.</p>';
    }

    public function header_text_callback()
    {
        $options = get_option($this->option_name);
        $value = isset($options['header_text']) ? $options['header_text'] : 'SÍGUENOS EN INSTAGRAM';
        echo '<input type="text" name="' . $this->option_name . '[header_text]" value="' . esc_attr($value) . '" class="regular-text" />';
    }

    public function header_subtitle_callback()
    {
        $options = get_option($this->option_name);
        $value = isset($options['header_subtitle']) ? $options['header_subtitle'] : '';
        echo '<input type="text" name="' . $this->option_name . '[header_subtitle]" value="' . esc_attr($value) . '" class="regular-text" placeholder="Ej: @nakama.bordados" />';
        echo '<p class="description">Texto más pequeño debajo o junto al título.</p>';
    }

    public function header_alignment_callback()
    {
        $options = get_option($this->option_name);
        $value = isset($options['header_alignment']) ? $options['header_alignment'] : 'left';
        ?>
        <select name="<?php echo $this->option_name; ?>[header_alignment]">
            <option value="left" <?php selected($value, 'left'); ?>>Izquierda</option>
            <option value="center" <?php selected($value, 'center'); ?>>Centro</option>
            <option value="right" <?php selected($value, 'right'); ?>>Derecha</option>
        </select>
        <?php
    }

    private function get_instagram_posts($limit = 8)
    {
        $options = get_option($this->option_name);
        $access_token = isset($options['access_token']) ? trim($options['access_token']) : '';

        if (empty($access_token)) {
            return new WP_Error('no_token', 'No Access Token configured.');
        }

        // --- AUTO REFRESH LOGIC ---
        $timestamp = isset($options['token_timestamp']) ? $options['token_timestamp'] : 0;
        if ($timestamp && (time() - $timestamp) > (45 * 86400)) { // 45 days

            $new_token = null;

            // Type A: Basic Display (IGQ...)
            if (strpos($access_token, 'IGQ') === 0) {
                $refresh_url = "https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token={$access_token}";
                $response = wp_remote_get($refresh_url);
                if (!is_wp_error($response)) {
                    $body = json_decode(wp_remote_retrieve_body($response), true);
                    if (isset($body['access_token'])) {
                        $new_token = $body['access_token'];
                    }
                }
            }
            // Type B: Graph API (EAA...) - Requires App ID + Secret
            elseif (strpos($access_token, 'EAA') === 0) {
                $app_id = isset($options['app_id']) ? $options['app_id'] : '';
                $app_secret = isset($options['app_secret']) ? $options['app_secret'] : '';

                if ($app_id && $app_secret) {
                    $refresh_url = "https://graph.facebook.com/v22.0/oauth/access_token?grant_type=fb_exchange_token&client_id={$app_id}&client_secret={$app_secret}&fb_exchange_token={$access_token}";
                    $response = wp_remote_get($refresh_url);
                    if (!is_wp_error($response)) {
                        $body = json_decode(wp_remote_retrieve_body($response), true);
                        if (isset($body['access_token'])) {
                            $new_token = $body['access_token'];
                        }
                    }
                }
            }

            // Save new token if refreshed
            if ($new_token) {
                $options['access_token'] = $new_token;
                $options['token_timestamp'] = time();
                update_option($this->option_name, $options);
                $access_token = $new_token; // Use new token for this request
            }
        }
        // --------------------------

        $transient_key = 'nakama_insta_feed_' . md5($access_token);
        $cached_data = get_transient($transient_key);

        if (false !== $cached_data) {
            return $cached_data;
        }

        // 1. Try Basic Display API (IGQ tokens or Instagram Login)
        // Endpoint: graph.instagram.com/me/media
        $basic_url = "https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp&access_token={$access_token}&limit={$limit}";
        $response = wp_remote_get($basic_url);

        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);

            if (isset($data['data'])) {
                $posts = $data['data'];
                set_transient($transient_key, $posts, 900);
                return $posts;
            }
            // If error is specifically OAuth, try Graph API Fallback
        }

        // 2. Fallback: Try Graph API (EAA tokens for Business/Creator)
        // Step A: Get User's Pages -> Instagram Business Account
        $accounts_url = "https://graph.facebook.com/v22.0/me/accounts?fields=instagram_business_account&access_token={$access_token}";
        $accounts_response = wp_remote_get($accounts_url);

        if (is_wp_error($accounts_response)) {
            // Return original Basic error if Graph also fails immediately
            return isset($data['error']) ? new WP_Error('api_error', 'Basic: ' . $data['error']['message']) : $accounts_response;
        }

        $accounts_body = json_decode(wp_remote_retrieve_body($accounts_response), true);

        $ig_business_id = null;
        if (isset($accounts_body['data'])) {
            foreach ($accounts_body['data'] as $page) {
                if (isset($page['instagram_business_account']['id'])) {
                    $ig_business_id = $page['instagram_business_account']['id'];
                    break;
                }
            }
        }

        if ($ig_business_id) {
            // Step B: Get Media from that ID
            $media_url = "https://graph.facebook.com/v22.0/{$ig_business_id}/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp&access_token={$access_token}&limit={$limit}";
            $media_response = wp_remote_get($media_url);

            if (!is_wp_error($media_response)) {
                $media_data = json_decode(wp_remote_retrieve_body($media_response), true);
                if (isset($media_data['data'])) {
                    $posts = $media_data['data'];
                    set_transient($transient_key, $posts, 900);
                    return $posts;
                }
            }
        }

        // If we reached here, both methods failed. Return meaningful error.
        $error_msg = 'No valid media found. ';
        if (isset($data['error'])) {
            $error_msg .= 'Basic API: ' . $data['error']['message'] . ' ';
        }
        if (isset($accounts_body['error'])) {
            $error_msg .= '| Graph API: ' . $accounts_body['error']['message'];
        } else if (empty($ig_business_id)) {
            $error_msg .= '| Graph API: No linked Instagram Business Account found (Check Facebook Page connection).';
        }

        return new WP_Error('api_error', $error_msg);
    }

    public function render_shortcode($atts)
    {
        $options = get_option($this->option_name);
        $header_text = isset($options['header_text']) ? $options['header_text'] : 'SÍGUENOS EN INSTAGRAM';
        $header_subtitle = isset($options['header_subtitle']) ? $options['header_subtitle'] : '';
        $alignment = isset($options['header_alignment']) ? $options['header_alignment'] : 'left';

        // Determine alignment classes
        $container_class = 'flex flex-col mb-8';
        $text_align_class = 'text-left';
        $items_align_class = 'items-start';
        $red_line_margin = 'mr-auto'; // default left

        switch ($alignment) {
            case 'center':
                $text_align_class = 'text-center';
                $items_align_class = 'items-center';
                $red_line_margin = 'mx-auto';
                break;
            case 'right':
                $text_align_class = 'text-right';
                $items_align_class = 'items-end';
                $red_line_margin = 'ml-auto';
                break;
            case 'left':
            default:
                $text_align_class = 'text-left';
                $items_align_class = 'items-start';
                $red_line_margin = 'mr-auto'; // removed md:mx-0 constraint to strictly follow setting
                break;
        }

        // Fetch posts
        $posts = $this->get_instagram_posts();

        if (is_wp_error($posts)) {
            if (current_user_can('manage_options')) {
                return '<div class="text-red-500 p-4">Error: ' . $posts->get_error_message() . '</div>';
            }
            return '';
        }

        ob_start();
        ?>
        <section class="w-full py-16 border-t border-gray-100 dark:border-gray-800" id="nakama-instagram-feed">
            <div class="px-4 max-w-screen-2xl mx-auto">
                <div class="<?php echo $container_class . ' ' . $items_align_class . ' ' . $text_align_class; ?>">
                    <h2 class="text-4xl font-display font-bold mb-2 uppercase section-title force-light-text leading-none">
                        <?php echo esc_html($header_text); ?>
                    </h2>
                    <?php if (!empty($header_subtitle)): ?>
                        <p class="text-lg md:text-xl font-light tracking-wide text-gray-500 dark:text-gray-400 mt-1 mb-2 font-body">
                            <?php echo esc_html($header_subtitle); ?>
                        </p>
                    <?php endif; ?>
                    <span class="pulse-red-text block h-1 w-20 bg-primary mt-2 <?php echo $red_line_margin; ?>"></span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php
                    if (!empty($posts)):
                        foreach ($posts as $post):
                            $image_url = ($post['media_type'] == 'VIDEO' && isset($post['thumbnail_url'])) ? $post['thumbnail_url'] : $post['media_url'];
                            $caption = isset($post['caption']) ? $post['caption'] : '';
                            $permalink = $post['permalink'];
                            ?>
                            <a href="<?php echo esc_url($permalink); ?>" target="_blank"
                                class="group relative block overflow-hidden rounded-sm aspect-square shadow-lg">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(substr($caption, 0, 50)); ?>"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    loading="lazy">
                                <div
                                    class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="material-icons-outlined text-white text-4xl">visibility</span>
                                </div>
                            </a>
                        <?php endforeach;
                    else: ?>
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
