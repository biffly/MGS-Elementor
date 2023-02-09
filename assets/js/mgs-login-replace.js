console.log('%c '+'%cMGS Elementor '+'%c << WP Login Replace >> '+'%cActivo', 'background-image:url(https://www.marceloscenna.com.ar/wp-content/themes/mgs-theme/imgs/logo.svg);background-size:contain;background-position:left bottom;background-repeat:no-repeat;font-size:30px;padding-right:40px;', 'color:#ff9100;font-size:20px;', 'color:#ffffff;font-size:12px;','color:greenyellow;')



/*btn.addEventListener('click', function() {
    if( prefersDarkScheme.matches ){
      document.body.classList.toggle('light-mode');
      var theme = document.body.classList.contains('light-mode') ? 'light' : 'dark';
    }else{
      document.body.classList.toggle('dark-mode');
      var theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
    }
    localStorage.setItem('mgs_replace_login_theme', theme);
});*/


jQuery.noConflict();
jQuery(document).ready(function($){
    //pongo el log fuera del loguin
    $('body').prepend('<div id="mgs-login-replace-login-wrapper"><div class="inner"></div></div>');
    $('body').prepend('<div id="mgs-login-replace-logo-wrapper"><div class="inner"></div></div>');

    $('.language-switcher').appendTo('#login');
    $('.wpml-login-ls').appendTo('#login');
    
    $('#login h1').attr('id', 'mgs-login-replace-logo');
    $('#mgs-login-replace-logo').appendTo('#mgs-login-replace-logo-wrapper .inner')
    $('#backtoblog').appendTo('#mgs-login-replace-logo-wrapper')
    $('#mgs-login-replace-logo-wrapper .inner').append('<h2 id="mgs-login-replace-title">' + mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo_text + '</h3>')
    $('#mgs-login-replace-logo-wrapper .inner').append('<h3 id="mgs-login-replace-desc">' + mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo_desc + '</h3>')
    
    $('#login').appendTo('#mgs-login-replace-login-wrapper .inner');
    $('#loginform').prepend('<h2>Ingrese al sitio</h2>');
    
    $('#mgs-login-replace-logo-wrapper').append('<button type="button" id="theme_switch" class="theme_switch"></button>');

    //logo
    if( mgs_elementor_login_replace_vars.mgs_login_replace_hide_wp_logo=='on' ){
        //custom logo
        if( mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo!='' ){
            $('#mgs-login-replace-logo a')
                //.css('background-image', 'url('+mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo+')')
                .attr('href', mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo_link)
                .attr('title', mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo_text)
                //.text(mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo_text)
                .text('')
                .addClass('mgs-login-replace-custom')
                .append('<img src="'+mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo+'" title="'+mgs_elementor_login_replace_vars.mgs_login_replace_custom_logo_text+'">')
        }else{
            $('#mgs-login-replace-logo a').css('display', 'none');
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

    //remember me
    if( mgs_elementor_login_replace_vars.mgs_login_replace_hide_chech_rememberme=='on' ){
        $('.forgetmenot').css('display', 'none');
    }
    //lost password
    if( mgs_elementor_login_replace_vars.mgs_login_replace_hide_reset_pass=='on' ){
        $('p#nav').css('display', 'none');
    }
    //regresar al sitio
    if( mgs_elementor_login_replace_vars.mgs_login_replace_hide_back_site=='on' ){
        $('p#backtoblog').css('display', 'none');
    }
    //Cambio de idioma
    if( mgs_elementor_login_replace_vars.mgs_login_replace_hide_lang_change=='on' ){
        $('.language-switcher').css('display', 'none');
        $('.wpml-login-ls').css('display', 'none');
    }

    /********************************************************** */
    /** theme color                                             */
    /** MGS 9-2-23                                              */
    /********************************************************** */
    var prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    // get conf del usuario si exite
    var currentTheme = MGS_GetCurrentColorTheme();

    MGS_SetTheme(currentTheme);

    $('#theme_switch').on('click', function(){
        if( $(this).hasClass('dark') ){
            //$(this).removeClass('dark').addClass('light')
            MGS_SetTheme('light')
            localStorage.setItem('mgs_replace_login_theme', 'light');
        }else{
            //$(this).removeClass('light').addClass('dark')
            MGS_SetTheme('dark')
            localStorage.setItem('mgs_replace_login_theme', 'dark');
        }

    })

    function MGS_GetCurrentColorTheme(){
        var _currentTheme = localStorage.getItem('mgs_replace_login_theme');
        if( _currentTheme=='dark' ){
            return _currentTheme;
        }else if( _currentTheme=='light' ){
            return _currentTheme;
        }else{
            if( prefersDarkScheme.matches ){
                _currentTheme = 'dark';
            }else{
                _currentTheme = 'light';
            }
            return _currentTheme;
        }
    }

    function MGS_SetTheme(theme){
        if( theme=='dark' ){
            $('body').removeClass('light-mode').addClass('dark-mode');
            $('#theme_switch').removeClass('light').addClass('dark');
        }else if( theme=='light' ){
            $('body').removeClass('dark-mode').addClass('light-mode');
            $('#theme_switch').removeClass('dark').addClass('light');
        }else{
            $('body').removeClass('dark-mode').addClass('light-mode');
            $('#theme_switch').removeClass('dark').addClass('light');
        }
    }

    console.log(currentTheme, prefersDarkScheme)
})