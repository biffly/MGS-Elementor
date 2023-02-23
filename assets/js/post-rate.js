jQuery(function($){
    //console.log(mgs_post_ratings_vars)
    //console.log(mgs_post_ratings_vars_msj_form)

    jQuery('.mgs_elementor_post_rate').each(function(i, obj){
        var post = jQuery(this).data('post');
        var div_id = jQuery(this).attr('id');
        var cookie_name = 'mgs_post_ratings_'+post+'_value';
        var cookie_value = Cookies.get(cookie_name);
        if( typeof post!='undefined' && post!='' ){
            if( typeof cookie_value!='undefined' && cookie_value!='' ){
                jQuery(this).removeClass('no_voted').removeClass('disabled').addClass('voted');
                jQuery(this).data('value', cookie_value)
                set_estrellas(div_id, cookie_value)
            }else{
                jQuery(this).addClass('no_voted');
                cookie_value = '';
            }
        }else{
            jQuery(this).addClass('disabled');
        }

        //console.log('MGS Post Ratings', cookie_value, post, mgs_post_ratings_vars);
    });

    jQuery('.estrella').on('click', function(e){
        e.preventDefault();
        var post = jQuery(this).data('post');
        var valor = jQuery(this).data('value');
        var parent = jQuery(this).data('parent');
        var submit_id = jQuery(this).data('submit_id');
        var cookie_name = 'mgs_post_ratings_'+post+'_value';
        var data = {
            'action'    : 'mgs_post_rating_set',
            'post'      : post,
            'submit_id' : submit_id,
            'value'     : valor
        };

        if( jQuery('#'+parent).hasClass('voted') ){
            console.log('ya votaste, lo lamento');
            return false;
        }
        
        jQuery('#'+parent+' .elementor-message').fadeOut(400, function(){
            jQuery(this).remove();
        });
        //console.log('data', data)
        jQuery.post(mgs_post_ratings_vars.ajaxurl, data, function(response){
            var resp = JSON.parse(response);
            //console.log('ajax', resp)
            if( resp.state=='ok' ){
                Cookies.set(cookie_name, resp.voted, { expires: 365 });
                set_estrellas(parent, resp.voted);
                jQuery('#'+parent+' input[name="post_rate"]').val(resp.voted);
                //muestro el aviso que corresponda, segun tenga o no form
                if( jQuery('#'+parent).hasClass('no_form') ){
                    jQuery('#'+parent).append('<div class="elementor-message elementor-message-success" role="alert" style="display:none;">'+mgs_post_ratings_vars_msj_form.POST_RATE_SUCCESS+'</div>');
                    jQuery('#'+parent+' .elementor-message').fadeIn();
                    jQuery('#'+parent).addClass('voted').removeClass('no_voted').data('value', resp.value);
                }else if( jQuery('#'+parent).hasClass('has_form') ){
                    jQuery('#'+parent+' .estrellas-wrapper').append('<div class="elementor-message elementor-message-success" role="alert" style="display:none;">'+mgs_post_ratings_vars_msj_form.FORM_OPTION+'</div>');
                    jQuery('#'+parent+' .elementor-message').fadeIn();
                }

            }else{
                jQuery('#'+parent).append('<div class="elementor-message elementor-message-danger" role="alert"  style="display:none;">'+mgs_post_ratings_vars_msj_form.POST_RATE_ERROR+'</div>');
                jQuery('#'+parent+' .elementor-message').fadeIn();
            }
        }).fail(function(){
            jQuery('#'+parent).append('<div class="elementor-message elementor-message-danger" role="alert"  style="display:none;">'+mgs_post_ratings_vars_msj_form.POST_RATE_ERROR+'</div>');
            jQuery('#'+parent+' .elementor-message').fadeIn();
        })
    })

    jQuery('.mgs_elementor_post_rate.no_voted .estrella').hover(function(){
            jQuery(this).addClass('estrella_hover').prevAll('.estrella').addClass('estrella_hover');
        }, function(){
            jQuery(this).removeClass('estrella_hover').prevAll('.estrella').removeClass('estrella_hover');
    });

    function set_estrellas(ele, v){
        //console.log('set estrella', ele, v)
        jQuery('#'+ele+' .estrella').removeClass('active');
        if( v==1 ){
            jQuery('#'+ele+' .estrella1').addClass('active');
        }else if( v==2 ){
            jQuery('#'+ele+' .estrella1').addClass('active');
            jQuery('#'+ele+' .estrella2').addClass('active');
        }else if( v==3 ){
            jQuery('#'+ele+' .estrella1').addClass('active');
            jQuery('#'+ele+' .estrella2').addClass('active');
            jQuery('#'+ele+' .estrella3').addClass('active');
        }else if( v==4 ){
            jQuery('#'+ele+' .estrella1').addClass('active');
            jQuery('#'+ele+' .estrella2').addClass('active');
            jQuery('#'+ele+' .estrella3').addClass('active');
            jQuery('#'+ele+' .estrella4').addClass('active');
        }else if( v==5 ){
            jQuery('#'+ele+' .estrella1').addClass('active');
            jQuery('#'+ele+' .estrella2').addClass('active');
            jQuery('#'+ele+' .estrella3').addClass('active');
            jQuery('#'+ele+' .estrella4').addClass('active');
            jQuery('#'+ele+' .estrella5').addClass('active');
        }
    }
    
    /*jQuery('.mgs_elementor_post_rate_form_button').on('click', function(e){
        e.preventDefault();

    })*/

    jQuery.extend(jQuery.validator.messages, {
        required    : mgs_post_ratings_vars_msj_form.FIELD_REQUIRED,
        email       : mgs_post_ratings_vars_msj_form.FORM_MAIL,
        url         : mgs_post_ratings_vars_msj_form.FORM_URL,
        number      : mgs_post_ratings_vars_msj_form.FORM_NUMBER,
        /*remote: "Please fix this field.",
        url: "Please enter a valid URL.",
        date: "Please enter a valid date.",
        dateISO: "Please enter a valid date (ISO).",
        number: "Please enter a valid number.",
        digits: "Please enter only digits.",
        creditcard: "Please enter a valid credit card number.",
        equalTo: "Please enter the same value again.",
        accept: "Please enter a value with a valid extension.",
        maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
        minlength: jQuery.validator.format("Please enter at least {0} characters."),
        rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
        range: jQuery.validator.format("Please enter a value between {0} and {1}."),
        max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
        min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")*/
    });

    jQuery('.mgs_elementor_post_rate').validate({
        submitHandler   : function(form){
            var form_id = jQuery(form).attr('id');
            if( jQuery('#'+form_id+' input[name="post_rate"]').val()!='' ){
                var data = {
                    'action'    : 'mgs_post_rating_set_comment',
                    'post'      : jQuery('#'+form_id).serialize()
                };
                jQuery('#'+form_id+' .elementor-message').fadeOut(400, function(){
                    jQuery(this).remove();
                });
                jQuery.post(mgs_post_ratings_vars.ajaxurl, data, function(response){
                    var resp = JSON.parse(response);
                    if( resp.state=='ok' ){
                        jQuery('#'+form_id).append('<div class="elementor-message elementor-message-success" role="alert" style="display:none;">'+mgs_post_ratings_vars_msj_form.FORM_SUCCESS+'</div>');
                        jQuery('#'+form_id+' .elementor-message').fadeIn();
                        jQuery('#'+form_id+' .mgs_elementor_post_rate_form').fadeOut();
                    }else if( resp.state=='no_post_rate'){
                        jQuery('#'+form_id).append('<div class="elementor-message elementor-message-danger" role="alert"  style="display:none;">'+mgs_post_ratings_vars_msj_form.INVALID_FORM+'</div>');
                        jQuery('#'+form_id+' .elementor-message').fadeIn();
                    }else if( resp.state=='already' ){
                        jQuery('#'+form_id).append('<div class="elementor-message elementor-message-danger" role="alert"  style="display:none;">Esta operación ya fue realizada, no puede votar mas de una ves.</div>');
                        jQuery('#'+form_id+' .elementor-message').fadeIn();
                    }
                    console.log(resp)
                    
                }).fail(function(){
                    jQuery('#'+parent).append('<div class="elementor-message elementor-message-danger" role="alert"  style="display:none;">'+mgs_post_ratings_vars_msj_form.POST_RATE_ERROR+'</div>');
                    jQuery('#'+parent+' .elementor-message').fadeIn();
                })
            }else{
                jQuery('#'+form_id).append('<div class="elementor-message elementor-message-danger" role="alert"  style="display:none;">'+mgs_post_ratings_vars_msj_form.INVALID_FORM+'</div>');
                jQuery('#'+form_id+' .elementor-message').fadeIn();
            }
            
            
            
            //console.log(jQuery(form).serialize());
            //$.post('subscript.php', $('#myForm').serialize());
        }
    });

})