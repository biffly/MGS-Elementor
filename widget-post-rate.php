<?php
if( !defined('ABSPATH') ) exit; // Exit if accessed directly.

class Elementor_MGS_Post_Rate_Widget extends \Elementor\Widget_Base{
    public function get_name(){return 'mgs_post_rate';}
    public function get_title(){return 'MGS Post Rate';}
    public function get_icon(){return 'eicon-code';}
    public function get_custom_help_url(){return 'https://developers.elementor.com/docs/widgets/';}
    public function get_categories(){return ['general'];}
    public function get_keywords(){return ['MGS', 'post', 'rate'];}

    protected function register_controls(){
        $this->start_controls_section('section_icons', ['label'=>__('Iconos', 'mgs_elementor'),'tab' => \Elementor\Controls_Manager::TAB_CONTENT,]);
            $this->add_control(
                'aling',
                [
                    'label'     => esc_html__('Alineación', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::CHOOSE,
                    'options'   => [
                        'left'      => [
                            'title'     => esc_html__('Izquierda', 'mgs_elementor'),
                            'icon'      => 'eicon-text-align-left',
                        ],
                        'center'    => [
                            'title'     => esc_html__('Centro', 'mgs_elementor'),
                            'icon'      => 'eicon-text-align-center',
                        ],
                        'right'     => [
                            'title'     => esc_html__('Derecha', 'mgs_elementor'),
                            'icon'      => 'eicon-text-align-right',
                        ],
                    ],
                    'default'       => 'center',
                    'toggle'        => true,
                    'selectors'     => [
                        '{{WRAPPER}} .estrellas-wrapper'   =>  'text-align: {{VALUE}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'size',
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
                        '{{WRAPPER}} .estrellas-wrapper a.estrella' => 'font-size: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_control(
                'icon',
                [
                    'label'     => esc_html__('Icono', 'mgs_elementor'),
                    'type'      => \Elementor\Controls_Manager::ICONS,
                    'default'   => [
                        'value'     => 'fas fa-star',
                        'library'   => 'fa-solid',
                    ]
                ]
            );

            $this->start_controls_tabs('colors_tabs');
                $this->start_controls_tab('colors_tab_normal',['label'=> esc_html__('Normal', 'mgs_elementor')]);
                    $this->add_control(
                        'color_normal',
                        [
                            'label'         => esc_html__('Color inactivo', 'mgs_elementor'),
                            'type'          => \Elementor\Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .estrellas-wrapper a.estrella' => 'color: {{VALUE}}',
                            ],
                        ]
                    );
                $this->end_controls_tab();
                $this->start_controls_tab('colors_tab_hover',['label'=> esc_html__('Hover', 'mgs_elementor')]);
                    $this->add_control(
                        'color_hover',
                        [
                            'label'         => esc_html__('Color mouse hover', 'mgs_elementor'),
                            'type'          => \Elementor\Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .estrellas-wrapper a.estrella:hover' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .estrellas-wrapper a.estrella_hover' => 'color: {{VALUE}}',
                            ],
                        ]
                    );
                $this->end_controls_tab();
                $this->start_controls_tab('colors_tab_active',['label'=> esc_html__('Activo', 'mgs_elementor')]);
                    $this->add_control(
                        'color_active',
                        [
                            'label'         => esc_html__('Color activo o seleccionado', 'mgs_elementor'),
                            'type'          => \Elementor\Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .estrellas-wrapper a.estrella.active' => 'color: {{VALUE}}',
                            ],
                        ]
                    );
                $this->end_controls_tab();
            $this->end_controls_tabs();
        $this->end_controls_section();
        
        $this->start_controls_section('section_form', ['label'=>__('Formulario', 'mgs_elementor'),'tab' => \Elementor\Controls_Manager::TAB_CONTENT,]);
            if( is_admin() && is_plugin_active('elementor-pro/elementor-pro.php') ){
                $repeater = new \Elementor\Repeater();
                $this->add_control(
                    'add_form',
                    [
                        'label'         => esc_html__('Agregar formulario', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SWITCHER,
                        'label_on'      => esc_html__('Si', 'mgs_elementor'),
                        'label_off'     => esc_html__('No', 'mgs_elementor'),
                        'return_value'  => 'yes',
                        'default'       => 'no',
                    ]
                );
                
                $repeater->start_controls_tabs('form_fields_tabs');
		            $repeater->start_controls_tab('form_fields_content_tab', ['label' => esc_html__('Contenido', 'mgs_elementor')]);
                        $repeater->add_control(
                            'field_type',
                            [
                                'label'     => esc_html__('Tipo', 'mgs_elementor'),
                                'type'      => \Elementor\Controls_Manager::SELECT,
                                'options'   => $this->get_fields_types(),
                                'default'   => 'text'
                            ]
                        );
                        $repeater->add_control(
                            'field_label',
                            [
                                'label' => esc_html__('Etiqueta', 'mgs_elementor'),
                                'type' => \Elementor\Controls_Manager::TEXT,
                                'default' => '',
                                'dynamic' => [
                                    'active' => true,
                                ],
                            ]
                        );
                        $repeater->add_control(
                            'placeholder',
                            [
                                'label' => esc_html__('Placeholder', 'mgs_elementor'),
                                'type' => \Elementor\Controls_Manager::TEXT,
                                'default' => '',
                                'conditions' => [
                                    'terms' => [
                                        [
                                            'name' => 'field_type',
                                            'operator' => 'in',
                                            'value' => [
                                                'text',
                                                'email',
                                                'textarea',
                                                'number',
                                                'url'
                                            ],
                                        ],
                                    ],
                                ],
                                'dynamic' => [
                                    'active' => true,
                                ],
                            ]
                        );
                        $repeater->add_control(
                            'required',
                            [
                                'label' => esc_html__('Obligatorio', 'mgs_elementor'),
                                'type' => \Elementor\Controls_Manager::SWITCHER,
                                'return_value' => 'true',
                                'default' => '',
                                'conditions' => [
                                    'terms' => [
                                        [
                                            'name' => 'field_type',
                                            'operator' => '!in',
                                            'value' => [
                                                'checkbox',
                                                'recaptcha',
                                                'recaptcha_v3',
                                                'hidden',
                                                'html',
                                                'step',
                                            ],
                                        ],
                                    ],
                                ],
                            ]
                        );
                        $repeater->add_control(
                            'field_options',
                            [
                                'label' => esc_html__('Opciones', 'mgs_elementor' ),
                                'type' => \Elementor\Controls_Manager::TEXTAREA,
                                'default' => '',
                                'description' => esc_html__('Una opcion por linea. Para diferenciar entre el valor y la etiqueta, separe estos valores con ("|"). Por ejemplo: Primer nombre|primer_nombre', 'mgs_elementor'),
                                'conditions' => [
                                    'terms' => [
                                        [
                                            'name' => 'field_type',
                                            'operator' => 'in',
                                            'value' => [
                                                'select',
                                                'checkbox',
                                                'radio',
                                            ],
                                        ],
                                    ],
                                ],
                            ]
                        );
                        $repeater->add_control(
                            'rows',
                            [
                                'label' => esc_html__('Filas', 'mgs_elementor'),
                                'type' => \Elementor\Controls_Manager::NUMBER,
                                'default' => 4,
                                'conditions' => [
                                    'terms' => [
                                        [
                                            'name' => 'field_type',
                                            'value' => 'textarea',
                                        ],
                                    ],
                                ],
                            ]
                        );
                    $repeater->end_controls_tab();
                    $repeater->start_controls_tab('form_fields_advanced_tab', ['label' => esc_html__('Avanzado', 'mgs_elementor')]);
                        $repeater->add_control(
                            'field_value',
                            [
                                'label' => esc_html__('Valor por defecto', 'mgs_elementor' ),
                                'type' => \Elementor\Controls_Manager::TEXT,
                                'default' => '',
                                'dynamic' => [
                                    'active' => true,
                                ],
                                'conditions' => [
                                    'terms' => [
                                        [
                                            'name' => 'field_type',
                                            'operator' => 'in',
                                            'value' => [
                                                'text',
                                                'email',
                                                'textarea',
                                                'url',
                                                'tel',
                                                'radio',
                                                'select',
                                                'number',
                                            ],
                                        ],
                                    ],
                                ],
                            ]
                        );
                        $repeater->add_responsive_control(
                            'width',
                            [
                                'label' => esc_html__('Ancho', 'mgs_elementor' ),
                                'type' => \Elementor\Controls_Manager::SELECT,
                                'options' => [
                                    '' => esc_html__('Por defecto', 'mgs_elementor'),
                                    '100' => '100%',
                                    '80' => '80%',
                                    '75' => '75%',
                                    '70' => '70%',
                                    '66' => '66%',
                                    '60' => '60%',
                                    '50' => '50%',
                                    '40' => '40%',
                                    '33' => '33%',
                                    '30' => '30%',
                                    '25' => '25%',
                                    '20' => '20%',
                                ],
                                'default' => '100',
                                'conditions' => [
                                    'terms' => [
                                        [
                                            'name' => 'field_type',
                                            'operator' => '!in',
                                            'value' => [
                                                'hidden',
                                                'recaptcha',
                                                'recaptcha_v3',
                                                'step',
                                            ],
                                        ],
                                    ],
                                ],
                            ]
                        );

                    $repeater->end_controls_tab();
                $repeater->end_controls_tabs();
                
                $this->add_control(
                    'form_fields',
                    [
                        'type'      => \Elementor\Controls_Manager::REPEATER,
                        'fields'    => $repeater->get_controls(),
                        'default'   => [
                            [
                                'custom_id' => 'nombre',
                                'field_type' => 'text',
                                'field_label' => esc_html__('Nombre', 'mgs_elementor'),
                                'placeholder' => esc_html__('Nombre', 'mgs_elementor'),
                                'width' => '100',
                            ],
                            [
                                'custom_id' => 'correo',
                                'field_type' => 'email',
                                'required' => 'true',
                                'field_label' => esc_html__('Correo electrónico', 'mgs_elementor'),
                                'placeholder' => esc_html__('Correo electrónico', 'mgs_elementor'),
                                'width' => '100',
                            ],
                            [
                                'custom_id' => 'comentario',
                                'field_type' => 'textarea',
                                'field_label' => esc_html__('Comentario', 'mgs_elementor'),
                                'placeholder' => esc_html__('Comentario', 'mgs_elementor'),
                                'width' => '100',
                            ],
                        ],
                        'title_field' => '{{{ field_label }}}',
                        'condition' => ['add_form' => 'yes'],
                    ]
                );
                $this->add_control(
                    'input_size',
                    [
                        'label' => esc_html__('Tamaño de los campos', 'mgs_elementor'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => $this->get_elementos_sizes(),
                        'default' => 'sm',
                        'separator' => 'before',
                        'condition' => ['add_form' => 'yes'],
                    ]
                );
                $this->add_control(
                    'show_labels',
                    [
                        'label' => esc_html__('Ocultar etiquetas?', 'mgs_elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__('Si', 'mgs_elementor'),
                        'label_off' => esc_html__('No', 'mgs_elementor'),
                        'return_value' => 'true',
                        'default' => 'true',
                        'separator' => 'before',
                        'condition' => ['add_form' => 'yes'],
                    ]
                );
                $this->add_control(
                    'mark_required',
                    [
                        'label' => esc_html__('Mostrar marcas de campos obligatorios?', 'mgs_elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__('Si', 'mgs_elementor'),
                        'label_off' => esc_html__('No', 'mgs_elementor'),
                        'default' => '',
                        'condition' => [
                            'show_labels'  => '',
                            'add_form'      => 'yes'
                        ],
                    ]
                );
                $this->add_control(
                    'heading_submit_button',
                    [
                        'label' => esc_html__('Boton', 'mgs_elementor'),
                        'type' => \Elementor\Controls_Manager::HEADING,
                        'condition' => ['add_form' => 'yes'],
                        'separator' => 'before',
                    ]
                );
                $this->add_control(
                    'button_text',
                    [
                        'label' => esc_html__('Texto del boton', 'mgs_elementor'),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'default' => esc_html__('Enviar', 'mgs_elementor'),
                        'placeholder' => esc_html__('Enviar', 'mgs_elementor'),
                        'condition' => ['add_form' => 'yes'],
                    ]
                );

                $this->add_control(
                    'button_size',
                    [
                        'label' => esc_html__('Tamaño del boton', 'mgs_elementor'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => 'sm',
                        'options' => $this->get_elementos_sizes(),
                        'condition' => ['add_form' => 'yes'],
                    ]
                );
                $this->add_responsive_control(
                    'button_width',
                    [
                        'label' => esc_html__('Columna del boton', 'mgs_elementor'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            '' => esc_html__( 'Default', 'mgs_elementor'),
                            '100' => '100%',
                            '80' => '80%',
                            '75' => '75%',
                            '70' => '70%',
                            '66' => '66%',
                            '60' => '60%',
                            '50' => '50%',
                            '40' => '40%',
                            '33' => '33%',
                            '30' => '30%',
                            '25' => '25%',
                            '20' => '20%',
                        ],
                        'default' => '100',
                        'frontend_available' => true,
                        'condition' => ['add_form' => 'yes'],
                    ]
                );
                $this->add_responsive_control(
                    'button_align',
                    [
                        'label' => esc_html__('Alinear boton', 'mgs_elementor'),
                        'type' => \Elementor\Controls_Manager::CHOOSE,
                        'options' => [
                            'start' => [
                                'title' => esc_html__('Izquierda', 'mgs_elementor'),
                                'icon' => 'eicon-text-align-left',
                            ],
                            'center' => [
                                'title' => esc_html__('Centro', 'mgs_elementor'),
                                'icon' => 'eicon-text-align-center',
                            ],
                            'end' => [
                                'title' => esc_html__('Derecha', 'mgs_elementor'),
                                'icon' => 'eicon-text-align-right',
                            ],
                            'stretch' => [
                                'title' => esc_html__('Justificado', 'mgs_elementor'),
                                'icon' => 'eicon-text-align-justify',
                            ],
                        ],
                        'default' => 'stretch',
                        'prefix_class' => 'elementor%s-button-align-',
                        'condition' => ['add_form' => 'yes'],
                    ]
                );
            }else{
                $this->add_control(
                    'important_note_1',
                    [
                        //'label'             => esc_html__('Aviso', 'mgs_elementor'),
                        'type'              => \Elementor\Controls_Manager::RAW_HTML,
                        'raw'               => __('Para poder agregar un formulario debe tener instalado y activo <strong>Elementor Pro</strong>', 'mgs_elementor'),
                        'content_classes'   => 'elementor-panel-alert elementor-panel-alert-warning',
                    ]
                );
            }
        $this->end_controls_section();
        
        $this->start_controls_section('section_messages',['label' => esc_html__('Avisos', 'mgs_elementor'),'tab' => \Elementor\Controls_Manager::TAB_CONTENT]);
            $this->add_control(
                'custom_messages',
                [
                    'label' => esc_html__('Avisos personalizados', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'default' => '',
                    'separator' => 'before',
                    'render_type' => 'none',
                ]
            );
            $default_messages = $this->get_default_messages();
            $this->add_control(
                'mjs_post_rate_success',
                [
                    'label' => esc_html__('Valoración éxitosa.', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $default_messages['POST_RATE_SUCCESS'],
                    'placeholder' => $default_messages['POST_RATE_SUCCESS'],
                    'label_block' => true,
                    'condition' => [
                        'custom_messages!' => '',
                    ],
                    'render_type' => 'none',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
            $this->add_control(
                'mjs_post_rate_error',
                [
                    'label' => esc_html__('Error al valorar', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $default_messages['POST_RATE_ERROR'],
                    'placeholder' => $default_messages['POST_RATE_ERROR'],
                    'label_block' => true,
                    'condition' => [
                        'custom_messages!' => '',
                    ],
                    'render_type' => 'none',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
            $this->add_control(
                'mjs_heading_mjs_form',
                [
                    'label' => esc_html__('Mensajes formulario', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'condition' => [
                        'custom_messages!'  => '',
                        'add_form' => 'yes'
                    ],
                    'separator' => 'before',
                ]
            );
            $this->add_control(
                'mjs_form_option',
                [
                    'label' => esc_html__('Formulario opcional', 'mgs_elementor'),
                    'description' => esc_html__('Aviso que se da cuando se valora y el formulario esta activo.', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $default_messages['FORM_OPTION'],
                    'placeholder' => $default_messages['FORM_OPTION'],
                    'label_block' => true,
                    'condition' => [
                        'custom_messages!'  => '',
                        'add_form'          => 'yes'
                    ],
                    'render_type' => 'none',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
            $this->add_control(
                'mjs_form_success',
                [
                    'label' => esc_html__('Mensaje de éxito', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $default_messages['FORM_SUCCESS'],
                    'placeholder' => $default_messages['FORM_SUCCESS'],
                    'label_block' => true,
                    'condition' => [
                        'custom_messages!'  => '',
                        'add_form'          => 'yes'
                    ],
                    'render_type' => 'none',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
            $this->add_control(
                'mjs_required_field_message',
                [
                    'label' => esc_html__('Campo requerido', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $default_messages['FIELD_REQUIRED'],
                    'placeholder' => $default_messages['FIELD_REQUIRED'],
                    'label_block' => true,
                    'condition' => [
                        'custom_messages!' => '',
                        'add_form'          => 'yes'
                    ],
                    'render_type' => 'none',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
            $this->add_control(
                'mjs_form_mail',
                [
                    'label' => esc_html__('Email incorrecto', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $default_messages['FORM_MAIL'],
                    'placeholder' => $default_messages['FORM_MAIL'],
                    'label_block' => true,
                    'condition' => [
                        'custom_messages!'  => '',
                        'add_form'          => 'yes'
                    ],
                    'render_type' => 'none',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
            $this->add_control(
                'mjs_form_url',
                [
                    'label' => esc_html__('URL incorrecta', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $default_messages['FORM_URL'],
                    'placeholder' => $default_messages['FORM_URL'],
                    'label_block' => true,
                    'condition' => [
                        'custom_messages!'  => '',
                        'add_form'          => 'yes'
                    ],
                    'render_type' => 'none',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
            $this->add_control(
                'mjs_form_number',
                [
                    'label' => esc_html__('Número incorrecto', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $default_messages['FORM_NUMBER'],
                    'placeholder' => $default_messages['FORM_NUMBER'],
                    'label_block' => true,
                    'condition' => [
                        'custom_messages!'  => '',
                        'add_form'          => 'yes'
                    ],
                    'render_type' => 'none',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
            $this->add_control(
                'mjs_error_message',
                [
                    'label' => esc_html__('Mensaje de error', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $default_messages['ERROR'],
                    'placeholder' => $default_messages['ERROR'],
                    'label_block' => true,
                    'condition' => [
                        'custom_messages!' => '',
                        'add_form'          => 'yes'
                    ],
                    'render_type' => 'none',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
            $this->add_control(
                'mjs_error_no_post_rate',
                [
                    'label' => esc_html__('Falta valoración', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $default_messages['INVALID_FORM'],
                    'placeholder' => $default_messages['INVALID_FORM'],
                    'label_block' => true,
                    'condition' => [
                        'custom_messages!' => '',
                        'add_form'          => 'yes'
                    ],
                    'render_type' => 'none',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
        $this->end_controls_section();
        
        $this->start_controls_section('section_form_style',['label' => esc_html__('Formulario', 'mgs_elementor'),'tab' => \Elementor\Controls_Manager::TAB_STYLE, 'condition' => ['add_form' => 'yes']]);
            $this->add_control(
                'column_gap',
                [
                    'label' => esc_html__('Separación columnas', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 10,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 60,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-group' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
                        '{{WRAPPER}} .elementor-form-fields-wrapper' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
                    ],
                ]
            );

            $this->add_control(
                'row_gap',
                [
                    'label' => esc_html__('Separación filas', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 10,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 60,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .elementor-field-group.recaptcha_v3-bottomleft, {{WRAPPER}} .elementor-field-group.recaptcha_v3-bottomright' => 'margin-bottom: 0;',
                        '{{WRAPPER}} .elementor-form-fields-wrapper' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'heading_label',
                [
                    'label' => esc_html__('Etiquetas', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            $this->add_control(
                'label_spacing',
                [
                    'label' => esc_html__('Separación', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 60,
                        ],
                    ],
                    'selectors' => [
                        'body.rtl {{WRAPPER}} .elementor-labels-inline .elementor-field-group > label' => 'padding-left: {{SIZE}}{{UNIT}};',
                        // for the label position = inline option
                        'body:not(.rtl) {{WRAPPER}} .elementor-labels-inline .elementor-field-group > label' => 'padding-right: {{SIZE}}{{UNIT}};',
                        // for the label position = inline option
                        'body {{WRAPPER}} .elementor-labels-above .elementor-field-group > label' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                        // for the label position = above option
                    ],
                ]
            );
            $this->add_control(
                'label_color',
                [
                    'label' => esc_html__('Color', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-group > label, {{WRAPPER}} .elementor-field-subgroup label' => 'color: {{VALUE}};',
                    ],
                    'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_TEXT,
                    ],
                ]
            );
            $this->add_control(
                'mark_required_color',
                [
                    'label' => esc_html__('Marcador obligatorio', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .elementor-mark-required .elementor-field-label:after' => 'color: {{COLOR}};',
                    ],
                    'condition' => [
                        'mark_required' => 'yes',
                    ],
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'label_typography',
                    'selector' => '{{WRAPPER}} .elementor-field-group > label',
                    'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                    ],
                ]
            );
        $this->end_controls_section();
        
        $this->start_controls_section('section_field_style',['label' => esc_html__('Campos', 'mgs_elementor'),'tab' => \Elementor\Controls_Manager::TAB_STYLE, 'condition' => ['add_form' => 'yes']]);
            $this->add_control(
                'field_text_color',
                [
                    'label' => esc_html__('Color', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-group .elementor-field' => 'color: {{VALUE}};',
                    ],
                    'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_TEXT,
                    ],
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'field_typography',
                    'selector' => '{{WRAPPER}} .elementor-field-group .elementor-field, {{WRAPPER}} .elementor-field-subgroup label',
                    'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                    ],
                ]
            );

            $this->add_control(
                'field_background_color',
                [
                    'label' => esc_html__('Fondo', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#ffffff',
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'background-color: {{VALUE}};',
                    ],
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'field_border_color',
                [
                    'label' => esc_html__('Color del borde', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-color: {{VALUE}};',
                        '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-color: {{VALUE}};',
                        '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper::before' => 'color: {{VALUE}};',
                    ],
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'field_border_width',
                [
                    'label' => esc_html__('Ancho del borde', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'placeholder' => '1',
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'field_border_radius',
                [
                    'label' => esc_html__('Radio del borde', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
        $this->end_controls_section();
        
        $this->start_controls_section('section_button_style',['label' => esc_html__('Boton', 'mgs_elementor'),'tab' => \Elementor\Controls_Manager::TAB_STYLE, 'condition' => ['add_form' => 'yes']]);
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'button_typography',
                    'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .elementor-button',
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Border::get_type(), [
                    'name' => 'button_border',
                    'selector' => '{{WRAPPER}} .elementor-button',
                    'exclude' => [
                        'color',
                    ],
                ]
            );

            $this->start_controls_tabs('tabs_button_style');
                $this->start_controls_tab('tab_button_normal', ['label' => esc_html__('Normal', 'mgs_elementor')]);
                    $this->add_control(
                        'button_background_color',
                        [
                            'label' => esc_html__('Fondo', 'mgs_elementor'),
                            'type' => \Elementor\Controls_Manager::COLOR,
                            'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .e-form__buttons__wrapper__button-next' => 'background-color: {{VALUE}};',
                                '{{WRAPPER}} .elementor-button[type="submit"]' => 'background-color: {{VALUE}};',
                            ],
                        ]
                    );
                    $this->add_control(
                        'button_text_color',
                        [
                            'label' => esc_html__('Color', 'mgs_elementor'),
                            'type' => \Elementor\Controls_Manager::COLOR,
                            'default' => '#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .e-form__buttons__wrapper__button-next' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .elementor-button[type="submit"]' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .elementor-button[type="submit"] svg *' => 'fill: {{VALUE}};',
                            ],
                        ]
                    );
                    $this->add_control(
                        'button_border_color',
                        [
                            'label' => esc_html__('Borde', 'mgs_elementor'),
                            'type' => \Elementor\Controls_Manager::COLOR,
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .e-form__buttons__wrapper__button-next' => 'border-color: {{VALUE}};',
                                '{{WRAPPER}} .elementor-button[type="submit"]' => 'border-color: {{VALUE}};',
                            ],
                            'condition' => [
                                'button_border_border!' => '',
                            ],
                        ]
                    );
                $this->end_controls_tab();
                
                $this->start_controls_tab('tab_button_hover',['label' => esc_html__('Hover', 'mgs_elementor')]);
                    $this->add_control(
                        'button_background_hover_color',
                        [
                            'label' => esc_html__('Fondo', 'mgs_elementor'),
                            'type' => \Elementor\Controls_Manager::COLOR,
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .e-form__buttons__wrapper__button-next:hover' => 'background-color: {{VALUE}};',
                                '{{WRAPPER}} .elementor-button[type="submit"]:hover' => 'background-color: {{VALUE}};',
                            ],
                        ]
                    );
                    $this->add_control(
                        'button_hover_color',
                        [
                            'label' => esc_html__('Color', 'mgs_elementor'),
                            'type' => \Elementor\Controls_Manager::COLOR,
                            'default' => '#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .e-form__buttons__wrapper__button-next:hover' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .elementor-button[type="submit"]:hover' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .elementor-button[type="submit"]:hover svg *' => 'fill: {{VALUE}};',
                            ],
                        ]
                    );
                    $this->add_control(
                        'button_hover_border_color',
                        [
                            'label' => esc_html__('Borde', 'mgs_elementor'),
                            'type' => \Elementor\Controls_Manager::COLOR,
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .e-form__buttons__wrapper__button-next:hover' => 'border-color: {{VALUE}};',
                                '{{WRAPPER}} .elementor-button[type="submit"]:hover' => 'border-color: {{VALUE}};',
                            ],
                            'condition' => [
                                'button_border_border!' => '',
                            ],
                        ]
                    );
                    $this->add_control(
                        'button_hover_animation',
                        [
                            'label' => esc_html__('Animación', 'mgs_elementor'),
                            'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
                        ]
                    );
                $this->end_controls_tab();
            $this->end_controls_tabs();

            $this->add_control(
                'button_border_radius',
                [
                    'label' => esc_html__('Radio del borde', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%', 'em'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'button_text_padding',
                [
                    'label' => esc_html__('Padding', 'elementor-pro' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
        $this->end_controls_section();
        
        $this->start_controls_section('section_messages_style',['label' => esc_html__('Avisos', 'mgs_elementor'),'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'message_typography',
                    'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                    ],
                    'selector' => '{{WRAPPER}} .elementor-message',
                ]
            );
            $this->add_control(
                'success_message_color',
                [
                    'label' => esc_html__('Color aviso de éxito', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-message.elementor-message-success' => 'color: {{COLOR}};',
                    ],
                ]
            );
            $this->add_control(
                'error_message_color',
                [
                    'label' => esc_html__('Color aviso de error', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-message.elementor-message-danger' => 'color: {{COLOR}};',
                        '{{WRAPPER}} .elementor-field-group label.error' => 'color: {{COLOR}};',
                    ],
                ]
            );
            $this->add_control(
                'inline_message_color',
                [
                    'label' => esc_html__('Color aviso inline.', 'mgs_elementor'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-message.elementor-help-inline' => 'color: {{COLOR}};',
                    ],
                ]
            );
        $this->end_controls_section();
    }

    protected function render(){
        //ID unico del submit, se utiliza para emparejar la valoracion con el comentario
        $submit_id = 'mgs_elementor_post_rate_'.uniqid().'_'.uniqid();
        $settings = $this->get_settings_for_display();

        if( !empty($settings['form_id']) ){
			$form_id = $settings['form_id'];
		}else{
            $form_id = 'mgs_elementor_post_rate_'.$this->get_id();
        }

        $this->add_render_attribute(
			[
                'form'          => [
                    'class'         => ['mgs_elementor_post_rate', 'no_voted'],
                    'id'            => $form_id,
                    'method'        => 'post',
                    'data-post'     => \ElementorPro\Core\Utils::get_current_post_id()
                ],
                'estrellas' => [
                    'class' => [
                        'estrellas-wrapper',
                        'estrellas-aling-' . $settings['aling'],
                    ],
                ],
                'comment'          => [
                    'class'         => ['mgs_elementor_post_rate_form', 'elementor-form-fields-wrapper']
                ],


				'wrapper' => [
					'class' => [
						'elementor-form-fields-wrapper',
						'elementor-labels-' . $settings['label_position'],
					],
				],
				'submit-group' => [
					'class' => [
						'elementor-field-group',
						'elementor-column',
						'elementor-field-type-submit',
					],
				],
				'button' => [
					'class'     => ['elementor-button', 'mgs_elementor_post_rate_form_button'],
                    'data-form' => $form_id
				],
				'icon-align' => [
					'class' => [
						empty( $settings['button_icon_align'] ) ? '' : 'elementor-align-icon-' . $settings['button_icon_align'],
						'elementor-button-icon',
					],
				],
			]
		);

        if( $settings['add_form']=='yes' ){
            $this->add_render_attribute('form', 'class', 'has_form');
        }else{
            $this->add_render_attribute('form', 'class', 'no_form');
        }

        if( empty($settings['button_width']) ){
			$settings['button_width'] = '100';
		}

		$this->add_render_attribute('submit-group', 'class', 'elementor-col-' . $settings['button_width'] . ' e-form__buttons');

		if( !empty( $settings['button_width_tablet'] ) ){
			$this->add_render_attribute('submit-group', 'class', 'elementor-md-' . $settings['button_width_tablet']);
		}

		if( !empty( $settings['button_width_mobile'] ) ){
			$this->add_render_attribute('submit-group', 'class', 'elementor-sm-' . $settings['button_width_mobile']);
		}

		if( !empty( $settings['button_size'] ) ){
			$this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['button_size']);
		}

		if( !empty( $settings['button_type'] ) ){
			$this->add_render_attribute('button', 'class', 'elementor-button-' . $settings['button_type']);
		}

		if( $settings['button_hover_animation'] ){
			$this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
		}

		if( !empty($settings['form_name']) ){
			$this->add_render_attribute('form', 'name', $settings['form_name']);
		}

		if( !empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( 'button', 'id', $settings['button_css_id']);
		}
        $referer_title = trim(wp_title('', false));
		if( !$referer_title && is_home() ) $referer_title = get_option('blogname');

        $mjs_forms = [];
        if( $settings['custom_messages']!='' ){
            $mjs_forms['POST_RATE_SUCCESS'] = $settings['mjs_post_rate_success'];
            $mjs_forms['POST_RATE_ERROR']   = $settings['mjs_post_rate_error'];

            $mjs_forms['FORM_OPTION']       = $settings['mjs_form_option'];
            $mjs_forms['FORM_SUCCESS']      = $settings['mjs_form_success'];
            $mjs_forms['FIELD_REQUIRED']    = $settings['mjs_required_field_message'];
            $mjs_forms['FORM_MAIL']         = $settings['mjs_form_mail'];
            $mjs_forms['FORM_URL']          = $settings['mjs_form_url'];
            $mjs_forms['FORM_NUMBER']       = $settings['mjs_form_number'];

            $mjs_forms['INVALID_FORM']      = $settings['mjs_error_no_post_rate'];
            $mjs_forms['ERROR']             = $settings['mjs_error_message'];
        }else{
            $mjs_forms = $this->get_default_messages();
        }

        ?>
        <form <?php $this->print_render_attribute_string('form')?>>
			<input type="hidden" name="post_id" value="<?php echo \ElementorPro\Core\Utils::get_current_post_id()?>"/>
			<input type="hidden" name="form_id" value="<?php echo esc_attr($this->get_id())?>"/>
			<input type="hidden" name="referer_title" value="<?php echo esc_attr($referer_title)?>" />
			<input type="hidden" name="submit_id" value="<?php echo $submit_id?>" />
			<input type="hidden" name="post_rate" value="" />
			<?php if( is_singular() ){?>
			<input type="hidden" name="queried_id" value="<?php echo get_the_ID()?>"/>
			<?php }?>
            
			<div <?php $this->print_render_attribute_string('estrellas');?>>
                <a href="#" class="estrella estrella1" data-value="1" data-post="<?php echo get_the_ID()?>" data-submit_id="<?php echo $submit_id?>" data-parent="<?php echo 'mgs_elementor_post_rate_'.$this->get_id()?>"><?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden'=>'true'])?></a>
                <a href="#" class="estrella estrella2" data-value="2" data-post="<?php echo get_the_ID()?>" data-submit_id="<?php echo $submit_id?>" data-parent="<?php echo 'mgs_elementor_post_rate_'.$this->get_id()?>"><?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden'=>'true'])?></a>
                <a href="#" class="estrella estrella3" data-value="3" data-post="<?php echo get_the_ID()?>" data-submit_id="<?php echo $submit_id?>" data-parent="<?php echo 'mgs_elementor_post_rate_'.$this->get_id()?>"><?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden'=>'true'])?></a>
                <a href="#" class="estrella estrella4" data-value="4" data-post="<?php echo get_the_ID()?>" data-submit_id="<?php echo $submit_id?>" data-parent="<?php echo 'mgs_elementor_post_rate_'.$this->get_id()?>"><?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden'=>'true'])?></a>
                <a href="#" class="estrella estrella5" data-value="5" data-post="<?php echo get_the_ID()?>" data-submit_id="<?php echo $submit_id?>" data-parent="<?php echo 'mgs_elementor_post_rate_'.$this->get_id()?>"><?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden'=>'true'])?></a>
                
                <input type="hidden" name="estrellas_value" value=""/>
            </div>

            <?php if( $settings['add_form']=='yes' ){?>
            <div <?php $this->print_render_attribute_string('comment');?>>
                <?php
                foreach( $settings['form_fields'] as $item_index => $item ){
					$item['input_size'] = $settings['input_size'];
                    if( empty($item['field_type']) || $item['field_type']=='' ){
                        $item['field_type'] = 'text';
                        $settings['form_fields'][$item_index]['field_type'] = 'text';
                    }
                    $this->form_fields_render_attributes($item_index, $settings, $item);
                    $field_type = $item['field_type'];
                    $item = apply_filters('elementor_pro/forms/render/item', $item, $item_index, $this);
                    $item = apply_filters("elementor_pro/forms/render/item/{$field_type}", $item, $item_index, $this);
                    $print_label = !in_array($item['field_type'], ['hidden', 'html', 'step'], true);
                ?>
                <div <?php $this->print_render_attribute_string('field-group'.$item_index)?>>
                <?php if( $print_label && $item['field_label'] ){?>
				    <label <?php $this->print_render_attribute_string('label'.$item_index)?>>
						<?php echo $item['field_label']?>
					</label>
				<?php }?>
                <?php
                    switch( $item['field_type'] ){
                        case 'textarea':
                            echo $this->make_textarea_field($item, $item_index);
                            break;
                        case 'select':
                            echo $this->make_select_field($item, $item_index);
                            break;
                        case 'radio':
                        case 'checkbox':
                            echo $this->make_radio_checkbox_field($item, $item_index, $item['field_type']);
                            break;
                        case 'text':
                        case 'email':
                        case 'url':
                        case 'password':
                        case 'hidden':
                        case 'search':
                            $this->add_render_attribute('input'.$item_index, 'class', 'elementor-field-textual');
                            ?>
                            <input size="1" <?php $this->print_render_attribute_string('input'.$item_index)?>>
                            <?php
                            break;
                        default:
                            $field_type = $item['field_type'];

                            /**
                             * Elementor form field render.
                             *
                             * Fires when a field is rendered in the frontend. This hook allows developers to
                             * add functionality when from fields are rendered.
                             *
                             * The dynamic portion of the hook name, `$field_type`, refers to the field type.
                             *
                             * @since 1.0.0
                             *
                             * @param array $item       The field value.
                             * @param int   $item_index The field index.
                             * @param Form  $this       An instance of the form.
                             */
                            //do_action( "elementor_pro/forms/render_field/{$field_type}", $item, $item_index, $this );
                    }
                ?>
                </div>
                <?php
                }
                ?>
                <div <?php $this->print_render_attribute_string('submit-group'); ?>>
					<button type="submit" <?php $this->print_render_attribute_string('button')?>>
						<span <?php $this->print_render_attribute_string('content-wrapper')?>>
							<?php if( !empty($settings['button_icon']) || !empty($settings['selected_button_icon']) ){?>
								<span <?php $this->print_render_attribute_string('icon-align')?>>
									<?php $this->render_icon_with_fallback($settings)?>
									<?php if (empty($settings['button_text']) ){?>
										<span class="elementor-screen-only"><?php echo esc_html__('Enviar', 'mgs_elementor')?></span>
									<?php }?>
								</span>
							<?php }?>

							<?php if( !empty($settings['button_text']) ){?>
								<span class="elementor-button-text"><?php $this->print_unescaped_setting('button_text')?></span>
							<?php }?>
						</span>
					</button>
				</div>
            </div>
            <?php }?>
        </form>
        <script>
            var mgs_post_ratings_vars_msj_form = <?php echo json_encode($mjs_forms)?>;
        </script>



        <?php
    }


    private function get_elementos_sizes(){
		return [
			'xs' => esc_html__('Muy chico', 'mgs_elementor'),
			'sm' => esc_html__('Chico', 'mgs_elementor'),
			'md' => esc_html__('Mediano', 'mgs_elementor'),
			'lg' => esc_html__('Grande', 'mgs_elementor'),
			'xl' => esc_html__('Muy grande', 'mgs_elementor'),
		];
	}

    private function get_attribute_name($item){
		return "form_fields[{$item['custom_id']}]";
	}

    private function get_attribute_id($item){
		return 'form-field-'.$item['custom_id'];
	}

    private function add_required_attribute($element) {
		$this->add_render_attribute($element, 'required', 'required');
		$this->add_render_attribute($element, 'aria-required', 'true');
	}

    private function form_fields_render_attributes($i, $instance, $item){
		$this->add_render_attribute(
			[
				'field-group' . $i => [
					'class' => [
						'elementor-field-type-' . $item['field_type'],
						'elementor-field-group',
						'mgs_post_rate-field-group',
						'elementor-column',
						'elementor-field-group-' . $item['custom_id'],
					],
				],
				'input' . $i => [
					'type' => $item['field_type'],
					'name' => $this->get_attribute_name( $item ),
					'id' => $this->get_attribute_id( $item ),
					'class' => [
						'elementor-field',
						'elementor-size-' . $item['input_size'],
						empty( $item['css_classes'] ) ? '' : esc_attr( $item['css_classes'] ),
					],
				],
				'label' . $i => [
					'for' => $this->get_attribute_id( $item ),
					'class' => 'elementor-field-label',
				],
			]
		);

		if ( empty( $item['width'] ) ) {
			$item['width'] = '100';
		}

		$this->add_render_attribute( 'field-group' . $i, 'class', 'elementor-col-' . $item['width'] );

		if ( ! empty( $item['width_tablet'] ) ) {
			$this->add_render_attribute( 'field-group' . $i, 'class', 'elementor-md-' . $item['width_tablet'] );
		}

		if ( $item['allow_multiple'] ) {
			$this->add_render_attribute( 'field-group' . $i, 'class', 'elementor-field-type-' . $item['field_type'] . '-multiple' );
		}

		if ( ! empty( $item['width_mobile'] ) ) {
			$this->add_render_attribute( 'field-group' . $i, 'class', 'elementor-sm-' . $item['width_mobile'] );
		}

		// Allow zero as placeholder.
		if ( ! empty( $item['placeholder'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'placeholder', $item['placeholder'] );
		}

		if ( ! empty( $item['field_value'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'value', $item['field_value'] );
		}

		if( $instance['show_labels'] ){
			$this->add_render_attribute('label'.$i, 'class', 'elementor-screen-only');
			$this->add_render_attribute('label'.$i, 'class', 'elementor-screen-only-'.$instance['show_labels']);
		}

		if ( ! empty( $item['required'] ) ) {
			$class = 'elementor-field-required';
			if ( ! empty( $instance['mark_required'] ) ) {
				$class .= ' elementor-mark-required';
			}
			$this->add_render_attribute( 'field-group' . $i, 'class', $class );
			$this->add_required_attribute( 'input' . $i );
		}
	}

    private function make_textarea_field($item, $item_index){
		$this->add_render_attribute( 'textarea' . $item_index, [
			'class' => [
				'elementor-field-textual',
				'elementor-field',
				esc_attr( $item['css_classes'] ),
				'elementor-size-' . $item['input_size'],
			],
			'name' => $this->get_attribute_name( $item ),
			'id' => $this->get_attribute_id( $item ),
			'rows' => $item['rows'],
		] );

		if ( $item['placeholder'] ) {
			$this->add_render_attribute( 'textarea' . $item_index, 'placeholder', $item['placeholder'] );
		}

		if ( $item['required'] ) {
			$this->add_required_attribute( 'textarea' . $item_index );
		}

		$value = empty( $item['field_value'] ) ? '' : $item['field_value'];

		return '<textarea ' . $this->get_render_attribute_string( 'textarea' . $item_index ) . '>' . $value . '</textarea>';
	}

    private function make_select_field( $item, $i ) {
		$this->add_render_attribute(
			[
				'select-wrapper' . $i => [
					'class' => [
						'elementor-field',
						'elementor-select-wrapper',
						'remove-before',
						esc_attr( $item['css_classes'] ),
					],
				],
				'select' . $i => [
					'name' => $this->get_attribute_name( $item ) . ( ! empty( $item['allow_multiple'] ) ? '[]' : '' ),
					'id' => $this->get_attribute_id( $item ),
					'class' => [
						'elementor-field-textual',
						'elementor-size-' . $item['input_size'],
					],
				],
			]
		);

		if ( $item['required'] ) {
			$this->add_required_attribute( 'select' . $i );
		}

		if ( $item['allow_multiple'] ) {
			$this->add_render_attribute( 'select' . $i, 'multiple' );
			if ( ! empty( $item['select_size'] ) ) {
				$this->add_render_attribute( 'select' . $i, 'size', $item['select_size'] );
			}
		}

		$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );

		if ( ! $options ) {
			return '';
		}

		ob_start();
		?>
		<div <?php $this->print_render_attribute_string( 'select-wrapper' . $i ); ?>>
			<div class="select-caret-down-wrapper">
				<?php
				if ( ! $item['allow_multiple'] ) {
					$icon = [
						'library' => 'eicons',
						'value' => 'eicon-caret-down',
						'position' => 'right',
					];
					\Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
				}
				?>
			</div>
			<select <?php $this->print_render_attribute_string( 'select' . $i ); ?>>
				<?php
				foreach ( $options as $key => $option ) {
					$option_id = $item['custom_id'] . $key;
					$option_value = esc_attr( $option );
					$option_label = esc_html( $option );

					if ( false !== strpos( $option, '|' ) ) {
						list( $label, $value ) = explode( '|', $option );
						$option_value = esc_attr( $value );
						$option_label = esc_html( $label );
					}

					$this->add_render_attribute( $option_id, 'value', $option_value );

					// Support multiple selected values
					if ( ! empty( $item['field_value'] ) && in_array( $option_value, explode( ',', $item['field_value'] ) ) ) {
						$this->add_render_attribute( $option_id, 'selected', 'selected' );
					} ?>
					<option <?php $this->print_render_attribute_string( $option_id ); ?>><?php
						// PHPCS - $option_label is already escaped
						echo $option_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></option>
				<?php } ?>
			</select>
		</div>
		<?php

		$select = ob_get_clean();
		return $select;
	}

    private function make_radio_checkbox_field($item, $item_index, $type){
		$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );
		$html = '';
		if ( $options ) {
			$html .= '<div class="elementor-field-subgroup ' . esc_attr( $item['css_classes'] ) . ' ' . $item['inline_list'] . '">';
			foreach ( $options as $key => $option ) {
				$element_id = $item['custom_id'] . $key;
				$html_id = $this->get_attribute_id( $item ) . '-' . $key;
				$option_label = $option;
				$option_value = $option;
				if ( false !== strpos( $option, '|' ) ) {
					list( $option_label, $option_value ) = explode( '|', $option );
				}

				$this->add_render_attribute(
					$element_id,
					[
						'type' => $type,
						'value' => $option_value,
						'id' => $html_id,
						'name' => $this->get_attribute_name( $item ) . ( ( 'checkbox' === $type && count( $options ) > 1 ) ? '[]' : '' ),
					]
				);

				if ( ! empty( $item['field_value'] ) && $option_value === $item['field_value'] ) {
					$this->add_render_attribute( $element_id, 'checked', 'checked' );
				}

				if ( $item['required'] && 'radio' === $type ) {
					$this->add_required_attribute( $element_id );
				}

				$html .= '<span class="elementor-field-option"><input ' . $this->get_render_attribute_string( $element_id ) . '> <label for="' . $html_id . '">' . $option_label . '</label></span>';
			}
			$html .= '</div>';
		}

		return $html;
	}

    private function render_icon_with_fallback( $settings ) {
		$migrated = isset( $settings['__fa4_migrated']['selected_button_icon'] );
		$is_new = empty( $settings['button_icon'] ) && \Elementor\Icons_Manager::is_migration_allowed();

		if ( $is_new || $migrated ) {
			\Elementor\Icons_Manager::render_icon( $settings['selected_button_icon'], [ 'aria-hidden' => 'true' ] );
		} else {
			?><i class="<?php echo esc_attr( $settings['button_icon'] ); ?>" aria-hidden="true"></i><?php
		}
	}

    private function get_fields_types(){
        return [
            'text'          => esc_html__('Text', 'mgs_elementor'),
            'email'         => esc_html__('Email', 'mgs_elementor'),
            'textarea'      => esc_html__('Textarea', 'mgs_elementor'),
            'number'        => esc_html__('Number', 'mgs_elementor'),
            'url'           => esc_html__('URL', 'mgs_elementor'),
            'select'        => esc_html__('Select', 'mgs_elementor'),
            //'checkbox'      => esc_html__('Checkbox', 'mgs_elementor'),
            //'radio'         => esc_html__('Radio', 'mgs_elementor'),
            //'acceptance'    => esc_html__('Acceptance', 'mgs_elementor'),
        ];
    }

    public function get_default_messages(){
        return [
            'POST_RATE_SUCCESS' => __('Valoracion enviada con éxito.', 'mgs_elementor'),
            'POST_RATE_ERROR'   => __('No se pudo enviar su valoración.', 'mgs_elementor'),
            
            'FORM_OPTION'       => __('Valoracion enviada con éxito. Si lo desea puede dejar una opinion.', 'mgs_elementor'),
            'FORM_SUCCESS'      => __('El formulario fue enviado con éxito.', 'mgs_elementor'),
            'FIELD_REQUIRED'    => __('Este campo es obligatorio.', 'mgs_elementor'),
            'FORM_MAIL'         => __('Por favor, introduce una dirección de correo electrónico válida.', 'mgs_elementor'),
            'FORM_URL'          => __('Por favor introduzca una URL válida.', 'mgs_elementor'),
            'FORM_NUMBER'       => __('Por favor ingrese un número valido.', 'mgs_elementor'),

            'ERROR'             => __('Ocurrió un error.', 'mgs_elementor'),
            'INVALID_FORM'      => __('Debe valorar antes de enviar su comentario.', 'mgs_elementor'),//deprecated
            'SERVER_ERROR'      => __('Error del Servidor. Formulario no enviado.', 'mgs_elementor'),//deprecated
        ];
    }
}