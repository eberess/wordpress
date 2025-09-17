<?php
// Empêcher l'accès direct et vérifier qu'Elementor est disponible
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class MPT_Elementor_Statistiques_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'twitch_stats_chaine';
    }

    public function get_title() {
        return 'Statistiques Twitch';
    }

    public function get_icon() {
        return 'eicon-info-box';
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
            'elements_afficher',
            [
                'label' => 'Éléments à afficher',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['live', 'replays', 'clips', 'vues'],
                'options' => [
                    'live' => 'Statut live',
                    'replays' => 'Nombre de replays',
                    'clips' => 'Nombre de clips',
                    'vues' => 'Vues totales',
                    'spectateurs' => 'Spectateurs actuels',
                    'derniere_activite' => 'Dernière activité',
                    'jeu_actuel' => 'Jeu en cours',
                ],
            ]
        );

        $this->add_control(
            'style_affichage',
            [
                'label' => 'Style d\'affichage',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'carte_moderne',
                'options' => [
                    'liste' => 'Liste simple',
                    'carte' => 'Carte classique',
                    'carte_moderne' => 'Carte moderne',
                    'grille' => 'Grille de stats',
                    'dashboard' => 'Dashboard pro',
                    'minimal' => 'Minimal',
                ],
            ]
        );

        $this->add_control(
            'afficher_icones',
            [
                'label' => 'Afficher les icônes',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
            ]
        );

        $this->add_control(
            'animations',
            [
                'label' => 'Animations',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
            ]
        );

        $this->add_control(
            'titre_personnalise',
            [
                'label' => 'Titre personnalisé',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Statistiques de la chaîne',
                'placeholder' => 'Ex: Stats en temps réel, Métriques...',
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
            'colonnes',
            [
                'label' => 'Colonnes',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '2',
                'options' => [
                    '1' => '1 colonne',
                    '2' => '2 colonnes',
                    '3' => '3 colonnes',
                    '4' => '4 colonnes',
                    'auto' => 'Automatique',
                ],
                'condition' => [
                    'style_affichage' => ['grille', 'dashboard'],
                ],
            ]
        );

        $this->add_responsive_control(
            'espacement_items',
            [
                'label' => 'Espacement entre les éléments',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
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
                'selector' => '{{WRAPPER}} .mpt-stats-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Couleur du titre',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9146ff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-stats-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => 'Espacement du titre',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-stats-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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
                'selector' => '{{WRAPPER}} .mpt-stats-container',
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => 'Espacement interne',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-stats-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .mpt-stats-container' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .mpt-stats-container',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_shadow',
                'selector' => '{{WRAPPER}} .mpt-stats-container',
            ]
        );

        $this->end_controls_section();

        // Section Style - Items
        $this->start_controls_section(
            'style_items_section',
            [
                'label' => 'Style des Éléments',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'label' => 'Typographie des labels',
                'selector' => '{{WRAPPER}} .mpt-stat-label',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'value_typography',
                'label' => 'Typographie des valeurs',
                'selector' => '{{WRAPPER}} .mpt-stat-value',
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => 'Couleur des labels',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .mpt-stat-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'value_color',
            [
                'label' => 'Couleur des valeurs',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .mpt-stat-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => 'Couleur des icônes',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9146ff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-stat-icon' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'afficher_icones' => 'oui',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => 'Taille des icônes',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-stat-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'afficher_icones' => 'oui',
                ],
            ]
        );

        $this->add_control(
            'item_background',
            [
                'label' => 'Fond des éléments',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .mpt-stat-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => 'Espacement interne des éléments',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-stat-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'item_border_radius',
            [
                'label' => 'Arrondi des éléments',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 25,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-stat-item' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
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
                    'glow' => 'Lueur',
                    'lift' => 'Élévation',
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
        if (!$twitch_data) {
            echo '<p>Impossible de récupérer les statistiques.</p>';
            return;
        }
        
        // Préparer les statistiques
        $stats = $this->prepare_stats($twitch_data, $settings);
        
        // Générer les styles CSS
        $custom_css = $this->generate_stats_css($settings, $widget_id);
        if (!empty($custom_css)) {
            echo '<style>' . $custom_css . '</style>';
        }
        
        // Rendu HTML
        echo '<div class="mpt-stats-container mpt-style-' . esc_attr($settings['style_affichage']) . '">';
        
        // Titre
        if (!empty($settings['titre_personnalise'])) {
            echo '<h3 class="mpt-stats-title">';
            if ($settings['afficher_icones'] === 'oui') {
                echo '<i class="fas fa-chart-bar mpt-title-icon"></i> ';
            }
            echo esc_html($settings['titre_personnalise']);
            echo '</h3>';
        }
        
        // Conteneur des stats
        $grid_class = $this->get_grid_class($settings);
        echo '<div class="mpt-stats-grid ' . $grid_class . '">';
        
        foreach ($stats as $key => $stat) {
            $this->render_stat_item($stat, $settings, $key);
        }
        
        echo '</div>'; // fin stats-grid
        echo '</div>'; // fin stats-container
        
        // JavaScript pour les animations
        if ($settings['animations'] === 'oui') {
            $this->render_animation_script($widget_id);
        }
    }
    
    /**
     * Prépare les statistiques à afficher
     */
    private function prepare_stats($twitch_data, $settings) {
        $stats = [];
        $elements = $settings['elements_afficher'];
        $icons = $settings['afficher_icones'] === 'oui';
        
        foreach ($elements as $element) {
            switch ($element) {
                case 'live':
                    $stats['live'] = [
                        'label' => 'Statut',
                        'value' => $twitch_data['is_live'] ? 'En direct' : 'Hors ligne',
                        'icon' => $icons ? ($twitch_data['is_live'] ? 'fas fa-circle text-red' : 'fas fa-circle text-gray') : '',
                        'color' => $twitch_data['is_live'] ? '#e74c3c' : '#95a5a6',
                        'type' => 'status'
                    ];
                    break;
                    
                case 'replays':
                    $count = count($twitch_data['replays_list']);
                    $stats['replays'] = [
                        'label' => 'Replays',
                        'value' => $count,
                        'icon' => $icons ? 'fas fa-video' : '',
                        'color' => '#9146ff',
                        'type' => 'number'
                    ];
                    break;
                    
                case 'clips':
                    $count = count($twitch_data['clips_list']);
                    $stats['clips'] = [
                        'label' => 'Clips',
                        'value' => $count,
                        'icon' => $icons ? 'fas fa-film' : '',
                        'color' => '#ff6b6b',
                        'type' => 'number'
                    ];
                    break;
                    
                case 'vues':
                    $views = $twitch_data['total_views'];
                    $stats['vues'] = [
                        'label' => 'Vues totales',
                        'value' => $this->format_number($views),
                        'icon' => $icons ? 'fas fa-eye' : '',
                        'color' => '#3498db',
                        'type' => 'number'
                    ];
                    break;
                    
                case 'spectateurs':
                    if ($twitch_data['is_live'] && $twitch_data['stream_info']) {
                        $viewers = $twitch_data['stream_info']->viewer_count;
                        $stats['spectateurs'] = [
                            'label' => 'Spectateurs',
                            'value' => number_format($viewers),
                            'icon' => $icons ? 'fas fa-users' : '',
                            'color' => '#e67e22',
                            'type' => 'number'
                        ];
                    }
                    break;
                    
                case 'derniere_activite':
                    if ($twitch_data['latest_replay']) {
                        $date = date_i18n(get_option('date_format'), strtotime($twitch_data['latest_replay']->created_at));
                        $stats['derniere_activite'] = [
                            'label' => 'Dernière activité',
                            'value' => $date,
                            'icon' => $icons ? 'fas fa-calendar-alt' : '',
                            'color' => '#27ae60',
                            'type' => 'text'
                        ];
                    }
                    break;
                    
                case 'jeu_actuel':
                    if ($twitch_data['is_live'] && $twitch_data['stream_info'] && !empty($twitch_data['stream_info']->game_name)) {
                        $stats['jeu_actuel'] = [
                            'label' => 'Jeu en cours',
                            'value' => $twitch_data['stream_info']->game_name,
                            'icon' => $icons ? 'fas fa-gamepad' : '',
                            'color' => '#8e44ad',
                            'type' => 'text'
                        ];
                    }
                    break;
            }
        }
        
        return $stats;
    }
    
    /**
     * Rend un élément de statistique
     */
    private function render_stat_item($stat, $settings, $key) {
        $animation_class = $settings['animations'] === 'oui' ? 'mpt-animate-item' : '';
        
        echo '<div class="mpt-stat-item ' . $animation_class . '" data-stat="' . esc_attr($key) . '">';
        
        if ($settings['style_affichage'] === 'dashboard') {
            // Style dashboard avec icône proéminente
            echo '<div class="mpt-stat-icon-large" style="color: ' . esc_attr($stat['color']) . ';">';
            if (!empty($stat['icon'])) {
                echo '<i class="' . esc_attr($stat['icon']) . '"></i>';
            }
            echo '</div>';
            echo '<div class="mpt-stat-content">';
            echo '<div class="mpt-stat-value">' . esc_html($stat['value']) . '</div>';
            echo '<div class="mpt-stat-label">' . esc_html($stat['label']) . '</div>';
            echo '</div>';
        } else {
            // Style standard
            echo '<div class="mpt-stat-header">';
            if (!empty($stat['icon'])) {
                echo '<span class="mpt-stat-icon" style="color: ' . esc_attr($stat['color']) . ';"><i class="' . esc_attr($stat['icon']) . '"></i></span>';
            }
            echo '<span class="mpt-stat-label">' . esc_html($stat['label']) . '</span>';
            echo '</div>';
            echo '<div class="mpt-stat-value" style="color: ' . esc_attr($stat['color']) . ';">' . esc_html($stat['value']) . '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Détermine la classe CSS pour la grille
     */
    private function get_grid_class($settings) {
        $style = $settings['style_affichage'];
        $columns = $settings['colonnes'] ?? '2';
        
        if (in_array($style, ['grille', 'dashboard'])) {
            return 'mpt-grid-' . $columns;
        }
        
        return 'mpt-grid-auto';
    }
    
    /**
     * Formate un nombre pour l'affichage
     */
    private function format_number($number) {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return number_format($number);
    }
    
    /**
     * Génère le CSS personnalisé
     */
    private function generate_stats_css($settings, $widget_id) {
        $css = '';
        $spacing = $settings['espacement_items']['size'] ?? 15;
        
        // Styles de base
        $css .= '
        .elementor-element-' . $widget_id . ' .mpt-stats-grid {
            display: grid;
            gap: ' . $spacing . 'px;
        }
        .elementor-element-' . $widget_id . ' .mpt-grid-1 { grid-template-columns: 1fr; }
        .elementor-element-' . $widget_id . ' .mpt-grid-2 { grid-template-columns: repeat(2, 1fr); }
        .elementor-element-' . $widget_id . ' .mpt-grid-3 { grid-template-columns: repeat(3, 1fr); }
        .elementor-element-' . $widget_id . ' .mpt-grid-4 { grid-template-columns: repeat(4, 1fr); }
        .elementor-element-' . $widget_id . ' .mpt-grid-auto { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
        ';
        
        // Styles selon le type
        switch ($settings['style_affichage']) {
            case 'dashboard':
                $css .= '
                .elementor-element-' . $widget_id . ' .mpt-stat-item {
                    display: flex;
                    align-items: center;
                    padding: 20px;
                    background: rgba(255,255,255,0.05);
                    border-radius: 12px;
                    border-left: 4px solid currentColor;
                }
                .elementor-element-' . $widget_id . ' .mpt-stat-icon-large {
                    font-size: 2.5em;
                    margin-right: 15px;
                }
                .elementor-element-' . $widget_id . ' .mpt-stat-value {
                    font-size: 1.8em;
                    font-weight: bold;
                    line-height: 1;
                }
                ';
                break;
                
            case 'carte_moderne':
                $css .= '
                .elementor-element-' . $widget_id . ' .mpt-stat-item {
                    background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255,255,255,0.2);
                    border-radius: 16px;
                    padding: 20px;
                    text-align: center;
                }
                ';
                break;
                
            case 'minimal':
                $css .= '
                .elementor-element-' . $widget_id . ' .mpt-stat-item {
                    padding: 10px 0;
                    border-bottom: 1px solid rgba(0,0,0,0.1);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                ';
                break;
        }
        
        // Effets de survol
        switch ($settings['hover_effect']) {
            case 'scale':
                $css .= '.elementor-element-' . $widget_id . ' .mpt-stat-item:hover { transform: scale(1.05); transition: transform 0.3s ease; }';
                break;
            case 'glow':
                $css .= '.elementor-element-' . $widget_id . ' .mpt-stat-item:hover { box-shadow: 0 0 20px rgba(145, 70, 255, 0.3); transition: box-shadow 0.3s ease; }';
                break;
            case 'lift':
                $css .= '.elementor-element-' . $widget_id . ' .mpt-stat-item:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; }';
                break;
        }
        
        // Animations
        if ($settings['animations'] === 'oui') {
            $css .= '
            .elementor-element-' . $widget_id . ' .mpt-animate-item {
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.6s ease;
            }
            .elementor-element-' . $widget_id . ' .mpt-animate-item.mpt-visible {
                opacity: 1;
                transform: translateY(0);
            }
            ';
        }
        
        // Responsive
        $css .= '
        @media (max-width: 768px) {
            .elementor-element-' . $widget_id . ' .mpt-stats-grid {
                grid-template-columns: 1fr !important;
            }
        }
        ';
        
        return $css;
    }
    
    /**
     * Génère le script d'animation
     */
    private function render_animation_script($widget_id) {
        ?>
        <script>
        (function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.classList.add('mpt-visible');
                        }, index * 100);
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            const items = document.querySelectorAll('.elementor-element-<?php echo $widget_id; ?> .mpt-animate-item');
            items.forEach(item => observer.observe(item));
        })();
        </script>
        <?php
    }
}
