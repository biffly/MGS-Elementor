<?php

if( !class_exists('MGS_Conditional') ){
    class MGS_Conditional{
        public $prefix = 'mgs_conditional_';
        public $debug = false;
        public function __construct(){
            if( get_option('mgs-elementor-addon-state-conditional')=='on' ){
                //css editor
                add_action('elementor/editor/before_enqueue_scripts', [$this, 'mgs_conditional_before_load_panel_assets']);
                //inject controles
                add_action('elementor/element/before_section_start', [$this, 'mgs_conditional_inject_custom_section'], 99999, 3);

                //determina si se debe mostrar o no
                add_filter('elementor/frontend/container/should_render', [$this, 'mgs_conditional_item_should_render'], 99999, 2);

                //si funciona, SOLO frontend modifica contenido
                add_action('elementor/frontend/after_render', [$this, 'add_div_after_all_elements']);

                //agrega icono OJO en editor
                add_action('wp_footer', [$this, 'mgs_conditional_editor_show_icon']);
            }
        }

        public function add_div_after_all_elements($element){
            if( $element->get_name()==='container' ){
                $settings = $element->get_settings();
                if( $settings[$this->prefix.'activar']==='yes' && $settings[$this->prefix.'callback']=='yes' && !$this->check_conditions($element, 'bool') ){
                    if( $settings[$this->prefix.'callback_source']=='texto' ){
                        echo '<div class="mgs_conditional_callback_msj">'.wp_kses_post($settings[$this->prefix.'callback_text']).'</div>';
                    }
                    if( $settings[$this->prefix.'callback_source']=='plantilla' ){
                        echo do_shortcode('[elementor-template id="'.$settings[$this->prefix.'callback_plantilla'].'"]');
                    }
                }

                //if( $this->debug ) echo '<pre>'.print_r($settings[$this->prefix.'condiciones'], true).'</pre>';
                if( $this->debug && $settings[$this->prefix.'activar']==='yes' ){
                    echo '<pre>'.print_r($this->check_conditions($element, 'array'), true).'</pre>';
                    //echo '<pre>'.print_r(elementor_theme_do_location( 'archive' ), true).'</pre>';

                }
            }else{
                //if( $this->debug ) echo '<pre>'.print_r($element->get_name(), true).'</pre>';
            }
        }

        public function mgs_conditional_validate_content($widget_content, $widget){
            //if( $widget->get_name()==='container' ){
                $settings = $widget->get_settings();
                $widget_content .= '<pre>'.print_r($settings, true).'</pre>';
            //}
            //if( $this)
            return $widget_content;
        }

        public function mgs_conditional_item_should_render($should_render, $widget){
            $settings = $widget->get_settings();
            if( $settings[$this->prefix.'activar']==='yes' ){
                return $this->check_conditions($widget);
            }
            return true;
        }

        public function mgs_conditional_inject_custom_section($element, $section_id, $args){
            if( $section_id==='section_effects' ){
                if( $element->get_name()==='container' ) $this->register_controls($element, $args);    
            }
            if( $section_id==='_section_background' ){
                if( $element->get_name()==='heading' ) $this->register_controls($element, $args);
            }
            /*
            if( $element->get_name()==='container' && $section_id==='section_effects' ){
                $this->register_controls($element, $args);
            }
            */
        }

        public function register_controls($element, $args){
            $element->start_controls_section('mgs_conditional', ['label' => __('MGS Condiciones', 'mgs_elementor'), 'tab' => \Elementor\Controls_Manager::TAB_ADVANCED]);

                $element->add_control(
                    $this->prefix.'activar',
                    [
                        'label'         => esc_html__('Activar condiciones?', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SWITCHER,
                        'label_on'      => esc_html__('Si', 'mgs_elementor'),
                        'label_off'     => esc_html__('No', 'mgs_elementor'),
                        'return_value'  => 'yes',
                        'default'       => '',
                        'prefix_class'   => 'mgs-conditional-',
                    ]
                );
                $element->add_control(
                    $this->prefix.'cumplir',
                    [
                        'label'         => esc_html__('Tipo de condiciones', 'mgs_elementor'),
                        'description'   => esc_html__('Determina si se deben cumplir todas las condiciones o solo alguna.', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT,
                        'options'       => [
                            'all'   => esc_html__('Todas', 'mgs_elementor'),
                            'some'  => esc_html__('Alguna', 'mgs_elementor'),
                        ],
                        'default'       => 'all',
                        'condition'     => [
                            $this->prefix.'activar'     => 'yes',
                        ]
                    ]
                );

                
                $repeater = new \Elementor\Repeater();
                $repeater->add_control(
                    $this->prefix.'condicional_by',
                    [
                        'label'         => esc_html__('Condicionar por', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT,
                        'default'       => 'none',
                        'options'       => [
                            'none'              => esc_html__('Ninguna', 'mgs_elementor'),
                            'page_type'         => esc_html__('Tipo de pagina', 'mgs_elementor'),
                            'user_type'         => esc_html__('Tipo de usuario', 'mgs_elementor'),
                            'custom_field_type' => esc_html__('Campos personalizados', 'mgs_elementor'),
                            'browser_type'      => esc_html__('Navegador', 'mgs_elementor'),
                            'url_param'         => esc_html__('Parametros en la URL', 'mgs_elementor'),
                        ]
                    ]
                );

                //tipos de paginas
                $repeater->add_control(
                    $this->prefix.'condicional_by_page_type',
                    [
                        'label'         => esc_html__('Tipo de pagina', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT,
                        'default'       => 'none',
                        'options'       => [
                            'none'          => esc_html__('Ninguna', 'mgs_elementor'),
                            'is_home'       => esc_html__('Es home', 'mgs_elementor'),
                            'not_home'      => esc_html__('No es home', 'mgs_elementor'),
                            'is_page'       => esc_html__('Es una pagina', 'mgs_elementor'),
                            'is_single'     => esc_html__('Es una entrada (single)', 'mgs_elementor'),
                        ],
                        'condition'     => [
                            $this->prefix.'condicional_by'     => 'page_type',
                        ],
                    ]
                );

                //tipos de Usuarios
                $repeater->add_control(
                    $this->prefix.'condicional_by_user_type',
                    [
                        'label'         => esc_html__('Tipo de usuarios', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT2,
                        'default'       => 'none',
                        'options'       => $this->get_user_roles(),
                        'condition'     => [
                            $this->prefix.'condicional_by'     => 'user_type',
                        ],
                        'multiple'      => true,
                        'label_block'   => true
                    ]
                );

                //tipos de navegadores
                $repeater->add_control(
                    $this->prefix.'condicional_by_browser_type',
                    [
                        'label'         => esc_html__('Tipo de navegador', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT2,
                        'default'       => 'none',
                        'options'       => [
                            'is_iphone'     => 'iPhone Safari',
                            'is_chrome'     => 'Google Chrome',
                            'is_safari'     => 'Safari',
                            'is_ns4'        => 'Netscape',
                            'is_opera'      => 'Opera',
                            'is_macIE'      => 'Mac Internet Explorer',
                            'is_winIE'      => 'Windows Internet Explorer',
                            'is_gecko'      => 'FireFox',
                            'is_lynx'       => 'Lynx',
                            'is_IE'         => 'Internet Explorer',
                            'is_edge'       => 'Microsoft Edge',
                            'is_android'    => 'Android',
                            'is_unknown'    => esc_html__('Desconocido', 'mgs_elementor')
                        ],
                        'condition'     => [
                            $this->prefix.'condicional_by'     => 'browser_type',
                        ],
                        'multiple'      => true,
                        'label_block'   => true
                    ]
                );

                //tipos de campos personalizados
                $desc = esc_html__('Introduzca el KEY del campo personalizado que desea comparar.', 'mgs_elementor');
                if( class_exists('ACF') ) $desc .= ' '.esc_html__('Puede utilizar un campo personalizado de ACF.', 'mgs_elementor');
                $repeater->add_control(
                    $this->prefix.'condicional_by_custom_field_type_key',
                    [
                        'label'         => esc_html__('Campo personalizado', 'mgs_elementor'),
                        'description'   => $desc,
                        'type'          => \Elementor\Controls_Manager::TEXT,
                        'condition'     => [
                            $this->prefix.'condicional_by'     => 'custom_field_type',
                        ],
                        'label_block'   => true,
                    ]
                );
                $repeater->add_control(
                    $this->prefix.'condicional_by_custom_field_type_condicion',
                    [
                        'label'         => esc_html__('Operador', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT,
                        'options'       => [
                            '=='    => esc_html__('Igual', 'mgs_elementor'),
                            '!='    => esc_html__('Distinto', 'mgs_elementor'),
                        ],
                        'condition'     => [
                            $this->prefix.'condicional_by'     => 'custom_field_type',
                        ],
                        'label_block'   => false,
                    ]
                );
                $repeater->add_control(
                    $this->prefix.'condicional_by_custom_field_type_value',
                    [
                        'label'         => esc_html__('Valor', 'mgs_elementor'),
                        'description'   => esc_html__('Valor contra el que se comparara el campo personalizado.', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::TEXT,
                        'condition'     => [
                            $this->prefix.'condicional_by'     => 'custom_field_type',
                        ],
                        'label_block'   => true,
                    ]
                );

                //parametros en la URL
                $repeater->add_control(
                    $this->prefix.'condicional_by_url_param_key',
                    [
                        'label'         => esc_html__('Nombre', 'mgs_elementor'),
                        'description'   => esc_html__('Nombre de la variable a cotejar.', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::TEXT,
                        'condition'     => [
                            $this->prefix.'condicional_by'     => 'url_param',
                        ],
                        'label_block'   => false,
                    ]
                );
                $repeater->add_control(
                    $this->prefix.'condicional_by_url_param_value',
                    [
                        'label'         => esc_html__('Valor', 'mgs_elementor'),
                        'description'   => esc_html__('Valor de la variable a cotejar.', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::TEXT,
                        'condition'     => [
                            $this->prefix.'condicional_by'     => 'url_param',
                        ],
                        'label_block'   => false,
                    ]
                );
                
                $element->add_control(
                    $this->prefix.'condiciones',
                    [
                        'label'         => esc_html__('Condiciones', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::REPEATER,
                        'fields'        => $repeater->get_controls(),
                        'title_field'   => '{{{ '.$this->prefix.'condicional_by }}}',
                        'condition'     => [
                            $this->prefix.'activar'     => 'yes',
                        ],
                    ]
                );

                $element->add_control(
                    $this->prefix.'callback',
                    [
                        'label'         => esc_html__('Activar callback?', 'mgs_elementor'),
                        'description'   => esc_html__('Muestra un aviso en caso de no cumplir las condiciones de visualización.', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SWITCHER,
                        'label_on'      => esc_html__('Si', 'mgs_elementor'),
                        'label_off'     => esc_html__('No', 'mgs_elementor'),
                        'return_value'  => 'yes',
                        'default'       => ''
                    ]
                );
                $element->add_control(
                    $this->prefix.'callback_source',
                    [
                        'label'         => esc_html__('Tipo de callback', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT,
                        'options'       => [
                            'texto'         => esc_html__('Texto', 'mgs_elementor'),
                            'plantilla'     => esc_html__('Plantilla', 'mgs_elementor'),
                        ],
                        'default'       => 'texto',
                        'condition'     => [
                            $this->prefix.'callback'     => 'yes',
                        ]
                    ]
                );
                $element->add_control(
                    $this->prefix.'callback_text',
                    [
                        'label'         => esc_html__('Texto callback', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::TEXTAREA,
                        'condition'     => [
                            $this->prefix.'callback'        => 'yes',
                            $this->prefix.'callback_source' => 'texto'
                        ],
                        'default'       => '',
                    ]
                );
                $element->add_control(
                    $this->prefix.'callback_plantilla',
                    [
                        'label'         => esc_html__('Seleccione una de sus plantillas', 'mgs_elementor'),
                        'type'          => \Elementor\Controls_Manager::SELECT2,
                        'options'       => $this->get_plantillas(),
                        'default'       => '',
                        'label_block'   => true,
                        'condition'     => [
                            $this->prefix.'callback'        => 'yes',
                            $this->prefix.'callback_source' => 'plantilla'
                        ]
                    ]
                );
                

            $element->end_controls_section();
        }

        private function check_conditions($item, $return='bool'){
		    $settings = $item->get_settings();
            $condicion = [];
            $conditions_count = 0;

            if( $settings[$this->prefix.'activar']==='yes' ){
                foreach( $settings[$this->prefix.'condiciones'] as $cond ){
                    $condicion[$conditions_count] = 'false';

                    //by page type
                    if( $cond[$this->prefix.'condicional_by']=='page_type' ){
                        if( $this->check_PAGE_conditions($cond[$this->prefix.'condicional_by_page_type']) ) $condicion[$conditions_count] = 'true';
                    }
                    //by user type
                    if( $cond[$this->prefix.'condicional_by']=='user_type' ){
                        if( $this->check_USER_conditions($cond[$this->prefix.'condicional_by_user_type']) ) $condicion[$conditions_count] = 'true';
                    }
                    //by campo personalizado
                    if( $cond[$this->prefix.'condicional_by']=='custom_field_type' ){
                        if( $this->check_CUSTOM_FIELD_conditions($cond[$this->prefix.'condicional_by_custom_field_type_key'], $cond[$this->prefix.'condicional_by_custom_field_type_condicion'], $cond[$this->prefix.'condicional_by_custom_field_type_value']) ) $condicion[$conditions_count] = 'true';
                    }
                    //by navegador
                    if( $cond[$this->prefix.'condicional_by']=='browser_type' ){
                        if( $this->check_BROWSER_conditions($cond[$this->prefix.'condicional_by_browser_type']) ) $condicion[$conditions_count] = 'true';
                    }
                    //URL param
                    if( $cond[$this->prefix.'condicional_by']=='url_param' ){
                        if( isset($_GET[$cond[$this->prefix.'condicional_by_url_param_key']]) ){
                            if( mgs_compare::is(
                                $_GET[$cond[$this->prefix.'condicional_by_url_param_key']], 
                                $cond[$this->prefix.'condicional_by_url_param_value'], 
                                '=='
                            ) ){
                                $condicion[$conditions_count] = 'true';
                            }
                        }
                    }

                    $conditions_count++;
                }

                $mostrar = false;
                if( $settings[$this->prefix.'cumplir']==='all' ){
                    if( !in_array('false', $condicion) ) $mostrar = true;
                }elseif( $settings[$this->prefix.'cumplir']==='some' ){
                    if( in_array('true', $condicion) ) $mostrar = true;
                }
    
                if( $return=='bool') return $mostrar;
                if( $return=='array') return $condicion;
            }
            return true;
        }

        private function check_USER_conditions($condition){
            if( $condition!='none' ){
                $_roles = $this->get_user_roles();
                unset($_roles['guest']);
                unset($_roles['logged_in']);
                $user = wp_get_current_user();
                if( $condition=='guest' && !is_user_logged_in() ) return true;
                if( $condition=='logged_in' && is_user_logged_in() ) return true;
                foreach( $_roles as $k=>$v ){
                    if( $condition==$k && is_user_logged_in() ){
                        if( in_array($k, (array) $user->roles) ) return true;
                    }    
                }

                return false;
            }
            return true;
        }

        private function check_PAGE_conditions($condition){
            if( $condition!='none' ){
                if( $condition=='is_home' && is_front_page() ) return true;
                if( $condition=='not_home' && !is_front_page() ) return true;
                if( $condition=='is_page' && get_post_type()=='page' && is_single() ) return true;
                if( $condition=='is_single' && get_post_type()=='post' && is_single() ) return true;
                
                return false;
            }
            return true;
        }

        private function check_CUSTOM_FIELD_conditions($key, $operador, $value){
            $valor_campo = NULL;
            $is_acf = false;

            //determino si el campo es ACF
            if( class_exists('ACF') ){
                if( $valor_campo = get_field($key) ) $is_acf = true;
            }

            //si NO es ACF obtengo el meta comun
            if( !$is_acf ){
                $valor_campo = get_post_custom_values($key);
                if( $valor_campo==NULL ) return false;
            }

            //realizo comparacion
            if( mgs_compare::is($valor_campo, $value, $operador) ) return true;

            return false;
        }

        private function check_BROWSER_conditions($condition){
            if( is_array($condition) ){
		        foreach( $condition as $browser_type ){
			        if( $this->get_browser_type()==$browser_type ){
                        return true;
                    }
		        }
            }
            return false;
        }

        public static function get_user_roles() {
            global $wp_roles;
    
            if( !isset($wp_roles) ){
                $wp_roles = new \WP_Roles();
            }
            $all_roles      = $wp_roles->roles;
            $editable_roles = apply_filters('editable_roles', $all_roles);
    
            $data = [
                'none'          => esc_html__('Ninguna', 'mgs_elementor'),
                'guest'         => esc_html__('Invitado, cualquier usuario sin loguear', 'mgs_elementor'),
                'logged_in'     => esc_html__('Logueado', 'mgs_elementor'),
            ];
    
            foreach ($editable_roles as $k=>$role ){
                $data[$k] = $role['name'];
            }
    
            return $data;
        }

        public static function get_browser_type(){
            global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $is_android, $is_edge, $is_macIE;
    
            $android = stripos($_SERVER['HTTP_USER_AGENT'], 'Android');

            if( $is_iphone ) return 'is_iphone';
            if( $is_chrome ) return 'is_chrome';
            if( $is_safari ) return 'is_safari';
            if( $is_NS4 ) return 'is_NS4';
            if( $is_opera ) return 'is_opera';
            if( $is_macIE ) return 'is_macIE';
            if( $is_winIE ) return 'is_winIE';
            if( $is_gecko ) return 'is_gecko';
            if( $is_lynx ) return 'is_lynx';
            if( $is_IE ) return 'is_IE';
            if( $is_edge ) return 'is_edge';
            if( $is_edge ) return 'is_edge';
            if( $android ) return 'is_android';

            return 'is_unknown';

        }

        public function get_plantillas(){
            $return = [];
            $elementor_library = get_posts([
                'post_type'     => 'elementor_library',
                'post_status'   => 'publish',
                'numberposts'   => -1,
                'order'         => 'ASC'
            ]);
            foreach( $elementor_library as $plantilla ){
                $return[$plantilla->ID] = $plantilla->post_title;
            }
            return $return;
        }

        public function mgs_conditional_editor_show_icon(){
            if( !class_exists('Elementor\Plugin') || !\Elementor\Plugin::$instance->preview->is_preview_mode() ) return;
    
            ?>
            <style>
                body.elementor-editor-active .elementor-element.mgs-conditional-yes::before {
                    content: '\e8ed';
                    color: #fff;
                    background-color: #556068;
                    display: block;
                    font-family: eicons;
                    font-size: 15px;
                    height: 24px;
                    width: 24px;
                    line-height: 24px;
                    text-align: center;
                    position: absolute;
                    top: 0;
                    left: calc(100% - 24px);
                }
                body.elementor-editor-active .elementor-element.mgs-conditional-yes::before:hover{
                    background-color: #495157;
                }

                .mgs-panel-alert{
                    background-color: #fcfcfc;
                    padding: 15px;
                    border-left: 3px solid transparent;
                    position: relative;
                    font-size: 12px;
                    font-weight: 300;
                    font-style: italic;
                    line-height: 1.5;
                    text-align: left;
                    border-radius: 0 3px 3px 0;
                    box-shadow: 0 1px 4px 0 rgba(0,0,0,.07);
                }
                .mgs-panel-alert-success{border-color:#39b54a;}
            </style>
            <?php
        }

        public function mgs_conditional_before_load_panel_assets(){
            wp_enqueue_style('mgs_conditional_editor_css', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/css/panel-conditional.css');
        }
    }
}
new MGS_Conditional();