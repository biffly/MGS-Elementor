<?php
if( !class_exists('MGS_LoginReplace') ){
    class MGS_LoginReplace{

        public function __construct(){
            if( get_option('mgs-elementor-addon-state-login_replace')=='on' ){
                add_action('login_enqueue_scripts', function(){
                    wp_register_script('mgs_elementor_login_replace_js', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/js/mgs-login-replace.js', ['jquery']);
                    $_array_options = [
                        'mgs_login_replace_hide_wp_logo'    => get_option('mgs_login_replace_hide_wp_logo'),
                        'mgs_login_replace_custom_logo'    => get_option('mgs_login_replace_custom_logo'),
                        'mgs_login_replace_custom_logo_link'    => get_site_url(),
                        'mgs_login_replace_custom_logo_text'    => get_bloginfo('name'),
                        'mgs_login_replace_customs_labels'    => get_option('mgs_login_replace_customs_labels'),
                        'mgs_login_replace_customs_labels_user'    => get_option('mgs_login_replace_customs_labels_user'),
                        'mgs_login_replace_customs_labels_pass'    => get_option('mgs_login_replace_customs_labels_pass'),
                        'mgs_login_replace_hide_labels'    => get_option('mgs_login_replace_hide_labels'),
                        'mgs_login_replace_hide_chech_rememberme'    => get_option('mgs_login_replace_hide_chech_rememberme'),
                        'mgs_login_replace_hide_reset_pass'    => get_option('mgs_login_replace_hide_reset_pass'),
                        'mgs_login_replace_hide_back_site'    => get_option('mgs_login_replace_hide_back_site'),
                        'mgs_login_replace_hide_lang_change'    => get_option('mgs_login_replace_hide_lang_change')
                    ];
                    wp_localize_script('mgs_elementor_login_replace_js', 'mgs_elementor_login_replace_vars', $_array_options);
                    wp_enqueue_script('mgs_elementor_login_replace_js');

                });
            }
        }

        public static function config(){
            if( get_option('mgs-elementor-addon-state-login_replace')=='on' && is_admin() ){
                wp_enqueue_media();
                ?>
                <div class="mgs_elementor_config mgs_elementor_login_replace_config">
                    <p><?php _e('Configure las opciones de como desea que se vea su pantalla de login.', 'mgs_elementor')?></p>
                    <div class="mgs-elementor-fake-form mb-0">
                        <div class="mgs-elementor-field-wrapper check_options">
                            <label for="mgs_login_replace_hide_wp_logo">
                                <?php _e('Ocultar logo de Wordpress', 'mgs_elementor')?>
                            </label>
                            <div class="mgs-switch">
                                <input type="checkbox" name="mgs_login_replace_hide_wp_logo" id="mgs_login_replace_hide_wp_logo" data-key="mgs_login_replace_hide_wp_logo" data-dependent="mgs_login_replace_custom_logo_wrapper" <?php echo ( get_option('mgs_login_replace_hide_wp_logo')=='on' ) ? 'checked' : ''?>>
                                <label for="mgs_login_replace_hide_wp_logo"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mgs-elementor-fake-form mt-0 mb-0">
                        <div class="mgs-elementor-field-wrapper mgs_login_replace_custom_logo_wrapper hidden">
                            <p class="desc"><?php _e('Si lo desea puede utilizar un logotipo personalizado', 'mgs_elementor')?></p>
                            <button class="fake_upload_area" id="mgs_login_replace_custom_logo_upload">Seleccione una imagen</button>
                            <input id="mgs_login_replace_custom_logo" type="text" name="mgs_login_replace_custom_logo" value="<?php echo get_option('mgs_login_replace_custom_logo')?>" /> 
                        </div>
                    </div>

                    <div class="mgs-elementor-fake-form mb-0 mb-0">
                        <div class="mgs-elementor-field-wrapper check_options">
                            <label for="mgs_login_replace_customs_labels">
                                <?php _e('Utilizar etiquetas personalizadas?', 'mgs_elementor')?>
                            </label>
                            <div class="mgs-switch">
                                <input type="checkbox" name="mgs_login_replace_customs_labels" id="mgs_login_replace_customs_labels" data-key="mgs_login_replace_customs_labels" data-dependent="mgs_login_replace_customs_labels_wrapper" <?php echo ( get_option('mgs_login_replace_customs_labels')=='on' ) ? 'checked' : ''?>>
                                <label for="mgs_login_replace_customs_labels"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mgs-elementor-fake-form mt-0 mb-0">
                        <div class="mgs-elementor-field-wrapper mgs_login_replace_customs_labels_wrapper hidden">
                            <label for="mgs_login_replace_customs_labels_user"><?php _e('Campo usuario', 'mgs_elementor')?></label>
                            <input type="text" class="mgs_elementor_input" name="mgs_login_replace_customs_labels_user" id="mgs_login_replace_customs_labels_user" value="<?php echo get_option('mgs_login_replace_customs_labels_user')?>"> 
                            <p class="desc"><?php _e('Etiqueta para el campo de nombre de usuario o correo electrónico.', 'mgs_elementor')?></p>
                        </div>
                    </div>
                    <div class="mgs-elementor-fake-form mt-0 mb-0">
                        <div class="mgs-elementor-field-wrapper mgs_login_replace_customs_labels_wrapper hidden">
                            <label for="mgs_login_replace_customs_labels_pass"><?php _e('Campo clave', 'mgs_elementor')?></label>
                            <input type="text" class="mgs_elementor_input" name="mgs_login_replace_customs_labels_pass" id="mgs_login_replace_customs_labels_pass" value="<?php echo get_option('mgs_login_replace_customs_labels_pass')?>"> 
                            <p class="desc"><?php _e('Etiqueta para el campo de Contraseña.', 'mgs_elementor')?></p>
                        </div>
                    </div>
                    <div class="mgs-elementor-fake-form mb-0 mb-0">
                        <div class="mgs-elementor-field-wrapper mgs_login_replace_customs_labels_wrapper check_options">
                            <label for="mgs_login_replace_hide_labels">
                                <?php _e('Ocultar labels? solo se mostraran los placeholders', 'mgs_elementor')?>
                            </label>
                            <div class="mgs-switch">
                                <input type="checkbox" name="mgs_login_replace_hide_labels" id="mgs_login_replace_hide_labels" data-key="mgs_login_replace_hide_labels" <?php echo ( get_option('mgs_login_replace_hide_labels')=='on' ) ? 'checked' : ''?>>
                                <label for="mgs_login_replace_hide_labels"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mgs-elementor-fake-form mt-0 mb-0">
                        <div class="mgs-elementor-field-wrapper check_options">
                            <label for="mgs_login_replace_hide_chech_rememberme">
                                <?php _e('Ocultar "Recordarme"', 'mgs_elementor')?>
                            </label>
                            <div class="mgs-switch">
                                <input type="checkbox" name="mgs_login_replace_hide_chech_rememberme" id="mgs_login_replace_hide_chech_rememberme" data-key="mgs_login_replace_hide_chech_rememberme" <?php echo ( get_option('mgs_login_replace_hide_chech_rememberme')=='on' ) ? 'checked' : ''?>>
                                <label for="mgs_login_replace_hide_chech_rememberme"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mgs-elementor-fake-form mt-0 mb-0">
                        <div class="mgs-elementor-field-wrapper check_options">
                            <label for="mgs_login_replace_hide_reset_pass">
                                <?php _e('Ocultar la opción "¿Olvidaste tu contraseña?"', 'mgs_elementor')?>
                            </label>
                            <div class="mgs-switch">
                                <input type="checkbox" name="mgs_login_replace_hide_reset_pass" id="mgs_login_replace_hide_reset_pass" data-key="mgs_login_replace_hide_reset_pass" <?php echo ( get_option('mgs_login_replace_hide_reset_pass')=='on' ) ? 'checked' : ''?>>
                                <label for="mgs_login_replace_hide_reset_pass"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mgs-elementor-fake-form mt-0 mb-0">
                        <div class="mgs-elementor-field-wrapper check_options">
                            <label for="mgs_login_replace_hide_back_site">
                                <?php _e('Ocultar la opción de regresar el sitio?', 'mgs_elementor')?>
                            </label>
                            <div class="mgs-switch">
                                <input type="checkbox" name="mgs_login_replace_hide_back_site" id="mgs_login_replace_hide_back_site" data-key="mgs_login_replace_hide_back_site" <?php echo ( get_option('mgs_login_replace_hide_back_site')=='on' ) ? 'checked' : ''?>>
                                <label for="mgs_login_replace_hide_back_site"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mgs-elementor-fake-form mt-0 mb-0">
                        <div class="mgs-elementor-field-wrapper check_options">
                            <label for="mgs_login_replace_hide_lang_change">
                                <?php _e('Ocultar la opción cambiar de idioma?', 'mgs_elementor')?>
                            </label>
                            <div class="mgs-switch">
                                <input type="checkbox" name="mgs_login_replace_hide_lang_change" id="mgs_login_replace_hide_lang_change" data-key="mgs_login_replace_hide_lang_change" <?php echo ( get_option('mgs_login_replace_hide_lang_change')=='on' ) ? 'checked' : ''?>>
                                <label for="mgs_login_replace_hide_lang_change"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
    }
}
new MGS_LoginReplace();