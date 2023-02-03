jQuery(document).ready(function(){
    
    check_feactured_image();
    gapi.load('client');

    jQuery('#cmd_mgs_dummy_content_generate').on('click', function(e){
        e.preventDefault();
        
        var cant = jQuery('#mgs_dummy_content_cant').val();
        var parrafos = jQuery('#mgs_dummy_content_parrafos').val();
        var parrafos_zise = jQuery('.parrafos_zise:checkbox:checked').map(function(){
            return this.value;
        }).get();
        var parrafos_content = jQuery('.parrafos_content:checkbox:checked').map(function(){
            return this.value;
        }).get();

        var imagen = ( jQuery('#mgs_dummy_content_feactured_img').is(':checked') ) ? 'on' : 'off';
        var search = ( jQuery('#mgs_dummy_content_img_search').val()!='' ) ? jQuery('#mgs_dummy_content_img_search').val() : 'Natural forest';

        //console.log(cant, parrafos, parrafos_zise, parrafos_content)
        //console.log(imagen, search)

        if( cant<1 ){
            jQuery('.alert-resume').html(mgs_dummy_content.cant_error);
            return false;
        }
        if( parrafos<1 ){
            jQuery('.alert-resume').html(mgs_dummy_content.parrafos_error);
            return false;
        }

        //categorias
        var tipo_cat = jQuery('input.cat_action:checked').val()
        var cat_cant_new = 0
        var cat_asignar = jQuery('#mgs_dummy_content_cat_asignar').val()
        var cat_asignar_random = ( jQuery('#mgs_dummy_content_cat_random').is(':checked') ) ? 'on' : 'off';
        
        if( tipo_cat=='news' ){
            cat_cant_new = jQuery('#mgs_dummy_content_cat_cant_new').val()
        }

        jQuery('.mgs_elementor_dummy_content_run .mgs-elementor-field-wrapper input').prop('disabled', 'disabled')
        jQuery('#cmd_mgs_dummy_content_generate').prop('disabled', 'disabled').html('<span class="material-symbols-outlined">hourglass_empty</span>').addClass('spined')
        jQuery('#cmd_mgs_dummy_content_delete').prop('disabled', 'disabled')
        jQuery.ajax({
            type		: "post",
            url			: mgs_elementor_ajax.ajaxurl,
            data		: {
                action		: 'mgs_dummy_content_generate',
                cant 		        : cant,
                parrafos            : parrafos,
                parrafos_zise       : parrafos_zise,
                parrafos_content    : parrafos_content,
                imagen              : imagen,
                search              : search,
                tipo_cat            : tipo_cat,
                cat_cant_new        : cat_cant_new,
                cat_asignar         : cat_asignar,
                cat_asignar_random  : cat_asignar_random
            }
        }).done(function(data){
            response = jQuery.parseJSON(data)
            console.log(response)
            jQuery('.mgs_elementor_dummy_content_run .mgs-elementor-field-wrapper input').prop('disabled', '')
            jQuery('#cmd_mgs_dummy_content_generate').prop('disabled', '').html('<span class="material-symbols-outlined">play_arrow</span>').removeClass('spined')
            jQuery('#cmd_mgs_dummy_content_delete').prop('disabled', '')
            if( response['items'].length > 0 ){
                jQuery('#cmd_mgs_dummy_content_delete').html('<span class="material-symbols-outlined">delete</span> '+mgs_dummy_content.delete_text+' ('+response['items'].length+')')
                jQuery('.delete_wrapper').removeClass('hidden')

                response['items'].forEach(element => {
                    if( imagen=='on' ){
                        if( element['img']['status']['code']=='200' ){
                            img_status = '<span class="p4 material-symbols-outlined">image</span>';
                        }else{
                            img_status = '<span class="p5 material-symbols-outlined">image_not_supported</span>';
                        }
                    }else{
                        img_status = '';
                    }
                    jQuery('.alert-resume').append('<p><span class="p1">Entrada:</span><span class="p2"><i>'+element['title']+'</i></span><span class="material-symbols-outlined p3">check_circle</span>'+img_status+'</p>')
                });
            }
        });
    })

    jQuery('#cmd_mgs_dummy_content_delete').on('click', function(e){
        e.preventDefault();
        jQuery('.mgs_elementor_dummy_content_run .mgs-elementor-field-wrapper input').prop('disabled', 'disabled')
        jQuery('#cmd_mgs_dummy_content_generate').prop('disabled', 'disabled')
        jQuery('#cmd_mgs_dummy_content_delete').prop('disabled', 'disabled').html('<span class="material-symbols-outlined">hourglass_empty</span> '+mgs_dummy_content.deleting_text).addClass('spined')
        jQuery.ajax({
            type		: "post",
            url			: mgs_elementor_ajax.ajaxurl,
            data		: {
                action		: 'mgs_dummy_content_delete',
            }
        }).done(function(data){
            response = jQuery.parseJSON(data)
            console.log(response)
            jQuery('.mgs_elementor_dummy_content_run .mgs-elementor-field-wrapper input').prop('disabled', '')
            jQuery('#cmd_mgs_dummy_content_generate').prop('disabled', '')
            jQuery('#cmd_mgs_dummy_content_delete').prop('disabled', '').html('<span class="material-symbols-outlined">delete</span> '+mgs_dummy_content.delete_text).removeClass('spined')
            jQuery('.delete_wrapper').addClass('hidden')
        });
    })

    jQuery('#cmd_mgs_dummy_content_save_api').on('click', function(e){
        var key = jQuery('#mgs_dummy_content_clave_api').val();

        jQuery('.dummy_content_api_messages').html('').attr('class', 'dummy_content_api_messages');
        if( key!='' ){
            gapi.client.setApiKey(key);
            return gapi.client.load("https://content.googleapis.com/discovery/v1/apis/customsearch/v1/rest").then(
                function(){
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
                    
                },
                function(err){
                    jQuery('.dummy_content_api_messages').html('<strong>Error</strong> al verificar su clave API, Google Cloud respondio: <code>'+err.error.message+'</code> solucione este problema para continuar.').addClass('error')
                }
            );
        }else{
            jQuery('.dummy_content_api_messages').html('<strong>Error</strong> La clave API no puede estar en blanco.').addClass('notice notice-error')
        }
    })
    jQuery('#cmd_mgs_dummy_content_delete_api').on('click', function(e){
        jQuery('.dummy_content_api_messages').html('').attr('class', 'dummy_content_api_messages');
        jQuery.ajax({
            type		: "post",
            url			: mgs_elementor_ajax.ajaxurl,
            data		: {
                action		: 'mgs_elementor_save_settings',
                key 		: 'mgs-elementor-addon-dummy_content_google_api_key',
                value       : ''
            }
        }).done(function(data){
            if( data==1 ){
                jQuery('.dummy_content_api_messages').html('Clave API eliminada con exito. Debera actualizar esta pagina [F5] para que los cambios se apliquen correctamente.').addClass('notice notice-success')
                jQuery('#mgs_dummy_content_clave_api').val('')
            }else{
                jQuery('.dummy_content_api_messages').html('No se pudo completar la operación, intente nuevamente.').addClass('notice notice-warning')
            }
        });
    })

    jQuery('#mgs_dummy_content_feactured_img').on('change', function(){
        check_feactured_image();
    })

    jQuery('input.cat_action').on('change', function(){
        var tipo_cat = jQuery(this).val()
        //console.log(tipo_cat)
        if( tipo_cat=='news' ){
            jQuery('#new_cat_div').removeClass('hidden')
        }else if( tipo_cat=='actuales' ){
            jQuery('#new_cat_div').addClass('hidden')
        }
    })

    function check_feactured_image(){
        if( jQuery('#mgs_dummy_content_feactured_img').is(':checked') ){
            jQuery('.mgs_dummy_content_img_search_wrapper').fadeIn();
        }else{
            jQuery('.mgs_dummy_content_img_search_wrapper').fadeOut();
        }
    }
})