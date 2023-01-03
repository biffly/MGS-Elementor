<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_MGS_Posts_Widget extends \Elementor\Widget_Base{
    private $sep_count;

    public function get_name(){return 'mgs_posts';}

    public function get_title(){return 'MGS Posts';}

    public function get_icon(){return 'eicon-code';}

    public function get_custom_help_url(){return 'https://developers.elementor.com/docs/widgets/';}

    public function get_categories(){return ['general'];}

    public function get_keywords(){return ['MGS', 'post', 'posts'];}

    protected function register_controls(){
		$this->start_controls_section('layout_section', ['label' => 'Layout', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT]);
            $this->add_control(
                'layout',
                [
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'label'     => esc_html__('Estilo', 'mgs_elementor'),
                    'options'   => $this->get_tipos_plantillas(),
                    'default'   => 'default',
                ]
            );
            $this->add_control(
                'layout_template_plantilla',
                [
                    'type'      => \Elementor\Controls_Manager::SELECT2,
                    'label'     => esc_html__('Plantilla', 'mgs_elementor'),
                    'multiple'  => false,
                    'options'   => $this->get_plantillas(),
                    'default'   => '',
                    'condition' => [
                        'layout'        => 'template'
                    ]
                ]
            );
            $this->add_responsive_control(
                'layout_columnas',
                [
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'label'     => esc_html__('Columnas', 'mgs_elementor'),
                    'options'   => [
                        1   => 1,
                        2   => 2,
                        3   => 3,
                        4   => 4,
                        5   => 5,
                        6   => 6,
                    ],
                    'default'   => 3,
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-wrapper' => 'grid-template-columns: repeat({{VALUE}}, 1fr)',
                    ],
                ]
            );
        $this->end_controls_section();

        //LAYPUT DEFAULT
        $this->add_layout_default();
        //FILTRO
        $this->add_filtro_section();
        //PAGINACÍON
        $this->add_pagination_section();
        //ESTILO LAYOUT DEFAULT
        $this->add_style_layout_default();

	}

    protected function render(){
        $un_id = uniqid();
        $settings = $this->get_settings_for_display();

        if( $settings['layout']=='default' ) echo $this->render_default_layout($un_id, $settings);

        if( $settings['layout']=='template' ) echo $this->render_template_layout($un_id, $settings);
	}

    private function render_template_layout($id, $settings){
        $out = '';
        $out .= '
            <div id="mgs-posts-wrapper-'.$id.'" class="mgs-posts-wrapper layout-template">
        ';
        //$out .= '<pre>'.print_r($settings, true).'</pre>';
        $query_args = $this->build_query($settings);
        $query = new WP_Query($query_args);
        if( $query->have_posts() ){
            while( $query->have_posts() ){
                $query->the_post();
                $post_id = get_the_ID();

                $out .= do_shortcode('[elementor-template id="'.$settings['layout_template_plantilla'].'"]');
                
            }
            $out .= $this->render_pagination($settings, $query);
        }else{
            $out .= 'No hay resultados';
        }        
        $out .= '
            </div>
        '; 
        //$out .= '<pre>'.print_r($settings, true).'</pre>';
        wp_reset_postdata();
        return $out; 
    }

    private function render_pagination($settings, $query){
        $out = '';
        if( $settings['paginacion_tipo']=='numbers' ){
            $acortar = ( $settings['paginacion_acortar']=='yes' ) ? false : true;
            $paginacion_limite_pag = ($settings['paginacion_limite_pag'] - 1) / 2;
            $out .= '<div class="mgs_pagination mgs_pagination_numbers '.$settings['paginacion_alignment'].'">';
            $out .= paginate_links([
                'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                'total'        => $query->max_num_pages,
                'current'      => max(1, get_query_var('paged') ),
                'format'       => '?paged=%#%',
                'show_all'     => $acortar,
                'type'         => 'plain',
                'end_size'     => $paginacion_limite_pag,
                'mid_size'     => $paginacion_limite_pag,
                'prev_next'    => false,
                'prev_text'    => sprintf( '<i></i> %1$s', __( 'Newer Posts', 'text-domain' ) ),
                'next_text'    => sprintf( '%1$s <i></i>', __( 'Older Posts', 'text-domain' ) ),
                'add_args'     => false,
                'add_fragment' => '',
            ]);
            $out .= '</div>';
        }
        if( $settings['paginacion_tipo']=='prev_next' ){
            $out .= '
                <div class="mgs_pagination mgs_pagination_prev_next '.$settings['paginacion_alignment'].'">
                    '.paginate_links([
                        'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                        'total'        => $query->max_num_pages,
                        'current'      => max(1, get_query_var('paged') ),
                        'format'       => '?paged=%#%',
                        'show_all'     => false,
                        'type'         => 'plain',
                        'end_size'     => 0,
                        'mid_size'     => 0,
                        'prev_next'    => true,
                        'prev_text'    => sprintf('<i></i> %1$s',$settings['paginacion_label_ant']),
                        'next_text'    => sprintf('%1$s <i></i>', $settings['paginacion_label_sig']),
                        'add_args'     => false,
                        'add_fragment' => '',
                    ]).'
                </div>
            ';
        }
        return $out;
    }

    private function render_default_layout($id, $settings){
        $out = '';
        $out .= '
            <div id="mgs-posts-wrapper-'.$id.'" class="mgs-posts-wrapper layout-default">
        ';

        $query_args = $this->build_query($settings);
        $query = new WP_Query($query_args);
        if( $query->have_posts() ){
            //$out .= '<pre>'.print_r($query, true).'</pre>';
            while( $query->have_posts() ){
                $query->the_post();
                $post_id = get_the_ID();
                

                $class = 'mgs-posts-post ';
                $thumb = '';
                $background = '';
                if( has_post_thumbnail($post_id) && $settings['layout_default_image']=='yes' ){
                    $class .= 'has_thumbnail ';
                    $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large')[0];
                    $background = 'background-image: url('.$thumb.');';

                }


                $out .= '
                    <div class="'.$class.'" id="mgs-posts-post-'.$post_id.'">
                        <a class="thumbnail" href="'.get_the_permalink($post_id).'" alt="'.get_the_title($post_id).'" style="'.$background.' padding-bottom:calc('.$settings['layout_default_image_proporcion'].' * 100%)"></a>
                        <div class="content">
                            '.$this->default_layout_render_title($settings, $post_id).'
                            '.$this->default_layout_render_excerpt($settings, $post_id).'
                            '.$this->default_layout_render_metas($settings, $post_id).'
                            '.$this->default_layout_render_leer_mas($settings, $post_id).'
                        </div>
                    </div>
                ';
            }
            
        }else{
            $out .= 'No hay resultados';
        }        
        $out .= '
            </div>
        ';       
        $out .= $this->render_pagination($settings, $query);

        
       // $out .= '<pre>'.print_r($settings, true).'</pre>';
        //$out .= '<pre>'.print_r($query_args, true).'</pre>';
        wp_reset_postdata();
        return $out;
    }

    private function add_layout_default(){
        $this->start_controls_section(
			'layout_default',
			[
				'label'     => esc_html__('Diseño', 'mgs-theme-upgrade'),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
            $this->add_control(
                'layout_default_image',
                [
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'label'         => esc_html__('Mostrar imagen', 'mgs-theme-upgrade'),
                    'label_on'      => esc_html__('Sí', 'mgs-theme-upgrade'),
                    'label_off'     => esc_html__('No', 'mgs-theme-upgrade'),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                    'condition' => [
                        'layout'    => 'default'
                    ]
                ]
            );
            $this->add_responsive_control(
                'layout_default_image_proporcion',
                [
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'label' => esc_html__('Proporción de la imagen', 'mgs-theme-upgrade'),
                    'placeholder' => 0.66,
                    'min' => 0.1,
                    'max' => 2,
                    'step' => 0.01,
                    'default' => 0.66,
                    'devices'       => ['desktop', 'tablet', 'mobile'],
                    'desktop_default' => [
                        'default' => 0.66
                    ],
                    'condition'     => [
                        'layout_default_image'  => 'yes',
                        'layout'    => 'default'
                    ]
                    
                ]
            );
            

            $this->add_separator(['layout'=>'default']);
            $this->add_control(
                'layout_default_title',
                [
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'label'         => esc_html__('Título', 'mgs-theme-upgrade'),
                    'label_on'      => esc_html__('Sí', 'mgs-theme-upgrade'),
                    'label_off'     => esc_html__('No', 'mgs-theme-upgrade'),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                    'condition' => [
                        'layout'    => 'default'
                    ]
                ]
            );
            $this->add_control(
                'layout_default_title_tag',
                [
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'label'     => esc_html__('Etiqueta HTML de título', 'mgs-theme-upgrade'),
                    'options'   => [
                        'h1'        => 'H1',
                        'h2'        => 'H2',
                        'h3'        => 'H3',
                        'h4'        => 'H4',
                        'h5'        => 'H5',
                        'h6'        => 'H6',
                        'div'        => 'div',
                        'span'        => 'span',
                        'p'        => 'p',
                    ],
                    'default'   => 'h2',
                    'condition' => [
                        'layout_default_title'  => 'yes',
                        'layout'    => 'default'
                    ]
                ]
            );
            $this->add_control(
                'layout_default_excerpt',
                [
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'label'         => esc_html__('Extracto', 'mgs-theme-upgrade'),
                    'label_on'      => esc_html__('Sí', 'mgs-theme-upgrade'),
                    'label_off'     => esc_html__('No', 'mgs-theme-upgrade'),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                    'condition' => [
                        'layout'    => 'default'
                    ]
                ]
            );
            $this->add_control(
                'layout_default_excerpt_lenght',
                [
                    'type'          => \Elementor\Controls_Manager::NUMBER,
                    'label'         => esc_html__('Longitud del extracto', 'mgs-theme-upgrade'),
                    'placeholder'   => 30,
                    'default'       => 30,
                    'condition'     => [
                        'layout_default_excerpt'  => 'yes',
                        'layout'                  => ['default', 'html', 'template']
                    ]
                ]
            );


            $this->add_separator(['layout'=>'default']);
            $this->add_control(
                'layout_default_metas',
                [
                    'type'      => \Elementor\Controls_Manager::SELECT2,
                    'label'     => esc_html__('Metadatos', 'mgs-theme-upgrade'),
                    'label_block'   => true,
                    'multiple' => true,
                    'options'   => [
                        'author'    => esc_html__('Autor', 'mgs-theme-upgrade'),
                        'date'      => esc_html__('Fecha', 'mgs-theme-upgrade'),
                        'time'      => esc_html__('Hora', 'mgs-theme-upgrade'),
                        'comments'  => esc_html__('Comentarios', 'mgs-theme-upgrade')
                    ],
                    'default'   => [],
                    'condition' => [
                    ]
                ]
            );
            $this->add_control(
                'layout_default_metas_separator',
                [
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'label'         => esc_html__('Separador metas', 'mgs-theme-upgrade'),
                    'dynamic'       => [
                        'active'        => true,
                    ],
                    'placeholder'   => '|',
                    'default'       => '|'
                ]
            );


            $this->add_separator(['layout'=>'default']);
            $this->add_control(
                'layout_default_leer_mas',
                [
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'label'         => esc_html__('Leer más', 'mgs-theme-upgrade'),
                    'label_on'      => esc_html__('Sí', 'mgs-theme-upgrade'),
                    'label_off'     => esc_html__('No', 'mgs-theme-upgrade'),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                    'condition' => [
                        'layout'    => 'default'
                    ]
                ]
            );
            $this->add_control(
                'layout_default_leer_mas_text',
                [
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'label'         => esc_html__('Texto de «Leer más»', 'mgs-theme-upgrade'),
                    'dynamic'       => [
                        'active'        => true,
                    ],
                    'placeholder'   => esc_html__('Leer más »', 'mgs-theme-upgrade'),
                    'default'       => esc_html__('Leer más »', 'mgs-theme-upgrade'),
                    'condition'     => [
                        'layout_default_leer_mas'  => 'yes',
                        'layout'    => 'default'
                    ]
                ]
            );
            $this->add_control(
                'layout_default_leer_mas_blank',
                [
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'label'         => esc_html__('Abrir en nueva ventana', 'mgs-theme-upgrade'),
                    'label_on'      => esc_html__('Sí', 'mgs-theme-upgrade'),
                    'label_off'     => esc_html__('No', 'mgs-theme-upgrade'),
                    'return_value'  => 'yes',
                    'default'       => 'no',
                    'condition'     => [
                        'layout_default_leer_mas'  => 'yes',
                        'layout'    => 'default'
                    ]
                ]
            );



        $this->end_controls_section();
    }
    private function add_filtro_section(){
        $this->start_controls_section(
            'filtro_section',
			[
                'label'     => 'Filtro',
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

            $this->add_control(
                'filtro_post_type',
                [
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'label'     => esc_html__('Origen', 'mgs-theme-upgrade'),
                    'options'   => $this->get_post_types_disponibles(),
                    'default'   => 'post',
                ]
            );
            $this->add_control(
                'filtro_is_archive',
                [
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'label'         => esc_html__('Es archivo?', 'mgs-theme-upgrade'),
                    'label_on'      => esc_html__('Sí', 'mgs-theme-upgrade'),
				    'label_off'     => esc_html__('No', 'mgs-theme-upgrade'),
				    'return_value'  => 'yes',
				    'default'       => '',
                ]
            );
            $this->add_control(
                'filtro_postcount',
                [
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'label' => esc_html__('Cantidad de entradas', 'mgs-theme-upgrade'),
                    'placeholder' => '3',
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                    'default' => 3,
                ]
            );
            $this->add_control(
                'filtro_postoffset',
                [
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'label' => esc_html__('Offest entradas', 'mgs-theme-upgrade'),
                    'placeholder' => '0',
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'default' => 0,
                    'condition'     => [
                        'filtro_is_archive'     => ''
                    ]
                ]
            );

            $this->add_control(
                'filtro_categories',
                [
                    'label'         => esc_html__('Categorías', 'mgs-theme-upgrade'),
                    'type'          => \Elementor\Controls_Manager::SELECT2,
                    'label_block'   => false,
                    'multiple'      => true,
                    'options'       => $this->get_categorias(),
                    'default'       => '',
                    'condition'     => [
                        'filtro_is_archive'     => ''
                    ]
                ]
            );

            $this->add_control(
                'filtro_q_listar',
                [
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'label'     => esc_html__('Fecha', 'mgs-theme-upgrade'),
                    'options'   => [
                        'all'           => __('Todo', 'mgs-theme-upgrade'),
                        'ayer'          => __('Día anterior', 'mgs-theme-upgrade'),
                        'semana_pasada' => __('Semana anterior', 'mgs-theme-upgrade'),
                        'mes_pasado'    => __('Mes anterior', 'mgs-theme-upgrade'),
                        'custom'        => __('Personalizado', 'mgs-theme-upgrade'),
                    ],
                    'default'   => 'all',
                    'condition'     => [
                        'filtro_is_archive'     => ''
                    ]
                ]
            );
            $this->add_control(
                'filtro_q_listar_custom_desde',
                [
                    'type'          => \Elementor\Controls_Manager::DATE_TIME,
                    'label'         => esc_html__('Desde', 'mgs-theme-upgrade'),
                    'description'   => esc_html__('Al establecer una fecha «Desde» se mostrarán todas las entradas publicadas desde la fecha elegida (inclusive).', 'mgs-theme-upgrade'),
                    'picker_options'    => [
                        'enableTime'        => false
                    ],
                    'condition' => [
                        'filtro_q_listar'   => 'custom',
                        'filtro_is_archive'   => '',
                    ]
                ]
            );
            $this->add_control(
                'filtro_q_listar_custom_hasta',
                [
                    'type'              => \Elementor\Controls_Manager::DATE_TIME,
                    'label'             => esc_html__('Hasta', 'mgs-theme-upgrade'),
                    'description'       => esc_html__('Al establecer una fecha «Hasta» se mostrarán todas las entradas publicadas hasta la fecha elegida (inclusive).', 'mgs-theme-upgrade'),
                    'picker_options'    => [
                        'enableTime'        => false
                    ],
                    'condition' => [
                        'filtro_q_listar'   => 'custom',
                        'filtro_is_archive'   => '',
                    ]
                ]
            );
            $this->add_control(
                'filtro_order_by',
                [
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'label'     => esc_html__('Ordenar por', 'mgs-theme-upgrade'),
                    'options'   => [
                        'post_date'     => __('Fecha', 'mgs-theme-upgrade'),
                        'title'         => __('Título', 'mgs-theme-upgrade'),
                        'menu_order'    => __('Orden del menú', 'mgs-theme-upgrade'),
                        'comment_count' => __('Cantidad de comentarios', 'mgs-theme-upgrade'),
                        'rand'          => __('Al azar', 'mgs-theme-upgrade')
                    ],
                    'default'   => 'post_date',
                ]
            );
            $this->add_control(
                'filtro_order',
                [
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'label'     => esc_html__('Ordenamiento', 'mgs-theme-upgrade'),
                    'options'   => [
                        'asc'   => 'ASC',
                        'desc'  => 'DESC',
                    ],
                    'default'   => 'DESC',
                ]
            );
            $this->add_control(
                'filtro_ignore_sticky',
                [
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'label'         => esc_html__('Ignorar las entradas fijas', 'mgs-theme-upgrade'),
                    'description'   => esc_html__('La ordenación de las entradas fijas solo es visible en la vista pública', 'mgs-theme-upgrade'),
                    'label_on'      => esc_html__('Sí', 'mgs-theme-upgrade'),
				    'label_off'     => esc_html__('No', 'mgs-theme-upgrade'),
				    'return_value'  => 'yes',
				    'default'       => 'yes',
                ]
            );
            $this->add_control(
                'filtro_related_posts',
                [
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'label'         => esc_html__('Entradas relacionadas', 'mgs-theme-upgrade'),
                    'label_on'      => esc_html__('Sí', 'mgs-theme-upgrade'),
				    'label_off'     => esc_html__('No', 'mgs-theme-upgrade'),
				    'return_value'  => 'yes',
				    'default'       => '',
                    'condition'     => [
                        'filtro_is_archive'     => ''
                    ]
                    
                ]
            );
            $this->add_control(
                'filtro_exclude_actual',
                [
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'label'         => esc_html__('Excluir entrada actual?', 'mgs-theme-upgrade'),
                    'label_on'      => esc_html__('Sí', 'mgs-theme-upgrade'),
				    'label_off'     => esc_html__('No', 'mgs-theme-upgrade'),
				    'return_value'  => 'yes',
				    'default'       => '',
                    'condition'     => [
                        'filtro_is_archive'     => ''
                    ]
                ]
            );

        $this->end_controls_section();
    }
    private function add_pagination_section(){
        $this->start_controls_section(
            'paginacion_section',
			[
                'label'     => 'Paginacíon',
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
            $this->add_control(
                'paginacion_tipo',
                [
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'label'     => esc_html__('Paginacíon', 'mgs-theme-upgrade'),
                    'options'   => [
                        ''                          => esc_html__('Ninguno', 'mgs-theme-upgrade'),
                        'numbers'                   => esc_html__('Números', 'mgs-theme-upgrade'),
                        'prev_next'                 => esc_html__('Anterior/Siguiente', 'mgs-theme-upgrade'),
                        //'numbers_and_prev_next'     => esc_html__('Números + Anterior/Siguiente', 'mgs-theme-upgrade'),
                        //'load_more_on_click'        => esc_html__('Cargar al hacer clic', 'mgs-theme-upgrade'),
                        //'load_more_infinite_scroll' => esc_html__('Scroll infinito', 'mgs-theme-upgrade'),
                    ],
                    'default'   => 'asc',
                ]
            );

            $this->add_control(
                'paginacion_limite_pag',
                [
                    'type'          => \Elementor\Controls_Manager::NUMBER,
                    'label'         => esc_html__('Límite de páginas', 'mgs-theme-upgrade'),
                    'placeholder'   => '5',
                    'min'           => 1,
                    'max'           => 100,
                    'step'          => 1,
                    'default'       => 5,
                    'condition'     => [
                        'paginacion_tipo'   => ['numbers']
                    ]
                ]
            );
            $this->add_control(
                'paginacion_acortar',
                [
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'label'         => esc_html__('Acortar', 'mgs-theme-upgrade'),
                    'label_on'      => esc_html__('Sí', 'mgs-theme-upgrade'),
				    'label_off'     => esc_html__('No', 'mgs-theme-upgrade'),
				    'return_value'  => 'yes',
				    'default'       => 'no',
                    'condition'     => [
                        'paginacion_tipo'   => 'numbers'
                    ]
                ]
            );
            $this->add_control(
                'paginacion_label_ant',
                [
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'label'         => esc_html__('Etiqueta anterior', 'mgs-theme-upgrade'),
                    'dynamic'       => [
                        'active'        => true,
                    ],
                    'placeholder'   => '&laquo; '.esc_html__('Anterior', 'mgs-theme-upgrade'),
                    'default'       => '&laquo; '.esc_html__('Anterior', 'mgs-theme-upgrade'),
                    'condition'     => [
                        'paginacion_tipo'   => ['prev_next', 'numbers_and_prev_next']
                    ]
                ]
            );
            $this->add_control(
                'paginacion_label_sig',
                [
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'label'         => esc_html__('Etiqueta siguiente', 'mgs-theme-upgrade'),
                    'dynamic'       => [
                        'active'        => true,
                    ],
                    'placeholder'   => esc_html__('Siguiente', 'mgs-theme-upgrade').' &raquo;',
                    'default'       => esc_html__('Siguiente', 'mgs-theme-upgrade').' &raquo;',
                    'condition'     => [
                        'paginacion_tipo'   => ['prev_next', 'numbers_and_prev_next']
                    ]
                ]
            );
            $this->add_control(
                'paginacion_alignment',
                [
                    'type'          => \Elementor\Controls_Manager::CHOOSE,
                    'label'         => esc_html__('Alineacíon', 'mgs-theme-upgrade'),
                    'options'       => [
                        'left'          => [
                            'title' => esc_html__('Izquierda', 'mgs-theme-upgrade'),
                            'icon'  => 'eicon-text-align-left',
                        ],
                        'center'        => [
                            'title' => esc_html__('Centro', 'mgs-theme-upgrade'),
                            'icon'  => 'eicon-text-align-center',
                        ],
                        'right'         => [
                            'title' => esc_html__('Derecha', 'mgs-theme-upgrade'),
                            'icon'  => 'eicon-text-align-right',
                        ],
                    ],
                    'default'       => 'center',
                    'condition'     => [
                        'paginacion_tipo'   => ['numbers', 'prev_next']
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs_pagination' => 'text-align: {{VALUE}}',
                    ]
                ]
            );
        $this->end_controls_section();
    }
    private function add_style_layout_default(){
        $this->start_controls_section(
			'layout_html_style_aviso',
			[
				'label'     => esc_html__('Aviso', 'mgs-theme-upgrade'),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout!'    => 'default'
                ]
			]
		);
            $this->add_control(
                'important_note_1',
                [
                    'label' => esc_html__('Aviso importante', 'mgs-theme-upgrade'),
                    'show_label'    => false,
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => esc_html__('Para este diseño debera especificar el diseño y estilos.', 'mgs-theme-upgrade'),
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                    'condition' => [
                        'layout!'    => 'default'
                    ]
                ]
            );
        $this->end_controls_section();



        $this->start_controls_section(
			'layout_default_style_col_row_gap',
			[
				'label'     => esc_html__('Dispocición', 'mgs-theme-upgrade'),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
            $this->add_responsive_control(
                'layout_default_style_col_gap',
                [
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'label' => esc_html__('Espacio entre columnas', 'mgs-theme-upgrade'),
                    'size_units'    => ['px'],
                    'devices' => ['desktop', 'tablet', 'mobile' ],
                    'default'   => [
                        'size' => 30,
                        'unit' => 'px'
                    ],
                    'desktop_default' => [
                        'size' => 30,
                        'unit' => 'px'
                    ],
                    'tablet_default' => [
                        'size' => 20,
                        'unit' => 'px'
                    ],
                    'mobile_default' => [
                        'size' => 10,
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-wrapper' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
                    ]
                    //mgs-posts-wrapper layout-default
                ]
            );
            $this->add_responsive_control(
                'layout_default_style_row_gap',
                [
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'label' => esc_html__('Espacio entre filas', 'mgs-theme-upgrade'),
                    'size_units'    => ['px'],
                    'devices' => ['desktop', 'tablet', 'mobile' ],
                    'default'   => [
                        'size' => 30,
                        'unit' => 'px'
                    ],
                    'desktop_default' => [
                        'size' => 30,
                        'unit' => 'px'
                    ],
                    'tablet_default' => [
                        'size' => 20,
                        'unit' => 'px'
                    ],
                    'mobile_default' => [
                        'size' => 10,
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-wrapper' => 'row-gap: {{SIZE}}{{UNIT}}',
                    ]
                ]
            );
        $this->end_controls_section();

        $this->start_controls_section(
			'layout_default_style_card',
			[
				'label'     => esc_html__('Tarjeta', 'mgs-theme-upgrade'),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout'    => 'default'
                ]
			]
		);
            $this->add_control(
                'layout_default_style_card_bg',
                [
                    'label'     => esc_html__('Color de fondo', 'mgs-theme-upgrade'),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'alpha'     => true,
                    'default'   => '#ffffff',
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post' => 'background-color: {{VALUE}}',
                    ]
                ]
            );
            $this->add_control(
                'layout_default_style_card_color_border',
                [
                    'label'     => esc_html__('Color del borde', 'mgs-theme-upgrade'),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'alpha'     => true, 
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post' => 'border-color: {{VALUE}}',
                    ]
                ]
            );
            $this->add_responsive_control(
                'layout_default_style_card_width_border',
                [
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'label' => esc_html__('Ancho del borde', 'mgs-theme-upgrade'),
                    'size_units'    => ['px'],
                    'devices' => ['desktop', 'tablet', 'mobile' ],
                    'default'   => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'desktop_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'tablet_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'mobile_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post' => 'border-width: {{SIZE}}{{UNIT}}',
                    ]

                ]
            );
            $this->add_control(
                'layout_default_style_card_radio_border',
                [
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'label' => esc_html__('Radio del borde', 'mgs-theme-upgrade'),
                    'size_units'    => ['px', '%'],
                    'devices' => ['desktop', 'tablet', 'mobile' ],
                    'default'   => [
                        'size' => '20',
                        'unit' => 'px'
                    ],
                    'desktop_default' => [
                        'size' => '20',
                        'unit' => 'px'
                    ],
                    'tablet_default' => [
                        'size' => '20',
                        'unit' => 'px'
                    ],
                    'mobile_default' => [
                        'size' => '20',
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post' => 'border-radius: {{SIZE}}{{UNIT}}',
                    ]
                ]
            );
        $this->end_controls_section();

        $this->start_controls_section(
			'layout_default_style_title',
			[
				'label'     => esc_html__('Contenido', 'mgs-theme-upgrade'),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout'                => 'default'
                ]
			]
		);
            $this->add_control(
                'layout_default_style_title_',
                [
                    'label' => esc_html__('Título', 'mgs-theme-upgrade'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'none',
                    'condition' => [
                        'layout_default_title'  => 'yes'
                    ]
                ]
            );
            $this->add_control(
                'layout_default_style_title_color',
                [
                    'label'     => esc_html__('Color', 'mgs-theme-upgrade'),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'alpha'     => true,
                    'default'   => '#333',
                    'selectors' => [
                        '{{WRAPPER}} .mgs_post_title a' => 'color: {{VALUE}}',
                    ],
                    'condition' => [
                        'layout_default_title'  => 'yes'
                    ]
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name'      => 'layout_default_style_title_font_family',
                    'selector'  => '{{WRAPPER}} .mgs_post_title a',
                    'condition' => [
                        'layout_default_title'  => 'yes'
                    ]
                ]
            );
            $this->add_responsive_control(
                'layout_default_style_title_space',
                [
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'label' => esc_html__('Separación', 'mgs-theme-upgrade'),
                    'size_units'    => ['px'],
                    'devices' => ['desktop', 'tablet', 'mobile' ],
                    'default'   => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'desktop_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'tablet_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'mobile_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs_post_title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        'layout_default_title'  => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'layout_default_style_extracto_',
                [
                    'label' => esc_html__('Extracto', 'mgs-theme-upgrade'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'layout_default_excerpt'  => 'yes'
                    ]
                ]
            );
            $this->add_control(
                'layout_default_style_extracto_color',
                [
                    'label'     => esc_html__('Color', 'mgs-theme-upgrade'),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'alpha'     => true,
                    'default'   => '#333',
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post .excerpt' => 'color: {{VALUE}}',
                    ],
                    'condition' => [
                        'layout_default_excerpt'  => 'yes'
                    ]
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name'      => 'layout_default_style_extracto_font_family',
                    'selector'  => '{{WRAPPER}} .mgs-posts-post .excerpt',
                    'condition' => [
                        'layout_default_excerpt'  => 'yes'
                    ]
                ]
            );
            $this->add_responsive_control(
                'layout_default_style_extracto_space',
                [
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'label' => esc_html__('Separación', 'mgs-theme-upgrade'),
                    'size_units'    => ['px'],
                    'devices' => ['desktop', 'tablet', 'mobile' ],
                    'default'   => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'desktop_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'tablet_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'mobile_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post .excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        'layout_default_excerpt'  => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'layout_default_style_metas_',
                [
                    'label' => esc_html__('Metadatos', 'mgs-theme-upgrade'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'layout_default_metas!'  => ''
                    ]
                ]
            );
            $this->add_control(
                'layout_default_style_metas_color',
                [
                    'label'     => esc_html__('Color', 'mgs-theme-upgrade'),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'alpha'     => true,
                    'default'   => '#333',
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post .metas' => 'color: {{VALUE}}',
                    ],
                    'condition' => [
                        'layout_default_metas!'  => ''
                    ]
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name'      => 'layout_default_style_metas_font_family',
                    'selector'  => '{{WRAPPER}} .mgs-posts-post .metas',
                    'condition' => [
                        'layout_default_metas!'  => ''
                    ]
                ]
            );
            $this->add_responsive_control(
                'layout_default_style_metas_space',
                [
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'label' => esc_html__('Separación', 'mgs-theme-upgrade'),
                    'size_units'    => ['px'],
                    'devices' => ['desktop', 'tablet', 'mobile' ],
                    'default'   => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'desktop_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'tablet_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'mobile_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post .metas' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        'layout_default_metas!'  => ''
                    ]
                ]
            );

            $this->add_control(
                'layout_default_style_leer_mas_',
                [
                    'label' => esc_html__('Leer más', 'mgs-theme-upgrade'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'layout_default_leer_mas'  => 'yes'
                    ]
                ]
            );
            $this->add_control(
                'layout_default_style_leer_mas_color',
                [
                    'label'     => esc_html__('Color', 'mgs-theme-upgrade'),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'alpha'     => true,
                    'default'   => '#333',
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post .leer_mas a' => 'color: {{VALUE}}',
                    ],
                    'condition' => [
                        'layout_default_leer_mas'  => 'yes'
                    ]
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name'      => 'layout_default_style_leer_mas_font_family',
                    'selector'  => '{{WRAPPER}} .mgs-posts-post .leer_mas a',
                    'condition' => [
                        'layout_default_leer_mas'  => 'yes'
                    ]
                ]
            );
            $this->add_control(
                'layout_default_style_leer_mas_alignment',
                [
                    'type'          => \Elementor\Controls_Manager::CHOOSE,
                    'label'         => esc_html__('Alineacíon', 'mgs-theme-upgrade'),
                    'options'       => [
                        'left'          => [
                            'title' => esc_html__('Izquierda', 'mgs-theme-upgrade'),
                            'icon'  => 'eicon-text-align-left',
                        ],
                        'center'        => [
                            'title' => esc_html__('Centro', 'mgs-theme-upgrade'),
                            'icon'  => 'eicon-text-align-center',
                        ],
                        'right'         => [
                            'title' => esc_html__('Derecha', 'mgs-theme-upgrade'),
                            'icon'  => 'eicon-text-align-right',
                        ],
                    ],
                    'default'       => 'center',
                    'condition'     => [
                        'layout_default_leer_mas'   => 'yes'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post .leer_mas' => 'text-align: {{VALUE}}',
                    ],
                ]
            );
            $this->add_responsive_control(
                'layout_default_style_leer_mas_space',
                [
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'label' => esc_html__('Separación', 'mgs-theme-upgrade'),
                    'size_units'    => ['px'],
                    'devices' => ['desktop', 'tablet', 'mobile' ],
                    'default'   => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'desktop_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'tablet_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'mobile_default' => [
                        'size' => '0',
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .mgs-posts-post .leer_mas' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        'layout_default_leer_mas'  => 'yes'
                    ]
                ]
            );

        $this->end_controls_section();
    }

    private function add_separator($condition=[]){
        if( !isset($this->sep_count) ) $this->sep_count = 1;

        $this->add_control(
            'hr_'.$this->sep_count, [
                'type' => \Elementor\Controls_Manager::DIVIDER,
                'condition'     => $condition
            ]
        );

        $this->sep_count++;
    }

    private function build_query($settings){
        $arg = [
            'post_type'             => $settings['filtro_post_type'],
            'post_status'           => 'publish',
            'orderby'               => $settings['filtro_order_by'],
            'order'                 => $settings['filtro_order'],
        ];

        if( $settings['filtro_is_archive']=='yes' ){
            //&& is_post_type_archive() 
            global $wp_query;
            $tax = $wp_query->get_queried_object();
            if( isset($tax->term_id) ) $arg['category__in'] = $tax->term_id;
            //echo '<pre>'.print_r($tax, true).'</pre>';
        }else{
            if( $settings['filtro_ignore_sticky']=='yes' ) $arg['ignore_sticky_posts'] = true;
    
            //excluir actual
            if( $settings['filtro_exclude_actual']=='yes' ){
                $arg['post__not_in'] = [get_the_ID()];
            }
    
            //categorías
            if( isset($settings['filtro_categories']) && $settings['filtro_categories']!='' && count($settings['filtro_categories'])>0 ){
                $arg['category__in'] = $settings['filtro_categories'];
                //$arg['post__not_in'] = [get_the_ID()];
            }
    
            //post relacionados
            if( $settings['filtro_related_posts']=='yes' ){
                $category__in = [];
                $cats = get_the_terms(get_the_ID(),'category');
                if( is_array($cats) ){
                    foreach( $cats as $cat ){
                        $category__in[] = $cat->term_id;
                    }
                    $arg['category__in'] = $category__in;
                    $arg['post__not_in'] = [get_the_ID()];
                }
            }
    
            //post de offset
            if( $settings['filtro_postoffset']>0 ){
                $arg['offset']  = $settings['filtro_postoffset'];
            }
    
            //post de ayer
            if( $settings['filtro_q_listar']=='ayer' ){
                $ayer = date('Y-m-d',strtotime("-1 days"));
                $arg['date_query'] = [
                    [
                        'year'  => date('Y', strtotime($ayer)),
                        'month' => date('m', strtotime($ayer)),
                        'day' => date('d', strtotime($ayer)),
                    ]
                ];
            }
            //post de la semana pasada
            if( $settings['filtro_q_listar']=='semana_pasada' ){
                $semana_pasada = date('Y-m-d', strtotime("-1 week"));
                $arg['date_query'] = [
                    [
                        'year'  => date('Y', strtotime($semana_pasada)),
                        'week'  => date('W', strtotime($semana_pasada))
                        
                    ]
                ];
            }
            //post del mes anterior
            if( $settings['filtro_q_listar']=='mes_pasado' ){
                $mes_pasado = date('Y-m-d', strtotime("-1 month"));
                $arg['date_query'] = [
                    [
                        'year'  => date('Y', strtotime($mes_pasado)),
                        'month' => date('m', strtotime($mes_pasado))
                        
                    ]
                ];
            }
            //post del mes anterior
            if( $settings['filtro_q_listar']=='custom' ){
                $desde = date('Y-m-d', strtotime($settings['filtro_q_listar_custom_desde']));
                $hasta = date('Y-m-d', strtotime($settings['filtro_q_listar_custom_hasta']));
                $arg['date_query'] = [
                    'after'         => $desde,
                    'before'        => $hasta,
                    'inclusive'     => true
                ];
            }
        }

        //paginacion
        if( $settings['paginacion_tipo']=='' ){
            $arg['nopaging'] = true;
            $arg['posts_per_page'] = $settings['filtro_postcount'];
        }else{
            $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
            $arg['nopaging'] = false;
            $arg['posts_per_page'] = $settings['filtro_postcount'];
            $arg['paged'] = $paged;
        }

        return $arg;
    }

    private function default_layout_render_title($settings, $post_id){
        $out = '';
        if( $settings['layout_default_title']=='yes' ){
            $out .= '
                <'.$settings['layout_default_title_tag'].' class="mgs_post_title">
                    <a href="'.get_the_permalink($post_id).'" alt="'.get_the_title($post_id).'">'.get_the_title($post_id).'</a>
                </'.$settings['layout_default_title_tag'].'>
            ';
        }

        return $out;
    }
    private function default_layout_render_excerpt($settings, $post_id){
        $out = '';
        if( $settings['layout_default_excerpt']=='yes' ){
            $post = get_post($post_id);
            //$out .= '<pre>'.print_r($post, true).'</pre>';
            $content = wp_strip_all_tags($post->post_content , true);
            $out .= '<div class="excerpt">'.wp_trim_words($content, $settings['layout_default_excerpt_lenght'], '').'</div>';
        }

        return $out;
    }
    private function default_layout_render_metas($settings, $post_id, $wrapper=true){
        $out = '';
        if( count($settings['layout_default_metas'])>0 ){
            $post = get_post($post_id);
            $metas = [];
            foreach($settings['layout_default_metas'] as $meta ){
                if( $meta=='author' ){
                    $author_id = $post->post_author;
                    $metas['author'] = '<span class="author">'.get_the_author_meta('display_name', $author_id).'</span>';
                }
                if( $meta=='date' ){
                    $metas['date'] = '<span class="date">'.get_the_date('', $post_id).'</span>';
                }
                if( $meta=='time' ){
                    $metas['time'] = '<span class="time">'.get_post_time('H:i', false, $post_id, true).'</span>';
                }
                if( $meta=='comments' ){
                    $_t = get_comments_number_text(
                        __('Sin comentarios', 'mgs-theme-upgrade'), 
                        __('Un comentario', 'mgs-theme-upgrade'), 
                        __('% comentarios', 'mgs-theme-upgrade'), 
                        $post_id
                    );
                    $metas['comments'] = '<span class="comments">'.$_t.'</span>';
                }
            }

            if( $settings['layout_default_metas_separator']!='' ){
                $_metas = implode('<span class="sep">'.$settings['layout_default_metas_separator'].'</span>', $metas);
            }else{
                $_metas = implode(' ', $metas);
            }

            if( $wrapper ){
                $out = '<div class="metas">'.$_metas.'</div>';
            }else{
                $out = $_metas;
            }
        }

        return $out;
    }
    private function default_layout_render_leer_mas($settings, $post_id){
        $out = '';
        if( $settings['layout_default_leer_mas']=='yes' ){
            $target = ( $settings['layout_default_leer_mas_blank']=='yes' ) ? '_blank' : '';
            $out .= '
                <div class="leer_mas"><a href="'.get_the_permalink($post_id).'" target="'.$target.'" alt="'.$settings['layout_default_leer_mas_text'].'">'.$settings['layout_default_leer_mas_text'].'</a></div>
            ';
        }
        return $out;
    }

    private function get_tipos_plantillas(){
        //tipos de plantillas
        $type_plantillas = [
            'default'       => esc_html__('Default', 'mgs-elementor'),
        ];
        if( post_type_exists('elementor_library') ){
            $type_plantillas['template'] = esc_html__('Plantilla', 'mgs-elementor');
        }
        return $type_plantillas;
    }

    private function get_plantillas(){
        //layout plantilla
        $plantillas_elementor = [];
        $plantillas = get_posts([
            'numberposts'       => -1,
            'post_type'         => 'elementor_library',
            'post_status'       => 'publish',
            /*'tax_query'         => [
                [
                    'taxonomy'      => 'elementor_library_type',
                    'field'         => 'slug',
                    'terms'         => 'container'
                ]
            ]*/
        ]);
        foreach( $plantillas as $k=>$v ){
            $plantillas_elementor[$v->ID] = get_the_title($v->ID);
        }

        return $plantillas_elementor;
    }

    private function get_post_types_disponibles(){
        //Listado de POST_TYPES
        $post_types_disponibles = get_post_types(
            [],
            'objects'
        );
        unset($post_types_disponibles['attachment']);
        unset($post_types_disponibles['revision']);
        unset($post_types_disponibles['nav_menu_item']);
        unset($post_types_disponibles['custom_css']);
        unset($post_types_disponibles['customize_changeset']);
        unset($post_types_disponibles['oembed_cache']);
        unset($post_types_disponibles['user_request']);
        unset($post_types_disponibles['wp_block']);
        unset($post_types_disponibles['wp_template']);
        unset($post_types_disponibles['wp_template_part']);
        unset($post_types_disponibles['wp_global_styles']);
        unset($post_types_disponibles['wp_navigation']);
        unset($post_types_disponibles['elementor_library']);
        unset($post_types_disponibles['elementor_snippet']);
        unset($post_types_disponibles['elementor_font']);
        unset($post_types_disponibles['elementor_icons']);
        unset($post_types_disponibles['e-landing-page']);
        $filtro_post_type = [];
        foreach( $post_types_disponibles as $k=>$v ){
            $filtro_post_type[$k] = $v->label;
        }

        return $filtro_post_type;
    }

    private function get_taxonomies_disponibles(){
        $taxs = [];
        $taxs['related'] = 'Categoría actual';
        $post_types = $this->get_post_types_disponibles();
        foreach( $post_types as $k=>$v ){
            $taxonomies = get_object_taxonomies($k, 'objects');
            foreach( $taxonomies as $tt ){
                $taxs[$tt->name] = $tt->label.' - '.$v;
            }
        }
        unset($taxs['post_format']);

        return $taxs;
    }

    private function get_post_types_by_taxonomy($tax='category'){
        $out = [];
        $post_types = get_post_types();
        foreach( $post_types as $post_type ){
            $taxonomies = get_object_taxonomies($post_type);
            if( in_array($tax, $taxonomies) ){
                $out[] = $post_type;
            }
        }
        return $out;
    }

    private function get_categorias(){
        $options = [];
        $args = [
            'hide_empty'    => false,
            'post_type'     => 'post',
            'taxonomy'      => 'category',
        ];
        $categories = get_categories($args);
        foreach( $categories as $key=>$category ){
            $options[$category->term_id] = $category->name;
        }
        return $options;
    }
}