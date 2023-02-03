<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_MGS_Slider_Widget extends \Elementor\Widget_Base{
    public function get_name(){
		return 'mgs_slider';
	}

    public function get_title(){
		return 'MGS Slider';
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
				'label' => __('Imágen', 'mgs_elementor'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        $this->add_control(
			'gallery',
			[
				'label' => esc_html__( 'Add Images', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'default' => [],
			]
		);
        $this->end_controls_section();

        //navegacion
        $this->start_controls_section(
			'nav_section',
			[
				'label' => __('Navegación', 'mgs_elementor'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        //flechas
        $this->add_control(
			'show_nav',
			[
				'label'         => esc_html__('Flechas', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::SWITCHER,
				'label_on'      => esc_html__('Si', 'mgs_elementor'),
				'label_off'     => esc_html__('No', 'mgs_elementor'),
				'return_value'  => 'yes',
				'default'       => '',
			]
		);
        //flechas tipo
        $this->add_control(
			'nav_type',
			[
				'label'         => esc_html__('Tipo', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::SWITCHER,
				'label_on'      => esc_html__('Texto', 'mgs_elementor'),
				'label_off'     => esc_html__('Icono', 'mgs_elementor'),
				'return_value'  => 'text',
				'default'       => 'text',
                'condition' => [
                    'show_nav' => 'yes',
                ],
			]
		);
        //text prev
        $this->add_control(
			'nav_text_prev',
			[
				'label'         => esc_html__('Texto previo', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::TEXT,
				'default'       => esc_html__('Anterior', 'mgs_elementor'),
                'condition' => [
                    'show_nav'  => 'yes',
                    'nav_type'  => 'text',
                ],
			]
		);
        //text next
        $this->add_control(
			'nav_text_prox',
			[
				'label'         => esc_html__('Texto próximo', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::TEXT,
				'default'       => esc_html__('Siguiente', 'mgs_elementor'),
                'condition' => [
                    'show_nav'  => 'yes',
                    'nav_type'  => 'text',
                ],
			]
		);
        //icon prev
        $this->add_control(
			'nav_icon_prev',
			[
				'label'         => esc_html__('Icono previo', 'textdomain'),
				'type'          => \Elementor\Controls_Manager::ICONS,
                'skin'          => 'inline',
				'default'       => [
					'value'         => 'fas fa-circle-left',
					'library'       => 'fa-solid',
				],
                'condition'     => [
                    'show_nav'      => 'yes',
                    'nav_type'      => '',
                ],
			]
		);
        //icon next
        $this->add_control(
			'nav_icon_prox',
			[
				'label'         => esc_html__('Icono próximo', 'textdomain'),
				'type'          => \Elementor\Controls_Manager::ICONS,
                'skin'          => 'inline',
				'default'       => [
					'value'         => 'fas fa-circle-right',
					'library'       => 'fa-solid',
				],
                'condition'     => [
                    'show_nav'      => 'yes',
                    'nav_type'      => '',
                ],
			]
		);

        //puntos
        $this->add_control(
			'show_dots',
			[
				'label'         => esc_html__('Puntos', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::SWITCHER,
				'label_on'      => esc_html__('Si', 'mgs_elementor'),
				'label_off'     => esc_html__('No', 'mgs_elementor'),
				'return_value'  => 'yes',
				'default'       => '',
			]
		);
        //tamaño punt0
        $this->add_responsive_control(
			'dot_size',
			[
				'type'              => \Elementor\Controls_Manager::SLIDER,
				'label'             => esc_html__('Tamaño', 'mga_elementor' ),
				'range'             => [
					'px'    => [
						'min'   => 0,
						'max'   => 100,
					],
				],
				'devices'           => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default'   => [
					'size'  => 20,
					'unit'  => 'px',
				],
				'tablet_default'    => [
					'size'  => 20,
					'unit'  => 'px',
				],
				'mobile_default'    => [
					'size'  => 10,
					'unit'  => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} button.owl-dot span' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
                'condition'     => [
                    'show_dots'      => 'yes',
                ],
			]
		);
        //border radius punto
        $this->add_control(
			'dot_border_radius',
			[
				'label'         => esc_html__('Radio', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::SLIDER,
				'size_units'    => ['px', '%'],
				'range'         => [
					'px'    => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
					'%'     => [
						'min'   => 0,
						'max'   => 100,
					],
				],
				'default'       => [
					'unit'  => '%',
					'size'  => 50,
				],
				'selectors' => [
					'{{WRAPPER}} button.owl-dot span' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
                'condition'     => [
                    'show_dots'      => 'yes',
                ],
			]
		);
        //color
        $this->add_control(
			'dot_color',
			[
				'label'         => esc_html__('Color', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} button.owl-dot span' => 'background-color: {{VALUE}}',
				],
                'condition'     => [
                    'show_dots'      => 'yes',
                ],
			]
		);
        //color activo
        $this->add_control(
			'dot_color_active',
			[
				'label'         => esc_html__('Color activo', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} button.owl-dot.active span' => 'background-color: {{VALUE}}',
				],
                'condition'     => [
                    'show_dots'      => 'yes',
                ],
			]
		);
        //separacion
        $this->add_responsive_control(
			'dot_gap',
			[
				'type'              => \Elementor\Controls_Manager::SLIDER,
				'label'             => esc_html__('Separación', 'mga_elementor' ),
				'range'             => [
					'px'    => [
						'min'   => 0,
						'max'   => 100,
					],
				],
				'devices'           => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default'   => [
					'size'  => 5,
					'unit'  => 'px',
				],
				'tablet_default'    => [
					'size'  => 5,
					'unit'  => 'px',
				],
				'mobile_default'    => [
					'size'  => 5,
					'unit'  => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} button.owl-dot span' => 'margin: {{SIZE}}{{UNIT}};',
				],
                'condition'     => [
                    'show_dots'      => 'yes',
                ],
			]
		);
        $this->end_controls_section();
        
        
        //opciones
        $this->start_controls_section(
			'options_section',
			[
				'label' => __('Opciones', 'mgs_elementor'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        //items
        $this->add_responsive_control(
			'elementos',
			[
				'type' => \Elementor\Controls_Manager::SLIDER,
				'label' => esc_html__('Mostrar', 'mgs_elementor' ),
                'description'   => esc_html__('Cantidad de elementos que se muestran en pantalla', 'mgs_elementor'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 12,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 6,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 3,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 2,
					'unit' => 'px',
				]
			]
		);
        //loop
        $this->add_control(
			'loop',
			[
				'label'         => esc_html__('Loop', 'mgs_elementor'),
				'description'   => esc_html__('Efecto continuo', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::SWITCHER,
				'label_on'      => esc_html__('Si', 'mgs_elementor'),
				'label_off'     => esc_html__('No', 'mgs_elementor'),
				'return_value'  => 'yes',
				'default'       => 'yes',
			]
		);
        //autowidth
        $this->add_control(
			'autowidth',
			[
				'label'         => esc_html__('Ancho automatico', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::SWITCHER,
				'label_on'      => esc_html__('Si', 'mgs_elementor'),
				'label_off'     => esc_html__('No', 'mgs_elementor'),
				'return_value'  => 'yes',
				'default'       => 'yes',
			]
		);
        //autoplay
        $this->add_control(
			'autoplay',
			[
				'label'         => esc_html__('Autoplay', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::SWITCHER,
				'label_on'      => esc_html__('Si', 'mgs_elementor'),
				'label_off'     => esc_html__('No', 'mgs_elementor'),
				'return_value'  => 'yes',
				'default'       => 'yes',
			]
		);
        //autoplayTimeout
        $this->add_control(
			'autoplayTimeout',
			[
				'label'         => esc_html__('Velocidad', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::SLIDER,
				'size_units'    => ['ms'],
				'range'         => [
					'ms'    => [
						'min'   => 1,
						'max'   => 15000,
						'step'  => 1,
					]
				],
				'default'       => [
					'unit'  => 'ms',
					'size'  => 5000,
                ],
                'condition'     => [
                    'autoplay'      => 'yes',
                ],
			]
		);
        //autoplayHoverPause
        $this->add_control(
			'autoplayHoverPause',
			[
				'label'         => esc_html__('Pausar onMouse hover', 'mgs_elementor'),
				'type'          => \Elementor\Controls_Manager::SWITCHER,
				'label_on'      => esc_html__('Si', 'mgs_elementor'),
				'label_off'     => esc_html__('No', 'mgs_elementor'),
				'return_value'  => 'yes',
				'default'       => 'yes',
			]
		);
        $this->end_controls_section();
	}

    protected function render(){
        $out = '';
        $nav_text_l = '';
        $nav_text_r = '';
        $un_id = uniqid();
        $w_id = 'mgs_slider_w_id_'.$un_id;
        $out .= '
            <div class="mgs-slider owl-carousel" id="'.$w_id.'">
        ';
        $settings = $this->get_settings_for_display();
		foreach( $settings['gallery'] as $image ){
            $medidas = wp_get_attachment_image_src($image['id'], 'full');
			$out .= '
                <div class="item" style="width:'.$medidas[1].'px">
                    <img decoding="sync" class="owl-laz" src="'.esc_attr($image['url']).'">
                </div>
            ';
		}

        $nav = ( isset($settings['show_nav']) && $settings['show_nav']=='yes' ) ? 'true' : 'false';
        if( $settings['show_nav']=='yes' && $settings['nav_type']=='text' ){
            $nav_text_l = ( isset($settings['nav_text_prev']) && $settings['nav_text_prev']!='' ) ? $settings['nav_text_prev'] : esc_html__('Anterior', 'mgs_elementor');
            $nav_text_r = ( isset($settings['nav_text_prox']) && $settings['nav_text_prox']!='' ) ? $settings['nav_text_prox'] : esc_html__('Siguiente', 'mgs_elementor');
        }elseif( $settings['show_nav']=='yes' && $settings['nav_type']=='' ){
            if( isset($settings['nav_icon_prev']) ){
                ob_start();
                \Elementor\Icons_Manager::render_icon( $settings['nav_icon_prev'], [ 'aria-hidden' => 'true' ] );
                $nav_text_l = ob_get_clean();
            }else{
                $nav_text_l = '<i class="fas fa-circle-left"></i>';
            }
            if( isset($settings['nav_icon_prox']) ){
                ob_start();
                \Elementor\Icons_Manager::render_icon( $settings['nav_icon_prox'], [ 'aria-hidden' => 'true' ] );
                $nav_text_r = ob_get_clean();
            }else{
                $nav_text_r = '<i class="fas fa-circle-right"></i>';
            }
            $nav_text_l = str_replace('"', "'", $nav_text_l);
            //$nav_text_l = htmlspecialchars($nav_text_l, ENT_QUOTES);
            
            $nav_text_r = str_replace('"', "'", $nav_text_r);
            //$nav_text_r = htmlspecialchars($nav_text_r, ENT_QUOTES);
        }

        $dots = ( isset($settings['show_dots']) && $settings['show_dots']=='yes' ) ? 'true' : 'false';
        
        $loop = ( $settings['loop']=='yes' ) ? 'true' : 'false';
        $autowidth = ( $settings['autowidth']=='yes' ) ? 'true' : 'false';
        $autoplay = ( $settings['autoplay']=='yes' ) ? 'true' : 'false';
        $autoplayHoverPause = ( $settings['autoplayHoverPause']=='yes' ) ? 'true' : 'false';
        $items = ( isset($settings['elementos']['size']) && $settings['elementos']['size']>0 ) ? $settings['elementos']['size'] : 5;
        $items_mobile = ( isset($settings['elementos_mobile']['size']) && $settings['elementos_mobile']['size']>0 ) ? $settings['elementos_mobile']['size'] : 2;
        $items_tablet = ( isset($settings['elementos_tablet']['size']) && $settings['elementos_tablet']['size']>0 ) ? $settings['elementos_tablet']['size'] : 3;
        
        //$out .= '<pre>'.print_r($settings['elementos'], true).'</pre>';
        
        $out .= '
            </div>
            <script>
                jQuery(function($){
                    $("#'.$w_id.'").owlCarousel({
                        loop                : '.$loop.',
                        autoplay            : '.$autoplay.',
                        autoplayHoverPause  : '.$autoplayHoverPause.',
                        autoplaySpeed       : '.$settings['autoplayTimeout']['size'].',
                        autoplayTimeout     : '.$settings['autoplayTimeout']['size'].',
                        slideTransition     : "linear",
                        lazyLoad            : false,
                        autoWidth           : '.$autowidth.',
                        items               : '.$items.',
                        nav                 : '.$nav.',
                        navText             : [
                            "'.$nav_text_l.'",
                            "'.$nav_text_r.'"
                        ],
                        dots                : '.$dots.',
                        responsive          : {
                            0       : {
                                items   : '.$items_mobile.'
                            },
                            600     : {
                                items   : '.$items_tablet.'
                            },
                            1000    : {
                                items   : '.$items.'
                            }
                        }
                    })
                });
            </script>
        ';
        
        //$out .= '<pre>'.print_r($settings['items'], true).'</pre>';
        echo $out;
	}
}