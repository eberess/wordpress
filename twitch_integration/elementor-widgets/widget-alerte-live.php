<?php
// Empêcher l'accès direct et vérifier qu'Elementor est disponible
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class MPT_Elementor_Alerte_Live_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'twitch_alerte_live';
    }

    public function get_title() {
        return 'Alerte Live Twitch';
    }

    public function get_icon() {
        return 'eicon-alert';
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
            'message_live',
            [
                'label' => 'Message quand en direct',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '🔴 Je suis en direct maintenant !',
                'placeholder' => 'Message affiché quand le stream est actif',
            ]
        );

        $this->add_control(
            'message_offline',
            [
                'label' => 'Message quand hors ligne',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '⚫ Stream hors ligne - Revenez bientôt !',
                'placeholder' => 'Message affiché quand pas de stream',
            ]
        );

        $this->add_control(
            'masquer_si_offline',
            [
                'label' => 'Masquer si hors ligne',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => '',
                'description' => 'Ne rien afficher quand le stream est hors ligne'
            ]
        );

        $this->add_control(
            'bouton_action',
            [
                'label' => 'Bouton d\'action',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
            ]
        );

        $this->add_control(
            'texte_bouton_live',
            [
                'label' => 'Texte bouton (live)',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Regarder maintenant',
                'condition' => [
                    'bouton_action' => 'oui',
                ],
            ]
        );

        $this->add_control(
            'texte_bouton_offline',
            [
                'label' => 'Texte bouton (offline)',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Voir les replays',
                'condition' => [
                    'bouton_action' => 'oui',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style
        $this->start_controls_section(
            'style_section',
            [
                'label' => 'Style',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'style_alerte',
            [
                'label' => 'Style d\'alerte',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'banniere',
                'options' => [
                    'banniere' => 'Bannière',
                    'carte' => 'Carte',
                    'badge' => 'Badge',
                    'minimal' => 'Minimal',
                ],
            ]
        );

        $this->add_control(
            'couleur_live',
            [
                'label' => 'Couleur Live',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .mpt-alerte-live' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'couleur_offline',
            [
                'label' => 'Couleur Offline',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#95a5a6',
                'selectors' => [
                    '{{WRAPPER}} .mpt-alerte-offline' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'message_typography',
                'selector' => '{{WRAPPER}} .mpt-alerte-message',
            ]
        );

        $this->add_control(
            'couleur_texte',
            [
                'label' => 'Couleur du texte',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-alerte-message' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => 'Espacement interne',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-alerte-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => 'Arrondi des coins',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-alerte-container' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'animation',
            [
                'label' => 'Animation',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'pulse',
                'options' => [
                    'none' => 'Aucune',
                    'pulse' => 'Pulsation',
                    'blink' => 'Clignotement',
                    'bounce' => 'Rebond',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        
        // Récupérer les données Twitch
        $twitch_data = mpt_get_twitch_data();
        $is_live = $twitch_data && $twitch_data['is_live'];
        
        // Si masquer quand offline et pas live, ne rien afficher
        if (!$is_live && $settings['masquer_si_offline'] === 'oui') {
            return;
        }
        
        // Déterminer le message et la classe
        $message = $is_live ? $settings['message_live'] : $settings['message_offline'];
        $class = $is_live ? 'mpt-alerte-live' : 'mpt-alerte-offline';
        $bouton_text = $is_live ? $settings['texte_bouton_live'] : $settings['texte_bouton_offline'];
        
        // URL du bouton
        $options = get_option('mpt_settings');
        $channel_name = $options['twitch_channel'] ?? '';
        $button_url = $is_live ? 'https://twitch.tv/' . $channel_name : '#replays';
        
        // Générer CSS personnalisé
        $custom_css = $this->generate_alert_css($settings, $widget_id, $is_live);
        if (!empty($custom_css)) {
            echo '<style>' . $custom_css . '</style>';
        }
        
        // Rendu HTML
        echo '<div class="mpt-alerte-container ' . $class . ' mpt-style-' . esc_attr($settings['style_alerte']) . '">';
        
        echo '<div class="mpt-alerte-content">';
        
        // Icône de statut
        echo '<div class="mpt-alerte-icon">';
        echo $is_live ? '🔴' : '⚫';
        echo '</div>';
        
        // Message
        echo '<div class="mpt-alerte-message">' . esc_html($message) . '</div>';
        
        // Bouton d'action
        if ($settings['bouton_action'] === 'oui' && !empty($bouton_text)) {
            echo '<a href="' . esc_url($button_url) . '" target="_blank" class="mpt-alerte-button">';
            echo esc_html($bouton_text);
            echo '</a>';
        }
        
        echo '</div>'; // fin alerte-content
        echo '</div>'; // fin alerte-container
    }
    
    /**
     * Génère le CSS personnalisé
     */
    private function generate_alert_css($settings, $widget_id, $is_live) {
        $css = '
        .elementor-element-' . $widget_id . ' .mpt-alerte-container {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-alerte-content {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
            justify-content: center;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-style-banniere {
            padding: 15px 30px;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-style-carte {
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .elementor-element-' . $widget_id . ' .mpt-style-badge {
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-flex;
            width: auto;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-style-minimal {
            padding: 10px 15px;
            background: transparent !important;
            border: 2px solid currentColor;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-alerte-icon {
            font-size: 1.5em;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-alerte-message {
            font-weight: bold;
            flex: 1;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-alerte-button {
            background: rgba(255,255,255,0.2);
            color: inherit;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        
        .elementor-element-' . $widget_id . ' .mpt-alerte-button:hover {
            background: rgba(255,255,255,0.3);
            text-decoration: none;
            color: inherit;
        }
        ';
        
        // Animations
        if ($is_live) {
            switch ($settings['animation']) {
                case 'pulse':
                    $css .= '
                    .elementor-element-' . $widget_id . ' .mpt-alerte-live {
                        animation: mpt-pulse 2s infinite;
                    }
                    @keyframes mpt-pulse {
                        0%, 100% { opacity: 1; }
                        50% { opacity: 0.7; }
                    }';
                    break;
                    
                case 'blink':
                    $css .= '
                    .elementor-element-' . $widget_id . ' .mpt-alerte-icon {
                        animation: mpt-blink 1s infinite;
                    }
                    @keyframes mpt-blink {
                        0%, 50% { opacity: 1; }
                        51%, 100% { opacity: 0; }
                    }';
                    break;
                    
                case 'bounce':
                    $css .= '
                    .elementor-element-' . $widget_id . ' .mpt-alerte-icon {
                        animation: mpt-bounce 2s infinite;
                    }
                    @keyframes mpt-bounce {
                        0%, 20%, 60%, 100% { transform: translateY(0); }
                        40% { transform: translateY(-10px); }
                        80% { transform: translateY(-5px); }
                    }';
                    break;
            }
        }
        
        // Responsive
        $css .= '
        @media (max-width: 768px) {
            .elementor-element-' . $widget_id . ' .mpt-alerte-content {
                flex-direction: column;
                gap: 10px;
            }
            
            .elementor-element-' . $widget_id . ' .mpt-style-banniere {
                padding: 12px 20px;
            }
        }
        ';
        
        return $css;
    }
}
