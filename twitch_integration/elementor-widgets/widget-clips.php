<?php
// Empêcher l'accès direct et vérifier qu'Elementor est disponible
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class MPT_Elementor_Clips_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'twitch_clips';
    }

    public function get_title() {
        return 'Clips Twitch';
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
            'nombre_clips',
            [
                'label' => 'Nombre de clips',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 6,
            ]
        );

        $this->add_control(
            'periode',
            [
                'label' => 'Période',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '30',
                'options' => [
                    '7' => 'Dernière semaine',
                    '30' => 'Dernier mois',
                    '90' => 'Derniers 3 mois',
                    '365' => 'Dernière année',
                ],
            ]
        );

        $this->add_control(
            'tri',
            [
                'label' => 'Tri par',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'vues',
                'options' => [
                    'vues' => 'Nombre de vues',
                    'date' => 'Date de création',
                    'duree' => 'Durée',
                ],
            ]
        );

        $this->add_control(
            'taille_image',
            [
                'label' => 'Taille des images',
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
            'afficher_vues',
            [
                'label' => 'Afficher les vues',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
            ]
        );

        $this->add_control(
            'afficher_duree',
            [
                'label' => 'Afficher la durée',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
            ]
        );

        $this->add_control(
            'afficher_createur',
            [
                'label' => 'Afficher le créateur',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'non',
            ]
        );

        $this->end_controls_section();

        // Section Layout
        $this->start_controls_section(
            'layout_section',
            [
                'label' => 'Disposition',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => 'Colonnes',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto' => 'Automatique',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ],
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label' => 'Espacement',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
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
            'style_carte',
            [
                'label' => 'Style des cartes',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'defaut',
                'options' => [
                    'defaut' => 'Défaut',
                    'ombre' => 'Avec ombre',
                    'bordure' => 'Avec bordure',
                    'moderne' => 'Moderne',
                ],
            ]
        );

        $this->add_control(
            'couleur_fond',
            [
                'label' => 'Couleur de fond',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f9fa',
                'selectors' => [
                    '{{WRAPPER}} .mpt-clip-card' => 'background-color: {{VALUE}};',
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
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-clip-card' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'cards_shadow',
                'selector' => '{{WRAPPER}} .mpt-clip-card',
            ]
        );

        $this->add_control(
            'hover_effect',
            [
                'label' => 'Effet au survol',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'lift',
                'options' => [
                    'none' => 'Aucun',
                    'lift' => 'Élévation',
                    'scale' => 'Agrandissement',
                    'rotate' => 'Rotation légère',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Typographie
        $this->start_controls_section(
            'typography_section',
            [
                'label' => 'Typographie',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => 'Titre des clips',
                'selector' => '{{WRAPPER}} .mpt-clip-card h4',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Couleur du titre',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333',
                'selectors' => [
                    '{{WRAPPER}} .mpt-clip-card h4' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'label' => 'Informations',
                'selector' => '{{WRAPPER}} .mpt-clip-card .mpt-clip-meta',
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label' => 'Couleur des informations',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666',
                'selectors' => [
                    '{{WRAPPER}} .mpt-clip-card .mpt-clip-meta' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        
        // Construire le shortcode
        $shortcode = '[twitch_clips';
        
        if ($settings['auto_refresh'] === 'oui') {
            $shortcode .= ' rafraichir="oui"';
        }
        
        if (!empty($settings['nombre_clips'])) {
            $shortcode .= ' nombre="' . intval($settings['nombre_clips']) . '"';
        }
        
        if (!empty($settings['periode'])) {
            $shortcode .= ' periode="' . $settings['periode'] . '"';
        }
        
        if (!empty($settings['tri'])) {
            $shortcode .= ' tri="' . $settings['tri'] . '"';
        }
        
        if (!empty($settings['taille_image'])) {
            $shortcode .= ' taille_image="' . $settings['taille_image'] . '"';
        }
        
        if (!empty($settings['columns']) && $settings['columns'] !== 'auto') {
            $shortcode .= ' colonnes="' . $settings['columns'] . '"';
        }
        
        if (!empty($settings['style_carte'])) {
            $shortcode .= ' style="' . $settings['style_carte'] . '"';
        }
        
        if (!empty($settings['couleur_fond'])) {
            $shortcode .= ' couleur_fond="' . $settings['couleur_fond'] . '"';
        }
        
        $shortcode .= ' afficher_vues="' . ($settings['afficher_vues'] === 'oui' ? 'oui' : 'non') . '"';
        $shortcode .= ' afficher_duree="' . ($settings['afficher_duree'] === 'oui' ? 'oui' : 'non') . '"';
        $shortcode .= ' afficher_createur="' . ($settings['afficher_createur'] === 'oui' ? 'oui' : 'non') . '"';
        
        $shortcode .= ']';
        
        // Styles CSS personnalisés
        $custom_css = '';
        
        // Effet de survol
        if ($settings['hover_effect'] === 'lift') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-clip-card:hover { transform: translateY(-8px); transition: transform 0.3s ease; }';
        } elseif ($settings['hover_effect'] === 'scale') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-clip-card:hover { transform: scale(1.05); transition: transform 0.3s ease; }';
        } elseif ($settings['hover_effect'] === 'rotate') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-clip-card:hover { transform: rotate(2deg); transition: transform 0.3s ease; }';
        }
        
        if (!empty($custom_css)) {
            echo '<style>' . $custom_css . '</style>';
        }
        
        echo do_shortcode($shortcode);
    }
}
