<?php
// Empêcher l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Fonction d'activation du plugin
 */
function mpt_activate_plugin() {
    // Vérifier la version de PHP
    if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( 'Ce plugin nécessite PHP 7.4 ou supérieur. Votre version actuelle est ' . PHP_VERSION );
    }

    // Vérifier la version de WordPress
    if ( version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( 'Ce plugin nécessite WordPress 5.0 ou supérieur.' );
    }

    // Créer les options par défaut si elles n'existent pas
    if ( ! get_option( 'mpt_settings' ) ) {
        add_option( 'mpt_settings', [
            'twitch_channel' => '',
            'twitch_client_id' => '',
            'twitch_client_secret' => ''
        ] );
    }
}

/**
 * Fonction de désactivation du plugin
 */
function mpt_deactivate_plugin() {
    // Nettoyer les transients
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_mpt_twitch_data_%'" );
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_mpt_twitch_data_%'" );
}

/**
 * Fonction de désinstallation du plugin
 */
function mpt_uninstall_plugin() {
    // Supprimer les options
    delete_option( 'mpt_settings' );
    
    // Nettoyer les transients
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_mpt_twitch_data_%'" );
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_mpt_twitch_data_%'" );
}
