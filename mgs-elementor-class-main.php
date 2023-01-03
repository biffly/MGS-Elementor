<?php
if( !defined('ABSPATH') ){ exit; }

if( !class_exists('MGS_Elementor_AddOns') ){
	class MGS_Elementor_AddOns{
        public $slug_admin;
        public $base_url;
        public $default_menu_item;
        public static $license_state = false;

        function __construct(){
            

            $this->slug_admin = 'mgs_elementor_admin';
            $this->base_url = admin_url('options-general.php?page='.$this->slug_admin);

            //estado de la licencia
            self::$license_state = $this->LicenseState();
            
            //determino el item default del menu y contenido
            global $mgs_elementor_config;
            foreach($mgs_elementor_config['menu'] as $key=>$item ){
                if( isset($item['default']) && $item['default'] ){
                    $this->default_menu_item = $key;
                }
            }

            add_action('plugins_loaded', [$this, 'init']);
            
            //admin
            add_action('admin_menu', [$this, 'mgs_elementor_admin_menu']);
            add_action('admin_enqueue_scripts', [$this, 'mgs_elementor_admin_enqueue_scripts']);
            add_action('wp_ajax_mgs_elementor_save_settings', [$this, 'mgs_elementor_save_settings_callback']);
            add_action('wp_ajax_mgs_elementor_registro_licensia', [$this, 'mgs_elementor_registro_licensia_callback']);


            
            //registro widget slider
            if( get_option('mgs-elementor-addon-state-slider')=='on' ){
                add_action('elementor/widgets/register', [$this, 'register_slider_widget']);
                add_action('wp_enqueue_scripts', [$this, 'js_css_widget_slider']);
            }
            
            //registro widget posts
            if( get_option('mgs-elementor-addon-state-posts')=='on' ){
                add_action('elementor/widgets/register', [$this, 'register_posts_widget']);
                add_action('wp_enqueue_scripts', [$this, 'js_css_widget_posts']);
            }
        }

        public function init(){
            if( !version_compare(ELEMENTOR_VERSION, MGS_ELEMENTOR_VERSION, '>=') ){
                add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
                return;
            }
            if( version_compare(PHP_VERSION, MGS_ELEMENTOR_PHP_VERSION, '<') ){
                add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
                return;
            }

            if( !self::$license_state ){
                add_action('admin_notices', [$this, 'admin_notice_not_license']);
                return;
            }



            //carga todos los complementos que no son widgets nuevos
        }

        public function register_posts_widget($widgets_manager){
            require_once('widget-posts.php');
            $widgets_manager->register( new \Elementor_MGS_Posts_Widget() );
        }
        public function js_css_widget_posts(){
            wp_enqueue_style('mgs_posts_css', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/css/posts.css');
            
        }

        public function register_slider_widget($widgets_manager){
            require_once('widget-slider.php');
            $widgets_manager->register( new \Elementor_MGS_Slider_Widget() );
        }
        public function js_css_widget_slider(){
            wp_enqueue_style('mgs_owl.carousel.min_css', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/css/owl.carousel.min.css');
            wp_enqueue_style('mgs_owl.theme.default.min_css', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/css/owl.theme.default.min.css');
            wp_enqueue_style('mgs_slider_css', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/css/slider.css');
            
            wp_enqueue_script('mgs_owl.carousel.min_js', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/js/owl.carousel.min.js', ['jquery'], '', true);
        }

        public function mgs_elementor_admin_menu(){
            add_options_page(
				'MGS Elementor',
				'MGS Elementor',
				'manage_options',
				$this->slug_admin,
				[$this, 'page']
			);
        }

        public function page(){
            global $mgs_elementor_config;
            ?>
                <div class="mgs-elementor-admin">
                    <header class="plg-head">
                        <div class="logo">
                            <img src="<?php echo MGS_ELEMENTOR_PLUGIN_DIR_URL?>/assets/imgs/logo-mgs.svg" alt="Marcelo Scenna">
                            <h1><?php echo MGS_ELEMENTOR_NAME?></h1>
                        </div>
                        <div class="menu">
                            <?php $this->_menu()?>
                        </div>
                        <div class="info">
                            <div class="ver"><?php echo MGS_ELEMENTOR_PLUGIN_VERSION?></div>
                            <div class="estado">Demo</div>
                        </div>
                    </header>
                    <content class="home">
                        <?php
                            if( !isset($_GET['sec']) ){//defaul
                                call_user_func( [$this, $mgs_elementor_config['menu'][$this->default_menu_item]['callback']] );
                            }else{ 
                                //otras secciones
                                call_user_func( [$this, $mgs_elementor_config['menu'][$_GET['sec']]['callback']] );
                            }
                        ?>
                    </content>
                    <footer>
                        <p>&copy; Marcelo Gabriel Scenna - <?php echo date('Y')?></p>
                    </footer>
                </div>
            <?php
        }

        private function _config(){
            global $mgs_elementor_config;
            $this->_title(__('Configuración', 'mgs_elementor'));
            ?>
            <div class="panel">
                <div class="inner-grid">
                    <?php
                    foreach( $mgs_elementor_config['addons'] as $key=>$addon ){
                        $class = '';
                        $show_switch = true;
                        if( isset($addon['just_for']) && $addon['just_for']!='' ){
                            if( call_user_func([$this, $addon['just_for']]) ){
                                $class .= $addon['just_for'].' ';
                            }else{
                                $class .= $addon['just_for'].'_not not_compatible ';
                                $show_switch = false;
                            }

                        }
                        $class .= ( get_option('mgs-elementor-addon-state-'.$key)=='on' ) ? 'active' : '';
                        
                        $class .= ( !self::$license_state ) ? 'disabled ' : '';
                    ?>
                    <div class="addon <?php echo $class?>" id="addon-mgs-elementor-addon-state-<?php echo $key?>">
                        <div class="overflow"></div>
                        <div class="cont">
                            <div class="title">
                                <div class="ico"><?php echo $addon['ico']?></div>
                                <div class="text"><?php echo $addon['title']?></div>
                            </div>
                            <div class="desc"><?php echo $addon['desc']?></div>
                            <div class="required"><?php echo $addon['required']?></div>
                            
                        </div>
                        <div class="acc">
                            <?php if( $show_switch ){?>
                            <div class="mgs-switch">
                                <input type="checkbox" name="mgs-elementor-addon-state-<?php echo $key?>" id="mgs-elementor-addon-state-<?php echo $key?>" data-key="mgs-elementor-addon-state-<?php echo $key?>" <?php echo ( get_option('mgs-elementor-addon-state-'.$key)=='on' ) ? 'checked' : ''?>>
                                <label for="mgs-elementor-addon-state-<?php echo $key?>"></label>
                                <span class="alert" id="mgs-elementor-addon-state-<?php echo $key?>-alert"></span>
                            </div>
                            <?php }?>
                        </div>
                        <?php if( isset($addon['config']) ){?>
                        <div class="menu_options">
                            <div class="menu">
                                <a href="#" class="addon_menu_config" data-target="addon-mgs-elementor-addon-state-<?php echo $key?>"><?php echo $addon['config']['ico'].$addon['config']['title']?></a>
                            </div>
                            <div class="cont">
                                <?php call_user_func([$addon['config']['callback'], 'config'])?>
                            </div>
                        </div>

                        <?php }?>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        private function _registro(){
            $this->_title(__('Registro', 'mgs_elementor'));
            $licence_state_icon = ( self::$license_state ) ? '<span class="material-symbols-outlined">verified</span>' : '<span class="material-symbols-outlined">warning</span>';
            $licence_state_class = ( self::$license_state ) ? 'license_success' : 'license_error';
            ?>
                <div class="panel w50">
                    <div class="inner">
                        <div class="registro">
                            <div class="desc">
                                <h2><div class="license_state_icon <?php echo $licence_state_class?>"><?php echo $licence_state_icon?></div><?php _e('Active su licencia', 'mgs_elementor')?></h2>
                                <p>
                                    <?php echo sprintf(
                                    __('Ingrese su codigo de licencia para poder utilizar MGS Elementor. Si no posee uno puede solicitarlo <a href="%s">aquí</a>.', 'mgs_elementor'),
                                    'mailto:scenna.marcelo@gmail.com');
                                    ?>
                                </p>
                            </div>
                            <div class="field">
                                <label for="mgs_elementor_license_code" id="mgs_elementor_license_code-alert" class="alert"></label>
                                <div class="mgs_password">
                                    <input type="password" class="mgs_elementor_input" name="mgs_elementor_license_code" id="mgs_elementor_license_code" <?php echo ( self::$license_state ) ? 'readonly disabled' : ''?> value="<?php echo ( self::$license_state ) ? get_option('RH5DLM7WHR')[0] : ''?>">
                                    <button data-parent="mgs_elementor_license_code" class="mgs_elementor_cmd mgs_password_acc" <?php echo ( self::$license_state ) ? 'disabled' : ''?>><span class="material-symbols-outlined">visibility</span></button>
                                </div>
                            </div>
                            <div class="action">
                                <button class="mgs_elementor_cmd activar_cmd"><?php echo ( self::$license_state ) ? __('Desactivar', 'mgs_elementor') : __('Activar', 'mgs_elementor')?></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        }
        private function _title($title){
            ?>
            <div class="title-wrap">
                <h2><?php echo $title?></h2>
            </div>
            <div id="notice_wrapper"></div>
            <?php
        }
        private function _menu(){
            global $mgs_elementor_config;
            $out = '<ul>';
            foreach($mgs_elementor_config['menu'] as $key=>$item ){
                if( isset($item['default']) && $item['default'] ){
                    $url = $this->base_url;
                    $class = ( !isset($_GET['sec']) ) ? 'active' : '';
                }else{
                    $url = $this->base_url.'&sec='.$key;
                    $class = ( isset($_GET['sec']) && $_GET['sec']==$key ) ? 'active' : '';
                }
                $out .= '
                    <li>
                        <a href="'.$url.'" class="'.$class.'">
                            <div class="ico"><span class="material-symbols-outlined">'.$item['ico'].'</span></div>
                            <div class="label">'.$item['label'].'</div>
                        </a>
                    </li>
                ';
            }
            $out .= '</ul>';
            echo $out;
        }
        public function mgs_elementor_admin_enqueue_scripts($hook){
            if( $hook=='settings_page_'.$this->slug_admin ){
                wp_enqueue_script('jquery');
                wp_register_script('mgs-elementor-admin-js', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/js/admin.js', ['jquery']);
   				wp_localize_script('mgs-elementor-admin-js', 'mgs_elementor_ajax', [
                    'ajaxurl'   => admin_url('admin-ajax.php'),
                    'setting_OK'    => __('Opción cambiada', 'mgs_elementor'),
                    'setting_KO'    => __('Error, no se pudo cambiar esta opción', 'mgs_elementor'),
                    'license_OK'    => __('Licencia registrada con exito.', 'mgs_elementor'),
                    'license_KO'    => __('No se pudo registrar su licencia', 'mgs_elementor'),
                    'activar_cmd_text_activar'      => __('Activar', 'mgs_elementor'),
                    'activar_cmd_text_deactivar'    => __('Desactivar', 'mgs_elementor'),
                ]);        
   				wp_enqueue_script('mgs-elementor-admin-js');

                wp_enqueue_style('mgs-elementor-admin-css', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/css/admin.css');
                wp_enqueue_style('mgs-elementor-material-icons-css', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0');
            }

        }

        public function mgs_elementor_save_settings_callback(){
            echo update_option($_POST['key'], $_POST['value']);
            die();
        }


        public function admin_notice_minimum_elementor_version() {
            deactivate_plugins(plugin_basename(MGS_ELEMENTOR_BASENAME));
            echo '<div class="notice notice-warning is-dismissible"><p><strong>'.MGS_ELEMENTOR_NAME.'</strong> requiere <strong>Elementor</strong> version '.MGS_ELEMENTOR_VERSION.' o mayor.</p></div>';
        }
        public function admin_notice_minimum_php_version(){
            deactivate_plugins(plugin_basename(MGS_ELEMENTOR_BASENAME));
            echo '<div class="notice notice-warning is-dismissible"><p><strong>'.MGS_ELEMENTOR_NAME.'</strong> requiere <strong>PHP</strong> '.MGS_ELEMENTOR_PHP_VERSION.' o mayor.</p></div>';
        }
        public function admin_notice_not_license(){
            $screen = get_current_screen();
            if( $screen->id!='settings_page_mgs_elementor_admin' ){
                echo '<div class="notice notice-warning"><p><strong>'.MGS_ELEMENTOR_NAME.'</strong> requiere que ingrese su licensia. <a href="'.$this->base_url.'&sec=registro" class="button">Configurar</a></p></div>';
            }elseif( $screen->id=='settings_page_mgs_elementor_admin' && !isset($_GET['sec']) ){
                echo '<div class="mgs-notice notice notice-warning"><p><strong>'.MGS_ELEMENTOR_NAME.'</strong> requiere que ingrese su licensia.</p></div>';
            }
        }

        private function LicenseState(){
            if( get_option('RH5DLM7WHR')=='' ){
                return false;
            }else{
                $_op = get_option('RH5DLM7WHR');
                if( $_op[0]==base64_decode($_op[1]) ){
                    return true;
                }
            }
            return false;
        }

        public function mgs_elementor_registro_licensia_callback(){
            $url = 'https://www.marceloscenna.com.ar/wp-json/mgs_licenses/license/';
            $args = [
                'headers'   =>  [
                    'Content-Type'  => 'application/json'
                ],
                'body'      => json_encode([
                    'action'    => 'mgs_lm_validate_key_w46vw43b5',
                    'key'       => $_POST['pub_key'],
                    'producto'  => MGS_ELEMENTOR_SLUG,
                    'dominio'   => $_SERVER['SERVER_NAME']
                ])
            ];
            if( $response = wp_remote_post($url, $args) ){
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if( $body['code']==='ok' ){
                    if( base64_decode($body['aditional']['verification'])==$_POST['pub_key'] ){
                        update_option('RH5DLM7WHR', [$_POST['pub_key'],$body['aditional']['verification'], time()]);
                        echo 'ok';
                    }else{
                        delete_option('RH5DLM7WHR');
                        echo 'error';
                    }
                }else{
                    echo 'error';
                }
            }else{
                echo 'error';
            }
            die();
        }

        public function is_elementor(){
			// Check if Elementor installed and activated
			if( !did_action('elementor/loaded') ){
				return false;
			}

			// Check for required Elementor version
			if( !version_compare(ELEMENTOR_VERSION, MGS_ELEMENTOR_VERSION, '>=') ){
				return false;
			}
			
			return true;
		}

        public static function reset_defaul_settings(){
            delete_option('mgs-elementor-addon-state-css');
        }

    }
    new MGS_Elementor_AddOns();
}