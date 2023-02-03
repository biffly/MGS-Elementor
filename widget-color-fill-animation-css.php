<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_MGS_Color_Fill_Animation_CSS extends \Elementor\Widget_Base{
    public function get_name(){
		return 'mgs_color_fill_animation_css';
	}

    public function get_title(){
		return 'MGS Color Fill Animation CSS';
	}

    public function get_icon(){
		return 'eicon-code';
	}

    public function get_custom_help_url(){
		return 'https://developers.elementor.com/docs/widgets/';
	}

    public function get_categories() {
		return ['general'];
	}

    public function get_keywords() {
		return ['MGS', 'slider'];
	}

    protected function register_controls(){
		$this->start_controls_section(
			'images_section',
			[
				'label' => __('Imágenes', 'mgs_elementor'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
            $this->add_control(
                'image_fondo',
                [
                    'label'         => esc_html__('Imagen fondo', 'mgs_elementor'),
                    'description'   => esc_html__('Imagen que se visualiza de fondo, normalmente en blanco y negro', 'mgs_elementor'),
                    'type'          => \Elementor\Controls_Manager::MEDIA,
                    'default'       => [
                        'url'           => MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/imgs/back.jpg',
                        //'url'           => \Elementor\Utils::get_placeholder_image_src(),
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .back-img' => 'background-image: url({{URL}});',
                    ],
                ]
            );
            $this->add_control(
                'image_frente',
                [
                    'label'         => esc_html__('Imagen final', 'mgs_elementor'),
                    'description'   => esc_html__('Imagen que se visualiza tras aplicar la mascara', 'mgs_elementor'),
                    'type'          => \Elementor\Controls_Manager::MEDIA,
                    'default'       => [
                        'url'           => MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/imgs/front.jpg',
                        //'url'           => \Elementor\Utils::get_placeholder_image_src(),
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .front-img' => 'background-image: url({{URL}});',
                    ],
                ]
            );
            $this->add_control(
                'image_mascara',
                [
                    'label'         => esc_html__('Mascara', 'mgs_elementor'),
                    'type'          => \Elementor\Controls_Manager::MEDIA,
                    'default'       => [
                        'url'           => MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/imgs/mask.png',
                        //'url'           => \Elementor\Utils::get_placeholder_image_src(),
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .front-img' => '-webkit-mask-image: url({{URL}}); mask-image: url({{URL}});',
                    ],
                ]
            );
            $this->add_control(
                'help_mask',
                [
                    //'label'             => esc_html__('Ayuda sobre la mascara', 'mgs_elementor'),
                    'type'              => \Elementor\Controls_Manager::RAW_HTML,
                    'raw'               => __('
                                                <p>La mascara es una imagen PNG con fondo transparente, y el contenido debe estar en negra, este contenido o partes en negro sera que que se dejara ver de la imagen final.</p>
                                                <p>La mascara esta compuesta de varios <code>pasos</code>. Estos permiten la animación. Estos <code>pasos</code> van de izquierda a derecha, mientras mas <code>pasos</code> tenga la imagen,mas fluida sera la animación. Se debe dejar el primer <code>pasos</code> completamente transparente.</p>
                                                <p>El ancho de la imagen de la mascara dependera de la cantidad de <code>pasos</code> creados. Cada paso debera ser proporcionalmente igual a las imagenes de fondo y final. Pero como esta mascara no requiere gran resolución recomendamos que su alto sea de no mas del 30% de las imagenes principales.</p>
                                                <h4>Material de referencia</h4>
                                                <ul>
                                                    <li>- Imagen fondo (<a href="'.MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/imgs/back.jpg'.'" target="_blank">back.jpg</a>)
                                                    <li>- Imagen frente (<a href="'.MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/imgs/front.jpg'.'" target="_blank">front.jpg</a>)
                                                    <li>- Mascara (<a href="'.MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/imgs/mask.png'.'" target="_blank">mask.jpg</a>)
                                                </ul>
                                            ', 'textdomain'),
                    'content_classes'   => 'mgs_elementor_editor_rawhtml',
                ]
            );
            $this->add_responsive_control(
                'alto_image',
                [
                    'type'          => \Elementor\Controls_Manager::SLIDER,
                    'label'         => esc_html__('Alto', 'mgs_elementor'),
                    'size_units'    => ['px', '%', 'rem', 'vh'],
                    'devices'       => ['desktop', 'tablet', 'mobile'],
                    'default'       => [
                        'unit'  => 'vh',
                        'size'  => 50,
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .mgs_color_fill_animation_css_wrapper' => 'height: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .mgs_color_fill_animation_css_wrapper .img_fondo' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'posicion-image',
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
                        'selectors' => [
                            '{{WRAPPER}} .back-img' => 'background-position: {{VALUE}}',
                            '{{WRAPPER}} .front-img' => 'background-position: {{VALUE}}',
                        ],
                    ]
            );
            $this->add_responsive_control(
                'size-image',
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
                        'selectors'     => [
                            '{{WRAPPER}} .back-img' => 'background-size: {{VALUE}}',
                            '{{WRAPPER}} .front-img' => 'background-size: {{VALUE}}',
                        ],
                    ]
            );
        $this->end_controls_section();
        
        $this->start_controls_section(
            'animation_section',
			[
                'label' => __('Animación', 'mgs_elementor'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
            //velocidad
            $this->add_control(
                'speed_ani',
                [
                    'type'          => \Elementor\Controls_Manager::SLIDER,
                    'label'         => esc_html__('Velocidad animación', 'mgs_elementor'),
                    'size_units'    => ['s', 'ms'],
                    'range'         => [
                        's'     => [
                            'min'       => 0,
                            'max'       => 60,
                            'step'      => 1,
                        ],
                        'ms'    => [
                            'min'       => 0,
                            'max'       => 10000,
                            'step'      => 1
                        ],
                    ],
                    'default'   => [
                        'unit'      => 's',
                        'size'      => 5,
                    ],
                ]
            );
            //delay
            $this->add_control(
                'delay_ani',
                [
                    'type'          => \Elementor\Controls_Manager::SLIDER,
                    'label'         => esc_html__('Tiempo de espera.', 'mgs_elementor'),
                    'description'   => esc_html__('Tiempo que se espera antes de comenzar la animación.', 'mgs_elementor'),
                    'size_units'    => ['s', 'ms'],
                    'range'         => [
                        's'     => [
                            'min'       => 0,
                            'max'       => 60,
                            'step'      => 1,
                        ],
                        'ms'    => [
                            'min'       => 0,
                            'max'       => 10000,
                            'step'      => 1
                        ],
                    ],
                    'default'   => [
                        'unit'      => 's',
                        'size'      => 0,
                    ]
                ]
            );
            $this->add_control(
                'steps_ani',
                [
                    'label' => esc_html__('Pasos', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 1000,
                    'step' => 1,
                    'default' => 3,
                ]
            );

            //dividewr
            $this->add_control(
                'hr',
                [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );


            $this->add_control(
                'type_ani',
                [
                    'label'     => esc_html__('Activar por', 'mgs_elementor'),
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'default'   => 'time',
                    'options'   => [
                        'time'      => esc_html__('Tiempo', 'mgs_elementor'),
                        'observer'  => esc_html__('Scroll', 'mgs_elementor'),
                    ]
                ]
            );
            //offset scroll
            $this->add_control(
                'offset_ani',
                [
                    'type'          => \Elementor\Controls_Manager::SLIDER,
                    'label'         => esc_html__('Porcentaje.', 'mgs_elementor'),
                    'description'   => esc_html__('Porcentaje del elemento que debe estar visible para comenzar la animación', 'mgs_elementor'),
                    'size_units'    => ['%'],
                    'range'         => [
                        '%'     => [
                            'min'       => 0,
                            'max'       => 100,
                            'step'      => 1,
                        ]
                    ],
                    'default'   => [
                        'unit'      => '%',
                        'size'      => 50,
                    ],
                    'condition' => [
                        'type_ani'  => 'observer',
                    ],
                ]
            );



        $this->end_controls_section();
    }

    protected function render(){
        $settings = $this->get_settings_for_display();
        $un_id = uniqid();
        $w_id = 'mgs_color_fill_animation_css_'.$un_id;
        $class_img_front = ($settings['type_ani']=='time') ? 'mask-animation ' : '';
        $observer_offset = ( $settings['type_ani']=='observer' && $settings['offset_ani']['size']>0 ) ? $settings['offset_ani']['size']/100 : 0;

        $button_replay = '';
        $script_button_replay = '';
        if( \Elementor\Plugin::$instance->editor->is_edit_mode() ){
            $button_replay = '<button class="reset-animation_'.$un_id.' reset-animation">RE-PLAY</button>';
            $script_button_replay = '
                <script>
                    const resetButton_'.$un_id.' = document.querySelector("#'.$w_id.' .reset-animation_'.$un_id.'");
                    const maskedImage_'.$un_id.' = document.querySelector("#'.$w_id.' .front-img");
                    resetButton_'.$un_id.'.addEventListener("click", () => {
                        maskedImage_'.$un_id.'.classList.remove("mask-animation");
                        setTimeout(() => maskedImage_'.$un_id.'.classList.add("mask-animation"), 300);
                    });
                </script>
            ';
        }


        $out .= '
            <style>
                #'.$w_id.' .mask-animation{
                    animation-name: mask_anim;
                    animation-duration: '.$settings['speed_ani']['size'].$settings['speed_ani']['unit'].';
                    animation-timing-function: steps('.$settings['steps_ani'].');
                    animation-fill-mode: forwards;
                    animation-delay: '.$settings['delay_ani']['size'].$settings['delay_ani']['unit'].';
                }
            </style>
            <div class="mgs_color_fill_animation_css_wrapper" id="'.$w_id.'">
                '.$button_replay.'
                <div class="front-img '.$class_img_front.'"></div>
                <img class="img_fondo" src="'.$settings['image_fondo']['url'].'">
            </div>
            '.$script_button_replay.'
        ';
        if( $settings['type_ani']=='observer' ){
            $out .= '
                <script>
                    const ani_'.$w_id.'             = document.querySelector("#'.$w_id.'");
                    const maskedImage_obs_'.$w_id.' = document.querySelector("#'.$w_id.' .front-img");
                    const obs_'.$w_id.'             = new IntersectionObserver(function(e, o){
                        e.forEach(e => {
                            if( e.isIntersecting ){
                                console.log("VISIBLE");
                                maskedImage_obs_'.$w_id.'.classList.add("mask-animation");
                            }
                        })
                    }, {
                        root        : null,
                        threshold   : '.$observer_offset.',
                    });

                    obs_'.$w_id.'.observe(ani_'.$w_id.');
                </script>
            ';
        }

        echo $out;
	}
}