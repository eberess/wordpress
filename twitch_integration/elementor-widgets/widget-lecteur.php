<?php
// Empêcher l'accès direct et vérifier qu'Elementor est disponible
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class MPT_Elementor_Lecteur_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'twitch_lecteur_principal';
    }

    public function get_title() {
        return 'Lecteur Principal Twitch';
    }

    public function get_icon() {
        return 'eicon-play-circle-o';
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
                'description' => 'Force le rafraîchissement du cache à chaque affichage'
            ]
        );

        $this->add_control(
            'mode_debug',
            [
                'label' => 'Mode debug',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => '',
                'description' => 'Affiche des informations de débogage (admin seulement)'
            ]
        );

        $this->add_control(
            'priorite_contenu',
            [
                'label' => 'Priorité du contenu',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto' => 'Automatique (Live puis dernier replay)',
                    'live_only' => 'Live seulement',
                    'replay_only' => 'Dernier replay seulement',
                ],
                'description' => 'Détermine quel contenu afficher en priorité'
            ]
        );

        $this->add_control(
            'message_indisponible',
            [
                'label' => 'Message si indisponible',
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Le contenu est indisponible pour le moment.',
                'placeholder' => 'Message personnalisé quand aucun contenu n\'est disponible',
            ]
        );

        $this->add_control(
            'afficher_titre',
            [
                'label' => 'Afficher le titre',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => '',
                'description' => 'Affiche le titre du stream/replay au-dessus du lecteur'
            ]
        );

        $this->add_control(
            'afficher_infos',
            [
                'label' => 'Afficher les informations',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => '',
                'description' => 'Affiche des infos sous le lecteur (statut, durée, etc.)'
            ]
        );

        $this->end_controls_section();

        // Section Dimensions
        $this->start_controls_section(
            'dimensions_section',
            [
                'label' => 'Dimensions',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => 'Hauteur',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', '%'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-player-wrapper' => 'height: {{SIZE}}{{UNIT}}; padding-bottom: 0;',
                ],
            ]
        );

        $this->add_responsive_control(
            'aspect_ratio',
            [
                'label' => 'Ratio d\'aspect',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => [
                    'custom' => 'Personnalisé (utilise la hauteur)',
                    '16_9' => '16:9 (Standard)',
                    '4_3' => '4:3 (Classique)',
                    '21_9' => '21:9 (Ultra-large)',
                ],
            ]
        );

        $this->add_responsive_control(
            'max_width',
            [
                'label' => 'Largeur maximale',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 1920,
                        'step' => 10,
                    ],
                    '%' => [
                        'min' => 50,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-player-wrapper' => 'max-width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Apparence
        $this->start_controls_section(
            'style_appearance_section',
            [
                'label' => 'Apparence',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'border_radius',
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
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-player-wrapper' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .mpt-player-wrapper',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .mpt-player-wrapper',
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => 'Couleur de fond (si pas de contenu)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .mpt-player-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Titre
        $this->start_controls_section(
            'style_title_section',
            [
                'label' => 'Style du titre',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'afficher_titre' => 'oui',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography_title',
                'selector' => '{{WRAPPER}} .mpt-player-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Couleur du titre',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .mpt-player-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => 'Espacement du titre',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-player-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Informations
        $this->start_controls_section(
            'style_info_section',
            [
                'label' => 'Style des informations',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'afficher_infos' => 'oui',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography_info',
                'selector' => '{{WRAPPER}} .mpt-player-info',
            ]
        );

        $this->add_control(
            'info_color',
            [
                'label' => 'Couleur des informations',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .mpt-player-info' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'info_background',
            [
                'label' => 'Fond des informations',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f9fa',
                'selectors' => [
                    '{{WRAPPER}} .mpt-player-info' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'info_padding',
            [
                'label' => 'Espacement interne des infos',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-player-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Récupérer les données Twitch
        if ($settings['auto_refresh'] === 'oui') {
            $options = get_option('mpt_settings');
            $channel_name = $options['twitch_channel'] ?? '';
            delete_transient('mpt_twitch_data_' . $channel_name);
        }
        
        $twitch_data = mpt_get_twitch_data();
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        $parent_domain = str_replace(['http://', 'https://'], '', get_home_url());
        
        // Déterminer le contenu à afficher
        $content_type = 'none';
        $content_data = null;
        
        if ($settings['priorite_contenu'] === 'live_only') {
            if ($twitch_data && $twitch_data['is_live']) {
                $content_type = 'live';
                $content_data = $twitch_data['stream_info'];
            }
        } elseif ($settings['priorite_contenu'] === 'replay_only') {
            if ($twitch_data && $twitch_data['latest_replay']) {
                $content_type = 'replay';
                $content_data = $twitch_data['latest_replay'];
            }
        } else { // auto
            if ($twitch_data && $twitch_data['is_live']) {
                $content_type = 'live';
                $content_data = $twitch_data['stream_info'];
            } elseif ($twitch_data && $twitch_data['latest_replay']) {
                $content_type = 'replay';
                $content_data = $twitch_data['latest_replay'];
            }
        }
        
        // Générer les styles CSS personnalisés
        $widget_id = $this->get_id();
        $custom_css = '';
        
        // Gestion du ratio d'aspect
        if ($settings['aspect_ratio'] !== 'custom') {
            $ratios = [
                '16_9' => '56.25%',
                '4_3' => '75%',
                '21_9' => '42.86%'
            ];
            $padding = $ratios[$settings['aspect_ratio']] ?? '56.25%';
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-player-wrapper { height: 0 !important; padding-bottom: ' . $padding . ' !important; }';
        }
        
        if (!empty($custom_css)) {
            echo '<style>' . $custom_css . '</style>';
        }
        
        // Début du rendu
        echo '<div class="mpt-player-container">';
        
        // Titre si activé
        if ($settings['afficher_titre'] === 'oui' && $content_data) {
            $title = '';
            if ($content_type === 'live') {
                $title = '🔴 Live : ' . ($content_data->title ?? $content_data->game_name ?? 'Stream en direct');
            } elseif ($content_type === 'replay') {
                $title = '📹 Replay : ' . $content_data->title;
            }
            
            if ($title) {
                echo '<h3 class="mpt-player-title">' . esc_html($title) . '</h3>';
            }
        }
        
        // Lecteur principal
        echo '<div class="mpt-player-wrapper">';
        
        if ($content_type === 'live') {
            echo '<iframe src="https://player.twitch.tv/?channel=' . esc_attr($channel_name) . '&parent=' . esc_attr($parent_domain) . '" frameborder="0" allowfullscreen="true" scrolling="no"></iframe>';
        } elseif ($content_type === 'replay') {
            echo '<iframe src="https://player.twitch.tv/?video=' . esc_attr($content_data->id) . '&parent=' . esc_attr($parent_domain) . '" frameborder="0" allowfullscreen="true" scrolling="no"></iframe>';
        } else {
            // Message personnalisé si pas de contenu
            $message = !empty($settings['message_indisponible']) ? $settings['message_indisponible'] : 'Le contenu est indisponible pour le moment.';
            echo '<div style="display: flex; align-items: center; justify-content: center; height: 100%; text-align: center; color: white; padding: 20px;">';
            echo '<p>' . esc_html($message) . '</p>';
            echo '</div>';
        }
        
        echo '</div>'; // fin mpt-player-wrapper
        
        // Informations si activées
        if ($settings['afficher_infos'] === 'oui') {
            echo '<div class="mpt-player-info">';
            
            if ($content_type === 'live' && $content_data) {
                echo '<div class="mpt-info-item"><strong>Statut:</strong> 🔴 En direct</div>';
                if (!empty($content_data->game_name)) {
                    echo '<div class="mpt-info-item"><strong>Jeu:</strong> ' . esc_html($content_data->game_name) . '</div>';
                }
                if (!empty($content_data->viewer_count)) {
                    echo '<div class="mpt-info-item"><strong>Spectateurs:</strong> ' . number_format($content_data->viewer_count) . '</div>';
                }
            } elseif ($content_type === 'replay' && $content_data) {
                echo '<div class="mpt-info-item"><strong>Type:</strong> ' . esc_html(ucfirst($content_data->type)) . '</div>';
                echo '<div class="mpt-info-item"><strong>Publié:</strong> ' . date_i18n(get_option('date_format'), strtotime($content_data->published_at)) . '</div>';
                if (!empty($content_data->duration)) {
                    $duration = $content_data->duration;
                    if (preg_match('/(\d+)h(\d+)m(\d+)s/', $duration, $matches)) {
                        $hours = intval($matches[1]);
                        $minutes = intval($matches[2]);
                        $duration_formatted = $hours > 0 ? $hours . 'h' . $minutes . 'm' : $minutes . 'm';
                        echo '<div class="mpt-info-item"><strong>Durée:</strong> ' . $duration_formatted . '</div>';
                    }
                }
                if (!empty($content_data->view_count)) {
                    echo '<div class="mpt-info-item"><strong>Vues:</strong> ' . number_format($content_data->view_count) . '</div>';
                }
            } else {
                echo '<div class="mpt-info-item">Aucun contenu disponible actuellement</div>';
            }
            
            echo '</div>'; // fin mpt-player-info
        }
        
        // Debug si activé et utilisateur admin
        if ($settings['mode_debug'] === 'oui' && current_user_can('manage_options')) {
            echo '<div style="background: #333; color: white; padding: 10px; margin-top: 10px; font-size: 12px; border-radius: 4px;">';
            echo '<strong>DEBUG:</strong><br>';
            echo 'Type de contenu: ' . $content_type . '<br>';
            echo 'Priorité: ' . $settings['priorite_contenu'] . '<br>';
            echo 'Live actif: ' . ($twitch_data && $twitch_data['is_live'] ? 'Oui' : 'Non') . '<br>';
            echo 'Replays disponibles: ' . ($twitch_data ? count($twitch_data['replays_list']) : 0) . '<br>';
            echo 'Chaîne: ' . esc_html($channel_name) . '<br>';
            echo 'Domaine parent: ' . esc_html($parent_domain);
            echo '</div>';
        }
        
        echo '</div>'; // fin mpt-player-container
        
        // Styles CSS pour les informations
        echo '<style>
        .mpt-player-container { width: 100%; }
        .mpt-player-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
        .mpt-player-info { margin-top: 15px; border-radius: 8px; }
        .mpt-info-item { margin: 8px 0; }
        .mpt-info-item:last-child { margin-bottom: 0; }
        </style>';
    }
}
