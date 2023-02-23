<?php
if( !class_exists('MGS_Elementor_External_CSS') ){
    class MGS_Elementor_External_CSS{
        public function __construct(){
            if( get_option('mgs-elementor-addon-state-css')=='on' && !is_admin() && file_exists(get_stylesheet_directory().'/mgs/main.css') ){
                add_action('wp_enqueue_scripts', function(){
					wp_enqueue_style('mgs-elementor-external-css', get_stylesheet_directory_uri().'/mgs/main.css?v='.rand(1,9).'.'.rand(1,9).'.'.rand(1,9));
				}, 99999);
            }
        }

        public static function config(){
            if( get_option('mgs-elementor-addon-state-css')=='on' && is_admin() ){
                ?>
                <div class="mgs_elementor_css_config">
                    <p><?php _e('Cree dentro de la carpeta de su tema una sub carpera llamada <code>mgs</code>. Y dentro de ella guarde su hoja de estilos CSS llamada <code>main.css</code>.', 'mgs_elementor')?></p>
                    <p><?php _e('Debe respetar los nombres para que funcione.', 'mgs_elementor')?></p>
                    <div class="status">
                        <div class="folder <?php echo self::test_folder_css('class')?>">
                            <div class="text"><?php echo self::test_folder_css('text')?></div>
                            <div class="ico"><?php echo self::test_folder_css('ico')?></div>
                        </div>
                        <div class="file <?php echo self::test_file_css('class')?>">
                            <div class="text"><?php echo self::test_file_css('text')?></div>
                            <div class="ico"><?php echo self::test_file_css('ico')?></div>
                        </div>
                    </div>
                </div>
                <?php
            }else{
                echo '<h2 class="error">Opciones no disponibles</h2>';
            }
        }

        public static function test_folder_css($return='flag'){
            $flag = false;
            if( is_dir(get_stylesheet_directory().'/mgs') ){
                $flag = true;
            }
            
            if( $return=='ico' ){
                if( $flag ){
                    return '<span class="material-symbols-outlined">folder_open</span>';
                }else{
                    return '<span class="material-symbols-outlined">folder_off</span>';
                }
            }elseif( $return=='class' ){
                if( $flag ){
                    return 'mgs_success';
                }else{
                    return 'mgs_error';
                }
            }elseif( $return=='text' ){
                if( $flag ){
                    return __('Carpeta encontrada.', 'mgs_elementor');
                }else{
                    return __('Carpeta no encontrada.', 'mgs_elementor');
                }
            }else{
                return $flag;
            }
        }
        
        public static function test_file_css($return='flag'){
            $flag = false;
            if( file_exists(get_stylesheet_directory().'/mgs/main.css') ){
                $flag = true;
            }
            
            if( $return=='ico' ){
                if( $flag ){
                    return '<span class="material-symbols-outlined">css</span>';
                }else{
                    return '<span class="material-symbols-outlined">css</span>';
                }
            }elseif( $return=='class' ){
                if( $flag ){
                    return 'mgs_success';
                }else{
                    return 'mgs_error';
                }
            }elseif( $return=='text' ){
                if( $flag ){
                    return __('Archivo encontrado.', 'mgs_elementor');
                }else{
                    return __('Archivo no encontrado.', 'mgs_elementor');
                }
            }else{
                return $flag;
            }
        }
    }
}
new MGS_Elementor_External_CSS();