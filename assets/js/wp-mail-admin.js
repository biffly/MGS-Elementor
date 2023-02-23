jQuery(document).ready(function(){
    mgs_wp_mail_check_wp_mail_use_smtp();

    jQuery('input#mgs_wp_mail-smpt').on('change', function(){
        //console.log('change?')
        mgs_wp_mail_check_wp_mail_use_smtp();
    })

    jQuery('#mgs_wp_mail-save_config').on('click', function(){
        if( mgs_wp_mail_validate_config() ){
            var data = mgs_wp_mail_get_config();
            console.log(data);
            
            jQuery.each(data, function(k,d){
                jQuery('#'+k).removeClass('errro').removeClass('saved');
                jQuery.ajax({
                    type		: "post",
                    url			: mgs_elementor_ajax.ajaxurl,
                    data		: {
                        action		: 'mgs_elementor_save_settings',
                        key 		: k,
                        value       : d
                    }
                }).done(function(r){
                    if( r==1 ){
                        jQuery('#'+k).addClass('saved')
                    }
                });
            })
        }
    })

    jQuery('#mgs_wp_mail-test_config').on('click', function(){
        if( mgs_wp_mail_validate_config() ){
            jQuery.ajax({
                type		: "post",
                url			: mgs_elementor_ajax.ajaxurl,
                data		: {
                    action		: 'mgs_wp_mail_test_envio',
                }
            }).done(function(r){
                console.log(r)
            });
        }
    })

    function mgs_wp_mail_validate_config(){
        //console.log('Validando configuracion')
        var name_from = ( jQuery('#mgs_wp_mail-name_from').val()!='' ) ? jQuery('#mgs_wp_mail-name_from').val() : ''; 
        var email_from = ( jQuery('#mgs_wp_mail-email_from').val()!='' ) ? jQuery('#mgs_wp_mail-email_from').val() : ''; 
        var smpt = ( jQuery('#mgs_wp_mail-smpt').is(':checked') ) ? 'on' : ''; 
        var smtp_host = ( jQuery('#mgs_wp_mail-smtp_host').val()!='' ) ? jQuery('#mgs_wp_mail-smtp_host').val() : ''; 
        var smpt_port = ( jQuery('#mgs_wp_mail-smpt_port').val()!='' ) ? jQuery('#mgs_wp_mail-smpt_port').val() : ''; 
        var smtp_user = ( jQuery('#mgs_wp_mail-smtp_user').val()!='' ) ? jQuery('#mgs_wp_mail-smtp_user').val() : ''; 
        var smpt_pass = ( jQuery('#mgs_wp_mail-smpt_pass').val()!='' ) ? jQuery('#mgs_wp_mail-smpt_pass').val() : ''; 
        var flag_ok = true;
        
        if( smpt=='on' ){
            jQuery('#mgs_wp_mail-smtp_host, #mgs_wp_mail-smpt_port, #mgs_wp_mail-smtp_user, #mgs_wp_mail-smpt_pass').removeClass('error')
            if( smtp_host=='' ){
                jQuery('#mgs_wp_mail-smtp_host').addClass('error').focus();
                //console.log('smtp_host', 'error');
                flag_ok = false;
            }
            if( smpt_port=='' ){
                jQuery('#mgs_wp_mail-smpt_port').addClass('error').focus();
                //console.log('smpt_port', 'error');
                flag_ok = false;
            }
            if( smtp_user=='' ){
                jQuery('#mgs_wp_mail-smtp_user').addClass('error').focus();
                //console.log('smtp_user', 'error');
                flag_ok = false;
            }
            if( smpt_pass=='' ){
                jQuery('#mgs_wp_mail-smpt_pass').addClass('error').focus();
                //console.log('smpt_pass', 'error');
                flag_ok = false;
            }
        }

        return flag_ok;
    }

    function mgs_wp_mail_get_config(){
        var name_from = ( jQuery('#mgs_wp_mail-name_from').val()!='' ) ? jQuery('#mgs_wp_mail-name_from').val() : ''; 
        var email_from = ( jQuery('#mgs_wp_mail-email_from').val()!='' ) ? jQuery('#mgs_wp_mail-email_from').val() : ''; 
        var smpt = ( jQuery('#mgs_wp_mail-smpt').is(':checked') ) ? 'on' : ''; 
        var smtp_host = ( jQuery('#mgs_wp_mail-smtp_host').val()!='' ) ? jQuery('#mgs_wp_mail-smtp_host').val() : ''; 
        var smpt_port = ( jQuery('#mgs_wp_mail-smpt_port').val()!='' ) ? jQuery('#mgs_wp_mail-smpt_port').val() : ''; 
        var smtp_user = ( jQuery('#mgs_wp_mail-smtp_user').val()!='' ) ? jQuery('#mgs_wp_mail-smtp_user').val() : ''; 
        var smpt_pass = ( jQuery('#mgs_wp_mail-smpt_pass').val()!='' ) ? jQuery('#mgs_wp_mail-smpt_pass').val() : ''; 

        return {
            'mgs_wp_mail-name_from'     : name_from,
            'mgs_wp_mail-email_from'    : email_from,
            'mgs_wp_mail-smpt'          : smpt,
            'mgs_wp_mail-smtp_host'     : smtp_host,
            'mgs_wp_mail-smpt_port'     : smpt_port,
            'mgs_wp_mail-smtp_user'     : smtp_user,
            'mgs_wp_mail-smpt_pass'     : smpt_pass
        };
    }

    function mgs_wp_mail_check_wp_mail_use_smtp(){
        //console.log('check??')
        if( jQuery('input#mgs_wp_mail-smpt').is(':checked') ){
            //jQuery('.mgs-elementor-fake-form-smtp').fadeIn();
            jQuery('.mgs-elementor-fake-form-smtp').css("display", "flex").hide().fadeIn();
        }else{
            jQuery('.mgs-elementor-fake-form-smtp').fadeOut();
        }
    }
})
/*
jQuery.ajax({
    type		: "post",
    url			: mgs_elementor_ajax.ajaxurl,
    data		: {
        action		: 'mgs_elementor_save_settings',
        key 		: 'mgs-elementor-addon-dummy_content_google_api_key',
        value       : key
    }
}).done(function(data){
    if( data==1 ){
        jQuery('.dummy_content_api_messages').html('Clave API verificada y guardada con exito. Debera actualizar esta pagina [F5] para que los cambios se apliquen correctamente.').addClass('notice notice-success')
    }else{
        jQuery('.dummy_content_api_messages').html('No se pudo completar la operación, intente nuevamente.').addClass('notice notice-warning')
    }
});
*/