<?php
// Empêcher l'accès direct et vérifier qu'Elementor est disponible
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class MPT_Elementor_Compteur_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'twitch_nombre_replays';
    }

    public function get_title() {
        return 'Compteur de Replays Twitch';
    }

    public function get_icon() {
        return 'eicon-counter';
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
            'type_compteur',
            [
                'label' => 'Type de compteur',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'replays',
                'options' => [
                    'replays' => 'Nombre de replays',
                    'clips' => 'Nombre de clips',
                    'vues_totales' => 'Vues totales',
                    'spectateurs_live' => 'Spectateurs en direct',
                    'personnalise' => 'Valeur personnalisée',
                ],
            ]
        );

        $this->add_control(
            'valeur_personnalisee',
            [
                'label' => 'Valeur personnalisée',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 100,
                'condition' => [
                    'type_compteur' => 'personnalise',
                ],
            ]
        );

        $this->add_control(
            'texte_personnalise',
            [
                'label' => 'Texte personnalisé',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'replays disponibles',
                'placeholder' => 'Ex: vidéos en ligne, contenus disponibles...',
            ]
        );

        $this->add_control(
            'prefixe',
            [
                'label' => 'Préfixe',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => 'Ex: +, >, ~',
            ]
        );

        $this->add_control(
            'suffixe',
            [
                'label' => 'Suffixe',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => 'Ex: K, M, %',
            ]
        );

        $this->add_control(
            'icone',
            [
                'label' => 'Icône',
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-video',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'position_icone',
            [
                'label' => 'Position de l\'icône',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'avant',
                'options' => [
                    'avant' => 'Avant le nombre',
                    'apres' => 'Après le texte',
                    'dessus' => 'Au-dessus',
                    'dessous' => 'En dessous',
                ],
                'condition' => [
                    'icone[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'animation_compteur',
            [
                'label' => 'Animation du compteur',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => 'oui',
                'description' => 'Animation de comptage depuis 0'
            ]
        );

        $this->add_control(
            'duree_animation',
            [
                'label' => 'Durée de l\'animation (ms)',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 500,
                        'max' => 5000,
                        'step' => 100,
                    ],
                ],
                'default' => [
                    'size' => 2000,
                ],
                'condition' => [
                    'animation_compteur' => 'oui',
                ],
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

        $this->add_control(
            'style_compteur',
            [
                'label' => 'Style de base',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'moderne',
                'options' => [
                    'defaut' => 'Défaut',
                    'badge' => 'Badge',
                    'encadre' => 'Encadré',
                    'moderne' => 'Moderne',
                    'minimal' => 'Minimal',
                    'carte' => 'Carte',
                    'neon' => 'Néon',
                ],
            ]
        );

        $this->add_responsive_control(
            'direction',
            [
                'label' => 'Direction',
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'row' => [
                        'title' => 'Horizontal',
                        'icon' => 'eicon-arrow-right',
                    ],
                    'column' => [
                        'title' => 'Vertical',
                        'icon' => 'eicon-arrow-down',
                    ],
                ],
                'default' => 'column',
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-container' => 'flex-direction: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'alignement',
            [
                'label' => 'Alignement',
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => 'Gauche',
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => 'Centre',
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => 'Droite',
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-container' => 'align-items: {{VALUE}}; justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Nombre
        $this->start_controls_section(
            'style_number_section',
            [
                'label' => 'Style du Nombre',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'number_typography',
                'selector' => '{{WRAPPER}} .mpt-counter-number',
            ]
        );

        $this->add_control(
            'couleur_nombre',
            [
                'label' => 'Couleur du nombre',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9146ff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-number' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'number_shadow',
                'selector' => '{{WRAPPER}} .mpt-counter-number',
            ]
        );

        $this->add_control(
            'gradient_nombre',
            [
                'label' => 'Dégradé du nombre',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'oui',
                'default' => '',
            ]
        );

        $this->add_control(
            'gradient_couleur1',
            [
                'label' => 'Couleur 1 du dégradé',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9146ff',
                'condition' => [
                    'gradient_nombre' => 'oui',
                ],
            ]
        );

        $this->add_control(
            'gradient_couleur2',
            [
                'label' => 'Couleur 2 du dégradé',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff6b6b',
                'condition' => [
                    'gradient_nombre' => 'oui',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Texte
        $this->start_controls_section(
            'style_text_section',
            [
                'label' => 'Style du Texte',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .mpt-counter-text',
            ]
        );

        $this->add_control(
            'couleur_texte',
            [
                'label' => 'Couleur du texte',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'espacement_texte',
            [
                'label' => 'Espacement avec le nombre',
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
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-text' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'direction' => 'column',
                ],
            ]
        );

        $this->end_controls_section();

        // Section Style - Icône
        $this->start_controls_section(
            'style_icon_section',
            [
                'label' => 'Style de l\'Icône',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'icone[value]!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'taille_icone',
            [
                'label' => 'Taille de l\'icône',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 24,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'couleur_icone',
            [
                'label' => 'Couleur de l\'icône',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9146ff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'espacement_icone',
            [
                'label' => 'Espacement de l\'icône',
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
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'position_icone' => 'avant',
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

        $this->add_control(
            'couleur_fond',
            [
                'label' => 'Couleur de fond',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'background_gradient',
                'label' => 'Arrière-plan',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .mpt-counter-wrapper',
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => 'Espacement interne',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .mpt-counter-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mpt-counter-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .mpt-counter-wrapper',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .mpt-counter-wrapper',
            ]
        );

        $this->add_control(
            'hover_effect',
            [
                'label' => 'Effet au survol',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => 'Aucun',
                    'scale' => 'Agrandissement',
                    'rotate' => 'Rotation',
                    'pulse' => 'Pulsation',
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
        if ($settings['auto_refresh'] === 'oui') {
            $options = get_option('mpt_settings');
            $channel_name = $options['twitch_channel'] ?? '';
            delete_transient('mpt_twitch_data_' . $channel_name);
        }
        
        $twitch_data = mpt_get_twitch_data();
        
        // Déterminer la valeur à afficher
        $counter_value = 0;
        switch ($settings['type_compteur']) {
            case 'replays':
                $counter_value = $twitch_data ? count($twitch_data['replays_list']) : 0;
                break;
            case 'clips':
                $counter_value = $twitch_data ? count($twitch_data['clips_list']) : 0;
                break;
            case 'vues_totales':
                $counter_value = $twitch_data ? $twitch_data['total_views'] : 0;
                break;
            case 'spectateurs_live':
                $counter_value = ($twitch_data && $twitch_data['is_live'] && $twitch_data['stream_info']) 
                    ? $twitch_data['stream_info']->viewer_count : 0;
                break;
            case 'personnalise':
                $counter_value = intval($settings['valeur_personnalisee']);
                break;
        }
        
        // Formater le nombre
        $formatted_number = $this->format_number($counter_value);
        
        // Générer les styles CSS personnalisés
        $custom_css = $this->generate_custom_css($settings, $widget_id);
        
        if (!empty($custom_css)) {
            echo '<style>' . $custom_css . '</style>';
        }
        
        // Rendu HTML
        echo '<div class="mpt-counter-wrapper mpt-style-' . esc_attr($settings['style_compteur']) . '">';
        echo '<div class="mpt-counter-container">';
        
        // Icône avant ou au-dessus
        if (!empty($settings['icone']['value']) && in_array($settings['position_icone'], ['avant', 'dessus'])) {
            echo '<div class="mpt-counter-icon">';
            \Elementor\Icons_Manager::render_icon($settings['icone'], ['aria-hidden' => 'true']);
            echo '</div>';
        }
        
        // Conteneur du nombre
        echo '<div class="mpt-counter-number-container">';
        
        // Préfixe
        if (!empty($settings['prefixe'])) {
            echo '<span class="mpt-counter-prefix">' . esc_html($settings['prefixe']) . '</span>';
        }
        
        // Nombre principal
        echo '<span class="mpt-counter-number" data-target="' . esc_attr($counter_value) . '" data-duration="' . esc_attr($settings['duree_animation']['size'] ?? 2000) . '">';
        if ($settings['animation_compteur'] === 'oui') {
            echo '0'; // Commencer à 0 pour l'animation
        } else {
            echo $formatted_number;
        }
        echo '</span>';
        
        // Suffixe
        if (!empty($settings['suffixe'])) {
            echo '<span class="mpt-counter-suffix">' . esc_html($settings['suffixe']) . '</span>';
        }
        
        echo '</div>'; // fin counter-number-container
        
        // Texte
        if (!empty($settings['texte_personnalise'])) {
            echo '<div class="mpt-counter-text">' . esc_html($settings['texte_personnalise']) . '</div>';
        }
        
        // Icône après ou en dessous
        if (!empty($settings['icone']['value']) && in_array($settings['position_icone'], ['apres', 'dessous'])) {
            echo '<div class="mpt-counter-icon">';
            \Elementor\Icons_Manager::render_icon($settings['icone'], ['aria-hidden' => 'true']);
            echo '</div>';
        }
        
        echo '</div>'; // fin counter-container
        echo '</div>'; // fin counter-wrapper
        
        // JavaScript pour l'animation
        if ($settings['animation_compteur'] === 'oui') {
            $this->render_animation_script($widget_id, $counter_value, $settings['duree_animation']['size'] ?? 2000);
        }
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
    private function generate_custom_css($settings, $widget_id) {
        $css = '';
        
        // Dégradé pour le nombre
        if ($settings['gradient_nombre'] === 'oui') {
            $color1 = $settings['gradient_couleur1'] ?? '#9146ff';
            $color2 = $settings['gradient_couleur2'] ?? '#ff6b6b';
            $css .= '.elementor-element-' . $widget_id . ' .mpt-counter-number {
                background: linear-gradient(45deg, ' . $color1 . ', ' . $color2 . ');
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }';
        }
        
        // Effets de survol
        switch ($settings['hover_effect']) {
            case 'scale':
                $css .= '.elementor-element-' . $widget_id . ' .mpt-counter-wrapper:hover { transform: scale(1.1); transition: transform 0.3s ease; }';
                break;
            case 'rotate':
                $css .= '.elementor-element-' . $widget_id . ' .mpt-counter-wrapper:hover { transform: rotate(5deg); transition: transform 0.3s ease; }';
                break;
            case 'pulse':
                $css .= '.elementor-element-' . $widget_id . ' .mpt-counter-wrapper:hover { animation: pulse 1s infinite; }
                @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }';
                break;
            case 'bounce':
                $css .= '.elementor-element-' . $widget_id . ' .mpt-counter-wrapper:hover { animation: bounce 0.6s; }
                @keyframes bounce { 0%, 20%, 60%, 100% { transform: translateY(0); } 40% { transform: translateY(-10px); } 80% { transform: translateY(-5px); } }';
                break;
        }
        
        // Styles selon le type
        switch ($settings['style_compteur']) {
            case 'neon':
                $css .= '.elementor-element-' . $widget_id . ' .mpt-counter-number {
                    text-shadow: 0 0 10px currentColor, 0 0 20px currentColor, 0 0 30px currentColor;
                }';
                break;
            case 'moderne':
                $css .= '.elementor-element-' . $widget_id . ' .mpt-counter-wrapper {
                    background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255,255,255,0.2);
                }';
                break;
        }
        
        // Styles de base
        $css .= '
        .elementor-element-' . $widget_id . ' .mpt-counter-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .elementor-element-' . $widget_id . ' .mpt-counter-number-container {
            display: flex;
            align-items: baseline;
        }
        .elementor-element-' . $widget_id . ' .mpt-counter-prefix,
        .elementor-element-' . $widget_id . ' .mpt-counter-suffix {
            font-size: 0.8em;
            opacity: 0.8;
        }
        ';
        
        return $css;
    }
    
    /**
     * Génère le script d'animation
     */
    private function render_animation_script($widget_id, $target, $duration) {
        ?>
        <script>
        (function() {
            function animateCounter() {
                const counter = document.querySelector('.elementor-element-<?php echo $widget_id; ?> .mpt-counter-number');
                if (!counter) return;
                
                const target = parseInt(counter.dataset.target);
                const duration = parseInt(counter.dataset.duration);
                const increment = target / (duration / 16); // 60fps
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    
                    // Formater le nombre
                    let displayValue = Math.floor(current);
                    if (displayValue >= 1000000) {
                        displayValue = (displayValue / 1000000).toFixed(1) + 'M';
                    } else if (displayValue >= 1000) {
                        displayValue = (displayValue / 1000).toFixed(1) + 'K';
                    } else {
                        displayValue = displayValue.toLocaleString();
                    }
                    
                    counter.textContent = displayValue;
                }, 16);
            }
            
            // Observer pour démarrer l'animation quand visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter();
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            const element = document.querySelector('.elementor-element-<?php echo $widget_id; ?>');
            if (element) {
                observer.observe(element);
            }
        })();
        </script>
        <?php
    }
}
