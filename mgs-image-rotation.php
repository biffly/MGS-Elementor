<?php

if( !class_exists('MGS_ImageRotation') ){
    class MGS_ImageRotation{
        public $debug = true;
        public function __construct(){
            if( get_option('mgs-elementor-addon-state-image_rotation')=='on' ){
                //css editor
                add_action('elementor/editor/before_enqueue_scripts', [$this, 'mgs_image_rotation_before_load_panel_assets']);
                //inject controles
                add_action('elementor/element/before_section_start', [$this, 'mgs_image_rotation_inject_custom_section'], 99999, 3);

                add_action('elementor/element/parse_css', [$this, 'add_custom_css_rule_to_the_css_file'], 99999, 2);
                //add_action('elementor/frontend/after_render', [$this, 'add_div_after_all_elements']);

                add_action( 'elementor/frontend/before_render', [$this, 'add_attributes_to_elements'] );

                add_action('wp_footer', [$this, 'mgs_image_rotation_editor_show']);
            }
        }

        public function add_div_after_all_elements($element){
            $settings = $element->get_settings();
            if( $this->debug && isset($settings['activar-image_rotation']) && $settings['activar-image_rotation'] ){
                $un_id = uniqid();
                echo '<div class="mgs-image_rotation-wrapper" id="mgs-image_rotation-wrapper-'.$un_id.'"></div>';
                //echo '<pre>'.print_r($settings, true).'</pre>'; 
                //echo '<pre>'.print_r($settings['altura-image_rotation']['size'], true).'</pre>'; 
            }
        }

        public function add_attributes_to_elements($element){
            $settings = $element->get_settings();
            if( $this->debug && isset($settings['activar-image_rotation']) && $settings['activar-image_rotation'] ){
                $un_id = uniqid();
                $imagen = array_rand($settings['gallery-image_rotation'], 1);
                $element->add_render_attribute(
                    '_wrapper',
                    [
                        'class'                 => 'mgs-image_rotation-wrapper-'.$un_id
                    ]
                );

                echo '
                    <style>
                        .mgs-image_rotation-wrapper-'.$un_id.'{
                            background-image: url('.$settings['gallery-image_rotation'][$imagen]['url'].') !important;
                        }
                    </style>
                ';

            }
        }

        public function add_custom_css_rule_to_the_css_file($post_css_file, $element){
            $settings = $element->get_settings();
            if( $this->debug && isset($settings['activar-image_rotation']) && $settings['activar-image_rotation'] ){
                $altura = $settings['altura-image_rotation']['size'].$settings['altura-image_rotation']['unit'];
                /*$post_css_file->get_stylesheet()->add_rules(
                    $element->get_unique_selector(),
                    [
                        'height'                => $altura
                    ]
                );*/

                //if( $this->debug ) echo '<pre>'.print_r($element->get_unique_selector(), true).'</pre>';
                //if( $this->debug ) echo '<pre>'.print_r($post_css_file, true).'</pre>';
            }
        }

        public function mgs_image_rotation_inject_custom_section($element, $section_id, $args){
            if( $section_id==='section_background_overlay' ){
                if( $element->get_name()==='container' ) $this->register_controls($element, $args);    
            }
        }

        public function register_controls($element, $args){
            $element->start_controls_section('image_rotation', ['label' => __('MGS Fondo aleatorio', 'mgs_elementor'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
                $element->add_control(
                    'activar-image_rotation',
                    [
                        'label'         => esc_html__('Activar fondo aleatorio?', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SWITCHER,
                        'label_on'      => esc_html__('Si', 'mgs_elementor'),
                        'label_off'     => esc_html__('No', 'mgs_elementor'),
                        'return_value'  => 'yes',
                        'default'       => '',
                        'prefix_class'   => 'mgs-image_rotation-',
                    ]
                );
                $element->add_control(
                    'gallery-image_rotation',
                    [
                        'label'     => esc_html__('Imagenes', 'mgs_elementor'),
                        'type'      => \Elementor\Controls_Manager::GALLERY,
                        'default'   => [],
                        'condition'     => [
                            'activar-image_rotation'     => 'yes',
                        ],
                        'selectors' => [
                            '{{WRAPPER}}' => 'background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255, 203, 1, 1) 10px, rgba(255, 203, 1, 1) 20px )',
                        ]
                    ]
                );
                $element->add_responsive_control(
                    'altura-image_rotation',
                    [
                        'label'         => esc_html__('Altura', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SLIDER,
                        'size_units'    => ['px', 'em', 'rem', 'vh'],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 1000,
                            ],
                            'em' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                            'em' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                            'vh' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'devices' => [ 'desktop', 'tablet', 'mobile' ],
                        'desktop_default' => [
                            'size' => 250,
                            'unit' => 'px',
                        ],
                        'tablet_default' => [
                            'size' => 200,
                            'unit' => 'px',
                        ],
                        'mobile_default' => [
                            'size' => 100,
                            'unit' => 'px',
                        ],
                        'condition'     => [
                            'activar-image_rotation'     => 'yes',
                        ],
                        'selectors' => [
                            '{{WRAPPER}}' => 'height: {{SIZE}}{{UNIT}}',
                        ],
                    ]
                );
                $element->add_responsive_control(
                    'posicion-image_rotation',
                    [
                        'label'         => esc_html__('Posición', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT,
                        'options'       => [
                            ''                  => esc_html__('Por defecto', 'mgs_elemantor'),
                            'center center'     => esc_html__('Centro centro', 'mgs_elemantor'),
                            'center left'       => esc_html__('Centro izquierda', 'mgs_elemantor'),
                            'center right'      => esc_html__('Centro derecha', 'mgs_elemantor'),
                            'top center'        => esc_html__('Arriba centro', 'mgs_elemantor'),
                            'top left'          => esc_html__('Arriba izquierda', 'mgs_elemantor'),
                            'top right'         => esc_html__('Arriba derecha', 'mgs_elemantor'),
                            'bottom center'     => esc_html__('Abajo centro', 'mgs_elemantor'),
                            'bottom left'       => esc_html__('Abajo izquierda', 'mgs_elemantor'),
                            'bottom right'      => esc_html__('Abajo derecha', 'mgs_elemantor'),
                        ],
                        'default'       => 'center center',
                        'condition'     => [
                            'activar-image_rotation'     => 'yes',
                        ],
                        'selectors' => [
                            '{{WRAPPER}}' => 'background-position: {{VALUE}}',
                        ],
                    ]
                );
                $element->add_responsive_control(
                    'repetir-image_rotation',
                    [
                        'label'         => esc_html__('Repetir', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT,
                        'options'       => [
                            ''                  => esc_html__('Por defecto', 'mgs_elemantor'),
                            'no-repeat'         => esc_html__('No-repetir', 'mgs_elemantor'),
                            'repeat'            => esc_html__('Repetir', 'mgs_elemantor'),
                            'repeat-x'          => esc_html__('Repetir-x', 'mgs_elemantor'),
                            'repeat-y'          => esc_html__('Repetir-y', 'mgs_elemantor')
                        ],
                        'default'       => 'no-repeat',
                        'condition'     => [
                            'activar-image_rotation'     => 'yes',
                        ],
                        'selectors' => [
                            '{{WRAPPER}}' => 'background-repeat: {{VALUE}}',
                        ],
                    ]
                );
                $element->add_responsive_control(
                    'size-image_rotation',
                    [
                        'label'         => esc_html__('Tamaño', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT,
                        'options'       => [
                            ''                  => esc_html__('Por defecto', 'mgs_elemantor'),
                            'auto'              => esc_html__('Auto', 'mgs_elemantor'),
                            'cover'             => esc_html__('Abarcar', 'mgs_elemantor'),
                            'contain'           => esc_html__('Contiene', 'mgs_elemantor')
                        ],
                        'default'       => 'cover',
                        'condition'     => [
                            'activar-image_rotation'     => 'yes',
                        ],
                        'selectors' => [
                            '{{WRAPPER}}' => 'background-size: {{VALUE}}',
                        ],
                    ]
                );
            $element->end_controls_section();
        }

        public function mgs_image_rotation_editor_show(){
            if( !class_exists('Elementor\Plugin') || !\Elementor\Plugin::$instance->preview->is_preview_mode() ) return;
    
            ?>
            <style>
                body.elementor-editor-active .elementor-element.mgs-image_rotation-yes::before {
                    content: 'FONDO ALEATORIO';
                    color: #fff;
                    background: linear-gradient(180deg, rgba(255,145,0,1) 0%, rgba(182,104,1,1) 100%);
                    display: block;
                    font-size: 25px;
                    line-height: 45px;
                    text-align: center;
                    position: absolute;
                    top: 10%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 50%;
                    height: auto;
                    border-radius: 10px;
                    box-shadow: 0 0 5px 0 rgb(0 0 0 / 50%);
                    font-family: sans-serif;
                }
            </style>
            <?php
        }

        public function mgs_image_rotation_before_load_panel_assets(){
            wp_enqueue_style('mgs_image_rotation_editor_css', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/css/panel-image-rotation.css');
        }
    }
}
new MGS_ImageRotation();
    