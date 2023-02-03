<?php
/*
Plugin Name: MGS Elementor
Plugin URI: 
Description: AddOns para Elementor
Version: 0.0.6
Author: Marcelo Scenna
Author URI: http://www.marceloscenna.com.ar
Text Domain: mgs_elementor
*/

if( !defined('ABSPATH') ){ exit; }
error_reporting(E_ALL & ~E_NOTICE);


//Plugin Update Checker
require 'plugin-update-checker-5.0/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/biffly/MGS-Elementor',
	__FILE__,
	'MGS_Elementor'
);
$myUpdateChecker->setBranch('master');
//Optional: If you're using a private repository, specify the access token like this:
//$myUpdateChecker->setAuthentication('your-token-here');

if( !defined('MGS_ELEMENTOR_VERSION') )		    define('MGS_ELEMENTOR_VERSION', '3.5.0');
if( !defined('MGS_ELEMENTOR_PHP_VERSION') )     define('MGS_ELEMENTOR_PHP_VERSION', '7.4.0');

if( !defined('MGS_ELEMENTOR_BASENAME') )        define('MGS_ELEMENTOR_BASENAME', plugin_basename(__FILE__));
if( !defined('MGS_ELEMENTOR_PLUGIN_DIR') )      define('MGS_ELEMENTOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
if( !defined('MGS_ELEMENTOR_PLUGIN_DIR_URL') )  define('MGS_ELEMENTOR_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
if( !defined('MGS_ELEMENTOR_NAME') )            define('MGS_ELEMENTOR_NAME', 'MGS Elementor');
if( !defined('MGS_ELEMENTOR_SLUG') )            define('MGS_ELEMENTOR_SLUG', 'MGS_Elementor');
if( !defined('MGS_ELEMENTOR_PLUGIN_VERSION') )  define('MGS_ELEMENTOR_PLUGIN_VERSION', '0.0.6');

register_deactivation_hook(__FILE__, ['MGS_Elementor_AddOns', 'reset_defaul_settings']);

//configuracion
require_once('config.php');

//clases y funciones utiles
require_once('mgs-compare.php');

//Dummy Content
require_once('mgs-dummy-content.php');

// registro de widgets
require_once('mgs-elementor-class-main.php');

//conditional
require_once('mgs-conditional.php');

//Image rotation
require_once('mgs-image-rotation.php');

//Login Replace
require_once('mgs-login-replace.php');

//css externa
require_once('mgs-css-external.php');
new MGS_Elementor_External_CSS();
