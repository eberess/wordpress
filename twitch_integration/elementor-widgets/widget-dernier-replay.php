<?php
// Empêcher l'accès direct et vérifier qu'Elementor est disponible
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class MPT_Elementor_Dernier_Replay_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'twitch_dernier_replay';
    }

    public function get_title() {
        return 'Dernier Replay Twitch';
    }

    public function get_icon() {
        return 'eicon-video-playlist';
    }

    public function get_categories() {
        return ['twitch-widgets'];
    }

    protected function register_controls() {
        // Section Contenu
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Paramètres',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'auto_refresh',
            [
                'label' => 'Rafraîchissement automatique',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => '',
            ]
        );

        $this->add_control(
            'style_affichage',
            [
                'label' => 'Style d\'affichage',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'hero',
                'options' => [
                    'hero' => 'Hero (Mise en avant)',
                    'carte' => 'Carte standard',
                    'banniere' => 'Bannière horizontale',
                    'minimal' => 'Minimal',
                    'call_to_action' => 'Call-to-Action',
                ],
            ]
        );

        $this->add_control(
            'message_accroche',
            [
                'label' => 'Message d\'accroche',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Regardez le dernier stream !',
                'placeholder' => 'Ex: Ne ratez pas ce replay, Dernière session...',
            ]
        );

        $this->add_control(
            'bouton_texte',
            [
                'label' => 'Texte du bouton',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Regarder maintenant',
                'placeholder' => 'Ex: Voir le replay, Regarder, Play...',
                'condition' => [
                    'style_affichage' => ['hero', 'call_to_action', 'banniere'],
                ],
            ]
        );

        $this->add_control(
            'afficher_infos_contexte',
            [
                'label' => 'Informations contextuelles',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['duree', 'date', 'vues'],
                'options' => [
                    'duree' => 'Durée du replay',
                    'date' => 'Date du stream',
                    'vues' => 'Nombre de vues',
                    'jeu' => 'Jeu streamé',
                    'type' => 'Type de contenu',
                    'temps_ecoule' => 'Il y a combien de temps',
                ],
            ]
        );

        $this->add_control(
            'taille_image',
            [
                'label' => 'Taille de l\'image',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'normale',
                'options' => [
                    'petite' => 'Petite (240x135)',
                    'normale' => 'Normale (320x180)',
                    'grande' => 'Grande (480x270)',
                    'full' => 'Pleine largeur',
                ],
                'condition' => [
                    'style_affichage!' => 'minimal',
                ],
            ]
        );

        $this->add_control(
            'overlay_image',
            [
                'label' => 'Overlay sur l\'image',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
                'description' => 'Ajoute un overlay sombre avec icône play',
                'condition' => [
                    'style_affichage!' => 'minimal',
                ],
            ]
        );

        $this->add_control(
            'message_si_vide',
            [
                'label' => 'Message si aucun replay',
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Aucun replay récent disponible. Revenez bientôt !',
                'placeholder' => 'Message affiché quand il n\'y a pas de replay',
            ]
        );

        $this->end_controls_section();

        // Section Contenu Conditionnel
        $this->start_controls_section(
            'conditional_section',
            [
                'label' => 'Affichage Conditionnel',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'priorite_live',
            [
                'label' => 'Priorité au live',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
                'description' => 'Affiche le live en cours au lieu du dernier replay'
            ]
        );

        $this->add_control(
            'masquer_si_live',
            [
                'label' => 'Masquer si live en cours',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => '',
                'description' => 'Cache ce widget quand le stream est en direct'
            ]
        );

        $this->add_control(
            'age_max_replay',
            [
                'label' => 'Âge maximum du replay (jours)',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 365,
                'default' => 30,
                'description' => 'Ne pas afficher les replays plus anciens que X jours'
            ]
        );

        $this->end_controls_section();

        // Section Style - Container
        $this->start_controls_section(
            'style_container_section',
            [
                'label' => 'Style du Conteneur',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'container_background',
                'label' => 'Arrière-plan',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .mpt-replay-container',
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => 'Espacement interne',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'container_border_radius',
            [
                'label' => 'Arrondi des coins',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-container' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_shadow',
                'selector' => '{{WRAPPER}} .mpt-replay-container',
            ]
        );

        $this->end_controls_section();

        // Section Style - Titre
        $this->start_controls_section(
            'style_title_section',
            [
                'label' => 'Style du Titre',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .mpt-replay-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Couleur du titre',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hook_typography',
                'label' => 'Typographie de l\'accroche',
                'selector' => '{{WRAPPER}} .mpt-replay-hook',
            ]
        );

        $this->add_control(
            'hook_color',
            [
                'label' => 'Couleur de l\'accroche',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9146ff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-hook' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Bouton
        $this->start_controls_section(
            'style_button_section',
            [
                'label' => 'Style du Bouton',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'style_affichage' => ['hero', 'call_to_action', 'banniere'],
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .mpt-replay-button',
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => 'Couleur du texte',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button_background',
                'label' => 'Arrière-plan du bouton',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .mpt-replay-button',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => 'Espacement interne',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => 'Arrondi des coins',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => 'Couleur au survol',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button_hover_background',
                'label' => 'Arrière-plan au survol',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .mpt-replay-button:hover',
            ]
        );

        $this->end_controls_section();

        // Section Style - Informations
        $this->start_controls_section(
            'style_info_section',
            [
                'label' => 'Style des Informations',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'info_typography',
                'selector' => '{{WRAPPER}} .mpt-replay-info',
            ]
        );

        $this->add_control(
            'info_color',
            [
                'label' => 'Couleur des informations',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-info' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'info_icon_color',
            [
                'label' => 'Couleur des icônes',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9146ff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-info i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        
        // Récupérer les données Twitch
        if ($settings['auto_refresh'] === 'oui') {
            $options = get_option('mpt_settings');
            $channel_name = $options['twitch_channel'] ?? '';
            delete_transient('mpt_twitch_data_' . $channel_name);
        }
        
        $twitch_data = mpt_get_twitch_data();
        
        // Logique conditionnelle
        $content_to_show = $this->determine_content($twitch_data, $settings);
        
        if (!$content_to_show) {
            $this->render_empty_state($settings);
            return;
        }
        
        // Générer les styles CSS
        $custom_css = $this->generate_replay_css($settings, $widget_id);
        if (!empty($custom_css)) {
            echo '<style>' . $custom_css . '</style>';
        }
        
        // Rendu selon le style
        switch ($settings['style_affichage']) {
            case 'hero':
                $this->render_hero_style($content_to_show, $settings);
                break;
            case 'banniere':
                $this->render_banner_style($content_to_show, $settings);
                break;
            case 'call_to_action':
                $this->render_cta_style($content_to_show, $settings);
                break;
            case 'minimal':
                $this->render_minimal_style($content_to_show, $settings);
                break;
            default:
                $this->render_card_style($content_to_show, $settings);
        }
    }
    
    /**
     * Détermine quel contenu afficher selon les conditions
     */
    private function determine_content($twitch_data, $settings) {
        if (!$twitch_data) return null;
        
        // Si masquer pendant le live
        if ($settings['masquer_si_live'] === 'oui' && $twitch_data['is_live']) {
            return null;
        }
        
        // Si priorité au live et live en cours
        if ($settings['priorite_live'] === 'oui' && $twitch_data['is_live']) {
            return [
                'type' => 'live',
                'data' => $twitch_data['stream_info'],
                'is_live' => true
            ];
        }
        
        // Vérifier l'âge du replay
        if ($twitch_data['latest_replay']) {
            $replay_date = strtotime($twitch_data['latest_replay']->created_at);
            $max_age = intval($settings['age_max_replay']) * 24 * 60 * 60; // en secondes
            
            if ((time() - $replay_date) <= $max_age) {
                return [
                    'type' => 'replay',
                    'data' => $twitch_data['latest_replay'],
                    'is_live' => false
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Rendu style Hero (mise en avant)
     */
    private function render_hero_style($content, $settings) {
        $data = $content['data'];
        $is_live = $content['is_live'];
        
        echo '<div class="mpt-replay-container mpt-hero-style">';
        
        // Image avec overlay
        $this->render_image_with_overlay($data, $settings, $is_live, 'hero');
        
        // Contenu textuel
        echo '<div class="mpt-hero-content">';
        
        if (!empty($settings['message_accroche'])) {
            echo '<div class="mpt-replay-hook">' . esc_html($settings['message_accroche']) . '</div>';
        }
        
        echo '<h2 class="mpt-replay-title">';
        if ($is_live) {
            echo '🔴 ' . esc_html($data->title ?? 'Stream en direct');
        } else {
            echo esc_html($data->title);
        }
        echo '</h2>';
        
        // Informations contextuelles
        $this->render_contextual_info($data, $settings, $is_live);
        
        // Bouton CTA
        if (!empty($settings['bouton_texte'])) {
            $url = $is_live ? 'https://twitch.tv/' . get_option('mpt_settings')['twitch_channel'] : $data->url;
            echo '<a href="' . esc_url($url) . '" target="_blank" class="mpt-replay-button mpt-hero-button">';
            echo '<i class="fas fa-play"></i> ' . esc_html($settings['bouton_texte']);
            echo '</a>';
        }
        
        echo '</div>'; // fin hero-content
        echo '</div>'; // fin container
    }
    
    /**
     * Rendu style Bannière horizontale
     */
    private function render_banner_style($content, $settings) {
        $data = $content['data'];
        $is_live = $content['is_live'];
        
        echo '<div class="mpt-replay-container mpt-banner-style">';
        echo '<div class="mpt-banner-content">';
        
        // Image miniature
        $this->render_image_with_overlay($data, $settings, $is_live, 'banner');
        
        // Contenu central
        echo '<div class="mpt-banner-text">';
        
        if (!empty($settings['message_accroche'])) {
            echo '<div class="mpt-replay-hook">' . esc_html($settings['message_accroche']) . '</div>';
        }
        
        echo '<h3 class="mpt-replay-title">';
        echo $is_live ? '🔴 Live : ' : '📹 Replay : ';
        echo esc_html($data->title ?? 'Contenu Twitch');
        echo '</h3>';
        
        $this->render_contextual_info($data, $settings, $is_live, 'inline');
        
        echo '</div>'; // fin banner-text
        
        // Bouton
        if (!empty($settings['bouton_texte'])) {
            $url = $is_live ? 'https://twitch.tv/' . get_option('mpt_settings')['twitch_channel'] : $data->url;
            echo '<a href="' . esc_url($url) . '" target="_blank" class="mpt-replay-button mpt-banner-button">';
            echo esc_html($settings['bouton_texte']) . ' <i class="fas fa-arrow-right"></i>';
            echo '</a>';
        }
        
        echo '</div>'; // fin banner-content
        echo '</div>'; // fin container
    }
    
    /**
     * Rendu style Call-to-Action
     */
    private function render_cta_style($content, $settings) {
        $data = $content['data'];
        $is_live = $content['is_live'];
        
        echo '<div class="mpt-replay-container mpt-cta-style">';
        
        echo '<div class="mpt-cta-content">';
        
        if ($is_live) {
            echo '<div class="mpt-live-indicator">🔴 EN DIRECT MAINTENANT</div>';
        }
        
        if (!empty($settings['message_accroche'])) {
            echo '<h3 class="mpt-replay-hook">' . esc_html($settings['message_accroche']) . '</h3>';
        }
        
        echo '<div class="mpt-replay-title">' . esc_html($data->title ?? 'Contenu Twitch') . '</div>';
        
        $this->render_contextual_info($data, $settings, $is_live, 'centered');
        
        if (!empty($settings['bouton_texte'])) {
            $url = $is_live ? 'https://twitch.tv/' . get_option('mpt_settings')['twitch_channel'] : $data->url;
            echo '<a href="' . esc_url($url) . '" target="_blank" class="mpt-replay-button mpt-cta-button">';
            echo '<i class="fas fa-play-circle"></i> ' . esc_html($settings['bouton_texte']);
            echo '</a>';
        }
        
        echo '</div>'; // fin cta-content
        echo '</div>'; // fin container
    }
    
    /**
     * Rendu style Minimal
     */
    private function render_minimal_style($content, $settings) {
        $data = $content['data'];
        $is_live = $content['is_live'];
        
        echo '<div class="mpt-replay-container mpt-minimal-style">';
        
        $url = $is_live ? 'https://twitch.tv/' . get_option('mpt_settings')['twitch_channel'] : $data->url;
        echo '<a href="' . esc_url($url) . '" target="_blank" class="mpt-minimal-link">';
        
        echo '<span class="mpt-minimal-icon">';
        echo $is_live ? '🔴' : '📹';
        echo '</span>';
        
        echo '<span class="mpt-replay-title">' . esc_html($data->title ?? 'Contenu Twitch') . '</span>';
        
        // Info compacte
        if (in_array('temps_ecoule', $settings['afficher_infos_contexte'])) {
            $time_ago = $is_live ? 'En cours' : $this->time_ago($data->created_at);
            echo '<span class="mpt-minimal-time">(' . $time_ago . ')</span>';
        }
        
        echo '</a>';
        echo '</div>';
    }
    
    /**
     * Rendu style Carte standard
     */
    private function render_card_style($content, $settings) {
        $data = $content['data'];
        $is_live = $content['is_live'];
        
        echo '<div class="mpt-replay-container mpt-card-style">';
        
        // Image
        $this->render_image_with_overlay($data, $settings, $is_live, 'card');
        
        // Contenu
        echo '<div class="mpt-card-content">';
        
        if (!empty($settings['message_accroche'])) {
            echo '<div class="mpt-replay-hook">' . esc_html($settings['message_accroche']) . '</div>';
        }
        
        echo '<h3 class="mpt-replay-title">' . esc_html($data->title ?? 'Contenu Twitch') . '</h3>';
        
        $this->render_contextual_info($data, $settings, $is_live);
        
        if (!empty($settings['bouton_texte'])) {
            $url = $is_live ? 'https://twitch.tv/' . get_option('mpt_settings')['twitch_channel'] : $data->url;
            echo '<a href="' . esc_url($url) . '" target="_blank" class="mpt-replay-button mpt-card-button">';
            echo esc_html($settings['bouton_texte']);
            echo '</a>';
        }
        
        echo '</div>'; // fin card-content
        echo '</div>'; // fin container
    }
    
    /**
     * Rendu de l'image avec overlay
     */
    private function render_image_with_overlay($data, $settings, $is_live, $context) {
        if ($settings['taille_image'] === 'none') return;
        
        // Déterminer la taille
        $sizes = [
            'petite' => ['240', '135'],
            'normale' => ['320', '180'],
            'grande' => ['480', '270'],
            'full' => ['640', '360']
        ];
        
        $size = $sizes[$settings['taille_image']] ?? $sizes['normale'];
        
        // URL de l'image
        if ($is_live && isset($data->thumbnail_url)) {
            $image_url = str_replace(['{width}', '{height}'], $size, $data->thumbnail_url);
        } elseif (!$is_live && isset($data->thumbnail_url)) {
            $image_url = str_replace(['%{width}', '%{height}'], $size, $data->thumbnail_url);
        } else {
            // Image par défaut
            $image_url = 'https://via.placeholder.com/' . $size[0] . 'x' . $size[1] . '/9146ff/ffffff?text=Twitch';
        }
        
        echo '<div class="mpt-replay-image-container mpt-' . $context . '-image">';
        echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($data->title ?? 'Twitch Content') . '">';
        
        if ($settings['overlay_image'] === 'oui') {
            echo '<div class="mpt-image-overlay">';
            echo '<div class="mpt-play-icon">';
            echo $is_live ? '<i class="fas fa-broadcast-tower"></i>' : '<i class="fas fa-play-circle"></i>';
            echo '</div>';
            echo '</div>';
        }
        
        if ($is_live) {
            echo '<div class="mpt-live-badge">🔴 LIVE</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Rendu des informations contextuelles
     */
    private function render_contextual_info($data, $settings, $is_live, $layout = 'default') {
        $infos = $settings['afficher_infos_contexte'];
        if (empty($infos)) return;
        
        $class = 'mpt-replay-info mpt-info-' . $layout;
        echo '<div class="' . $class . '">';
        
        foreach ($infos as $info) {
            switch ($info) {
                case 'duree':
                    if (!$is_live && !empty($data->duration)) {
                        $duration = $this->format_duration($data->duration);
                        echo '<span class="mpt-info-item"><i class="fas fa-clock"></i> ' . $duration . '</span>';
                    }
                    break;
                    
                case 'date':
                    $date = $is_live ? 'En cours' : date_i18n('j M Y', strtotime($data->created_at));
                    echo '<span class="mpt-info-item"><i class="fas fa-calendar"></i> ' . $date . '</span>';
                    break;
                    
                case 'vues':
                    if (!$is_live && isset($data->view_count)) {
                        echo '<span class="mpt-info-item"><i class="fas fa-eye"></i> ' . number_format($data->view_count) . ' vues</span>';
                    } elseif ($is_live && isset($data->viewer_count)) {
                        echo '<span class="mpt-info-item"><i class="fas fa-users"></i> ' . number_format($data->viewer_count) . ' spectateurs</span>';
                    }
                    break;
                    
                case 'jeu':
                    if ($is_live && !empty($data->game_name)) {
                        echo '<span class="mpt-info-item"><i class="fas fa-gamepad"></i> ' . esc_html($data->game_name) . '</span>';
                    }
                    break;
                    
                case 'type':
                    if (!$is_live && !empty($data->type)) {
                        echo '<span class="mpt-info-item"><i class="fas fa-tag"></i> ' . ucfirst($data->type) . '</span>';
                    }
                    break;
                    
                case 'temps_ecoule':
                    $time_ago = $is_live ? 'En cours' : $this->time_ago($data->created_at);
                    echo '<span class="mpt-info-item"><i class="fas fa-history"></i> ' . $time_ago . '</span>';
                    break;
            }
        }
        
        echo '</div>';
    }
    
    /**
     * Rendu de l'état vide
     */
    private function render_empty_state($settings) {
        echo '<div class="mpt-replay-container mpt-empty-state">';
        echo '<div class="mpt-empty-content">';
        echo '<i class="fas fa-video-slash mpt-empty-icon"></i>';
        echo '<p>' . esc_html($settings['message_si_vide']) . '</p>';
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Formate la durée
     */
    private function format_duration($duration) {
        if (preg_match('/(\d+)h(\d+)m(\d+)s/', $duration, $matches)) {
            $hours = intval($matches[1]);
            $minutes = intval($matches[2]);
            return $hours > 0 ? $hours . 'h' . $minutes . 'm' : $minutes . 'm';
        }
        return $duration;
    }
    
    /**
     * Calcule le temps écoulé
     */
    private function time_ago($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'À l\'instant';
        if ($time < 3600) return floor($time/60) . ' min';
        if ($time < 86400) return floor($time/3600) . 'h';
        if ($time < 2592000) return floor($time/86400) . 'j';
        
        return date('j M Y', strtotime($datetime));
    }
    
    /**
     * Génère le CSS personnalisé
     */
    private function generate_replay_css($settings, $widget_id) {
        $css = '
        .elementor-element-' . $widget_id . ' .mpt-replay-container {
            position: relative;
            overflow: hidden;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-hero-style {
            text-align: center;
            padding: 40px 20px;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-banner-style .mpt-banner-content {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-banner-text {
            flex: 1;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-cta-style {
            text-align: center;
            padding: 30px 20px;
            background: linear-gradient(135deg, rgba(145, 70, 255, 0.1), rgba(255, 107, 107, 0.1));
        }
        
        .elementor-element-' . $widget_id . ' .mpt-minimal-style .mpt-minimal-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
            padding: 10px;
            border-radius: 6px;
            transition: background 0.3s ease;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-minimal-link:hover {
            background: rgba(145, 70, 255, 0.1);
        }
        
        .elementor-element-' . $widget_id . ' .mpt-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-replay-image-container:hover .mpt-image-overlay {
            opacity: 1;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-play-icon {
            font-size: 3em;
            color: white;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-live-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #e74c3c;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-replay-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 15px 0;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-info-inline {
            gap: 10px;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-info-centered {
            justify-content: center;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-info-item {
            font-size: 14px;
            color: #666;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-info-item i {
            margin-right: 5px;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-replay-button {
            display: inline-block;
            padding: 12px 24px;
            background: #9146ff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-replay-button:hover {
            background: #7c3aed;
            transform: translateY(-2px);
        }
        
        .elementor-element-' . $widget_id . ' .mpt-empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-empty-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .elementor-element-' . $widget_id . ' .mpt-banner-content {
                flex-direction: column;
                text-align: center;
            }
            
            .elementor-element-' . $widget_id . ' .mpt-replay-info {
                justify-content: center;
            }
        }
        ';
        
        return $css;
    }
}
