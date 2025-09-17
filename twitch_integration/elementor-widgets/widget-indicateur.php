<?php
// Empêcher l'accès direct et vérifier qu'Elementor est disponible
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class MPT_Elementor_Indicateur_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'twitch_status_indicateur';
    }

    public function get_title() {
        return 'Indicateur de Statut Twitch';
    }

    public function get_icon() {
        return 'eicon-dot-circle-o';
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
            'taille',
            [
                'label' => 'Taille',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'normale',
                'options' => [
                    'petite' => 'Petite',
                    'normale' => 'Normale',
                    'grande' => 'Grande',
                ],
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => 'Style',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'defaut',
                'options' => [
                    'defaut' => 'Défaut',
                    'minimal' => 'Minimal',
                    'badge' => 'Badge',
                ],
            ]
        );

        $this->add_control(
            'afficher_texte',
            [
                'label' => 'Afficher le texte',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
                'description' => 'Affiche "LIVE" ou "OFFLINE"'
            ]
        );

        $this->add_control(
            'texte_live_personnalise',
            [
                'label' => 'Texte Live personnalisé',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => 'Ex: EN DIRECT, LIVE, 🔴 LIVE',
                'condition' => [
                    'afficher_texte' => 'oui',
                ],
            ]
        );

        $this->add_control(
            'texte_offline_personnalise',
            [
                'label' => 'Texte Offline personnalisé',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => 'Ex: HORS LIGNE, OFFLINE, ⚫ OFFLINE',
                'condition' => [
                    'afficher_texte' => 'oui',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Couleurs
        $this->start_controls_section(
            'style_colors_section',
            [
                'label' => 'Couleurs',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'couleur_live',
            [
                'label' => 'Couleur Live',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#00ff00',
                'description' => 'Couleur du point quand le stream est en direct'
            ]
        );

        $this->add_control(
            'couleur_offline',
            [
                'label' => 'Couleur Offline',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff0000',
                'description' => 'Couleur du point quand le stream est hors ligne'
            ]
        );

        $this->add_control(
            'couleur_fond',
            [
                'label' => 'Couleur de fond',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9146ff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-status-wrapper' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'style!' => 'minimal',
                ],
            ]
        );

        $this->add_control(
            'couleur_texte',
            [
                'label' => 'Couleur du texte',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-status-text' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'afficher_texte' => 'oui',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Typographie
        $this->start_controls_section(
            'style_typography_section',
            [
                'label' => 'Typographie',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'afficher_texte' => 'oui',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography_texte',
                'selector' => '{{WRAPPER}} .mpt-status-text',
            ]
        );

        $this->end_controls_section();

        // Section Style - Dimensions
        $this->start_controls_section(
            'style_dimensions_section',
            [
                'label' => 'Dimensions & Espacement',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => 'Espacement interne',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-status-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'margin',
            [
                'label' => 'Espacement externe',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-status-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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
                    '{{WRAPPER}} .mpt-status-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'style!' => 'minimal',
                ],
            ]
        );

        $this->add_responsive_control(
            'alignement',
            [
                'label' => 'Alignement',
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => 'Gauche',
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => 'Centre',
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => 'Droite',
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Effets
        $this->start_controls_section(
            'style_effects_section',
            [
                'label' => 'Effets',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .mpt-status-wrapper',
                'condition' => [
                    'style!' => 'minimal',
                ],
            ]
        );

        $this->add_control(
            'animation_pulse',
            [
                'label' => 'Animation pulsation (Live)',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
                'description' => 'Animation du point quand en direct'
            ]
        );

        $this->add_control(
            'hover_effect',
            [
                'label' => 'Effet au survol',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'scale',
                'options' => [
                    'none' => 'Aucun',
                    'scale' => 'Agrandissement',
                    'rotate' => 'Rotation',
                    'glow' => 'Lueur',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Générer des styles CSS personnalisés
        $custom_css = '';
        $widget_id = $this->get_id();
        
        // Animation pulsation
        if ($settings['animation_pulse'] !== 'oui') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-dot-green { animation: none !important; }';
        }
        
        // Effets de survol
        if ($settings['hover_effect'] === 'scale') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-status-wrapper:hover { transform: scale(1.1); transition: transform 0.3s ease; }';
        } elseif ($settings['hover_effect'] === 'rotate') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-status-wrapper:hover { transform: rotate(5deg); transition: transform 0.3s ease; }';
        } elseif ($settings['hover_effect'] === 'glow') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-status-wrapper:hover { box-shadow: 0 0 20px rgba(145, 70, 255, 0.5); transition: box-shadow 0.3s ease; }';
        }
        
        if (!empty($custom_css)) {
            echo '<style>' . $custom_css . '</style>';
        }
        
        // Construction du shortcode
        $shortcode = '[twitch_status_indicateur';
        
        if ($settings['auto_refresh'] === 'oui') {
            $shortcode .= ' rafraichir="oui"';
        }
        
        if (!empty($settings['taille'])) {
            $shortcode .= ' taille="' . $settings['taille'] . '"';
        }
        
        if (!empty($settings['style'])) {
            $shortcode .= ' style="' . $settings['style'] . '"';
        }
        
        // Gestion du texte : si pas activé, on passe "non"
        $texte_value = ($settings['afficher_texte'] === 'oui') ? 'oui' : 'non';
        $shortcode .= ' texte="' . $texte_value . '"';
        
        if (!empty($settings['couleur_live'])) {
            $shortcode .= ' couleur_live="' . $settings['couleur_live'] . '"';
        }
        
        if (!empty($settings['couleur_offline'])) {
            $shortcode .= ' couleur_offline="' . $settings['couleur_offline'] . '"';
        }
        
        $shortcode .= ']';
        
        // Modifier le shortcode pour les textes personnalisés
        $output = do_shortcode($shortcode);
        
        // Remplacer les textes par défaut si des textes personnalisés sont définis
        if ($settings['afficher_texte'] === 'oui') {
            if (!empty($settings['texte_live_personnalise'])) {
                $output = str_replace('>LIVE<', '>' . esc_html($settings['texte_live_personnalise']) . '<', $output);
            }
            if (!empty($settings['texte_offline_personnalise'])) {
                $output = str_replace('>OFFLINE<', '>' . esc_html($settings['texte_offline_personnalise']) . '<', $output);
            }
        }
        
        echo $output;
    }
}
