<?php
global $mgs_elementor_config;

$mgs_elementor_config = [
    'menu'      => [
        'config'    => [
            'label'     => __('Configuración', 'mgs_elementor'),
            'ico'       => 'settings',
            'callback'  => '_config',
            'default'   => true   
        ],
        'registro'  => [
            'label'     => __('Registro', 'mgs_elementor'),
            'ico'       => 'app_registration',
            'callback'  => '_registro',
        ],
    ],
    'addons'    => [
        'slider'            => [
            'title'     => 'Slider',
            'desc'      => __('Permite crear sliders o carrousels de forma rapida.', 'mgs_elementor'),
            'required'  => __('* Requiere Elementor.', 'mgs_elementor'),
            'ico'       => '<span class="material-symbols-outlined">view_carousel</span>',
            'just_for'  => 'is_elementor'
        ],
        'conditional'       => [
            'title'     => 'Conditional',
            'desc'      => __('Agrega la opcion de esconder/mostrar contenedores segun ciertas condiciones.', 'mgs_elementor'),
            'required'  => __('* Requiere Elementor.<br>* Requiere la opcion de Flexbox activa de Elementor.', 'mgs_elementor'),
            'ico'       => '<span class="material-symbols-outlined">help_center</span>',
            'just_for'  => 'is_elementor'
        ],
        'css'               => [
            'title'     => 'Carga CSS adicional',
            'desc'      => __('Carga un CSS personalizado que no se vera afectado por las actualizaciones de su tema.', 'mgs_elementor'),
            'required'  => '',
            'config'    => [
                'title'     => __('Opciones', 'mgs_elementor'),
                'ico'       => '<span class="material-symbols-outlined">settings</span>',
                'callback'  => 'MGS_Elementor_External_CSS'
            ],
            'ico'       => '<span class="material-symbols-outlined">css</span>'
        ],
        'posts'             => [
            'title'     => 'Visualizar posts',
            'desc'      => __('Cree plantillas de visualización de entradas segun su gusto y placer, luego configure la cantidad, orden y dispocicion. utilice las plantillas de elementor.', 'mgs_elementor'),
            'required'  => __('* Requiere Elementor.', 'mgs_elementor'),
            'ico'       => '<span class="material-symbols-outlined">view_list</span>'
        ],
        'image_rotation'    => [
            'title'     => 'Rotador de imagenes aleatorias',
            'desc'      => __('Agrege imagenes aletorias, como una imagen o como un fondo.', 'mgs_elementor'),
            'required'  => __('* Requiere Elementor.', 'mgs_elementor'),
            'ico'       => '<span class="material-symbols-outlined">image</span>'
        ],
    ]
];