<?php
/**
 * Plugin Name:       Intégration Twitch
 * Description:       Un plugin WordPress pour intégrer des fonctionnalités de Twitch.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            El Beressa
 * Author URI:        https://github.com/eberess
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       mon-plugin-twitch
 */


// Empêcher l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Inclure le fichier d'activation
require_once plugin_dir_path( __FILE__ ) . 'activation.php';

// Hooks d'activation/désactivation
register_activation_hook( __FILE__, 'mpt_activate_plugin' );
register_deactivation_hook( __FILE__, 'mpt_deactivate_plugin' );
register_uninstall_hook( __FILE__, 'mpt_uninstall_plugin' );

// --- SECTION PAGE DE RÉGLAGES ---

// Créer le menu principal dans WordPress
function mpt_ajouter_page_options() {
    // Menu principal
    add_menu_page(
        'Twitch Integration', // Titre de la page
        'Twitch', // Nom du menu
        'manage_options', // Capacité requise
        'twitch-integration', // Slug du menu
        'mpt_page_options_html', // Fonction de callback
        'dashicons-video-alt3', // Icône (caméra vidéo)
        30 // Position dans le menu
    );
    
    // Sous-menu Réglages (renomme la page principale)
    add_submenu_page(
        'twitch-integration',
        'Réglages Twitch',
        'Réglages',
        'manage_options',
        'twitch-integration'
    );
    
    // Sous-menu Analytics (pour plus tard...)
    add_submenu_page(
        'twitch-integration',
        'Analytics Twitch',
        'Analytics',
        'manage_options',
        'twitch-analytics',
        'mpt_page_analytics_html'
    );
    
    // Sous-menu Aide
    add_submenu_page(
        'twitch-integration',
        'Aide & Documentation',
        'Aide',
        'manage_options',
        'twitch-help',
        'mpt_page_help_html'
    );
}
add_action('admin_menu', 'mpt_ajouter_page_options');

// Contenu HTML de la page de réglages
function mpt_page_options_html() {
    // Traitement des actions
    if (isset($_POST['clear_cache'])) {
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        delete_transient('mpt_twitch_data_' . $channel_name);
        echo '<div class="notice notice-success"><p>Cache vidé avec succès !</p></div>';
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('mpt_options_group');
            do_settings_sections('mon-plugin-twitch');
            submit_button('Enregistrer les réglages');
            ?>
        </form>
        
        <hr>
        
        <h2>Outils de débogage</h2>
        <form method="post" style="margin: 20px 0;">
            <input type="submit" name="clear_cache" value="Vider le cache" class="button">
        </form>
        
        <h3>Test des permissions API</h3>
        <?php echo do_shortcode('[twitch_test_api]'); ?>
        
        <h3>Test des données Twitch</h3>
        <div style="background: #f1f1f1; padding: 15px; border-radius: 5px;">
            <?php echo do_shortcode('[twitch_debug]'); ?>
        </div>
        
        <h3>Shortcodes disponibles</h3>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Shortcode</th>
                    <th>Description</th>
                    <th>Options principales</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>[twitch_status_indicateur]</code></td>
                    <td>Indicateur de statut live/hors ligne</td>
                    <td><code>couleur_live="#00ff00" couleur_offline="#ff0000" taille="normale|petite|grande" texte="oui|non" style="defaut|minimal|badge"</code></td>
                </tr>
                <tr>
                    <td><code>[twitch_lecteur_principal]</code></td>
                    <td>Lecteur principal (live ou dernier replay)</td>
                    <td><code>rafraichir="oui" debug="oui"</code></td>
                </tr>
                <tr>
                    <td><code>[twitch_derniers_replays]</code></td>
                    <td>Grille des derniers replays</td>
                    <td><code>nombre="5" colonnes="auto|1|2|3|4" couleur_fond="#f8f9fa" style="defaut|ombre|bordure" afficher_duree="oui|non" taille_image="normale|petite|grande"</code></td>
                </tr>
                <tr>
                    <td><code>[twitch_nombre_replays]</code></td>
                    <td>Affiche le nombre de replays disponibles</td>
                    <td><code>texte="replays disponibles" couleur="#9146ff" style="defaut|badge|encadre"</code></td>
                </tr>
                <tr>
                    <td><code>[twitch_stats_chaine]</code></td>
                    <td>Statistiques de la chaîne</td>
                    <td><code>afficher="tout|replays|live|derniere_activite" style="liste|carte" couleur="#333"</code></td>
                </tr>
                <tr>
                    <td><code>[twitch_dernier_replay]</code></td>
                    <td>Affiche uniquement le dernier replay</td>
                    <td><code>style="carte|minimal" taille="normale|petite|grande" afficher_duree="oui|non" couleur_fond="#f8f9fa"</code></td>
                </tr>
                <tr>
                    <td><code>[twitch_debug]</code></td>
                    <td>Informations de débogage (admin seulement)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><code>[twitch_clips]</code></td>
                    <td>Grille des clips Twitch</td>
                    <td><code>nombre="6" periode="30" tri="vues" style="defaut|ombre|bordure" afficher_vues="oui" afficher_duree="oui"</code></td>
                </tr>
                <tr>
                    <td><code>[twitch_dernier_clip]</code></td>
                    <td>Affiche le dernier clip populaire</td>
                    <td><code>style="carte|minimal" taille="normale|petite|grande" afficher_vues="oui" afficher_duree="oui"</code></td>
                </tr>
                <tr>
                    <td><code>[twitch_stats_avancees]</code></td>
                    <td>Statistiques complètes avec clips</td>
                    <td><code>afficher="tout|live|replays|clips|followers" style="liste|carte" icones="oui|non"</code></td>
                </tr>
                <tr>
                    <td><code>[twitch_notification_live]</code></td>
                    <td>Notification en temps réel si live</td>
                    <td><code>style="banniere|popup|discrete" position="top|bottom" auto_hide="10"</code></td>
                </tr>
                <tr>
                    <td><code>[twitch_debug]</code></td>
                    <td>Informations de débogage (admin seulement)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><code>[twitch_test_api]</code></td>
                    <td>Test des permissions API (admin seulement)</td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>
        
        <h3>Exemples d'utilisation</h3>
        <div style="background: #f1f1f1; padding: 15px; border-radius: 5px;">
            <h4>Indicateur personnalisé :</h4>
            <code>[twitch_status_indicateur couleur_live="#00ff00" couleur_offline="#ff4444" taille="grande" style="badge"]</code>
            
            <h4>Grille de replays avec 3 colonnes :</h4>
            <code>[twitch_derniers_replays nombre="6" colonnes="3" style="ombre" afficher_duree="oui"]</code>
            
            <h4>Compteur de replays en badge :</h4>
            <code>[twitch_nombre_replays style="badge" couleur="#9146ff" texte="vidéos disponibles"]</code>
            
            <h4>Statistiques en carte :</h4>
            <code>[twitch_stats_chaine style="carte" afficher="tout"]</code>
            
            <h4>Dernier replay en format minimal :</h4>
            <code>[twitch_dernier_replay style="minimal" afficher_duree="oui"]</code>
        </div>
    </div>
    <?php
}

// Page Analytics
function mpt_page_analytics_html() {
    ?>
    <div class="wrap">
        <h1>📊 Analytics Twitch</h1>
        
        <div class="mpt-analytics-dashboard">
            <div class="mpt-stats-cards">
                <div class="mpt-stat-card">
                    <h3>Données en temps réel</h3>
                    <?php echo do_shortcode('[twitch_debug]'); ?>
                </div>
            </div>
            
            <div class="mpt-quick-actions">
                <h3>Actions rapides</h3>
                <form method="post" style="margin: 20px 0;">
                    <input type="submit" name="clear_all_cache" value="Vider tout le cache" class="button button-secondary">
                    <input type="submit" name="test_api_connection" value="Tester la connexion API" class="button button-secondary">
                </form>
                
                <?php
                if (isset($_POST['clear_all_cache'])) {
                    global $wpdb;
                    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_mpt_twitch_data_%'");
                    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_mpt_twitch_data_%'");
                    echo '<div class="notice notice-success"><p>✅ Tous les caches ont été vidés !</p></div>';
                }
                
                if (isset($_POST['test_api_connection'])) {
                    echo '<div class="mpt-api-test">';
                    echo '<h4>Test de connexion API</h4>';
                    echo do_shortcode('[twitch_test_api]');
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        
        <style>
        .mpt-analytics-dashboard { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 20px; }
        .mpt-stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .mpt-quick-actions { background: #f8f9fa; padding: 20px; border-radius: 8px; }
        @media (max-width: 768px) { .mpt-analytics-dashboard { grid-template-columns: 1fr; } }
        </style>
    </div>
    <?php
}

// Page Aide
function mpt_page_help_html() {
    ?>
    <div class="wrap">
        <h1>📚 Aide & Documentation</h1>
        
        <div class="mpt-help-content">
            <div class="mpt-help-section">
                <h2>🚀 Démarrage rapide</h2>
                <ol>
                    <li><strong>Créez une application Twitch</strong> sur <a href="https://dev.twitch.tv/console" target="_blank">dev.twitch.tv/console</a></li>
                    <li><strong>Copiez votre Client ID et Client Secret</strong> dans les réglages</li>
                    <li><strong>Entrez le nom de votre chaîne Twitch</strong></li>
                    <li><strong>Testez la connexion</strong> dans l'onglet Analytics</li>
                    <li><strong>Utilisez les widgets Elementor</strong> ou les shortcodes</li>
                </ol>
            </div>
            
            <div class="mpt-help-section">
                <h2>🎨 Widgets Elementor disponibles</h2>
                <div class="mpt-widgets-grid">
                    <div class="mpt-widget-card">
                        <h4>🟢 Indicateur de Statut</h4>
                        <p>Affiche si le stream est en direct ou hors ligne avec personnalisation complète.</p>
                    </div>
                    <div class="mpt-widget-card">
                        <h4>📺 Lecteur Principal</h4>
                        <p>Lecteur intelligent qui affiche le live ou le dernier replay automatiquement.</p>
                    </div>
                    <div class="mpt-widget-card">
                        <h4>📹 Grille des Replays</h4>
                        <p>Grille personnalisable des derniers replays avec filtres et styles.</p>
                    </div>
                    <div class="mpt-widget-card">
                        <h4>🎬 Clips Twitch</h4>
                        <p>Affichage des clips populaires avec tri par vues ou date.</p>
                    </div>
                    <div class="mpt-widget-card">
                        <h4>🔢 Compteur</h4>
                        <p>Compteurs animés pour replays, clips, vues avec styles multiples.</p>
                    </div>
                    <div class="mpt-widget-card">
                        <h4>📊 Statistiques</h4>
                        <p>Dashboard de statistiques avec métriques en temps réel.</p>
                    </div>
                    <div class="mpt-widget-card">
                        <h4>⏰ Countdown</h4>
                        <p>Compte à rebours vers vos prochains streams ou événements.</p>
                    </div>
                    <div class="mpt-widget-card">
                        <h4>🎯 Dernier Replay</h4>
                        <p>Mise en avant du dernier contenu avec call-to-action optimisé.</p>
                    </div>
                </div>
            </div>
            
            <div class="mpt-help-section">
                <h2>🔧 Résolution de problèmes</h2>
                <div class="mpt-troubleshooting">
                    <details>
                        <summary><strong>❌ "Aucune donnée récupérée"</strong></summary>
                        <ul>
                            <li>Vérifiez votre Client ID et Client Secret</li>
                            <li>Assurez-vous que le nom de chaîne est correct</li>
                            <li>Testez la connexion API dans Analytics</li>
                            <li>Videz le cache et réessayez</li>
                        </ul>
                    </details>
                    
                    <details>
                        <summary><strong>📹 "Aucun replay disponible"</strong></summary>
                        <ul>
                            <li>Vérifiez que votre chaîne a des VODs sauvegardées</li>
                            <li>Les replays expirent automatiquement sur Twitch</li>
                            <li>Certains streamers désactivent la sauvegarde</li>
                        </ul>
                    </details>
                    
                    <details>
                        <summary><strong>🎨 "Les styles ne s'appliquent pas"</strong></summary>
                        <ul>
                            <li>Videz le cache de votre plugin de cache</li>
                            <li>Vérifiez les conflits avec votre thème</li>
                            <li>Utilisez l'inspecteur pour voir les CSS appliqués</li>
                        </ul>
                    </details>
                </div>
            </div>
        </div>
        
        <style>
        .mpt-help-content { max-width: 1200px; }
        .mpt-help-section { background: white; padding: 30px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .mpt-widgets-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .mpt-widget-card { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #9146ff; }
        .mpt-widget-card h4 { margin-top: 0; color: #9146ff; }
        .mpt-troubleshooting details { margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 6px; }
        .mpt-troubleshooting summary { cursor: pointer; font-weight: bold; margin-bottom: 10px; }
        </style>
    </div>
    <?php
}

// Enregistrer les champs
function mpt_enregistrer_reglages() {
    register_setting('mpt_options_group', 'mpt_settings');
    register_setting('mpt_options_group', 'mpt_notifications');
    
    // Section API
    add_settings_section('mpt_section_api', 'Réglages de l\'API Twitch', null, 'mon-plugin-twitch');
    add_settings_field('mpt_twitch_channel', 'Nom de la chaîne Twitch', 'mpt_field_channel_html', 'mon-plugin-twitch', 'mpt_section_api');
    add_settings_field('mpt_twitch_client_id', 'Twitch Client ID', 'mpt_field_client_id_html', 'mon-plugin-twitch', 'mpt_section_api');
    add_settings_field('mpt_twitch_client_secret', 'Twitch Client Secret', 'mpt_field_client_secret_html', 'mon-plugin-twitch', 'mpt_section_api');
    
    // Section Notifications
    add_settings_section('mpt_section_notifications', 'Notifications en temps réel', 'mpt_section_notifications_desc', 'mon-plugin-twitch');
    add_settings_field('mpt_notifications_live', 'Notifications Live', 'mpt_field_notifications_live_html', 'mon-plugin-twitch', 'mpt_section_notifications');
    add_settings_field('mpt_webhook_discord', 'Webhook Discord', 'mpt_field_webhook_discord_html', 'mon-plugin-twitch', 'mpt_section_notifications');
    add_settings_field('mpt_email_notifications', 'Email de notification', 'mpt_field_email_notifications_html', 'mon-plugin-twitch', 'mpt_section_notifications');
    
    // Section Thèmes
    add_settings_section('mpt_section_themes', 'Thèmes et Apparence', 'mpt_section_themes_desc', 'mon-plugin-twitch');
    add_settings_field('mpt_theme_preset', 'Thème prédéfini', 'mpt_field_theme_preset_html', 'mon-plugin-twitch', 'mpt_section_themes');
    add_settings_field('mpt_custom_css', 'CSS personnalisé', 'mpt_field_custom_css_html', 'mon-plugin-twitch', 'mpt_section_themes');
}
add_action('admin_init', 'mpt_enregistrer_reglages');

// Affichage des champs HTML
function mpt_field_channel_html() {
    $options = get_option('mpt_settings');
    echo '<input type="text" name="mpt_settings[twitch_channel]" value="' . esc_attr($options['twitch_channel'] ?? '') . '">';
}
function mpt_field_client_id_html() {
    $options = get_option('mpt_settings');
    echo '<input type="text" name="mpt_settings[twitch_client_id]" value="' . esc_attr($options['twitch_client_id'] ?? '') . '" size="50">';
}
function mpt_field_client_secret_html() {
    $options = get_option('mpt_settings');
    echo '<input type="password" name="mpt_settings[twitch_client_secret]" value="' . esc_attr($options['twitch_client_secret'] ?? '') . '" size="50">';
}

// Fonctions pour les notifications
function mpt_section_notifications_desc() {
    echo '<p>Configurez les notifications automatiques quand votre stream commence.</p>';
}

function mpt_field_notifications_live_html() {
    $options = get_option('mpt_notifications', []);
    $checked = isset($options['live_notifications']) && $options['live_notifications'] ? 'checked' : '';
    echo '<input type="checkbox" name="mpt_notifications[live_notifications]" value="1" ' . $checked . '>';
    echo '<label> Activer les notifications quand le stream commence</label>';
}

function mpt_field_webhook_discord_html() {
    $options = get_option('mpt_notifications', []);
    echo '<input type="url" name="mpt_notifications[discord_webhook]" value="' . esc_attr($options['discord_webhook'] ?? '') . '" size="70" placeholder="https://discord.com/api/webhooks/...">';
    echo '<p class="description">URL du webhook Discord pour recevoir les notifications</p>';
}

function mpt_field_email_notifications_html() {
    $options = get_option('mpt_notifications', []);
    echo '<input type="email" name="mpt_notifications[notification_email]" value="' . esc_attr($options['notification_email'] ?? '') . '" size="50" placeholder="admin@monsite.com">';
    echo '<p class="description">Email pour recevoir les notifications de stream</p>';
}

// Fonctions pour les thèmes
function mpt_section_themes_desc() {
    echo '<p>Personnalisez l\'apparence de vos widgets Twitch avec des thèmes prédéfinis ou du CSS personnalisé.</p>';
}

function mpt_field_theme_preset_html() {
    $options = get_option('mpt_settings');
    $current_theme = $options['theme_preset'] ?? 'default';
    
    $themes = [
        'default' => 'Défaut',
        'gaming_dark' => 'Gaming Dark',
        'streamer_pro' => 'Streamer Pro',
        'minimal_clean' => 'Minimal Clean',
        'neon_glow' => 'Neon Glow',
        'retro_arcade' => 'Retro Arcade'
    ];
    
    echo '<select name="mpt_settings[theme_preset]">';
    foreach ($themes as $value => $label) {
        $selected = $current_theme === $value ? 'selected' : '';
        echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
    echo '<p class="description">Choisissez un thème prédéfini qui sera appliqué à tous vos widgets</p>';
}

function mpt_field_custom_css_html() {
    $options = get_option('mpt_settings');
    $custom_css = $options['custom_css'] ?? '';
    echo '<textarea name="mpt_settings[custom_css]" rows="10" cols="70" placeholder="/* Votre CSS personnalisé ici */">' . esc_textarea($custom_css) . '</textarea>';
    echo '<p class="description">CSS personnalisé qui sera ajouté après le thème sélectionné</p>';
}

// --- SECTION CENTRALE ---

/**
 * Fonction de débogage pour afficher les données Twitch
 */
function mpt_debug_twitch_data() {
    if (!current_user_can('manage_options')) {
        return 'Accès refusé';
    }
    
    $twitch_data = mpt_get_twitch_data();
    ob_start();
    echo '<pre style="background: #f1f1f1; padding: 15px; border-radius: 5px; overflow: auto; max-height: 500px;">';
    echo 'Données Twitch Debug:' . "\n";
    echo '=====================' . "\n";
    if ($twitch_data) {
        echo 'User ID: ' . ($twitch_data['user_id'] ?? 'NON TROUVÉ') . "\n";
        echo 'Status Live: ' . ($twitch_data['is_live'] ? 'OUI' : 'NON') . "\n";
        echo 'Nombre de replays: ' . count($twitch_data['replays_list']) . "\n";
        echo 'Dernier replay: ' . ($twitch_data['latest_replay'] ? 'OUI' : 'NON') . "\n";
        
        if (!empty($twitch_data['debug_info'])) {
            echo "\n" . 'Informations de debug:' . "\n";
            echo '----------------------' . "\n";
            foreach ($twitch_data['debug_info'] as $info) {
                echo '- ' . $info . "\n";
            }
        }
        
        if (!empty($twitch_data['replays_list'])) {
            echo "\n" . 'Liste des replays:' . "\n";
            echo '-------------------' . "\n";
            foreach ($twitch_data['replays_list'] as $i => $replay) {
                echo ($i + 1) . '. ' . $replay->title . ' (' . $replay->type . ')' . "\n";
                echo '   ID: ' . $replay->id . "\n";
                echo '   Date: ' . $replay->created_at . "\n";
                echo '   Durée: ' . $replay->duration . "\n";
                echo '   URL: ' . $replay->url . "\n\n";
            }
        }
        
        echo "\n" . 'Données complètes:' . "\n";
        echo '==================' . "\n";
        print_r($twitch_data);
    } else {
        echo 'ERREUR: Aucune donnée récupérée' . "\n";
        echo 'Vérifiez les logs WordPress pour plus de détails.';
    }
    echo '</pre>';
    return ob_get_clean();
}
add_shortcode('twitch_debug', 'mpt_debug_twitch_data');

/**
 * Fonction pour tester les permissions de l'API Twitch
 */
function mpt_test_api_permissions() {
    if (!current_user_can('manage_options')) {
        return 'Accès refusé';
    }
    
    $options = get_option('mpt_settings');
    $client_id = $options['twitch_client_id'] ?? '';
    $client_secret = $options['twitch_client_secret'] ?? '';
    $channel_name = $options['twitch_channel'] ?? '';
    
    if (empty($client_id) || empty($client_secret) || empty($channel_name)) {
        return '<p style="color: red;">Configuration incomplète. Vérifiez vos paramètres.</p>';
    }
    
    // Test du token
    $token_response = wp_remote_post('https://id.twitch.tv/oauth2/token', [
        'body' => ['client_id' => $client_id, 'client_secret' => $client_secret, 'grant_type' => 'client_credentials']
    ]);
    
    if (is_wp_error($token_response) || 200 !== wp_remote_retrieve_response_code($token_response)) {
        return '<p style="color: red;">Erreur d\'authentification. Vérifiez vos Client ID et Client Secret.</p>';
    }
    
    $token_body = json_decode(wp_remote_retrieve_body($token_response));
    $access_token = $token_body->access_token;
    
    ob_start();
    echo '<div style="background: #f9f9f9; padding: 15px; border-radius: 5px;">';
    echo '<h4>Test des permissions API Twitch</h4>';
    
    // Test validation du token
    $validate_response = wp_remote_get('https://id.twitch.tv/oauth2/validate', [
        'headers' => ['Authorization' => 'Bearer ' . $access_token]
    ]);
    
    if (!is_wp_error($validate_response) && 200 === wp_remote_retrieve_response_code($validate_response)) {
        $validate_body = json_decode(wp_remote_retrieve_body($validate_response));
        echo '<p style="color: green;">✓ Token valide</p>';
        echo '<p>Scopes disponibles: ' . implode(', ', $validate_body->scopes ?? []) . '</p>';
    } else {
        echo '<p style="color: red;">✗ Token invalide</p>';
    }
    
    // Test récupération utilisateur
    $user_response = wp_remote_get('https://api.twitch.tv/helix/users?login=' . $channel_name, [
        'headers' => ['Client-ID' => $client_id, 'Authorization' => 'Bearer ' . $access_token]
    ]);
    
    if (!is_wp_error($user_response) && 200 === wp_remote_retrieve_response_code($user_response)) {
        $user_body = json_decode(wp_remote_retrieve_body($user_response));
        if (!empty($user_body->data)) {
            echo '<p style="color: green;">✓ Utilisateur trouvé: ' . $user_body->data[0]->display_name . ' (ID: ' . $user_body->data[0]->id . ')</p>';
        } else {
            echo '<p style="color: red;">✗ Utilisateur non trouvé</p>';
        }
    } else {
        echo '<p style="color: red;">✗ Erreur API utilisateur</p>';
    }
    
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('twitch_test_api', 'mpt_test_api_permissions');

/**
 * Récupère les données de Twitch (live et vidéos) et les met en cache.
 * Ajoute un mécanisme de débogage pour les erreurs d'API.
 * @return array Les données formatées de Twitch.
 */
function mpt_get_twitch_data() {
    $options = get_option('mpt_settings');
    $channel_name = $options['twitch_channel'] ?? '';

    if (empty($channel_name)) {
        error_log('MPT Twitch Plugin Error: Nom de la chaîne Twitch non configuré.');
        return null;
    }

    $cached_data = get_transient('mpt_twitch_data_' . $channel_name);
    if (false !== $cached_data) {
        return $cached_data;
    }

    $client_id = $options['twitch_client_id'] ?? '';
    $client_secret = $options['twitch_client_secret'] ?? '';
    if (empty($client_id) || empty($client_secret)) {
        error_log('MPT Twitch Plugin Error: Client ID ou Client Secret manquant.');
        return null;
    }
    
    // Étape 1 : Récupération du jeton d'authentification (access token)
    $token_response = wp_remote_post('https://id.twitch.tv/oauth2/token', [
        'body' => ['client_id' => $client_id, 'client_secret' => $client_secret, 'grant_type' => 'client_credentials']
    ]);

    if (is_wp_error($token_response) || 200 !== wp_remote_retrieve_response_code($token_response)) {
        error_log('MPT Twitch Plugin Error: Échec de la récupération du jeton d\'authentification. Réponse API : ' . wp_remote_retrieve_body($token_response));
        return null;
    }

    $token_body = json_decode(wp_remote_retrieve_body($token_response));
    $access_token = $token_body->access_token;
    $twitch_data = [
        'is_live'       => false,
        'stream_info'   => null,
        'latest_replay' => null,
        'replays_list'  => [],
        'clips_list'    => [],
        'latest_clip'   => null,
        'user_info'     => null,
        'user_id'       => null,
        'debug_info'    => [],
        'followers_count' => 0,
        'total_views'   => 0
    ];

    // Étape 2 : Récupération des informations utilisateur pour obtenir l'ID
    $user_response = wp_remote_get('https://api.twitch.tv/helix/users?login=' . $channel_name, [
        'headers' => ['Client-ID' => $client_id, 'Authorization' => 'Bearer ' . $access_token]
    ]);

    if (!is_wp_error($user_response) && 200 === wp_remote_retrieve_response_code($user_response)) {
        $user_body = json_decode(wp_remote_retrieve_body($user_response));
        if (!empty($user_body->data)) {
            $user_data = $user_body->data[0];
            $user_id = $user_data->id;
            $twitch_data['user_id'] = $user_id;
            $twitch_data['user_info'] = $user_data;
            $twitch_data['followers_count'] = $user_data->view_count ?? 0;
            $twitch_data['total_views'] = $user_data->view_count ?? 0;
            $twitch_data['debug_info'][] = 'User ID trouvé: ' . $user_id;
            error_log('MPT Twitch Plugin Info: User ID trouvé pour ' . $channel_name . ': ' . $user_id);
        } else {
            error_log('MPT Twitch Plugin Error: Utilisateur non trouvé: ' . $channel_name);
            $twitch_data['debug_info'][] = 'Erreur: Utilisateur non trouvé';
            return $twitch_data;
        }
    } else {
        error_log('MPT Twitch Plugin Error: Échec de la récupération des infos utilisateur. Réponse API : ' . wp_remote_retrieve_body($user_response));
        $twitch_data['debug_info'][] = 'Erreur API utilisateur: ' . wp_remote_retrieve_response_code($user_response);
        return $twitch_data;
    }

    // Étape 3 : Récupération du statut live avec user_id
    $live_response = wp_remote_get('https://api.twitch.tv/helix/streams?user_id=' . $user_id, [
        'headers' => ['Client-ID' => $client_id, 'Authorization' => 'Bearer ' . $access_token]
    ]);

    if (!is_wp_error($live_response) && 200 === wp_remote_retrieve_response_code($live_response)) {
        $live_body = json_decode(wp_remote_retrieve_body($live_response));
        if (!empty($live_body->data)) {
            $twitch_data['is_live'] = true;
            $twitch_data['stream_info'] = $live_body->data[0];
            $twitch_data['debug_info'][] = 'Live détecté';
        } else {
            $twitch_data['debug_info'][] = 'Pas de live en cours';
        }
    } else {
        error_log('MPT Twitch Plugin Error: Échec de la récupération du statut live. Réponse API : ' . wp_remote_retrieve_body($live_response));
        $twitch_data['debug_info'][] = 'Erreur API live: ' . wp_remote_retrieve_response_code($live_response);
    }

    // Étape 4 : Récupération des vidéos avec user_id
    $video_types = ['archive', 'highlight', 'upload'];
    $all_videos = [];
    
    foreach ($video_types as $type) {
        $replays_response = wp_remote_get('https://api.twitch.tv/helix/videos?user_id=' . $user_id . '&first=20&type=' . $type, [
            'headers' => ['Client-ID' => $client_id, 'Authorization' => 'Bearer ' . $access_token]
        ]);

        if (!is_wp_error($replays_response) && 200 === wp_remote_retrieve_response_code($replays_response)) {
            $replays_body = json_decode(wp_remote_retrieve_body($replays_response));
            if (!empty($replays_body->data)) {
                $all_videos = array_merge($all_videos, $replays_body->data);
                $count = count($replays_body->data);
                error_log('MPT Twitch Plugin Info: Trouvé ' . $count . ' vidéos de type ' . $type);
                $twitch_data['debug_info'][] = 'Type ' . $type . ': ' . $count . ' vidéos';
            } else {
                $twitch_data['debug_info'][] = 'Type ' . $type . ': 0 vidéos';
            }
        } else {
            $error_code = wp_remote_retrieve_response_code($replays_response);
            $error_body = wp_remote_retrieve_body($replays_response);
            error_log('MPT Twitch Plugin Error: Échec de la récupération des vidéos type ' . $type . '. Code: ' . $error_code . ', Réponse: ' . $error_body);
            $twitch_data['debug_info'][] = 'Erreur type ' . $type . ': ' . $error_code;
        }
    }
    
    // Étape 5 : Essayer aussi avec l'ancienne méthode (user_login) au cas où
    if (empty($all_videos)) {
        $twitch_data['debug_info'][] = 'Tentative avec user_login...';
        foreach ($video_types as $type) {
            $replays_response = wp_remote_get('https://api.twitch.tv/helix/videos?user_login=' . $channel_name . '&first=20&type=' . $type, [
                'headers' => ['Client-ID' => $client_id, 'Authorization' => 'Bearer ' . $access_token]
            ]);

            if (!is_wp_error($replays_response) && 200 === wp_remote_retrieve_response_code($replays_response)) {
                $replays_body = json_decode(wp_remote_retrieve_body($replays_response));
                if (!empty($replays_body->data)) {
                    $all_videos = array_merge($all_videos, $replays_body->data);
                    $count = count($replays_body->data);
                    $twitch_data['debug_info'][] = 'user_login ' . $type . ': ' . $count . ' vidéos';
                }
            }
        }
    }
    
    if (!empty($all_videos)) {
        // Trier par date de création (plus récent en premier)
        usort($all_videos, function($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });
        
        $twitch_data['replays_list'] = array_slice($all_videos, 0, 8); // Garder les 8 plus récents
        $twitch_data['latest_replay'] = $all_videos[0];
        $total_count = count($all_videos);
        error_log('MPT Twitch Plugin Info: Total de ' . $total_count . ' vidéos trouvées, gardé les 8 plus récentes.');
        $twitch_data['debug_info'][] = 'Total final: ' . $total_count . ' vidéos (gardé 8)';
    } else {
        error_log('MPT Twitch Plugin Info: Aucune vidéo trouvée pour la chaîne ' . $channel_name . ' (ID: ' . $user_id . ')');
        $twitch_data['debug_info'][] = 'Aucune vidéo trouvée';
    }
    
    // Étape 6 : Récupération des clips
    $clips_response = wp_remote_get('https://api.twitch.tv/helix/clips?broadcaster_id=' . $user_id . '&first=10&started_at=' . date('c', strtotime('-30 days')), [
        'headers' => ['Client-ID' => $client_id, 'Authorization' => 'Bearer ' . $access_token]
    ]);

    if (!is_wp_error($clips_response) && 200 === wp_remote_retrieve_response_code($clips_response)) {
        $clips_body = json_decode(wp_remote_retrieve_body($clips_response));
        if (!empty($clips_body->data)) {
            // Trier par nombre de vues (plus populaires en premier)
            $clips_sorted = $clips_body->data;
            usort($clips_sorted, function($a, $b) {
                return $b->view_count - $a->view_count;
            });
            
            $twitch_data['clips_list'] = array_slice($clips_sorted, 0, 8);
            $twitch_data['latest_clip'] = $clips_sorted[0];
            $clips_count = count($clips_body->data);
            error_log('MPT Twitch Plugin Info: ' . $clips_count . ' clips trouvés');
            $twitch_data['debug_info'][] = 'Clips trouvés: ' . $clips_count;
        } else {
            $twitch_data['debug_info'][] = 'Aucun clip trouvé';
        }
    } else {
        error_log('MPT Twitch Plugin Error: Échec de la récupération des clips. Réponse API : ' . wp_remote_retrieve_body($clips_response));
        $twitch_data['debug_info'][] = 'Erreur récupération clips: ' . wp_remote_retrieve_response_code($clips_response);
    }
    
    set_transient('mpt_twitch_data_' . $channel_name, $twitch_data, 300);
    return $twitch_data;
}

// --- SECTION DES SHORTCODES (VERSION 2 AVEC PURGE DU CACHE) ---

function mpt_shortcode_status_indicateur($atts) {
    // Attributs par défaut
    $atts = shortcode_atts([
        'rafraichir' => 'non',
        'couleur_live' => '#00ff00',
        'couleur_offline' => '#ff0000',
        'taille' => 'normale',
        'texte' => 'oui',
        'style' => 'defaut'
    ], $atts);
    
    // On vérifie si l'attribut 'rafraichir' est utilisé
    if ($atts['rafraichir'] === 'oui') {
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        delete_transient('mpt_twitch_data_' . $channel_name);
    }
    
    $twitch_data = mpt_get_twitch_data();
    $is_live = $twitch_data && $twitch_data['is_live'];
    $status_class = $is_live ? 'mpt-dot-green' : 'mpt-dot-red';
    $status_title = $is_live ? 'En direct !' : 'Hors ligne';
    $status_text = $is_live ? 'LIVE' : 'OFFLINE';
    
    // Classes CSS selon la taille
    $size_class = '';
    switch ($atts['taille']) {
        case 'petite': $size_class = 'mpt-size-small'; break;
        case 'grande': $size_class = 'mpt-size-large'; break;
        default: $size_class = 'mpt-size-normal';
    }
    
    // Style personnalisé
    $custom_style = '';
    if ($atts['style'] === 'minimal') {
        $custom_style = 'mpt-style-minimal';
    } elseif ($atts['style'] === 'badge') {
        $custom_style = 'mpt-style-badge';
    }
    
    ob_start();
    ?>
    <style>
    .mpt-status-wrapper.<?php echo $size_class; ?> {
        padding: <?php echo $atts['taille'] === 'petite' ? '4px 8px' : ($atts['taille'] === 'grande' ? '12px 16px' : '8px 12px'); ?>;
    }
    .mpt-status-wrapper.<?php echo $size_class; ?> svg {
        width: <?php echo $atts['taille'] === 'petite' ? '16px' : ($atts['taille'] === 'grande' ? '24px' : '20px'); ?>;
        height: <?php echo $atts['taille'] === 'petite' ? '16px' : ($atts['taille'] === 'grande' ? '24px' : '20px'); ?>;
    }
    .mpt-status-wrapper .mpt-dot-green { background-color: <?php echo esc_attr($atts['couleur_live']); ?>; }
    .mpt-status-wrapper .mpt-dot-red { background-color: <?php echo esc_attr($atts['couleur_offline']); ?>; }
    .mpt-style-minimal { background: transparent !important; padding: 4px !important; }
    .mpt-style-badge { border-radius: 20px; font-weight: bold; }
    .mpt-status-text { margin-left: 8px; font-weight: bold; font-size: 12px; color: white; }
    </style>
    <a href="https://twitch.tv/<?php $options = get_option('mpt_settings'); echo esc_attr($options['twitch_channel'] ?? ''); ?>" target="_blank" rel="noopener" class="mpt-status-wrapper <?php echo $size_class; ?> <?php echo $custom_style; ?>" title="<?php echo $status_title; ?>">
        <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0h1.714v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/></svg>
        <div class="mpt-dot <?php echo $status_class; ?>"></div>
        <?php if ($atts['texte'] === 'oui') : ?>
            <span class="mpt-status-text"><?php echo $status_text; ?></span>
        <?php endif; ?>
    </a>
    <?php
    return ob_get_clean();
}
add_shortcode('twitch_status_indicateur', 'mpt_shortcode_status_indicateur');

function mpt_shortcode_lecteur_principal($atts) {
    if (isset($atts['rafraichir']) && $atts['rafraichir'] === 'oui') {
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        delete_transient('mpt_twitch_data_' . $channel_name);
    }
    
    $twitch_data = mpt_get_twitch_data();
    $options = get_option('mpt_settings');
    $channel_name = $options['twitch_channel'] ?? '';
    $parent_domain = str_replace(['http://', 'https://'], '', get_home_url());
    
    // Mode debug
    $debug_mode = isset($atts['debug']) && $atts['debug'] === 'oui' && current_user_can('manage_options');
    
    ob_start();
    ?>
    <div class="mpt-player-wrapper">
    <?php if ($twitch_data && $twitch_data['is_live']) : ?>
        <iframe src="https://player.twitch.tv/?channel=<?php echo esc_attr($channel_name); ?>&parent=<?php echo esc_attr($parent_domain); ?>" frameborder="0" allowfullscreen="true" scrolling="no"></iframe>
        <?php if ($debug_mode) : ?>
            <p style="background: green; color: white; padding: 5px; margin: 5px 0;">DEBUG: Live actif</p>
        <?php endif; ?>
    <?php elseif ($twitch_data && $twitch_data['latest_replay']) : ?>
        <iframe src="https://player.twitch.tv/?video=<?php echo esc_attr($twitch_data['latest_replay']->id); ?>&parent=<?php echo esc_attr($parent_domain); ?>" frameborder="0" allowfullscreen="true" scrolling="no"></iframe>
        <?php if ($debug_mode) : ?>
            <p style="background: blue; color: white; padding: 5px; margin: 5px 0;">DEBUG: Replay - <?php echo esc_html($twitch_data['latest_replay']->title); ?></p>
        <?php endif; ?>
    <?php else: ?>
        <div style="text-align:center; color:white; padding:25% 20px; background: #333;">
            <p>Le contenu est indisponible pour le moment.</p>
            <?php if ($debug_mode) : ?>
                <p style="background: red; color: white; padding: 10px; margin: 10px 0; font-size: 12px;">
                    DEBUG: Pas de live, pas de replay<br>
                    Données: <?php echo $twitch_data ? 'Récupérées' : 'NULL'; ?><br>
                    <?php if ($twitch_data) : ?>
                        Live: <?php echo $twitch_data['is_live'] ? 'OUI' : 'NON'; ?><br>
                        Replays: <?php echo count($twitch_data['replays_list']); ?><br>
                    <?php endif; ?>
                    Chaîne: <?php echo esc_html($channel_name); ?><br>
                    Domaine: <?php echo esc_html($parent_domain); ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('twitch_lecteur_principal', 'mpt_shortcode_lecteur_principal');

function mpt_shortcode_derniers_replays($atts) {
    // Attributs par défaut
    $atts = shortcode_atts([
        'rafraichir' => 'non',
        'debug' => 'non',
        'nombre' => '5',
        'colonnes' => 'auto',
        'couleur_fond' => '#f8f9fa',
        'couleur_texte' => '#333',
        'style' => 'defaut',
        'afficher_duree' => 'oui',
        'afficher_type' => 'oui',
        'taille_image' => 'normale'
    ], $atts);
    
    if ($atts['rafraichir'] === 'oui') {
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        delete_transient('mpt_twitch_data_' . $channel_name);
    }
    
    $twitch_data = mpt_get_twitch_data();
    $debug_mode = $atts['debug'] === 'oui' && current_user_can('manage_options');
    
    if (!$twitch_data || empty($twitch_data['replays_list'])) {
        ob_start();
        ?>
        <div class="mpt-no-replays" style="padding: 20px; background: <?php echo esc_attr($atts['couleur_fond']); ?>; border-radius: 8px; text-align: center; color: <?php echo esc_attr($atts['couleur_texte']); ?>;">
            <p>Aucun replay disponible.</p>
            <?php if ($debug_mode) : ?>
                <p style="background: orange; color: white; padding: 10px; margin: 10px 0; font-size: 12px;">
                    DEBUG: <?php echo $twitch_data ? 'Données récupérées mais liste vide' : 'Aucune donnée récupérée'; ?><br>
                    <?php if ($twitch_data) : ?>
                        Nombre de replays: <?php echo count($twitch_data['replays_list']); ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Limiter le nombre de replays
    $nombre_replays = min(intval($atts['nombre']), count($twitch_data['replays_list']));
    $replays_to_show = array_slice($twitch_data['replays_list'], 0, $nombre_replays);
    
    // Définir les colonnes
    $grid_columns = 'repeat(auto-fit, minmax(300px, 1fr))';
    if ($atts['colonnes'] !== 'auto') {
        $grid_columns = 'repeat(' . intval($atts['colonnes']) . ', 1fr)';
    }
    
    // Taille des images
    $image_size = ['320', '180'];
    if ($atts['taille_image'] === 'petite') {
        $image_size = ['240', '135'];
    } elseif ($atts['taille_image'] === 'grande') {
        $image_size = ['480', '270'];
    }
    
    ob_start();
    ?>
    <style>
    .mpt-replays-grid-custom {
        display: grid;
        grid-template-columns: <?php echo $grid_columns; ?>;
        gap: 20px;
        margin: 20px 0;
    }
    .mpt-replay-card-custom {
        display: block;
        background: <?php echo esc_attr($atts['couleur_fond']); ?>;
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
        color: <?php echo esc_attr($atts['couleur_texte']); ?>;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        <?php if ($atts['style'] === 'ombre') : ?>
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        <?php elseif ($atts['style'] === 'bordure') : ?>
        border: 2px solid #9146ff;
        <?php endif; ?>
    }
    .mpt-replay-card-custom:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        text-decoration: none;
        color: <?php echo esc_attr($atts['couleur_texte']); ?>;
    }
    .mpt-replay-duration {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
    }
    .mpt-replay-type-badge {
        display: inline-block;
        background: #9146ff;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        margin-right: 8px;
        text-transform: uppercase;
    }
    </style>
    
    <div class="mpt-replays-grid-custom">
        <?php if ($debug_mode) : ?>
            <p style="background: green; color: white; padding: 10px; margin: 10px 0; grid-column: 1/-1; border-radius: 4px;">
                DEBUG: <?php echo count($twitch_data['replays_list']); ?> replays trouvés, affichage de <?php echo $nombre_replays; ?>
            </p>
        <?php endif; ?>
        
        <?php foreach ($replays_to_show as $replay) : 
            $thumbnail_url = str_replace(['%{width}', '%{height}'], $image_size, $replay->thumbnail_url);
            
            // Convertir la durée en format lisible
            $duration_formatted = '';
            if ($atts['afficher_duree'] === 'oui' && !empty($replay->duration)) {
                $duration = $replay->duration;
                if (preg_match('/(\d+)h(\d+)m(\d+)s/', $duration, $matches)) {
                    $hours = intval($matches[1]);
                    $minutes = intval($matches[2]);
                    if ($hours > 0) {
                        $duration_formatted = $hours . 'h' . $minutes . 'm';
                    } else {
                        $duration_formatted = $minutes . 'm';
                    }
                }
            }
            ?>
            <a href="<?php echo esc_url($replay->url); ?>" target="_blank" rel="noopener" class="mpt-replay-card-custom">
                <div style="position: relative;">
                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($replay->title); ?>" style="width: 100%; height: auto;">
                    <?php if ($duration_formatted) : ?>
                        <span class="mpt-replay-duration"><?php echo $duration_formatted; ?></span>
                    <?php endif; ?>
                </div>
                <div style="padding: 12px 16px;">
                    <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600; line-height: 1.4;">
                        <?php echo esc_html($replay->title); ?>
                    </h4>
                    <div style="font-size: 14px; color: #666;">
                        <?php if ($atts['afficher_type'] === 'oui') : ?>
                            <span class="mpt-replay-type-badge"><?php echo esc_html($replay->type); ?></span>
                        <?php endif; ?>
                        <span>Publié le <?php echo date_i18n(get_option('date_format'), strtotime($replay->published_at)); ?></span>
                        <?php if ($debug_mode) : ?>
                            <br><strong>ID:</strong> <?php echo esc_html($replay->id); ?>
                            <br><strong>Vues:</strong> <?php echo number_format($replay->view_count ?? 0); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('twitch_derniers_replays', 'mpt_shortcode_derniers_replays');


/**
 * Shortcode pour afficher le nombre de replays
 */
function mpt_shortcode_nombre_replays($atts) {
    $atts = shortcode_atts([
        'rafraichir' => 'non',
        'texte' => 'replays disponibles',
        'couleur' => '#9146ff',
        'style' => 'defaut'
    ], $atts);
    
    if ($atts['rafraichir'] === 'oui') {
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        delete_transient('mpt_twitch_data_' . $channel_name);
    }
    
    $twitch_data = mpt_get_twitch_data();
    $nombre = $twitch_data ? count($twitch_data['replays_list']) : 0;
    
    $style_css = '';
    if ($atts['style'] === 'badge') {
        $style_css = 'background: ' . esc_attr($atts['couleur']) . '; color: white; padding: 4px 12px; border-radius: 20px; font-weight: bold; display: inline-block;';
    } elseif ($atts['style'] === 'encadre') {
        $style_css = 'border: 2px solid ' . esc_attr($atts['couleur']) . '; color: ' . esc_attr($atts['couleur']) . '; padding: 8px 16px; border-radius: 8px; display: inline-block; font-weight: bold;';
    } else {
        $style_css = 'color: ' . esc_attr($atts['couleur']) . '; font-weight: bold;';
    }
    
    return '<span style="' . $style_css . '">' . $nombre . ' ' . esc_html($atts['texte']) . '</span>';
}
add_shortcode('twitch_nombre_replays', 'mpt_shortcode_nombre_replays');

/**
 * Shortcode pour afficher les statistiques de la chaîne
 */
function mpt_shortcode_stats_chaine($atts) {
    $atts = shortcode_atts([
        'rafraichir' => 'non',
        'afficher' => 'tout', // tout, replays, live, derniere_activite
        'style' => 'liste',
        'couleur' => '#333'
    ], $atts);
    
    if ($atts['rafraichir'] === 'oui') {
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        delete_transient('mpt_twitch_data_' . $channel_name);
    }
    
    $twitch_data = mpt_get_twitch_data();
    if (!$twitch_data) {
        return '<p>Impossible de récupérer les statistiques.</p>';
    }
    
    $stats = [];
    
    if ($atts['afficher'] === 'tout' || $atts['afficher'] === 'live') {
        $stats['Statut'] = $twitch_data['is_live'] ? '🔴 En direct' : '⚫ Hors ligne';
    }
    
    if ($atts['afficher'] === 'tout' || $atts['afficher'] === 'replays') {
        $stats['Replays disponibles'] = count($twitch_data['replays_list']);
    }
    
    if (($atts['afficher'] === 'tout' || $atts['afficher'] === 'derniere_activite') && $twitch_data['latest_replay']) {
        $stats['Dernière activité'] = date_i18n(get_option('date_format'), strtotime($twitch_data['latest_replay']->created_at));
    }
    
    ob_start();
    if ($atts['style'] === 'carte') {
        echo '<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; color: ' . esc_attr($atts['couleur']) . ';">';
        echo '<h4 style="margin-top: 0; color: #9146ff;">Statistiques de la chaîne</h4>';
        foreach ($stats as $label => $value) {
            echo '<p style="margin: 8px 0;"><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</p>';
        }
        echo '</div>';
    } else {
        echo '<ul style="list-style: none; padding: 0; color: ' . esc_attr($atts['couleur']) . ';">';
        foreach ($stats as $label => $value) {
            echo '<li style="margin: 8px 0;"><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</li>';
        }
        echo '</ul>';
    }
    return ob_get_clean();
}
add_shortcode('twitch_stats_chaine', 'mpt_shortcode_stats_chaine');

/**
 * Shortcode pour afficher le dernier replay uniquement
 */
function mpt_shortcode_dernier_replay($atts) {
    $atts = shortcode_atts([
        'rafraichir' => 'non',
        'style' => 'carte',
        'taille' => 'normale',
        'afficher_duree' => 'oui',
        'couleur_fond' => '#f8f9fa'
    ], $atts);
    
    if ($atts['rafraichir'] === 'oui') {
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        delete_transient('mpt_twitch_data_' . $channel_name);
    }
    
    $twitch_data = mpt_get_twitch_data();
    if (!$twitch_data || !$twitch_data['latest_replay']) {
        return '<p>Aucun replay récent disponible.</p>';
    }
    
    $replay = $twitch_data['latest_replay'];
    $image_size = $atts['taille'] === 'petite' ? ['240', '135'] : ($atts['taille'] === 'grande' ? ['480', '270'] : ['320', '180']);
    $thumbnail_url = str_replace(['%{width}', '%{height}'], $image_size, $replay->thumbnail_url);
    
    // Durée formatée
    $duration_formatted = '';
    if ($atts['afficher_duree'] === 'oui' && !empty($replay->duration)) {
        $duration = $replay->duration;
        if (preg_match('/(\d+)h(\d+)m(\d+)s/', $duration, $matches)) {
            $hours = intval($matches[1]);
            $minutes = intval($matches[2]);
            $duration_formatted = $hours > 0 ? $hours . 'h' . $minutes . 'm' : $minutes . 'm';
        }
    }
    
    ob_start();
    if ($atts['style'] === 'minimal') {
        ?>
        <a href="<?php echo esc_url($replay->url); ?>" target="_blank" style="display: inline-block; text-decoration: none; color: inherit;">
            <strong><?php echo esc_html($replay->title); ?></strong>
            <?php if ($duration_formatted) : ?>
                <span style="color: #666; margin-left: 8px;">(<?php echo $duration_formatted; ?>)</span>
            <?php endif; ?>
        </a>
        <?php
    } else {
        ?>
        <div style="background: <?php echo esc_attr($atts['couleur_fond']); ?>; border-radius: 8px; overflow: hidden; max-width: <?php echo $image_size[0]; ?>px;">
            <a href="<?php echo esc_url($replay->url); ?>" target="_blank" style="text-decoration: none; color: inherit;">
                <div style="position: relative;">
                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($replay->title); ?>" style="width: 100%; height: auto;">
                    <?php if ($duration_formatted) : ?>
                        <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.8); color: white; padding: 2px 6px; border-radius: 4px; font-size: 12px;">
                            <?php echo $duration_formatted; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div style="padding: 12px;">
                    <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600;">
                        <?php echo esc_html($replay->title); ?>
                    </h4>
                    <p style="margin: 0; color: #666; font-size: 14px;">
                        <?php echo esc_html($replay->type); ?> - 
                        <?php echo date_i18n(get_option('date_format'), strtotime($replay->published_at)); ?>
                    </p>
                </div>
            </a>
        </div>
        <?php
    }
    return ob_get_clean();
}
add_shortcode('twitch_dernier_replay', 'mpt_shortcode_dernier_replay');

/**
 * Shortcode pour afficher les clips Twitch
 */
function mpt_shortcode_clips_twitch($atts) {
    $atts = shortcode_atts([
        'rafraichir' => 'non',
        'nombre' => '6',
        'colonnes' => 'auto',
        'periode' => '30', // jours
        'tri' => 'vues', // vues, date, duree
        'style' => 'defaut',
        'taille_image' => 'normale',
        'afficher_vues' => 'oui',
        'afficher_duree' => 'oui',
        'afficher_createur' => 'non',
        'couleur_fond' => '#f8f9fa'
    ], $atts);
    
    if ($atts['rafraichir'] === 'oui') {
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        delete_transient('mpt_twitch_data_' . $channel_name);
    }
    
    $twitch_data = mpt_get_twitch_data();
    if (!$twitch_data || empty($twitch_data['clips_list'])) {
        return '<p>Aucun clip disponible.</p>';
    }
    
    // Limiter le nombre de clips
    $nombre_clips = min(intval($atts['nombre']), count($twitch_data['clips_list']));
    $clips_to_show = array_slice($twitch_data['clips_list'], 0, $nombre_clips);
    
    // Définir les colonnes
    $grid_columns = 'repeat(auto-fit, minmax(280px, 1fr))';
    if ($atts['colonnes'] !== 'auto') {
        $grid_columns = 'repeat(' . intval($atts['colonnes']) . ', 1fr)';
    }
    
    // Taille des images
    $image_size = $atts['taille_image'] === 'petite' ? ['240', '135'] : ($atts['taille_image'] === 'grande' ? ['480', '270'] : ['320', '180']);
    
    ob_start();
    ?>
    <style>
    .mpt-clips-grid {
        display: grid;
        grid-template-columns: <?php echo $grid_columns; ?>;
        gap: 20px;
        margin: 20px 0;
    }
    .mpt-clip-card {
        display: block;
        background: <?php echo esc_attr($atts['couleur_fond']); ?>;
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        <?php if ($atts['style'] === 'ombre') : ?>
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        <?php elseif ($atts['style'] === 'bordure') : ?>
        border: 2px solid #9146ff;
        <?php endif; ?>
    }
    .mpt-clip-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        text-decoration: none;
        color: inherit;
    }
    .mpt-clip-stats {
        position: absolute;
        top: 8px;
        left: 8px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
    }
    .mpt-clip-duration {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
    }
    .mpt-clip-creator {
        color: #9146ff;
        font-size: 12px;
        font-weight: bold;
    }
    </style>
    
    <div class="mpt-clips-grid">
        <?php foreach ($clips_to_show as $clip) : 
            $thumbnail_url = str_replace(['%{width}', '%{height}'], $image_size, $clip->thumbnail_url); ?>
            <a href="<?php echo esc_url($clip->url); ?>" target="_blank" rel="noopener" class="mpt-clip-card">
                <div style="position: relative;">
                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($clip->title); ?>" style="width: 100%; height: auto;">
                    
                    <?php if ($atts['afficher_vues'] === 'oui') : ?>
                        <span class="mpt-clip-stats">👁 <?php echo number_format($clip->view_count); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($atts['afficher_duree'] === 'oui') : ?>
                        <span class="mpt-clip-duration"><?php echo intval($clip->duration); ?>s</span>
                    <?php endif; ?>
                </div>
                <div style="padding: 12px;">
                    <h4 style="margin: 0 0 8px; font-size: 14px; font-weight: 600; line-height: 1.4;">
                        <?php echo esc_html($clip->title); ?>
                    </h4>
                    <div style="font-size: 12px; color: #666;">
                        <?php if ($atts['afficher_createur'] === 'oui') : ?>
                            <div class="mpt-clip-creator">Par <?php echo esc_html($clip->creator_name); ?></div>
                        <?php endif; ?>
                        <div>Créé le <?php echo date_i18n(get_option('date_format'), strtotime($clip->created_at)); ?></div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('twitch_clips', 'mpt_shortcode_clips_twitch');

/**
 * Shortcode pour afficher le dernier clip
 */
function mpt_shortcode_dernier_clip($atts) {
    $atts = shortcode_atts([
        'rafraichir' => 'non',
        'style' => 'carte',
        'taille' => 'normale',
        'afficher_vues' => 'oui',
        'afficher_duree' => 'oui',
        'couleur_fond' => '#f8f9fa'
    ], $atts);
    
    if ($atts['rafraichir'] === 'oui') {
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        delete_transient('mpt_twitch_data_' . $channel_name);
    }
    
    $twitch_data = mpt_get_twitch_data();
    if (!$twitch_data || !$twitch_data['latest_clip']) {
        return '<p>Aucun clip récent disponible.</p>';
    }
    
    $clip = $twitch_data['latest_clip'];
    $image_size = $atts['taille'] === 'petite' ? ['240', '135'] : ($atts['taille'] === 'grande' ? ['480', '270'] : ['320', '180']);
    $thumbnail_url = str_replace(['%{width}', '%{height}'], $image_size, $clip->thumbnail_url);
    
    ob_start();
    if ($atts['style'] === 'minimal') {
        ?>
        <a href="<?php echo esc_url($clip->url); ?>" target="_blank" style="display: inline-block; text-decoration: none; color: inherit;">
            <strong>🎬 <?php echo esc_html($clip->title); ?></strong>
            <?php if ($atts['afficher_vues'] === 'oui') : ?>
                <span style="color: #666; margin-left: 8px;">(<?php echo number_format($clip->view_count); ?> vues)</span>
            <?php endif; ?>
        </a>
        <?php
    } else {
        ?>
        <div style="background: <?php echo esc_attr($atts['couleur_fond']); ?>; border-radius: 8px; overflow: hidden; max-width: <?php echo $image_size[0]; ?>px;">
            <a href="<?php echo esc_url($clip->url); ?>" target="_blank" style="text-decoration: none; color: inherit;">
                <div style="position: relative;">
                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($clip->title); ?>" style="width: 100%; height: auto;">
                    <?php if ($atts['afficher_vues'] === 'oui') : ?>
                        <span style="position: absolute; top: 8px; left: 8px; background: rgba(0,0,0,0.8); color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                            👁 <?php echo number_format($clip->view_count); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($atts['afficher_duree'] === 'oui') : ?>
                        <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.8); color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                            <?php echo intval($clip->duration); ?>s
                        </span>
                    <?php endif; ?>
                </div>
                <div style="padding: 12px;">
                    <h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600;">
                        <?php echo esc_html($clip->title); ?>
                    </h4>
                    <p style="margin: 0; color: #666; font-size: 14px;">
                        Clip par <?php echo esc_html($clip->creator_name); ?> - 
                        <?php echo date_i18n(get_option('date_format'), strtotime($clip->created_at)); ?>
                    </p>
                </div>
            </a>
        </div>
        <?php
    }
    return ob_get_clean();
}
add_shortcode('twitch_dernier_clip', 'mpt_shortcode_dernier_clip');

/**
 * Shortcode pour afficher les statistiques avancées
 */
function mpt_shortcode_stats_avancees($atts) {
    $atts = shortcode_atts([
        'rafraichir' => 'non',
        'afficher' => 'tout', // tout, live, replays, clips, followers
        'style' => 'carte',
        'couleur' => '#333',
        'icones' => 'oui'
    ], $atts);
    
    if ($atts['rafraichir'] === 'oui') {
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        delete_transient('mpt_twitch_data_' . $channel_name);
    }
    
    $twitch_data = mpt_get_twitch_data();
    if (!$twitch_data) {
        return '<p>Impossible de récupérer les statistiques.</p>';
    }
    
    $stats = [];
    $icons = $atts['icones'] === 'oui';
    
    if ($atts['afficher'] === 'tout' || $atts['afficher'] === 'live') {
        $icon = $icons ? ($twitch_data['is_live'] ? '🔴 ' : '⚫ ') : '';
        $stats['Statut'] = $icon . ($twitch_data['is_live'] ? 'En direct' : 'Hors ligne');
    }
    
    if ($atts['afficher'] === 'tout' || $atts['afficher'] === 'replays') {
        $icon = $icons ? '📹 ' : '';
        $stats['Replays'] = $icon . count($twitch_data['replays_list']) . ' disponibles';
    }
    
    if ($atts['afficher'] === 'tout' || $atts['afficher'] === 'clips') {
        $icon = $icons ? '🎬 ' : '';
        $stats['Clips'] = $icon . count($twitch_data['clips_list']) . ' récents';
    }
    
    if (($atts['afficher'] === 'tout' || $atts['afficher'] === 'followers') && $twitch_data['user_info']) {
        $icon = $icons ? '👥 ' : '';
        $stats['Vues totales'] = $icon . number_format($twitch_data['total_views']);
    }
    
    if (($atts['afficher'] === 'tout') && $twitch_data['latest_replay']) {
        $icon = $icons ? '📅 ' : '';
        $stats['Dernière activité'] = $icon . date_i18n(get_option('date_format'), strtotime($twitch_data['latest_replay']->created_at));
    }
    
    ob_start();
    if ($atts['style'] === 'carte') {
        echo '<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; color: ' . esc_attr($atts['couleur']) . '; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
        echo '<h4 style="margin-top: 0; color: #9146ff; display: flex; align-items: center;"><span style="margin-right: 8px;">📊</span> Statistiques de la chaîne</h4>';
        foreach ($stats as $label => $value) {
            echo '<div style="margin: 12px 0; padding: 8px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">';
            echo '<strong>' . esc_html($label) . ':</strong> <span>' . esc_html($value) . '</span>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div style="color: ' . esc_attr($atts['couleur']) . ';">';
        foreach ($stats as $label => $value) {
            echo '<div style="margin: 8px 0; display: flex; justify-content: space-between; align-items: center;">';
            echo '<strong>' . esc_html($label) . ':</strong> <span>' . esc_html($value) . '</span>';
            echo '</div>';
        }
        echo '</div>';
    }
    return ob_get_clean();
}
add_shortcode('twitch_stats_avancees', 'mpt_shortcode_stats_avancees');

// --- NOTIFICATIONS ---

/**
 * Vérifie les changements de statut et envoie des notifications
 */
function mpt_check_live_status_change() {
    $options = get_option('mpt_settings');
    $notifications = get_option('mpt_notifications', []);
    
    if (empty($notifications['live_notifications'])) {
        return; // Notifications désactivées
    }
    
    $channel_name = $options['twitch_channel'] ?? '';
    if (empty($channel_name)) {
        return;
    }
    
    // Récupérer le statut actuel
    $current_data = mpt_get_twitch_data();
    $is_currently_live = $current_data && $current_data['is_live'];
    
    // Récupérer le dernier statut connu
    $last_status = get_option('mpt_last_live_status', false);
    
    // Si le statut a changé vers "live"
    if ($is_currently_live && !$last_status) {
        mpt_send_live_notifications($current_data);
        update_option('mpt_last_live_status', true);
    } elseif (!$is_currently_live && $last_status) {
        update_option('mpt_last_live_status', false);
    }
}

/**
 * Envoie les notifications quand le stream commence
 */
function mpt_send_live_notifications($stream_data) {
    $notifications = get_option('mpt_notifications', []);
    $options = get_option('mpt_settings');
    $channel_name = $options['twitch_channel'] ?? '';
    
    $stream_info = $stream_data['stream_info'] ?? null;
    $title = $stream_info->title ?? 'Stream en direct';
    $game = $stream_info->game_name ?? 'Jeu non spécifié';
    $viewers = $stream_info->viewer_count ?? 0;
    
    // Notification Discord
    if (!empty($notifications['discord_webhook'])) {
        $discord_message = [
            'embeds' => [[
                'title' => '🔴 ' . $channel_name . ' est maintenant en direct !',
                'description' => $title,
                'color' => 9520895, // Couleur Twitch
                'fields' => [
                    ['name' => 'Jeu', 'value' => $game, 'inline' => true],
                    ['name' => 'Spectateurs', 'value' => number_format($viewers), 'inline' => true]
                ],
                'url' => 'https://twitch.tv/' . $channel_name,
                'timestamp' => date('c'),
                'footer' => ['text' => 'Twitch Notification']
            ]]
        ];
        
        wp_remote_post($notifications['discord_webhook'], [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($discord_message)
        ]);
    }
    
    // Notification Email
    if (!empty($notifications['notification_email'])) {
        $subject = '🔴 ' . $channel_name . ' est en direct !';
        $message = "
        <h2>Stream en direct !</h2>
        <p><strong>Chaîne :</strong> {$channel_name}</p>
        <p><strong>Titre :</strong> {$title}</p>
        <p><strong>Jeu :</strong> {$game}</p>
        <p><strong>Spectateurs :</strong> " . number_format($viewers) . "</p>
        <p><a href='https://twitch.tv/{$channel_name}' target='_blank'>Regarder maintenant</a></p>
        ";
        
        wp_mail($notifications['notification_email'], $subject, $message, ['Content-Type: text/html; charset=UTF-8']);
    }
}

/**
 * Shortcode pour afficher une notification live en temps réel
 */
function mpt_shortcode_notification_live($atts) {
    $atts = shortcode_atts([
        'style' => 'banniere', // banniere, popup, discrete
        'position' => 'top', // top, bottom
        'auto_hide' => '10', // secondes
        'couleur' => '#9146ff'
    ], $atts);
    
    $twitch_data = mpt_get_twitch_data();
    if (!$twitch_data || !$twitch_data['is_live']) {
        return ''; // Pas de notification si pas en live
    }
    
    $stream_info = $twitch_data['stream_info'];
    $channel_name = get_option('mpt_settings')['twitch_channel'] ?? '';
    
    ob_start();
    ?>
    <style>
    .mpt-live-notification {
        position: fixed;
        <?php echo $atts['position']; ?>: 20px;
        right: 20px;
        background: <?php echo esc_attr($atts['couleur']); ?>;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        z-index: 9999;
        max-width: 350px;
        animation: slideIn 0.5s ease-out;
    }
    .mpt-live-notification.discrete {
        padding: 8px 12px;
        font-size: 14px;
    }
    .mpt-live-notification .close-btn {
        position: absolute;
        top: 5px;
        right: 10px;
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
    }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    </style>
    
    <div class="mpt-live-notification <?php echo esc_attr($atts['style']); ?>" id="mpt-live-notif">
        <button class="close-btn" onclick="document.getElementById('mpt-live-notif').style.display='none'">&times;</button>
        <div style="display: flex; align-items: center;">
            <div style="margin-right: 12px; font-size: 20px;">🔴</div>
            <div>
                <strong><?php echo esc_html($channel_name); ?> est en direct !</strong>
                <div style="font-size: 13px; opacity: 0.9; margin-top: 4px;">
                    <?php echo esc_html($stream_info->title ?? 'Stream en cours'); ?>
                </div>
                <a href="https://twitch.tv/<?php echo esc_attr($channel_name); ?>" target="_blank" 
                   style="color: white; text-decoration: underline; font-size: 13px;">
                    Regarder maintenant
                </a>
            </div>
        </div>
    </div>
    
    <?php if ($atts['auto_hide'] > 0) : ?>
    <script>
    setTimeout(function() {
        var notif = document.getElementById('mpt-live-notif');
        if (notif) {
            notif.style.animation = 'slideIn 0.5s ease-out reverse';
            setTimeout(function() { notif.style.display = 'none'; }, 500);
        }
    }, <?php echo intval($atts['auto_hide']) * 1000; ?>);
    </script>
    <?php endif; ?>
    
    <?php
    return ob_get_clean();
}
add_shortcode('twitch_notification_live', 'mpt_shortcode_notification_live');

// Vérifier le statut toutes les 5 minutes
add_action('wp', function() {
    if (!wp_next_scheduled('mpt_check_live_status')) {
        wp_schedule_event(time(), 'mpt_five_minutes', 'mpt_check_live_status');
    }
});

add_action('mpt_check_live_status', 'mpt_check_live_status_change');

// Ajouter un intervalle personnalisé de 5 minutes
add_filter('cron_schedules', function($schedules) {
    $schedules['mpt_five_minutes'] = [
        'interval' => 300,
        'display' => 'Toutes les 5 minutes'
    ];
    return $schedules;
});

// --- SECTION D'INTÉGRATION ELEMENTOR ---

// --- SECTION D'INTÉGRATION ELEMENTOR (SEULEMENT SI ELEMENTOR EST ACTIF) ---

/**
 * Vérifie si Elementor est actif et charge les widgets
 */
function mpt_init_elementor_widgets() {
    // Vérifier si Elementor est installé et actif
    if ( ! did_action( 'elementor/loaded' ) ) {
        return;
    }

    // Ajouter les actions Elementor seulement si Elementor est disponible
    add_action( 'elementor/elements/categories_registered', 'mpt_add_elementor_widget_categories' );
    add_action( 'elementor/widgets/register', 'mpt_register_elementor_widgets' );
}
add_action( 'plugins_loaded', 'mpt_init_elementor_widgets' );

/**
 * Ajoute une catégorie personnalisée pour les widgets Twitch
 */
function mpt_add_elementor_widget_categories( $elements_manager ) {
    $elements_manager->add_category(
        'twitch-widgets',
        [
            'title' => 'Twitch',
            'icon' => 'fa fa-plug',
        ]
    );
}

/**
 * Enregistre les widgets personnalisés pour Elementor.
 */
function mpt_register_elementor_widgets( $widgets_manager ) {
    // Vérifier que les fichiers existent avant de les inclure
    $widget_files = [
        __DIR__ . '/elementor-widgets/widget-indicateur.php',
        __DIR__ . '/elementor-widgets/widget-lecteur.php',
        __DIR__ . '/elementor-widgets/widget-replays.php',
        __DIR__ . '/elementor-widgets/widget-compteur.php',
        __DIR__ . '/elementor-widgets/widget-statistiques.php',
        __DIR__ . '/elementor-widgets/widget-dernier-replay.php',
        __DIR__ . '/elementor-widgets/widget-countdown.php',
        __DIR__ . '/elementor-widgets/widget-clips.php',
        __DIR__ . '/elementor-widgets/widget-alerte-live.php'
    ];

    foreach ( $widget_files as $file ) {
        if ( file_exists( $file ) ) {
            require_once( $file );
        }
    }

    // Vérifier que les classes existent avant de les enregistrer
    if ( class_exists( 'MPT_Elementor_Indicateur_Widget' ) ) {
        $widgets_manager->register( new \MPT_Elementor_Indicateur_Widget() );
    }
    if ( class_exists( 'MPT_Elementor_Lecteur_Widget' ) ) {
        $widgets_manager->register( new \MPT_Elementor_Lecteur_Widget() );
    }
    if ( class_exists( 'MPT_Elementor_Replays_Widget' ) ) {
        $widgets_manager->register( new \MPT_Elementor_Replays_Widget() );
    }
    if ( class_exists( 'MPT_Elementor_Compteur_Widget' ) ) {
        $widgets_manager->register( new \MPT_Elementor_Compteur_Widget() );
    }
    if ( class_exists( 'MPT_Elementor_Statistiques_Widget' ) ) {
        $widgets_manager->register( new \MPT_Elementor_Statistiques_Widget() );
    }
    if ( class_exists( 'MPT_Elementor_Dernier_Replay_Widget' ) ) {
        $widgets_manager->register( new \MPT_Elementor_Dernier_Replay_Widget() );
    }
    if ( class_exists( 'MPT_Elementor_Countdown_Widget' ) ) {
        $widgets_manager->register( new \MPT_Elementor_Countdown_Widget() );
    }
    if ( class_exists( 'MPT_Elementor_Clips_Widget' ) ) {
        $widgets_manager->register( new \MPT_Elementor_Clips_Widget() );
    }
    if ( class_exists( 'MPT_Elementor_Alerte_Live_Widget' ) ) {
        $widgets_manager->register( new \MPT_Elementor_Alerte_Live_Widget() );
    }
}

/**
 * Charger les styles du plugin avec thèmes
 */
function mpt_enqueue_styles() {
    // Style de base
    wp_enqueue_style( 'mpt-styles', plugin_dir_url( __FILE__ ) . 'style.css' );
    
    // Thème sélectionné
    $options = get_option('mpt_settings');
    $theme = $options['theme_preset'] ?? 'default';
    
    if ($theme !== 'default') {
        $theme_css = mpt_get_theme_css($theme);
        if ($theme_css) {
            wp_add_inline_style('mpt-styles', $theme_css);
        }
    }
    
    // CSS personnalisé
    $custom_css = $options['custom_css'] ?? '';
    if (!empty($custom_css)) {
        wp_add_inline_style('mpt-styles', $custom_css);
    }
}
add_action( 'wp_enqueue_scripts', 'mpt_enqueue_styles' );

/**
 * Retourne le CSS d'un thème prédéfini
 */
function mpt_get_theme_css($theme) {
    $themes = [
        'gaming_dark' => '
            /* Gaming Dark Theme */
            .mpt-status-wrapper { background: linear-gradient(45deg, #1a1a2e, #16213e) !important; }
            .mpt-replay-card-custom, .mpt-clip-card { background: #0f0f23 !important; color: #e94560 !important; }
            .mpt-replay-card-custom h4, .mpt-clip-card h4 { color: #00d4aa !important; }
            .mpt-replay-type-badge { background: #e94560 !important; }
            .mpt-player-wrapper { border: 2px solid #00d4aa; }
        ',
        'streamer_pro' => '
            /* Streamer Pro Theme */
            .mpt-status-wrapper { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
            .mpt-replay-card-custom, .mpt-clip-card { 
                background: linear-gradient(145deg, #ffffff, #f0f0f0) !important; 
                box-shadow: 20px 20px 60px #d9d9d9, -20px -20px 60px #ffffff !important;
            }
            .mpt-replay-card-custom:hover, .mpt-clip-card:hover { 
                transform: translateY(-8px) scale(1.02) !important; 
            }
            .mpt-replay-type-badge { background: linear-gradient(45deg, #667eea, #764ba2) !important; }
        ',
        'minimal_clean' => '
            /* Minimal Clean Theme */
            .mpt-status-wrapper { background: #ffffff !important; color: #333 !important; border: 1px solid #e0e0e0; }
            .mpt-replay-card-custom, .mpt-clip-card { 
                background: #ffffff !important; 
                border: 1px solid #f0f0f0 !important;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
            }
            .mpt-replay-card-custom h4, .mpt-clip-card h4 { color: #2c3e50 !important; }
            .mpt-replay-type-badge { background: #34495e !important; }
            .mpt-dot-green { background: #27ae60 !important; }
            .mpt-dot-red { background: #e74c3c !important; }
        ',
        'neon_glow' => '
            /* Neon Glow Theme */
            .mpt-status-wrapper { 
                background: #000 !important; 
                border: 2px solid #00ffff !important;
                box-shadow: 0 0 20px #00ffff !important;
                text-shadow: 0 0 10px #00ffff !important;
            }
            .mpt-replay-card-custom, .mpt-clip-card { 
                background: #111 !important; 
                border: 1px solid #ff00ff !important;
                box-shadow: 0 0 15px rgba(255, 0, 255, 0.3) !important;
            }
            .mpt-replay-card-custom h4, .mpt-clip-card h4 { 
                color: #00ffff !important; 
                text-shadow: 0 0 5px #00ffff !important;
            }
            .mpt-replay-type-badge { 
                background: #ff00ff !important; 
                box-shadow: 0 0 10px #ff00ff !important;
            }
            .mpt-dot-green { box-shadow: 0 0 15px #00ff00 !important; }
        ',
        'retro_arcade' => '
            /* Retro Arcade Theme */
            .mpt-status-wrapper { 
                background: linear-gradient(45deg, #ff6b6b, #feca57) !important;
                border-radius: 0 !important;
                font-family: "Courier New", monospace !important;
                text-transform: uppercase !important;
                letter-spacing: 2px !important;
            }
            .mpt-replay-card-custom, .mpt-clip-card { 
                background: #2c2c54 !important; 
                border: 3px solid #ff9ff3 !important;
                border-radius: 0 !important;
                color: #f8f8f8 !important;
            }
            .mpt-replay-card-custom h4, .mpt-clip-card h4 { 
                color: #ff6348 !important; 
                font-family: "Courier New", monospace !important;
                text-transform: uppercase !important;
            }
            .mpt-replay-type-badge { 
                background: #ff6348 !important; 
                border-radius: 0 !important;
                font-family: "Courier New", monospace !important;
            }
        '
    ];
    
    return $themes[$theme] ?? '';
}
