<?php
if( !defined('ABSPATH') ) exit; // Exit if accessed directly.

class Elementor_MGS_The_Content extends \Elementor\Widget_Base{
    public function get_name(){return 'mgs_the_content';}
    public function get_title(){return 'MGS The Content';}
    public function get_icon(){return 'eicon-code';}
    public function get_custom_help_url(){return 'https://developers.elementor.com/docs/widgets/';}
    public function get_categories(){return ['general'];}
    public function get_keywords(){return ['MGS', 'post', 'plantilla'];}

    protected function register_controls(){
        $this->start_controls_section('section_options', ['label'=>__('Opciones', 'mgs_elementor'),'tab' => \Elementor\Controls_Manager::TAB_CONTENT,]);
            $this->add_responsive_control(
                'columnas',
                [
                    'label'     => esc_html__('Columnas', 'mgs_elementor'),
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'options'   => [
                        1   => esc_html__('Una columna', 'mgs_elementor'),
                        2   => esc_html__('Dos columnas', 'mgs_elementor'),
                        3   => esc_html__('Tres columnas', 'mgs_elementor'),
                        4   => esc_html__('Cuatro columnas', 'mgs_elementor'),
                    ],
                    'default'   => 1,
                    'selectors' => [
                        '{{WRAPPER}} .mgs_the_content' => 'columns: {{VALUE}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'separacion',
                [
                    'type'              => \Elementor\Controls_Manager::SLIDER,
                    'label'             => esc_html__('Tamaño', 'mgs_elementor'),
                    'label_block'       => true,
                    'range'             => [
                        'px'    => [
                            'min'   => 1,
                            'max'   => 100,
                        ],
                    ],
                    'devices'           => ['desktop', 'tablet', 'mobile'],
                    'desktop_default'   => [
                        'size'  => 20,
                        'unit'  => 'px',
                    ],
                    'tablet_default'    => [
                        'size'  => 20,
                        'unit'  => 'px',
                    ],
                    'mobile_default' => [
                        'size'  => 20,
                        'unit'  => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs_the_content' => 'column-gap: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
        $this->end_controls_section();
    }

    protected function render(){
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute([
            'wrapper' => [
                'class' => [
                    'mgs_the_content'
                ],
            ],
        ]);
        ?>
        <div <?php $this->print_render_attribute_string('wrapper')?>><?php the_content()?></div>
        <?php
    }
}