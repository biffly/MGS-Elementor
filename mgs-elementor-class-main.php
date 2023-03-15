<?php

if( !defined('ABSPATH') ){ exit; }

if( !class_exists('MGS_Elementor_AddOns') ){
	class MGS_Elementor_AddOns{
        public $slug_admin;
        public $base_url;
        public $default_menu_item;
        public static $license_state = false;
        public static $elementor_state = false;
        public static $post_rate_tbl;
        public static $post_rate_comments_tbl;

        function __construct(){
            global $wpdb;

            $this->slug_admin = 'mgs_elementor_admin';
            $this->base_url = admin_url('options-general.php?page='.$this->slug_admin);

            //estado de la licencia
            self::$license_state = $this->LicenseState();

            //if( is_plugin_active('elementor/elementor.php') ){
            if( did_action('elementor/loaded') ){
                self::$elementor_state = true;
            }
            
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


            //registro widget post-rate
            if( get_option('mgs-elementor-addon-state-post-rate')=='on' ){
                self::$post_rate_tbl = $wpdb->prefix."mgs_post_rate";
                self::$post_rate_comments_tbl = $wpdb->prefix."mgs_post_rate_comments";
                //verifico si existe las tablas
                if( !$this->is_post_rate_tables_exist() ){
                    $this->create_table_post_rates();
                }
                add_action('elementor/widgets/register', [$this, 'register_post_rate']);
                add_action('wp_enqueue_scripts', [$this, 'js_css_post_rate']);
                add_action('wp_ajax_mgs_post_rating_set', [$this, 'mgs_post_rating_set']);
                add_action('wp_ajax_nopriv_mgs_post_rating_set', [$this,'mgs_post_rating_set']);
                add_action('wp_ajax_mgs_post_rating_set_comment', [$this, 'mgs_post_rating_set_comment']);
                add_action('wp_ajax_nopriv_mgs_post_rating_set_comment', [$this,'mgs_post_rating_set_comment']);
                if( is_admin() ){
                    require_once('mgs-post-rate-admin.php');
                }
            }

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

            //registro widget color fill animation css
            if( get_option('mgs-elementor-addon-state-colorfillani-css')=='on' ){
                add_action('elementor/widgets/register', [$this, 'register_color_fill_animation_css']);
                add_action('wp_enqueue_scripts', [$this, 'js_css_color_fill_animation_css']);
                add_action('elementor/editor/before_enqueue_scripts', [$this, 'js_css_color_fill_animation_css_admin']);
                //remove limite de tamaño de imagenes
                add_filter('big_image_size_threshold', '__return_false');
            }

            //registro widget the conternt
            if( get_option('mgs-elementor-addon-state-widget-content')=='on' ){
                add_action('elementor/widgets/register', [$this, 'register_widget_the_content']);
            }
        }

        public function init(){
            if( self::$elementor_state ){
                if( !version_compare(ELEMENTOR_VERSION, MGS_ELEMENTOR_VERSION, '>=') ){
                    add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
                    return;
                }
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

        public function register_post_rate($widgets_manager){
            require_once('widget-post-rate.php');
            $widgets_manager->register( new \Elementor_MGS_Post_Rate_Widget() );
        }
        public function js_css_post_rate(){
            $vars = [];
            $vars['ajaxurl'] = admin_url('admin-ajax.php');

            wp_enqueue_style('mgs_elementor_post_rate_css', plugins_url('assets/css/post-rate.css', __FILE__));

            wp_enqueue_script('mgs_elementor_post_rate_js_cookies', plugins_url('assets/js/js.cookie.min.js', __FILE__), ['jquery']);
            wp_enqueue_script('mgs_elementor_post_rate_js_validate', plugins_url('assets/js/jquery.validate.min.js', __FILE__), ['jquery']);
            wp_enqueue_script('mgs_elementor_post_rate_main', plugins_url('assets/js/post-rate.js', __FILE__), ['jquery', 'mgs_elementor_post_rate_js_validate']);
            wp_localize_script('mgs_elementor_post_rate_main', 'mgs_post_ratings_vars', $vars);
        }
        public function mgs_post_rating_set(){
            $current_value = get_post_meta($_POST['post'], 'mgs_post_rating_value', true);
            $current_veces = get_post_meta($_POST['post'], 'mgs_post_rating_veces', true);

            if( $current_value=='' ) $current_value = 0;
            if( $current_veces=='' ) $current_veces = 0;

            $new_value = $current_value + $_POST['value'];
            $new_veces = $current_veces + 1;

            update_post_meta($_POST['post'], 'mgs_post_rating_value', $new_value);
            update_post_meta($_POST['post'], 'mgs_post_rating_veces', $new_veces);

            //guardo submit en BBDD
            global $wpdb;
            $current_datetime_gmt = current_time('mysql', true);
		    $current_datetime = get_date_from_gmt($current_datetime_gmt);
            $wpdb->insert(
                self::$post_rate_tbl,
                [
                    'post_id'           => $_POST['post'],
                    'submit_id'         => $_POST['submit_id'],
                    'post_rate'         => $_POST['value'],
                    'created_at_gmt'    => $current_datetime_gmt,
			        'created_at'        => $current_datetime,
                ]
            );
            echo json_encode([
                'value'     => $new_value,
                'veces'     => $new_veces,
                'voted'     => $_POST['value'],
                'submit_id' => $_POST['submit_id'],
                'bd_id'     => $wpdb->insert_id,
                'state'     => 'ok'
            ]);
            die();
        }
        public function mgs_post_rating_set_comment(){
            global $wpdb;
            parse_str($_POST['post'], $post);
            
            //busco valoracion
            $t1 = self::$post_rate_tbl;
            $t2 = self::$post_rate_comments_tbl;
            $val_id = $wpdb->get_var("SELECT id FROM $t1 WHERE submit_id='".$post['submit_id']."'");
            if( $val_id ){
                //busco en tabla comments
                $comment_id = $wpdb->get_var("SELECT id FROM $t2 WHERE submit_id='".$post['submit_id']."'");
                if( $comment_id ){
                    echo json_encode([
                        'state'     => 'already'
                    ]);
                    die();
                }else{
                    //al parecer todo esta ok, cargo comment
                    foreach( $post['form_fields'] as $k=>$v ){
                        $wpdb->insert(
                            $t2,
                            [
                                'submit_id' => $post['submit_id'],
                                'key'       => $k,
                                'value'     => $v
                            ]
                        );
                    }

                    $this->send_post_rate_email($post);

                    echo json_encode([
                        'state'     => 'ok',
                        'post'      => $post
                    ]);
                    die();
                }
            }else{
                echo json_encode([
                    'state'     => 'no_post_rate'
                ]);
                die();
            }
            die();
        }
        public function is_post_rate_tables_exist(){
            global $wpdb;
            $t1 = self::$post_rate_tbl;
            $t2 = self::$post_rate_comments_tbl;
            if( $wpdb->get_var("SHOW TABLES LIKE '$t1'")!=$t1 || $wpdb->get_var("SHOW TABLES LIKE '$t2'")!=$t2 ){
                return false;
            }else{
                return true;
            }
        }
        public function create_table_post_rates(){
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $t1 = self::$post_rate_tbl;
            $t2 = self::$post_rate_comments_tbl;
            $e_post_rate_tbl = "CREATE TABLE $t1 (
                id bigint(20) unsigned auto_increment primary key,
                post_id bigint(20) unsigned not null,
                submit_id varchar(500) not null,
                post_rate varchar(20) not null,
                created_at_gmt datetime not null,
                updated_at_gmt datetime not null,
                created_at datetime not null,
                updated_at datetime not null
            ) {$charset_collate};";

            $e_post_rate_comments_tbl = "CREATE TABLE $t2 (
                id bigint(20) unsigned auto_increment primary key,
                submit_id varchar(500) not null,
                `key` varchar(60) null,
                value longtext null
            ) {$charset_collate};";

            require_once ABSPATH.'wp-admin/includes/upgrade.php';
            dbDelta($e_post_rate_tbl.$e_post_rate_comments_tbl);
        }
        public function send_post_rate_email($post){
            if( isset($post['send_email_to']) && isset($post['send_email_subget']) && isset($post['submit_id']) ){
                if( $post['send_email_to']!='' ){
                    $to = $post['send_email_to'];
                }else{
                    return;
                }
                if( $post['send_email_subget']!='' ){
                    $asunto = $post['send_email_subget'];
                }else{
                    return;
                }
                if( $post['submit_id']!='' ){
                    global $wpdb;
                    $t1 = self::$post_rate_tbl;
                    $t2 = self::$post_rate_comments_tbl;
                    $resu_1 = $wpdb->get_row("SELECT * FROM $t1 WHERE submit_id='".$post['submit_id']."'");
                    $resu_2 = $wpdb->get_results("SELECT * FROM $t2 WHERE submit_id='".$post['submit_id']."'");
                    $out = '';
                    $out = '<strong>'.__('Valoración', 'mgs_elementor').':</strong> '.$resu_1->post_rate.'<br/>';
                    $out .= '<strong>'.__('Fecha', 'mgs_elementor').':</strong> '.$resu_1->created_at_gmt.'<br/>';
                    $out .= '<strong>'.__('Lugar', 'mgs_elementor').':</strong> '.get_the_title($resu_1->post_id).' (<a href="'.get_the_permalink($resu_1->post_id).'" target="_balnk">'.__('Ver', 'mgs_elementor').'</a>) (<a href="'.get_admin_url().'options-general.php?page=mgs_elementor_admin&sec=post-rate&addon=post-rate&view='.$resu_1->post_id.'#'.$post['submit_id'].'" target="_balnk">'.__('Admin', 'mgs_elementor').'</a>)<br/>';
                    foreach( $resu_2 as $f ){
                        $out .= '<strong>'.$f->key.':</strong> '.$f->value.'<br/>';
                    }
                    $out = '<strong>ID:</strong> '.$post['submit_id'].'<br/>';
                    if( isset($post['enviar_mail_add_text']) && $post['enviar_mail_add_text']=='yes' ){
                        $out .= '<br/><br/>'.$post['enviar_mail_text'];
                    }
                    $out = '<br/><br/><hr><br/>Enviado en forma automática por MGS Elementor Post Rate.';
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    wp_mail($to, $asunto, $out, $headers);
                    return;
                }else{
                    return;
                }



            }else{
                return;
            }
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

        public function register_color_fill_animation_css($widgets_manager){
            require_once('widget-color-fill-animation-css.php');
            $widgets_manager->register( new \Elementor_MGS_Color_Fill_Animation_CSS() );
        }
        public function js_css_color_fill_animation_css(){
            wp_enqueue_style('color_fill_animation_css', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/css/color_fill_animation_css.css');
        }
        public function js_css_color_fill_animation_css_admin(){
            wp_enqueue_style('color_fill_animation_css_admin', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/css/color_fill_animation_css_admin.css');
        }

        public function register_widget_the_content($widgets_manager){
            require_once('widget-content.php');
            $widgets_manager->register( new \Elementor_MGS_The_Content() );
        }

        public function mgs_elementor_admin_menu(){
            add_options_page(
				'MGS Elementor',
				'MGS Elementor',
				'manage_options',
				$this->slug_admin,
				[$this, 'page_v2']
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

        public function page_v2(){
            global $mgs_elementor_config;
            ?>
            <div class="mgs-elementor-admin-v2">
                <header class="mgs-elementor-admin-head">
                    <div class="bar-left">
                        <div class="logo">
                            <img src="<?php echo MGS_ELEMENTOR_PLUGIN_DIR_URL?>/assets/imgs/logo-mgs.svg" alt="Marcelo Scenna">
                            <h1><?php echo MGS_ELEMENTOR_NAME?></h1>
                        </div>
                        <div class="menu">
                            <?php $this->_menu()?>
                        </div>
                    </div>
                    <div class="bar-right">
                        <div class="info">
                            <div class="ver"><?php echo MGS_ELEMENTOR_PLUGIN_VERSION?></div>
                            <div class="estado">Demo</div>
                        </div>
                        <div class="close">
                            <a href="<?php echo get_dashboard_url()?>" title="<?php _e('Cerrar', 'mgs_elementor')?>"><span class="material-symbols-outlined">close</span></a>
                        </div>
                    </div>
                </header>
                <content>
                    <?php
                        if( !isset($_GET['sec']) ){//defaul
                            call_user_func( [$this, $mgs_elementor_config['menu'][$this->default_menu_item]['callback']] );
                        }else{ 
                            //otras secciones
                            if( isset($_GET['addon']) && get_option('mgs-elementor-addon-state-'.$_GET['addon'])=='on' ){
                                if( isset($mgs_elementor_config['addons'][$_GET['addon']]['menu'][$_GET['sec']]['class']) ){

                                    call_user_func([
                                        $mgs_elementor_config['addons'][$_GET['addon']]['menu'][$_GET['sec']]['class'],
                                        $mgs_elementor_config['addons'][$_GET['addon']]['menu'][$_GET['sec']]['callback']
                                    ]);
                                }else{
                                    call_user_func( [$this, $mgs_elementor_config['menu'][$_GET['sec']]['callback']] );
                                }
                            }else{
                                call_user_func( [$this, $mgs_elementor_config['menu'][$_GET['sec']]['callback']] );
                            }
                        }
                    ?>
                </content>
                <footer>
                    <p>&copy; Marcelo Gabriel Scenna - <?php echo date('Y')?></p>
                </footer>
            </div>
            <?php
        }
        private function _config_v2(){
            global $mgs_elementor_config;
            ?>
            <div class="addons-container">
                <div class="inner">
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
                            $class .= ( get_option('mgs-elementor-addon-state-'.$key)=='on' ) ? 'active ' : '';
                            $class .= ( !self::$license_state ) ? 'disabled ' : '';
                            $class .= ( isset($addon['config']) || isset($addon['run']) ) ? 'w_menu ' : '';
                    ?>
                            <div class="addon <?php echo $class?>" id="addon-mgs-elementor-addon-state-<?php echo $key?>">
                                <div class="overflow"></div>
                                <div class="acc">
                                    <div class="mgs-switch">
                                        <input type="checkbox" name="mgs-elementor-addon-state-<?php echo $key?>" id="mgs-elementor-addon-state-<?php echo $key?>" data-key="mgs-elementor-addon-state-<?php echo $key?>" <?php echo ( get_option('mgs-elementor-addon-state-'.$key)=='on' ) ? 'checked' : ''?> <?php echo ( !$show_switch ) ? 'disabled' : '';?>>
                                        <label for="mgs-elementor-addon-state-<?php echo $key?>"></label>
                                        <span class="alert" id="mgs-elementor-addon-state-<?php echo $key?>-alert"></span>
                                    </div>
                                </div>
                                <div class="cont">
                                    <div class="title">
                                        <div class="ico"><?php echo $addon['ico']?></div>
                                        <div class="text"><?php echo $addon['title']?></div>
                                    </div>
                                    <div class="desc"><?php echo $addon['desc']?></div>
                                    <div class="required"><?php echo $addon['required']?></div>
                                </div>
                                <?php if( isset($addon['config']) || isset($addon['run']) ){?>
                                <div class="menu_options">
                                    <div class="menu">
                                    <?php if( isset($addon['config']) ){?>
                                        <a href="#" class="addon_menu_config_v2" data-target="mgs-elementor-addon-<?php echo $key?>-config" data-parent="addon-mgs-elementor-addon-state-<?php echo $key?>" title="<?php echo $addon['config']['title']?>"><?php echo $addon['config']['ico']?></a>
                                    <?php }?>
                                    <?php if( isset($addon['run']) ){?>
                                        <a href="#" class="addon_menu_config_v2" data-target="mgs-elementor-addon-<?php echo $key?>-run" data-parent="addon-mgs-elementor-addon-state-<?php echo $key?>" title="<?php echo $addon['run']['title']?>"><?php echo $addon['run']['ico']?></a>
                                    <?php }?>
                                    </div>
                                </div>
                                <?php }?>
                            </div>
                            <?php if( isset($addon['config']) && get_option('mgs-elementor-addon-state-'.$key)=='on' ){?>
                            <div class="addon-modal addon-config" id="mgs-elementor-addon-<?php echo $key?>-config">
                                <div class="inner">
                                    <?php call_user_func([$addon['config']['callback'], 'config'])?>
                                </div>
                            </div>
                            <?php }?>
                            <?php if( isset($addon['run']) && get_option('mgs-elementor-addon-state-'.$key)=='on' ){?>
                            <div class="addon-modal addon-run" id="mgs-elementor-addon-<?php echo $key?>-run">
                                <div class="inner">
                                    <?php call_user_func([$addon['config']['callback'], 'run'])?>
                                </div>
                            </div>
                            <?php }?>
                    <?php
                        }
                    ?>
                </div>
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
                        $class .= ( get_option('mgs-elementor-addon-state-'.$key)=='on' ) ? 'active ' : '';
                        $class .= ( !self::$license_state ) ? 'disabled ' : '';
                        $class .= ( isset($addon['config']) || isset($addon['run']) ) ? 'w_menu ' : '';
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
                        <?php if( isset($addon['config']) || isset($addon['run']) ){?>
                        <div class="menu_options">
                            <div class="menu">
                                <?php if( isset($addon['config']) ){?>
                                    <a href="#" class="addon_menu_config" data-target="mgs-elementor-addon-<?php echo $key?>-config" data-parent="addon-mgs-elementor-addon-state-<?php echo $key?>"><?php echo $addon['config']['ico'].$addon['config']['title']?></a>
                                <?php }?>
                                <?php if( isset($addon['run']) ){?>
                                    <a href="#" class="addon_menu_config" data-target="mgs-elementor-addon-<?php echo $key?>-run" data-parent="addon-mgs-elementor-addon-state-<?php echo $key?>"><?php echo $addon['run']['ico'].$addon['run']['title']?></a>
                                <?php }?>
                            </div>
                            <div class="cont">
                                <?php if( isset($addon['config']) ){?>
                                    <div class="tab" id="mgs-elementor-addon-<?php echo $key?>-config"><?php call_user_func([$addon['config']['callback'], 'config'])?></div>
                                <?php }?>
                                <?php if( isset($addon['run']) ){?>
                                    <div class="tab" id="mgs-elementor-addon-<?php echo $key?>-run"><?php call_user_func([$addon['config']['callback'], 'run'])?></div>
                                <?php }?>
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
            //menus por default
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
            //menus de AddOns
            foreach( $mgs_elementor_config['addons'] as $id_addon=>$addon ){
                if( isset($addon['menu']) && get_option('mgs-elementor-addon-state-'.$id_addon)=='on' ){
                    foreach( $addon['menu'] as $key=>$item ){
                        $url = $this->base_url.'&sec='.$key.'&addon='.$id_addon;
                        $class = ( isset($_GET['sec']) && $_GET['sec']==$key ) ? 'active' : '';
                        $out .= '
                            <li>
                                <a href="'.$url.'" class="'.$class.'">
                                    <div class="ico"><span class="material-symbols-outlined">'.$item['ico'].'</span></div>
                                    <div class="label">'.$item['label'].'</div>
                                </a>
                            </li>
                        ';
                    }
                }
            }

            $out .= '</ul>';
            echo $out;
        }
        public function mgs_elementor_admin_enqueue_scripts($hook){
            //if( $hook=='settings_page_'.$this->slug_admin ){
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
                wp_enqueue_style('mgs-elementor-admin_v2-css', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/css/admin_v2.css');
                wp_enqueue_style('mgs-elementor-material-icons-css', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0');
            //}

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