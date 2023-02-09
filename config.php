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
        'colorfillani-css'  => [
            'title'     => 'Animaciones con mascaras CSS',
            'desc'      => 'Permite crear animaciones con mascaras CSS, coloridad y super originales',
            'required'  => __('* Requiere Elementor.', 'mgs_elementor'),
            'ico'       => '<span class="material-symbols-outlined">view_carousel</span>',
            'just_for'  => 'is_elementor'
        ],
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
            'ico'       => '<span class="material-symbols-outlined">view_list</span>',
            'just_for'  => 'is_elementor'
        ],
        'image_rotation'    => [
            'title'     => 'Rotador de imagenes aleatorias',
            'desc'      => __('Agrege imagenes aletorias, como una imagen o como un fondo.', 'mgs_elementor'),
            'required'  => __('* Requiere Elementor.', 'mgs_elementor'),
            'ico'       => '<span class="material-symbols-outlined">image</span>',
            'just_for'  => 'is_elementor'
        ],
        'dummy_content'     => [
            'title'     => 'Generador de contenido',
            'desc'      => __('Genera contenido ficticio para entornos de desarrollo. Utiliza la API de google para buscar imagenes de dominio publico y loripsum.net para la generación de los textos.', 'mgs_elementor'),
            'ico'       => '<span class="material-symbols-outlined">shuffle</span>',
            'run'       => [
                'title'     => __('Ejecutar', 'mgs_elementor'),
                'ico'       => '<span class="material-symbols-outlined">play_arrow</span>',
                'callback'  => 'MGS_Dummy_Content'
            ],
            'config'    => [
                'title'     => __('Opciones', 'mgs_elementor'),
                'ico'       => '<span class="material-symbols-outlined">settings</span>',
                'callback'  => 'MGS_Dummy_Content'
            ],
        ],
        'login_replace'     => [
            'title'     => __('Reemplazo de login', 'mgs_elementor'),
            'desc'      => __('Permite reemplazar la pantalla de login a la administracion de wordpress para dar un aspecto de marca blanca.', 'mgs_elementor'),
            'ico'       => '<span class="material-symbols-outlined">login</span>',
            'config'    => [
                'title'     => __('Opciones', 'mgs_elementor'),
                'ico'       => '<span class="material-symbols-outlined">settings</span>',
                'callback'  => 'MGS_LoginReplace'
            ],
        ]
        
    ]
];