<?php
// Empêcher l'accès direct et vérifier qu'Elementor est disponible
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class MPT_Elementor_Replays_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'twitch_derniers_replays';
    }

    public function get_title() {
        return 'Grille des Replays Twitch';
    }

    public function get_icon() {
        return 'eicon-video-camera';
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
            'nombre_replays',
            [
                'label' => 'Nombre de replays',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 5,
                'description' => 'Nombre maximum de replays à afficher'
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
            'afficher_type',
            [
                'label' => 'Afficher le type',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
                'description' => 'Affiche le badge ARCHIVE/HIGHLIGHT/UPLOAD'
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

        // Section Style - Cartes
        $this->start_controls_section(
            'style_cards_section',
            [
                'label' => 'Style des cartes',
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
                    '{{WRAPPER}} .mpt-replay-card-custom' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_radius_cards',
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
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-card-custom' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'cards_shadow',
                'selector' => '{{WRAPPER}} .mpt-replay-card-custom',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'cards_border',
                'selector' => '{{WRAPPER}} .mpt-replay-card-custom',
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
                    'glow' => 'Lueur',
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
            ]
        );

        $this->add_control(
            'heading_title',
            [
                'label' => 'Titre des replays',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography_title',
                'selector' => '{{WRAPPER}} .mpt-replay-card-custom h4',
            ]
        );

        $this->add_control(
            'couleur_titre',
            [
                'label' => 'Couleur du titre',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-card-custom h4' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'heading_meta',
            [
                'label' => 'Informations (date, type)',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography_meta',
                'selector' => '{{WRAPPER}} .mpt-replay-card-custom .mpt-replay-meta',
            ]
        );

        $this->add_control(
            'couleur_meta',
            [
                'label' => 'Couleur des informations',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-card-custom .mpt-replay-meta' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'couleur_date',
            [
                'label' => 'Couleur de la date',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#999',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-card-custom .mpt-replay-date' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Badges et éléments
        $this->start_controls_section(
            'style_badges_section',
            [
                'label' => 'Badges et éléments',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'couleur_badge_type',
            [
                'label' => 'Couleur du badge type',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9146ff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-type-badge' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'afficher_type' => 'oui',
                ],
            ]
        );

        $this->add_control(
            'couleur_duree',
            [
                'label' => 'Couleur de la durée',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-duration' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'afficher_duree' => 'oui',
                ],
            ]
        );

        $this->add_control(
            'fond_duree',
            [
                'label' => 'Fond de la durée',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.8)',
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-duration' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'afficher_duree' => 'oui',
                ],
            ]
        );

        $this->add_control(
            'style_image',
            [
                'label' => 'Style des images',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'normal' => 'Normal',
                    'grayscale' => 'Noir et blanc',
                    'sepia' => 'Sépia',
                    'blur' => 'Flou léger',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Espacement
        $this->start_controls_section(
            'style_spacing_section',
            [
                'label' => 'Espacement',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'cards_padding',
            [
                'label' => 'Espacement interne des cartes',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-card-custom > div:last-child' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => 'Espacement du titre',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-replay-card-custom h4' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        
        // Générer les styles CSS personnalisés
        $custom_css = '';
        
        // Effet de survol
        if ($settings['hover_effect'] === 'lift') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-replay-card-custom:hover { transform: translateY(-8px); transition: transform 0.3s ease; }';
        } elseif ($settings['hover_effect'] === 'scale') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-replay-card-custom:hover { transform: scale(1.05); transition: transform 0.3s ease; }';
        } elseif ($settings['hover_effect'] === 'glow') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-replay-card-custom:hover { box-shadow: 0 0 20px rgba(145, 70, 255, 0.3); transition: box-shadow 0.3s ease; }';
        }
        
        // Style des images
        if ($settings['style_image'] === 'grayscale') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-replay-card-custom img { filter: grayscale(100%); transition: filter 0.3s ease; }';
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-replay-card-custom:hover img { filter: grayscale(0%); }';
        } elseif ($settings['style_image'] === 'sepia') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-replay-card-custom img { filter: sepia(100%); transition: filter 0.3s ease; }';
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-replay-card-custom:hover img { filter: sepia(0%); }';
        } elseif ($settings['style_image'] === 'blur') {
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-replay-card-custom img { filter: blur(1px); transition: filter 0.3s ease; }';
            $custom_css .= '.elementor-element-' . $widget_id . ' .mpt-replay-card-custom:hover img { filter: blur(0px); }';
        }
        
        // Ajouter les styles de base
        $grid_columns = 'repeat(auto-fit, minmax(300px, 1fr))';
        if (!empty($settings['columns']) && $settings['columns'] !== 'auto') {
            $grid_columns = 'repeat(' . intval($settings['columns']) . ', 1fr)';
        }
        
        $gap_size = $settings['gap']['size'] ?? 20;
        
        $custom_css .= '
        .elementor-element-' . $widget_id . ' .mpt-replays-grid-custom {
            display: grid;
            grid-template-columns: ' . $grid_columns . ';
            gap: ' . $gap_size . 'px;
            margin: 20px 0;
        }
        .elementor-element-' . $widget_id . ' .mpt-replay-card-custom {
            display: block;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .elementor-element-' . $widget_id . ' .mpt-replay-card-custom img {
            width: 100%;
            height: auto;
            display: block;
        }
        .elementor-element-' . $widget_id . ' .mpt-replay-card-custom > div:last-child {
            padding: 12px;
        }
        .elementor-element-' . $widget_id . ' .mpt-replay-card-custom h4 {
            margin: 0 0 8px;
            font-size: 16px;
            font-weight: 600;
            line-height: 1.4;
        }
        .elementor-element-' . $widget_id . ' .mpt-replay-meta {
            font-size: 14px;
        }
        .elementor-element-' . $widget_id . ' .mpt-replay-date {
            font-size: 14px;
        }
        .elementor-element-' . $widget_id . ' .mpt-replay-type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-right: 8px;
        }
        .elementor-element-' . $widget_id . ' .mpt-replay-duration {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .elementor-element-' . $widget_id . ' .mpt-replays-grid-custom {
                grid-template-columns: 1fr !important;
            }
        }
        ';
        
        if (!empty($custom_css)) {
            echo '<style>' . $custom_css . '</style>';
        }
        
        // Construction du shortcode avec rendu personnalisé
        if ($settings['auto_refresh'] === 'oui') {
            $options = get_option('mpt_settings');
            $channel_name = $options['twitch_channel'] ?? '';
            delete_transient('mpt_twitch_data_' . $channel_name);
        }
        
        $twitch_data = mpt_get_twitch_data();
        $debug_mode = false; // Pas de debug dans Elementor par défaut
        
        if (!$twitch_data || empty($twitch_data['replays_list'])) {
            echo '<div class="mpt-no-replays" style="padding: 20px; text-align: center; color: #666;">
                    <p>Aucun replay disponible.</p>
                  </div>';
            return;
        }
        
        // Limiter le nombre de replays
        $nombre_replays = min(intval($settings['nombre_replays'] ?? 5), count($twitch_data['replays_list']));
        $replays_to_show = array_slice($twitch_data['replays_list'], 0, $nombre_replays);
        
        // Définir les colonnes
        $grid_columns = 'repeat(auto-fit, minmax(300px, 1fr))';
        if (!empty($settings['columns']) && $settings['columns'] !== 'auto') {
            $grid_columns = 'repeat(' . intval($settings['columns']) . ', 1fr)';
        }
        
        // Taille des images
        $image_size = ['320', '180'];
        if ($settings['taille_image'] === 'petite') {
            $image_size = ['240', '135'];
        } elseif ($settings['taille_image'] === 'grande') {
            $image_size = ['480', '270'];
        }
        
        echo '<div class="mpt-replays-grid-custom">';
        
        foreach ($replays_to_show as $replay) {
            $thumbnail_url = str_replace(['%{width}', '%{height}'], $image_size, $replay->thumbnail_url);
            
            // Convertir la durée en format lisible
            $duration_formatted = '';
            if ($settings['afficher_duree'] === 'oui' && !empty($replay->duration)) {
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
            
            echo '<a href="' . esc_url($replay->url) . '" target="_blank" rel="noopener" class="mpt-replay-card-custom">';
            echo '<div style="position: relative;">';
            echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr($replay->title) . '" style="width: 100%; height: auto;">';
            
            if ($duration_formatted) {
                echo '<span class="mpt-replay-duration">' . $duration_formatted . '</span>';
            }
            
            echo '</div>';
            echo '<div>';
            echo '<h4 style="margin: 0 0 8px; font-size: 16px; font-weight: 600; line-height: 1.4;">' . esc_html($replay->title) . '</h4>';
            echo '<div class="mpt-replay-meta">';
            
            if ($settings['afficher_type'] === 'oui') {
                echo '<span class="mpt-replay-type-badge">' . esc_html($replay->type) . '</span>';
            }
            
            echo '<span class="mpt-replay-date">Publié le ' . date_i18n(get_option('date_format'), strtotime($replay->published_at)) . '</span>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
        }
        
        echo '</div>';
    }
}
