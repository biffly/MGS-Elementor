<?php
if( !class_exists('MGS_Elementor_WP_Mail') ){
    class MGS_Elementor_WP_Mail{
        public function __construct(){
            if( get_option('mgs-elementor-addon-state-wp-mail')=='on' && is_admin() ){
                add_action('admin_enqueue_scripts', [$this, 'mgs_wp_mail_admin_enqueue_scripts']);
                add_action('wp_ajax_mgs_wp_mail_test_envio', [$this, 'mgs_wp_mail_test_envio_callback']);
            }

            if( get_option('mgs-elementor-addon-state-wp-mail')=='on' ){
                if( get_option('mgs_wp_mail-name_from')!='' ) add_filter('wp_mail_from_name', function($original_email_address){
                    return get_option('mgs_wp_mail-name_from');
                });
                if( get_option('mgs_wp_mail-email_from')!='' ) add_filter('wp_mail_from', function($original_email_from){
                    return get_option('mgs_wp_mail-email_from');
                });

                add_action('phpmailer_init', 'mgs_wp_mail_send_smtp_email');
            }
        }

        public function mgs_wp_mail_send_smtp_email($phpmailer){
            if( get_option('mgs_wp_mail-smtp')=='on' ){
                $phpmailer->isSMTP();
                $phpmailer->Host        = get_option('mgs_wp_mail-smtp_host');
                $phpmailer->SMTPAuth    = true;
                $phpmailer->Port        = get_option('mgs_wp_mail-smpt_port');
                $phpmailer->Username    = get_option('mgs_wp_mail-smtp_user');
                $phpmailer->Password    = get_option('mgs_wp_mail-smpt_pass');
                $phpmailer->SMTPSecure  = 'tls';
                $phpmailer->From        = get_option('mgs_wp_mail-email_from');
                $phpmailer->FromName    = get_option('mgs_wp_mail-name_from');
                $phpmailer->SMTPDebug   = 2;
            }
        }

        public function mgs_wp_mail_test_envio_callback(){
            $mail = wp_mail('scenna.marcelo@gmail.com', 'MGS Dev - test mail config', 'Esta es una prueba de configuración');
            echo $mail;
            die();
        }

        public static function config(){
            if( get_option('mgs-elementor-addon-state-wp-mail')=='on' && is_admin() ){
            ?>
                <h2 class="mb-0"><?php _e('Remitente', 'mgs_elementor')?><small><?php _e('Estas opciones afectaran al remitente que utiliza wordpress para enviar correos electrónicos.', 'mgs_elementor')?></small></h2>
                <div class="mgs-elementor-fake-form">
                    <div class="mgs-elementor-field-wrapper">
                        <label for="mgs_wp_mail-name_from"><?php _e('Nombre', 'mgs_elementor')?></label>
                        <input type="text" class="mgs_elementor_input" name="mgs_wp_mail-name_from" id="mgs_wp_mail-name_from" value="<?php echo get_option('mgs_wp_mail-name_from')?>">
                    </div>
                    <div class="mgs-elementor-field-wrapper">
                        <label for="mgs_wp_mail-email_from"><?php _e('Correo de envio', 'mgs_elementor')?></label>
                        <input type="email" class="mgs_elementor_input" name="email_from-email_from" id="mgs_wp_mail-email_from" value="<?php echo get_option('mgs_wp_mail-email_from')?>">
                    </div>
                </div>
                <div class="mgs-elementor-fake-form">
                    <div class="mgs-elementor-field-wrapper aling-bottom checkbox">
                        <div class="mgs-switch">
                            <input type="checkbox" name="mgs_wp_mail-smpt" id="mgs_wp_mail-smpt" <?php echo ( get_option('mgs_wp_mail-smpt')=='on' ) ? 'checked' : ''?>>
                            <label for="mgs_wp_mail-smpt"></label>
                        </div>
                        <label for="mgs_wp_mail-smpt"> <?php _e('Utilizar SMPT', 'mgs_elementor')?></label>
                    </div>
                </div>
                <div class="mgs-elementor-fake-form mgs-elementor-fake-form-smtp hidden">
                    <div class="mgs-elementor-field-wrapper">
                        <label for="mgs_wp_mail-smtp_host"><?php _e('SMTP server', 'mgs_elementor')?></label>
                        <input type="text" class="mgs_elementor_input" name="mgs_wp_mail-smtp_host" id="mgs_wp_mail-smtp_host" value="<?php echo get_option('mgs_wp_mail-smtp_host')?>">
                    </div>
                    <div class="mgs-elementor-field-wrapper">
                        <label for="mgs_wp_mail-smpt_port"><?php _e('SMTP Puerto', 'mgs_elementor')?></label>
                        <input type="text" class="mgs_elementor_input" name="email_from-smpt_port" id="mgs_wp_mail-smpt_port" value="<?php echo get_option('mgs_wp_mail-smpt_port')?>">
                    </div>
                </div>
                <div class="mgs-elementor-fake-form mgs-elementor-fake-form-smtp hidden">
                    <div class="mgs-elementor-field-wrapper">
                        <label for="mgs_wp_mail-smtp_user"><?php _e('SMTP usuario', 'mgs_elementor')?></label>
                        <input type="text" class="mgs_elementor_input" name="mgs_wp_mail-smtp_user" id="mgs_wp_mail-smtp_user" value="<?php echo get_option('mgs_wp_mail-smtp_user')?>">
                    </div>
                    <div class="mgs-elementor-field-wrapper">
                        <label for="mgs_wp_mail-smpt_pass"><?php _e('SMTP Clave', 'mgs_elementor')?></label>
                        <input type="text" class="mgs_elementor_input" name="email_from-smpt_pass" id="mgs_wp_mail-smpt_pass" value="<?php echo get_option('mgs_wp_mail-smpt_pass')?>">
                    </div>
                </div>

                <div class="mgs-elementor-fake-form">
                    <div class="mgs-elementor-field-wrapper">
                        <button type="button" id="mgs_wp_mail-test_config" class="mgs_elementor_cmd">Probar</button>  
                        <button type="button" id="mgs_wp_mail-save_config" class="mgs_elementor_cmd">Guardar</button>
                    </div>
                </div>
                <div class="mgs-elementor-fake-form">
                    <div class="mgs-elementor-field-wrapper aling-bottom">
                    </div>
                </div>
            <?php
            }else{
                echo '<h2 class="error">Opciones no disponibles</h2>';
            }
        }

        public function mgs_wp_mail_admin_enqueue_scripts(){
            wp_enqueue_script('jquery');
            wp_register_script('mgs-wp-mail-admin-js', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/js/wp-mail-admin.js', ['jquery']);
            wp_enqueue_script('mgs-wp-mail-admin-js');
        }
    }
}
new MGS_Elementor_WP_Mail();