<?php
// Empêcher l'accès direct et vérifier qu'Elementor est disponible
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class MPT_Elementor_Countdown_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'twitch_countdown';
    }

    public function get_title() {
        return 'Countdown Twitch';
    }

    public function get_icon() {
        return 'eicon-countdown';
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
            'countdown_type',
            [
                'label' => 'Type de countdown',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'manual',
                'options' => [
                    'manual' => 'Date/heure manuelle',
                    'schedule' => 'Planning automatique',
                    'next_stream' => 'Prochain stream (si configuré)',
                ],
            ]
        );

        $this->add_control(
            'target_date',
            [
                'label' => 'Date cible',
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'default' => date('Y-m-d H:i', strtotime('+1 day')),
                'condition' => [
                    'countdown_type' => 'manual',
                ],
            ]
        );

        $this->add_control(
            'title_text',
            [
                'label' => 'Titre',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Prochain stream dans :',
                'placeholder' => 'Ex: Stream dans, Événement dans...',
            ]
        );

        $this->add_control(
            'finished_text',
            [
                'label' => 'Texte quand terminé',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '🔴 Stream en cours !',
                'placeholder' => 'Texte affiché quand le countdown est terminé',
            ]
        );

        $this->add_control(
            'show_elements',
            [
                'label' => 'Éléments à afficher',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['days', 'hours', 'minutes', 'seconds'],
                'options' => [
                    'days' => 'Jours',
                    'hours' => 'Heures',
                    'minutes' => 'Minutes',
                    'seconds' => 'Secondes',
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
            'layout_style',
            [
                'label' => 'Style de mise en page',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => 'Horizontal',
                    'vertical' => 'Vertical',
                    'grid' => 'Grille 2x2',
                    'compact' => 'Compact',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'numbers_typography',
                'label' => 'Typographie des chiffres',
                'selector' => '{{WRAPPER}} .mpt-countdown-number',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'labels_typography',
                'label' => 'Typographie des labels',
                'selector' => '{{WRAPPER}} .mpt-countdown-label',
            ]
        );

        $this->add_control(
            'numbers_color',
            [
                'label' => 'Couleur des chiffres',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9146ff',
                'selectors' => [
                    '{{WRAPPER}} .mpt-countdown-number' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'labels_color',
            [
                'label' => 'Couleur des labels',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .mpt-countdown-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => 'Couleur de fond',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f9fa',
                'selectors' => [
                    '{{WRAPPER}} .mpt-countdown-item' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .mpt-countdown-item' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        
        // Déterminer la date cible
        $target_timestamp = 0;
        if ($settings['countdown_type'] === 'manual') {
            $target_timestamp = strtotime($settings['target_date']);
        }
        
        // Si pas de date valide, ne rien afficher
        if ($target_timestamp <= time()) {
            echo '<div class="mpt-countdown-finished">' . esc_html($settings['finished_text']) . '</div>';
            return;
        }
        
        $elements = $settings['show_elements'];
        $layout = $settings['layout_style'];
        
        ?>
        <div class="mpt-countdown-container mpt-layout-<?php echo esc_attr($layout); ?>">
            <?php if (!empty($settings['title_text'])) : ?>
                <h3 class="mpt-countdown-title"><?php echo esc_html($settings['title_text']); ?></h3>
            <?php endif; ?>
            
            <div class="mpt-countdown-wrapper" id="countdown-<?php echo $widget_id; ?>" data-target="<?php echo $target_timestamp; ?>">
                <?php if (in_array('days', $elements)) : ?>
                    <div class="mpt-countdown-item">
                        <div class="mpt-countdown-number" data-unit="days">00</div>
                        <div class="mpt-countdown-label">Jours</div>
                    </div>
                <?php endif; ?>
                
                <?php if (in_array('hours', $elements)) : ?>
                    <div class="mpt-countdown-item">
                        <div class="mpt-countdown-number" data-unit="hours">00</div>
                        <div class="mpt-countdown-label">Heures</div>
                    </div>
                <?php endif; ?>
                
                <?php if (in_array('minutes', $elements)) : ?>
                    <div class="mpt-countdown-item">
                        <div class="mpt-countdown-number" data-unit="minutes">00</div>
                        <div class="mpt-countdown-label">Minutes</div>
                    </div>
                <?php endif; ?>
                
                <?php if (in_array('seconds', $elements)) : ?>
                    <div class="mpt-countdown-item">
                        <div class="mpt-countdown-number" data-unit="seconds">00</div>
                        <div class="mpt-countdown-label">Secondes</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
        .mpt-countdown-container {
            text-align: center;
        }
        .mpt-countdown-title {
            margin-bottom: 20px;
            color: #333;
        }
        .mpt-countdown-wrapper {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .mpt-layout-vertical .mpt-countdown-wrapper {
            flex-direction: column;
            align-items: center;
        }
        .mpt-layout-grid .mpt-countdown-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 300px;
            margin: 0 auto;
        }
        .mpt-layout-compact .mpt-countdown-wrapper {
            gap: 5px;
        }
        .mpt-layout-compact .mpt-countdown-item {
            padding: 8px 12px;
        }
        .mpt-countdown-item {
            padding: 15px 20px;
            text-align: center;
            min-width: 80px;
        }
        .mpt-countdown-number {
            font-size: 2em;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 5px;
        }
        .mpt-countdown-label {
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .mpt-countdown-finished {
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
            color: #e74c3c;
            padding: 20px;
        }
        @media (max-width: 768px) {
            .mpt-countdown-wrapper {
                gap: 10px;
            }
            .mpt-countdown-item {
                padding: 10px 15px;
                min-width: 60px;
            }
            .mpt-countdown-number {
                font-size: 1.5em;
            }
        }
        </style>
        
        <script>
        (function() {
            function updateCountdown() {
                const container = document.getElementById('countdown-<?php echo $widget_id; ?>');
                if (!container) return;
                
                const target = parseInt(container.dataset.target) * 1000;
                const now = new Date().getTime();
                const distance = target - now;
                
                if (distance < 0) {
                    container.innerHTML = '<div class="mpt-countdown-finished"><?php echo esc_js($settings['finished_text']); ?></div>';
                    return;
                }
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                const daysEl = container.querySelector('[data-unit="days"]');
                const hoursEl = container.querySelector('[data-unit="hours"]');
                const minutesEl = container.querySelector('[data-unit="minutes"]');
                const secondsEl = container.querySelector('[data-unit="seconds"]');
                
                if (daysEl) daysEl.textContent = days.toString().padStart(2, '0');
                if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
                if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
                if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        })();
        </script>
        <?php
    }
}
