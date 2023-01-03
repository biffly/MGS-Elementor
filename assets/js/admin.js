jQuery(document).ready(function(){
    //activa o desctiva addon
    jQuery('.mgs-switch input[type="checkbox"]').on('change', function(e){
        e.preventDefault();
        var setting_key = jQuery(this).data('key');
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
        
        if( jQuery('#'+target).hasClass('active') ){
            console.log(target, 'active')
            if( jQuery('#'+target+' .menu_options').hasClass('active') ){
                jQuery('#'+target+' .menu_options').removeClass('active')
            }else{
                jQuery('#'+target+' .menu_options').addClass('active')
            }
        }
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
})