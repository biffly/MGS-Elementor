console.log('%c '+'%cMGS Elementor '+'%c << WP Login Replace >> '+'%cActivo', 'background-image:url(https://www.marceloscenna.com.ar/wp-content/themes/mgs-theme/imgs/logo.svg);background-size:contain;background-position:left bottom;background-repeat:no-repeat;font-size:30px;padding-right:40px;', 'color:#ff9100;font-size:20px;', 'color:#ffffff;font-size:12px;','color:greenyellow;')

jQuery.noConflict();
jQuery(document).ready(function($){
    console.log(mgs_elementor_login_replace_vars)

    //logo
    if( mgs_elementor_login_replace_vars.mgs_login_replace_hide_wp_logo=='on' ){
        //custom logo
        if( mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo!='' ){
            $('#login h1 a')
                .css('background-image', 'url('+mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo+')')
                .attr('href', mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo_link)
                .attr('title', mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo_text)
                .text(mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo_text)
                .addClass('custom')
        }else{
            $('#login h1 a').css('display', 'none')
        }
    }

    //labels
    if( mgs_elementor_login_replace_vars.mgs_login_replace_customs_labels=='on' ){
        //set placeholders to default labels
        $('#user_login').attr('placeholder', $('label[for="user_login"]').text());
        $('#user_pass').attr('placeholder', $('label[for="user_pass"]').text());

        //set new placeholders
        $('#user_login').attr('placeholder', mgs_elementor_login_replace_vars.mgs_login_replace_customs_labels_user);
        $('#user_pass').attr('placeholder', mgs_elementor_login_replace_vars.mgs_login_replace_customs_labels_pass);
        
        //set new labels
        $('label[for="user_login"]').text(mgs_elementor_login_replace_vars.mgs_login_replace_customs_labels_user)
        $('label[for="user_pass"]').text(mgs_elementor_login_replace_vars.mgs_login_replace_customs_labels_pass)

        //oculto labels?
        if( mgs_elementor_login_replace_vars.mgs_login_replace_hide_labels=='on' ){
            $('label[for="user_login"]').css('display', 'none').attr('aria-label', mgs_elementor_login_replace_vars.mgs_login_replace_customs_labels_user)
            $('label[for="user_pass"]').css('display', 'none').attr('aria-label', mgs_elementor_login_replace_vars.mgs_login_replace_customs_labels_pass)
        }
    }


})