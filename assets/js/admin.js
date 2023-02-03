jQuery(document).ready(function(){
    
    var custom_uploader;

    //revisa si alguna opcion dependent debe estar activa
    jQuery('.mgs-switch input[type="checkbox"]').each(function(i, obj) {
        if( jQuery(this).data('dependent')!='' && jQuery(this).is(':checked') ){
            jQuery('.'+jQuery(this).data('dependent')).fadeIn(1);
        }
    });

    //activa o desctiva addon
    jQuery('.mgs-switch input[type="checkbox"]').on('change', function(e){
        e.preventDefault();
        var setting_key = jQuery(this).data('key');
        var setting_dependent = jQuery(this).data('dependent');
        var cheched = '';
        if( jQuery(this).is(':checked') ){
            cheched = 'on';
        }else{
            cheched = 'off';
        }

        jQuery('#'+setting_key+'-alert').fadeOut(100, function(){
            jQuery(this).removeClass('alert-ok').removeClass('alert-ko').html('');
        });

        jQuery.ajax({
            type		: "post",
            url			: mgs_elementor_ajax.ajaxurl,
            data		: {
                action		: 'mgs_elementor_save_settings',
                key 		: setting_key,
                value       : cheched
            }
        }).done(function(data){
            if( data==1 ){
                //jQuery('#'+setting_key).prop('checked', cheched);
                jQuery('#'+setting_key+'-alert').html(mgs_elementor_ajax.setting_OK).addClass('alert-ok').fadeIn();
                if( cheched=='on' ){
                    jQuery('#addon-'+setting_key).addClass('active');
                }else{
                    jQuery('#addon-'+setting_key).removeClass('active');
                }

                //check if dependent
                if( setting_dependent!=''){
                    if( cheched=='on' ){
                        jQuery('.'+setting_dependent).fadeIn(100);
                    }else{
                        jQuery('.'+setting_dependent).fadeOut(100);
                    }
                }

                setTimeout(function(){
                    jQuery('#'+setting_key+'-alert').fadeOut(500, function(){
                        jQuery(this).removeClass('alert-ok').html('');
                    });
                }, 5000);
            }else{
                jQuery('#'+setting_key).prop('checked', !cheched);
                jQuery('#'+setting_key+'-alert').html(mgs_elementor_ajax.setting_KO).addClass('alert-ko').fadeIn();
            }
        });
    })

    //verifico licensia
    jQuery('.activar_cmd').on('click', function(e){
        e.preventDefault();
        jQuery('.activar_cmd').prop('disabled', true);
        jQuery('#mgs_elementor_license_code').prop('disabled', true);
        jQuery('.mgs_password_acc').prop('disabled', true);
        jQuery('#mgs_elementor_license_code-alert').removeClass('alert_success').removeClass('alert_error').html('');
        jQuery('.license_state_icon').removeClass('license_success').removeClass('license_error').html('');

        jQuery.ajax({
            type		: "post",
            url			: mgs_elementor_ajax.ajaxurl,
            data		: {
                action		: 'mgs_elementor_registro_licensia',
                pub_key 	: jQuery('#mgs_elementor_license_code').val()
            }
        }).done(function(data){
            if( data=='ok' ){
                jQuery('.activar_cmd').prop('disabled', false).html(mgs_elementor_ajax.activar_cmd_text_deactivar);
                jQuery('#mgs_elementor_license_code').prop('disabled', true);
                jQuery('.mgs_password_acc').prop('disabled', true);
                jQuery('#mgs_elementor_license_code-alert').removeClass('alert_error').addClass('alert_success').html(mgs_elementor_ajax.license_OK);
                jQuery('.license_state_icon').removeClass('license_error').addClass('license_success').html('<span class="material-symbols-outlined">verified</span>');
            }else{
                jQuery('.activar_cmd').prop('disabled', false);
                jQuery('#mgs_elementor_license_code').prop('disabled', false);
                jQuery('.mgs_password_acc').prop('disabled', false);
                jQuery('#mgs_elementor_license_code-alert').addClass('alert_error').html(mgs_elementor_ajax.license_KO);
                jQuery('.license_state_icon').addClass('license_error').html('<span class="material-symbols-outlined">warning</span>');
            }
        });
    })

    //toggle addon opciones
    jQuery('.addon_menu_config').on('click', function(e){
        e.preventDefault()
        var target = jQuery(this).data('target');
        var parent = jQuery(this).data('parent');
        console.log(target)
        //if( jQuery('#'+parent).hasClass('active') ){
            /*
            if( jQuery('#'+parent+' .menu_options').hasClass('active') ){
                jQuery('#'+parent+' .menu_options').removeClass('active')
                jQuery('#'+target).removeClass('active')
            }else{
                jQuery('#'+parent+' .menu_options').addClass('active')
                jQuery('#'+target).addClass('active')
            }
            */
            if( jQuery('#'+target).hasClass('active') ){
                jQuery('#'+target).removeClass('active')
                jQuery(this).removeClass('active')
            }else{
                jQuery('.cont .tab.active').removeClass('active')
                jQuery('.menu_options .menu a.addon_menu_config').removeClass('active')

                jQuery('#'+target).addClass('active')    
                jQuery(this).addClass('active')
            }
        //}
    })

    //muevo todas las admin notice
    jQuery('.notice').appendTo('#notice_wrapper').ready(function(){
        jQuery('.mgs-elementor-admin content').addClass('alerted');
    });

    //ver clave
    jQuery('.mgs_password_acc').on('click', function(){
        var parent = jQuery(this).data('parent');
        if( jQuery('#'+parent).attr('type')=='password' ){
            jQuery(this).html('<span class="material-symbols-outlined">visibility_off</span>');
            jQuery('#'+parent).attr('type', 'text')
        }else{
            jQuery(this).html('<span class="material-symbols-outlined">visibility</span>');
            jQuery('#'+parent).attr('type', 'password')
        }
    })

    //upload custom logo
    jQuery('#mgs_login_replace_custom_logo_upload').on('click', function(e){
        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if( custom_uploader ){
            custom_uploader.open();
            return;
        }

        custom_uploader = wp.media.frames.file_frame = wp.media({
            title       : 'Choose Image',
            button      : {
                text        : 'Choose Image'
            },
            multiple    : false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function(){
            //console.log(custom_uploader.state().get('selection').toJSON());
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery('#mgs_login_replace_custom_logo').val(attachment.url);
            SaveSettings('mgs_login_replace_custom_logo', attachment.url)
        });

        //Open the uploader dialog
        custom_uploader.open();
    })

    jQuery('#mgs_login_replace_customs_labels_user').on('focusout', function(){
        SaveSettings('mgs_login_replace_customs_labels_user', jQuery(this).val())
    })
    jQuery('#mgs_login_replace_customs_labels_pass').on('focusout', function(){
        SaveSettings('mgs_login_replace_customs_labels_pass', jQuery(this).val())
    })


    function SaveSettings(setting_key, setting_value){
        jQuery.ajax({
            type		: "post",
            url			: mgs_elementor_ajax.ajaxurl,
            data		: {
                action		: 'mgs_elementor_save_settings',
                key 		: setting_key,
                value       : setting_value
            }
        }).done(function(data){
            if( data==1 ){
            }else{
            }
        });
    }
})