<?php
/*
Plugin Name: wp-allbooking
Plugin URI: http://www.joomlaskin.it
Description: Wordpress booking online Manage your Hotel, Bed and Breakfast or Motel directly in Wordpress! Activate the plugin and create a page which includes the text {ALLBOOK}
Version: 1.2
Author: Joomlaskin
Author URI: http://www.joomlaskin.it
*/

/*  Copyright 2010 Joomlaskin

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

//print_r($_REQUEST);

//session_start();

foreach($_REQUEST as $key => $value){
	if(get_magic_quotes_gpc()){$value = stripslashes($value);}
	if (is_array($value)) {
		foreach($value as $key2 => $value2){
			$_REQUEST[$key] = mysql_real_escape_string(trim($value2));
		} } }



define ("PLUGIN_DIR_ALLBOOK", basename(dirname(__FILE__)));
define ("PLUGIN_URL_ALLBOOK", get_settings("siteurl")."/wp-content/plugins/".PLUGIN_DIR_ALLBOOK);
define ("PLUGIN_PATH_ALLBOOK",ABSPATH."wp-content/plugins/".PLUGIN_DIR_ALLBOOK);
add_action("admin_init", "allbook_admin_init");
add_action("admin_menu", "allbook_menu");
add_action("admin_head", "allbook_add_admin" );
add_action("wp_head", "allbook_add_head" );
add_filter("the_content","allbook_insert");
add_filter("plugin_action_links", "allbook_links", 10, 2 );
register_activation_hook(__FILE__,'allbook_activate');

$allbook_file_plugin = "wp-allbooking/wp-allbooking.php";
add_action("deactivate_" . $allbook_file_plugin, "allbook_deactivate");
add_action("activate_" . $allbook_file_plugin,  "allbook_activate");

$langplugin = get_bloginfo('language');
$filelang = PLUGIN_PATH_ALLBOOK."/lang/".$langplugin.".php";

if (file_exists($filelang)) {
    include($filelang);
} else {
    include(PLUGIN_PATH_ALLBOOK."/lang/en-US.php");
}


//$GLOBALS["version"] = "free";
//if (file_exists(PLUGIN_PATH. '/wp-res-pro.php')) { require_once(PLUGIN_PATH. "/wp-res-pro.php" );  $GLOBALS["version"] = "pro";}
//
//function allbook_adm_init() {
//	if ($_REQUEST['page']=="resources")
//	wp_enqueue_script('jquery-form');
//	wp_enqueue_script('jqtreetable', PLUGIN_URL.'/js/jQTreeTable/jqtreetable.js');
//	wp_enqueue_script('jquery-ui-dialog');
//	wp_enqueue_script('jquery-ui-resizable');
//
//
//	//<script src="'.PLUGIN_URL.'/js/jQTreeTable/jqtreetable.js" type="text/javascript"></script>';
//
//}


function AllbookresLocale($locale = "") {
	global $locale;

		$mofile = PLUGIN_PATH_ALLBOOK  .'/lang/'.PLUGIN_DIR_ALLBOOK.'-'.$locale.'.mo';
		return load_textdomain(PLUGIN_DIR_ALLBOOK, $mofile);

	if ( empty( $locale ) ) $locale = get_locale();
	if ( !empty( $locale ) ) {

		$mofile = PLUGIN_PATH_ALLBOOK  .'/lang/'.PLUGIN_DIR_ALLBOOK.'-'.$locale.'.mo';

		if (file_exists($mofile))    return load_textdomain(PLUGIN_DIR_ALLBOOK, $mofile);
		else                        return false;
	} return
	false;
}


if ( !function_exists('wp_sanitize_redirect') ) :
function wp_sanitize_redirect($location) {
	$location = preg_replace('|^a-z0-9-~+_.?#=&;,/:%!|i', '', $location);
	$location = wp_kses_no_null($location);


	$strip = array('%0d', '%0a', '%0D', '%0A');
	$location = _deep_replace($strip, $location);
	return $location;
}
endif;

function allbook_links($links, $file){

	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(dirname(__FILE__).'/wp-allbooking.php');

	if ($file == $this_plugin){
		$settings_link1 = '<a href="admin.php?page='.$this_plugin.'settings">' . __('Settings', 'wp-allbooking') . '</a>';
		//$settings_link2 = '<a href="http://reservation.isaev.asia/donate">' . __('Donate!', 'wp-allbooking') . '</a>';
		array_unshift( $links, $settings_link2 );
		//array_unshift( $links, $settings_link1 );
	}
	return $links;
}


function allbook_add_head()
{

AllbookresLocale() ;

	echo '
<link type="text/css" href="'.PLUGIN_URL_ALLBOOK.'/assets/css/smoothness/jquery-ui-1.8.9.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="'.PLUGIN_URL_ALLBOOK.'/css/style.css" />
<link href="'.PLUGIN_URL_ALLBOOK.'/css/style/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="'.PLUGIN_URL_ALLBOOK.'/assets/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
    <script language="JavaScript" type="text/javascript">
    /*<![CDATA[*/
    $(document).ready(function(){
	$(".cart").fancybox({
		 \'width\' : 700,
		 \'height\' : 300,
		 \'autoScale\' : false,
		 \'transitionIn\' : \'none\',
		 \'transitionOut\' : \'none\',
		 \'type\' : \'iframe\' ,
         \'onComplete\': function() {
        $("#fancybox-wrap, #fancybox-overlay").delay(10000).fadeOut();
        }
	 });

	 });
     $("#fancybox-close").click(function() {
     $(\'#fancybox-overlay\').stop();
     $(\'#fancybox-wrap\').stop();
     });
    /*]]>*/
    </script>
';
}

function allbook_add_admin()
{

AllbookresLocale();
if ($_REQUEST["page"]=="allbooking") {
 echo '<link href="'.PLUGIN_URL_ALLBOOK.'/css/res/admin.css" rel="stylesheet" type="text/css" /> ';
 echo '
<link type="text/css" href="'.PLUGIN_URL_ALLBOOK.'/assets/css/smoothness/jquery-ui-1.8.9.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/editor/scripts/jHtmlArea-0.7.0.js"></script>
<link rel="Stylesheet" type="text/css" href="'.PLUGIN_URL_ALLBOOK.'/assets/editor/style/jHtmlArea.css" />
<link rel="Stylesheet" type="text/css" href="'.PLUGIN_URL_ALLBOOK.'/assets/editor/editor.css" />
';
echo '<script>
	$(function() {
		$( "#tabs" ).tabs();
        $(\'#date\').datepicker({ dateFormat: \'yy-mm-dd\' });
        $(\'#date1\').datepicker({ dateFormat: \'yy-mm-dd\' });
        $(\'#date2\').datepicker({ dateFormat: \'yy-mm-dd\' });
        $("#allbook_invoice").htmlarea();
        $("#allbook_customemail").htmlarea();
	});
	</script>
';
}
}




function allbook_insert($content)
{
	if (preg_match('{ALLBOOK}',$content))
	{
		$allbook_output = allbook_task();
		$content = str_replace('{ALLBOOK}',$allbook_output,$content);
	}
    else if (preg_match('#{CATBOOK (.*?)}#s',$content))
    {
    $regex = '#{CATBOOK (.*?)}#s';
	preg_match_all( $regex, $content, $matches );
	for($x=0; $x<count($matches[0]); $x++)
	{
		$parts = explode(" ", $matches[1][$x]);
		if(count($parts) > 0)
		{
			//$vid= explode('=',$parts[0]);
			$catid = $parts[0];

session_start();
require_once(PLUGIN_PATH_ALLBOOK."/calendar.class.php");
$action=isset($_REQUEST['action'])?$_REQUEST['action']:"";
$view_type=isset($_REQUEST['view_type'])?$_REQUEST['view_type']:"";
$year=isset($_REQUEST['year'])?$_REQUEST['year']:"";
$month=isset($_GET['month'])?$_GET['month']:"";
$day=isset($_GET['day'])?$_GET['day']:"";
$settings = allbook_get_settings();
$allbook_width = $settings['allbook_width'];
$allbook_height = $settings['allbook_height'];
$cat=isset($_REQUEST['cat'])?$_REQUEST['cat']:"1";

    // create calendar object
    $objCalendar = new Calendar();

    ## +---------------------------------------------------------------------------+
    ## | 2. General Settings:                                                      |
    ## +---------------------------------------------------------------------------+

    ## *** set calendar width and height
    $objCalendar->SetCalendarDimensions($allbook_width, $allbook_height);
    ## *** set week day name length - "short" or "long"
    $objCalendar->SetWeekDayNameLength("long");
    ## *** set start day of week: from 1 (Sanday) to 7 (Saturday)
    $objCalendar->SetWeekStartedDay("1");
    ## *** set calendar caption
    $objCalendar->SetCaption("ApPHP Calendar v".Calendar::Version());

     $objCalendar->SetCatid($catid);

    ## +---------------------------------------------------------------------------+
    ## | 3. Draw Calendar:                                                         |
    ## +---------------------------------------------------------------------------+

    $replace = $objCalendar->Show();

		}

		$content = str_replace($matches[0][$x], $replace, $content);
	}
    }
	return $content;
}



function allbook_task()
{
switch ($_REQUEST['pager']) {
	case 'start': echo allbook_page();        break ;
    case 'checkout': echo allbook_user_page();        break ;
    case 'conf': echo allbook_pageconf();        break ;
    case 'done': echo allbook_pagedone();        break ;
    case 'cancel': echo allbook_pagecancell();        break ;
    case 'sellaok': echo allbook_pagedonesella();        break ;
    case 'sellano': echo allbook_pagedonesellano();        break ;
    case 'dettails': echo allbook_dettails();        break ;
    case 'home': echo allbook_home();        break ;
    case 'cart': echo allbook_cart();        break ;
    default:  echo allbook_page();        break;
	}
}

function allbook_page()
{
session_start();
require_once(PLUGIN_PATH_ALLBOOK."/calendar.class.php");
$action=isset($_REQUEST['action'])?$_REQUEST['action']:"";
$view_type=isset($_REQUEST['view_type'])?$_REQUEST['view_type']:"";
$year=isset($_REQUEST['year'])?$_REQUEST['year']:"";
$month=isset($_GET['month'])?$_GET['month']:"";
$day=isset($_GET['day'])?$_GET['day']:"";
$settings = allbook_get_settings();
$allbook_width = $settings['allbook_width'];
$allbook_height = $settings['allbook_height'];
$cat=isset($_REQUEST['cat'])?$_REQUEST['cat']:"1";

    // create calendar object
    $objCalendar = new Calendar();

    ## +---------------------------------------------------------------------------+
    ## | 2. General Settings:                                                      |
    ## +---------------------------------------------------------------------------+

    ## *** set calendar width and height
    $objCalendar->SetCalendarDimensions($allbook_width, $allbook_height);
    ## *** set week day name length - "short" or "long"
    $objCalendar->SetWeekDayNameLength("long");
    ## *** set start day of week: from 1 (Sanday) to 7 (Saturday)
    $objCalendar->SetWeekStartedDay("1");
    ## *** set calendar caption
    $objCalendar->SetCaption("ApPHP Calendar v".Calendar::Version());

    ## +---------------------------------------------------------------------------+
    ## | 3. Draw Calendar:                                                         |
    ## +---------------------------------------------------------------------------+

    $objCalendar->Show();

	?>

<?php

}

function allbook_user_page()
{

global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');

$checkout = $_REQUEST["checkout"];
if ($checkout == "checkout") {
include PLUGIN_PATH_ALLBOOK.'/checkout.php';
?>

<? } else {
  echo "<script>document.location.href='index.php'</script>";
}
?>
<?php

}

function allbook_pageconf()
{
//session_start();

global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');
//ob_start();

?>
<?
$conferma = $_REQUEST["conferma"];
    if ($conferma == "send") {
include PLUGIN_PATH_ALLBOOK.'/bookingsave.php';
   ?>
                <?php
                $filenamepp = PLUGIN_PATH_ALLBOOK.'/booking/payment_processing.php';
                ?>
        	<? if ($paypal == 1 && $paypal_email!="" && $total!="" && file_exists($filenamepp)) {
				echo "Full payment:&nbsp;&nbsp;<b>&euro;".$total."</b><br/><br/>";
			?>
				<form  action="https://www.paypal.com/cgi-bin/webscr" method="post" name="payform" target="_blank">
					<input type="hidden" name="cmd" value="_xclick">
					<input type="hidden" name="custom" value="<?= $invoice ?>">
					<input type="hidden" name="business" value="<?= $paypal_email() ?>">
					<input type="hidden" name="lc" value="US">
					<input type="hidden" name="item_name" value="Booking Payment">
					<input type="hidden" name="amount" value="<?= $total;?>">
					<input type="hidden" name="currency_code" value="USD">
					<input type="hidden" name="cn" value="Add special instructions to the seller">
					<input type="hidden" name="no_shipping" value="1">
					<input type="hidden" name="rm" value="1">
					<input type="hidden" name="notifiy" value="<?php echo PLUGIN_DIR_ALLBOOK_ALLBOOK ?>/booking/payment_processing.php">
					<input type="hidden" name="return" value="<?php echo get_settings("siteurl") ?>/<?php echo 'index.php?page_id='.$post_id ?>&pager=done">
					<input type="hidden" name="cancel_return" value="<?php echo get_settings("siteurl") ?>/<?php echo 'index.php?page_id='.$post_id ?>&pager=cancel">
					<input type="hidden" name="bn" value="PP-BuyNowBF">

					<input style='cursor:pointer;' type="image"  src="https://www.paypal.com/en_US/i/bnr/horizontal_solution_PPeCheck.gif" border="0" name="submit" alt="">
					<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
                <? if ($bancasella == 1) {   ?>
                <?php
                $filename = PLUGIN_PATH_ALLBOOK.'/booking/bancasella/GestPayCrypt.inc.php';
                if (file_exists($filename)) {
                 include PLUGIN_PATH_ALLBOOK.'/booking/bancasella/request_tot.php';
                 }
                 }
                ?>
            <? } else if ($bancasella == 1 && file_exists(PLUGIN_PATH_ALLBOOK.'/booking/bancasella/GestPayCrypt.inc.php')) {  ?>
                            <?php
                $filename = PLUGIN_PATH_ALLBOOK.'/booking/bancasella/GestPayCrypt.inc.php';
                if (file_exists($filename)) {
                 include PLUGIN_PATH_ALLBOOK.'/booking/bancasella/request_tot.php';
                 }
                 ?>
			<? } ?>

<? } else {
  echo "<script>document.location.href='index.php'</script>";
}
?>
<?php

}

function allbook_pagedone()
{
session_start();
?>
<?php echo CONFIRM ?>
<?php

$_SESSION=array(); // Desetta tutte le variabili di sessione.
session_destroy(); //DISTRUGGE la sessione.

?>
<?php
}

function allbook_pagecancell()
{
session_start();
?>
<?php echo CANCELLED ?>
<?php

$_SESSION=array(); // Desetta tutte le variabili di sessione.
session_destroy(); //DISTRUGGE la sessione.

?>

<?php
}

function allbook_pagedonesella()
{
$filename = PLUGIN_PATH_ALLBOOK.'/booking/bancasella/GestPayCrypt.inc.php';
                if (file_exists($filename)) {
                 include PLUGIN_PATH_ALLBOOK.'/booking/bancasella/response.php';
                 }
}

function allbook_pagedonesellano()
{
$filename = PLUGIN_PATH_ALLBOOK.'/booking/bancasella/GestPayCrypt.inc.php';
                if (file_exists($filename)) {
                 include PLUGIN_PATH_ALLBOOK.'/booking/bancasella/cancel.php';
                 }
}


function allbook_dettails()
{

}

function allbook_home()
{

}

function allbook_cart()
{
include PLUGIN_PATH_ALLBOOK.'/cart.php';
}

function allbook_menu() {

global $submenu, $menu;
AllbookresLocale() ;

if ( strpos($_SERVER['HTTP_HOST'],'isaev.asia') !== FALSE ) {$user_role_plugin = $user_role_settings  = 0 ; } else { $user_role_plugin = get_option("allbook_security_plugin") ; $user_role_settings = get_option("allbook_security_settings") ;}




	add_menu_page(__("Reservation","wp-allbooking"), __("All Booking","wp-allbooking"), $user_role_plugin,"allbooking" , 'allbook_options',PLUGIN_URL_ALLBOOK."/img/ico16x16.png");
//	add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Configuration","wp-allbooking"), $user_role_plugin,"conf", 'allbook_options');
//	add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Site config","wp-allbooking"), $user_role_plugin, "siteconf", 'allbook_options');
//	add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Hotel","wp-allbooking"), $user_role_plugin, "hotel", 'allbook_options');
//	add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Room Types","wp-allbooking"), $user_role_settings, "room", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Seasons","wp-allbooking"), $user_role_settings, "seasons", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Bookings","wp-allbooking"), $user_role_settings, "bookings", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Availability Rooms","wp-allbooking"), $user_role_settings, "availability", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Bookings history","wp-allbooking"), $user_role_settings, "history", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Bookings stats","wp-allbooking"), $user_role_settings, "stats", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Language","wp-allbooking"), $user_role_settings, "lang", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Email template","wp-allbooking"), $user_role_settings, "tempemail", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Constants","wp-allbooking"), $user_role_settings, "constants", 'allbook_options');
//    //add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Menu","wp-allbooking"), $user_role_plugin, "menumanager", 'allbook_options');
//	$submenu[plugin_basename( "settings" )][0][0] = __("Reservation","wp-allbooking");



	}




function allbook_options() {

echo '<div class="wrap">';
echo allbook_menu_admin();


	if(isset($_REQUEST['page']))  switch ($_REQUEST['page']) {

		//case "resources" : 	adm_resources(); break;
		case "allbooking"  :  allbook_adm_settings(); break;
        case "conf"  :  allbook_adm_configuration(); break;
        case "menumanager"  :  allbook_adm_menumanager(); break;
        case "hotel"  :  allbook_adm_hotel(); break;
        case "room"  :  allbook_adm_roomtype(); break;
        case "seasons"  :  allbook_adm_seasons(); break;
        case "bookings"  :  allbook_adm_booking(); break;
        case "availability"  :  allbook_adm_avv(); break;
        case "history"  :  allbook_adm_history(); break;
        case "stats"  :  allbook_adm_stat(); break;
        case "lang"  :  allbook_adm_lang(); break;
        case "tempemail"  :  allbook_adm_tempemail(); break;
        case "constants"  :  allbook_adm_constants(); break;
        case "siteconf"  :  allbook_adm_siteconf(); break;

	}
	else
		allbook_adm_settings();

echo '</div>';



}

function allbook_activate()
{

	global $wpdb;

    $table_name = "wp_resservation_book";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cognome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `indirizzo` text COLLATE utf8_unicode_ci NOT NULL,
  `cap` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `citta` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `prov` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nazione` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `telefono` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data_book` datetime NOT NULL,
  `data` datetime NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `note` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `code` text COLLATE utf8_unicode_ci NOT NULL,
  `id_book` int(10) NOT NULL,
  `price` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `invoice` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
	}


    $table_name = "wp_resservation_cat";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time_start_cat` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time_end_cat` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rangetime` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql = "INSERT INTO $table_name (`id`, name`, `time_start_cat`, `time_end_cat`, `rangetime`) VALUES
(1, 'Dinner', '20:00', '20:00', '60'),
(7, 'Lunch', '12:00', '12:00', '60'),
(6, 'Breakfast', '08:00', '08:00', '60');
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);   }


    $table_name = "wp_resservation_disp";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
   `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `max` text COLLATE utf8_unicode_ci NOT NULL,
  `price` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `category` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=184;
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql = "INSERT INTO $table_name (`id`, `date`, `time_start, `time_end`, `max`, `price`, `status`, `description`, `category`) VALUES
(183, '2011-04-21', '09:00:00', '10:00:00', '7', '9', 1, 'Breakfast', 6),
(182, '2011-04-21', '08:00:00', '09:00:00', '7', '9', 1, 'Breakfast', 6),
(181, '2011-03-23', '13:00:00', '14:00:00', '8', '6', 1, 'Lunch', 7),
(180, '2011-03-23', '12:00:00', '13:00:00', '8', '6', 1, 'Lunch', 7),
(179, '2011-03-31', '21:00:00', '22:00:00', '7', '56', 1, 'Dinner', 1),
(178, '2011-03-31', '20:00:00', '21:00:00', '7', '56', 1, 'Dinner', 1),
(177, '2011-03-30', '09:00:00', '10:00:00', '3', '10', 1, 'Breakfast', 6),
(176, '2011-03-30', '08:00:00', '09:00:00', '3', '10', 1, 'Breakfast', 6),
(174, '2011-03-26', '20:00:00', '21:00:00', '9', '12', 1, 'Dinner', 1),
(173, '2011-03-16', '09:00:00', '10:00:00', '4', '5', 1, 'Breakfast', 6),
(172, '2011-03-16', '08:00:00', '09:00:00', '4', '5', 1, 'Breakfast', 6),
(171, '2011-03-15', '13:00:00', '14:00:00', '5', '34', 1, 'Lunch', 7),
(170, '2011-03-15', '12:00:00', '13:00:00', '4', '34', 1, 'Lunch', 7),
(169, '2011-03-14', '09:00:00', '10:00:00', '3', '43', 1, 'Breakfast', 6),
(168, '2011-03-14', '08:00:00', '09:00:00', '3', '43', 1, 'Breakfast', 6),
(167, '2011-03-13', '20:00:00', '21:00:00', '0', '21', 1, 'Dinner', 1);
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);   }

$table_name = "wp_resservation_invoice";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
   `id` int(10) NOT NULL AUTO_INCREMENT,
  `book_id` text COLLATE utf8_unicode_ci NOT NULL,
  `invocie` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(1) NOT NULL,
  `qty` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `trans_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `trans_date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);   }

add_option("allbook_db_version", "1.0");
add_option("allbook_calendar_color","gold");
add_option("allbook_security_plugin","7");
add_option("allbook_security_settings","10");

add_option("allbook_paypal","paypal@email.com");
add_option("allbook_paypal_active","1");
add_option("allbook_datastart","9:30");
add_option("allbook_dataend","17:30");
add_option("allbook_datarange","30 mins");
add_option("allbook_cash_active","1");
add_option("allbook_customemail","Text");
add_option("allbook_width","500px");
add_option("allbook_height","500px");
add_option("allbook_invoice","Text");
add_option("allbook_pageid","Text");
add_option("allbook_valute","USD");
add_option("allbook_catinit","1");
add_option("allbook_indirizzo","1");
add_option("allbook_cap","1");
add_option("allbook_citta","1");
add_option("allbook_provincia","1");
add_option("allbook_nazione","1");
add_option("allbook_telefono","1");

$wpdb->query($sql);

update_option("allbook_db_version", "1.0");
}


function allbook_deactivate()
{


}

function allbook_get_settings() {

	$AllbookSettingsArray=array(

		'allbook_db_version'						=> get_option('allbook_db_version'),

		'allbook_security_plugin'					=> get_option('allbook_security_plugin'),
		'allbook_security_settings'						=> get_option('allbook_security_settings'),
		'allbook_paypal'				=> get_option('allbook_paypal'),
        'allbook_paypal_active'				=> get_option('allbook_paypal_active'),
        'allbook_datastart'				=> get_option('allbook_datastart'),
        'allbook_dataend'				=> get_option('allbook_dataend'),
        'allbook_datarange'				=> get_option('allbook_datarange'),
        'allbook_cash_active'				=> get_option('allbook_cash_active'),
        'allbook_customemail'				=> get_option('allbook_customemail'),
        'allbook_width'				=> get_option('allbook_width'),
        'allbook_height'				=> get_option('allbook_height'),
        'allbook_invoice'				=> get_option('allbook_invoice'),
        'allbook_pageid'				=> get_option('allbook_pageid'),
        'allbook_valute'				=> get_option('allbook_valute'),
        'allbook_catinit'				=> get_option('allbook_catinit'),
        'allbook_indirizzo'				=> get_option('allbook_indirizzo'),
        'allbook_cap'				=> get_option('allbook_cap'),
        'allbook_citta'				=> get_option('allbook_citta'),
        'allbook_provincia'				=> get_option('allbook_provincia'),
        'allbook_nazione'				=> get_option('allbook_nazione'),
        'allbook_telefono'				=> get_option('allbook_telefono'),

		'allbook_uninstall'							=> get_option('allbook_uninstall')

	);

	return $AllbookSettingsArray;

}

function allbook_admin_init() {

		register_setting('allbook-options', 'allbook_paypal');
        register_setting('allbook-options', 'allbook_paypal_active');
        register_setting('allbook-options', 'allbook_datastart');
        register_setting('allbook-options', 'allbook_dataend');
        register_setting('allbook-options', 'allbook_datarange');
        register_setting('allbook-options', 'allbook_cash_active');
        register_setting('allbook-options', 'allbook_customemail');
        register_setting('allbook-options', 'allbook_width');
        register_setting('allbook-options', 'allbook_height');
        register_setting('allbook-options', 'allbook_invoice');
        register_setting('allbook-options', 'allbook_pageid');
        register_setting('allbook-options', 'allbook_valute');
        register_setting('allbook-options', 'allbook_catinit');
        register_setting('allbook-options', 'allbook_indirizzo');
        register_setting('allbook-options', 'allbook_cap');
        register_setting('allbook-options', 'allbook_citta');
        register_setting('allbook-options', 'allbook_provincia');
        register_setting('allbook-options', 'allbook_nazione');
        register_setting('allbook-options', 'allbook_telefono');




}

function allbook_pass ()
{

	$fp = file ("pass.txt",1);
	return (trim($fp[rand(0,count($fp)-1)]));

}




/////////////////////////////////////////////////////////////////////////////
//					Admin page section						//
/////////////////////////////////////////////////////////////////////////////


function allbook_menu_admin () {
	global $wpdb;

    //require (PLUGIN_PATH_ALLBOOK."/booking/admin/hometext.php");

	$pageadr=$_REQUEST['page'];
	if ($_REQUEST["page"]) $divid=$_REQUEST["page"]; else $divid="allbooking";
	if ($_REQUEST["page"]=="suborder" || $_REQUEST["page"]=="mail") $divid="allbooking";
	if ($_REQUEST["page"]=="makeorder1" || $_REQUEST["page"]=="makeorder2")  $divid="makeorder";

?>
	<br>
    <div id="tabs">
	<ul>
	<li><a href="#settings"><?php echo CONFIGURATION ?></a></li>
    <li><a href="#dates"><?php echo LISTDATE ?></a></li>
    <li><a href="#create"><?php echo CREATEAVAILABILITY ?></a></li>
    <li><a href="#lisbook"><?php ECHO HISTORY ?></a></li>
    <li><a href="#category"><?php echo CATEGORY ?></a></li>
    <li><a href="#help"><?php echo HELP ?></a></li>
	</ul>
    <div id="settings">
    <form method="post" action="options.php" id="options">

    <?php

		wp_nonce_field('update-options');
		settings_fields('allbook-options');
        $settings = allbook_get_settings();
		?>

        <table class="form-table" style="clear:none;">
					<tbody>
                       <tr valign="top">
							<th scope="row"><?php echo WIDTH ?></th>
							<td>
								<fieldset>

									<label for="allbook_width">
										<input type="text" name="allbook_width" id="allbook_width" value="<?php echo $settings['allbook_width']; ?>" />
										<?php echo WIDTH ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo HEIGHT ?></th>
							<td>
								<fieldset>

									<label for="allbook_height">
										<input type="text" name="allbook_height" id="allbook_height" value="<?php echo $settings['allbook_height']; ?>" />
										<?php echo HEIGHT ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php echo PAYPAL ?></th>
							<td>
								<fieldset>

									<label for="allbook_paypal">
										<input type="text" name="allbook_paypal" id="allbook_paypal" value="<?php echo $settings['allbook_paypal']; ?>" />
										<?php echo PAYPAL ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALUTE ?></th>
							<td>
								<fieldset>

									<label for="allbook_valute">
										<input type="text" name="allbook_valute" id="allbook_paypal" value="<?php echo $settings['allbook_valute']; ?>" />
										<?php echo VALUTE ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo ACTIVEPAYPAL ?></th>
							<td>
								<fieldset>
                                 <?php
                                   if ($settings['allbook_paypal_active'] == 0) {
                                  $seletpaypal = NO;
                                  } else {
                                  $seletpaypal = YES;
                                  }
                                  ?>
									<label for="allbook_paypal_active">
                                    <select name="allbook_paypal_active" id="allbook_paypal_active" size="1" />
                                        <option value="<?php echo $settings['allbook_paypal_active'] ?>"><?php echo $seletpaypal ?></option>
                                        <option value="1"><?php echo YES ?></option>
                                        <option value="0"><?php echo NO ?></option>
                                        </select>
										<?php echo DEFAULTPAYPAL ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo ACTIVECASH ?></th>
							<td>
								<fieldset>
                                 <?php
                                  if ($settings['allbook_cash_active'] == 0) {
                                  $seletccash = NO;
                                  } else {
                                  $seletccash = YES;
                                  }
                                  ?>
									<label for="allbook_cash_active">
                                    <select name="allbook_cash_active" id="allbook_cash_active" size="1" />
                                        <option value="<?php echo $settings['allbook_cash_active'] ?>"><?php echo $seletccash ?></option>
                                        <option value="1"><?php echo YES ?></option>
                                        <option value="0"><?php echo NO ?></option>
                                        </select>
										<?php echo DEFAULTCASH ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo EMAILCUSTOM ?></th>
							<td>
								<fieldset>

									<label for="allbook_customemail">
                                    <textarea name="allbook_customemail" id="allbook_customemail" cols="50" rows="15">
                                    <?php echo $settings['allbook_customemail']; ?>
                                    </textarea>
										<?php echo DEFAULTEMAIL ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo INVOICECUSTOM ?></th>
							<td>
                                <fieldset>

									<label for="allbook_invoice">
                                    <textarea name="allbook_invoice" id="allbook_invoice" cols="50" rows="15">
                                    <?php echo $settings['allbook_invoice']; ?>
                                    </textarea>
									 <?php echo DEFAULTINVOICE ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo PAGEID ?></th>
							<td>
								<fieldset>

									<label for="allbook_pageid">
										<input type="text" name="allbook_pageid" id="allbook_pageid" value="<?php echo $settings['allbook_pageid']; ?>" />
										<?php echo DEFAULTPAGEID ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo CATEGORYSTART ?></th>
							<td>
								<fieldset>

									<label for="allbook_catinit">
										<input type="text" name="allbook_catinit" id="allbook_catinit" value="<?php echo $settings['allbook_catinit']; ?>" />
										<?php echo DEFAULTCATEGORY ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATEADRESS ?></th>
							<td>
								<fieldset>

									<label for="allbook_catinit">
										<p><input name="allbook_indirizzo" type="radio" value="1" <?php if ($settings['allbook_indirizzo'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_indirizzo" type="radio" value="0" <?php if ($settings['allbook_indirizzo'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTADRESS ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATEAZIP ?></th>
							<td>
								<fieldset>

									<label for="allbook_cap">
										<p><input name="allbook_cap" type="radio" value="1" <?php if ($settings['allbook_cap'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_cap" type="radio" value="0" <?php if ($settings['allbook_cap'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTZIP ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATECITY ?></th>
							<td>
								<fieldset>

									<label for="allbook_citta">
										<p><input name="allbook_citta" type="radio" value="1" <?php if ($settings['allbook_citta'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_citta" type="radio" value="0" <?php if ($settings['allbook_citta'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTCITY ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATEPROVINCE ?></th>
							<td>
								<fieldset>

									<label for="allbook_provincia">
										<p><input name="allbook_provincia" type="radio" value="1" <?php if ($settings['allbook_provincia'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_provincia" type="radio" value="0" <?php if ($settings['allbook_provincia'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTPROVINCE ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATECOUNTRY ?></th>
							<td>
								<fieldset>

									<label for="allbook_nazione">
										<p><input name="allbook_nazione" type="radio" value="1" <?php if ($settings['allbook_nazione'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_nazione" type="radio" value="0" <?php if ($settings['allbook_nazione'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTCOUNTRY ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATEPHONE ?></th>
							<td>
								<fieldset>

									<label for="allbook_telefono">
										<p><input name="allbook_telefono" type="radio" value="1" <?php if ($settings['allbook_telefono'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_telefono" type="radio" value="0" <?php if ($settings['allbook_telefono'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTPHONE ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>

					</tbody>
				</table>
                <p class="submit" style="text-align:center;">
			<input type="submit" name="Submit" class="button-primary" value="<?php echo SAVECHANGES ?>" />
		</p>
    </form>
</div>
<div id="dates">
  <style type="text/css">
  /*<![CDATA[*/
  div.pagination {
	padding: 3px;
	margin: 3px;
}

div.pagination a {
	padding: 2px 5px 2px 5px;
	margin: 2px;
	border: 1px solid #AAAADD;

	text-decoration: none; /* no underline */
	color: #000099;
}
div.pagination a:hover, div.pagination a:active {
	border: 1px solid #000099;

	color: #000;
}
div.pagination span.current {
	padding: 2px 5px 2px 5px;
	margin: 2px;
		border: 1px solid #000099;

		font-weight: bold;
		background-color: #000099;
		color: #FFF;
	}
	div.pagination span.disabled {
		padding: 2px 5px 2px 5px;
		margin: 2px;
		border: 1px solid #EEE;

		color: #DDD;
	}

  /*]]>*/
  </style>
<form name="rangetime" action="admin.php?page=allbooking#dates" method="POST">
<? echo DATES ?> <input name="date" type="text" id="date" /><br />
<input type="submit" name="Search" value="search" />
</form>
<?php
$action = $_GET['action'];
switch ($action) {
case 'list':
default:
    $date = $_REQUEST["date"];
    if ($date) {
    $where = "WHERE date = '".$date."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$pagelist = $_GET['pagelist'];
	if($pagelist)
		$start = ($pagelist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, date,time_start,time_end,max,price,status,description FROM $tbl_name $where LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($pagelist == 0) $pagelist = 1;					//if no page var is given, default to 1.
	$prev = $pagelist - 1;							//previous page is page - 1
	$next = $pagelist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($pagelist > 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$prev#dates\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $pagelist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($pagelist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $pagelist && $pagelist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $pagelist - $adjacents; $counter <= $pagelist + $adjacents; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
			}
		}

		//next button
		if ($pagelist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$next#dates\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo DATES ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND ?></td>
 <td class='resint'><?php echo MAXBOOKING ?></td>
 <td class='resint'><?php echo PRICE ?></td>
 <td class='resint'><?php echo STATUS ?></td>
 <td class='resint'><?php echo DESCRIPTION ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		  switch ($row['status']) {
		    case 0:
            $status = "not available";
		    break;
		    case 1:
            $status = "available";
		    break;
		    case 2:
            $status = "Resserved";
		    break;
		    default:
            $status = "available";
            break;
		  }
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['date']."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "<td class='resint'>".$row['max']."</td>";
        echo "<td class='resint'>".$row['price']."</td>";
        echo "<td class='resint'>".$status."</td>";
        echo "<td class='resint'>".$row['description']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=edit&id=".$row['id']."#dates\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=delete&del=yes&id=".$row['id']."#dates\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php
break;
case 'edit':
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
$id = $_REQUEST["id"];
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, date,time_start,time_end,max,price,status,description FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);
?>
 <table>
	<?php
		while($row = mysql_fetch_array($result))
		{
		echo '<form name="edit" action="admin.php?page=allbooking&action=save#dates" method="post">';
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>ID</td>";
        echo "<td class='resint'><input name=\"id\" type=\"hidden\" value=\"".$row['id']."\" />".$row['id']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".DATES."</td>";
        echo "<td class='resint'>".$row['date']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMESTART."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMEEND."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".MAXBOOKING."</td>";
        echo "<td class='resint'><input name=\"max\" type=\"text\" value=\"".$row['max']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".PRICE."</td>";
        echo "<td class='resint'><input name=\"price\" type=\"text\" value=\"".$row['price']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".STATUS."</td>";
        echo "<td class='resint'><input name=\"status\" type=\"text\" value=\"".$row['status']."\" /></td>";

        echo "</tr>";
        echo "<td class='resint'>".DESCRIPTION."</td>";
        echo "<td class='resint'><input name=\"description\" type=\"text\" value=\"".$row['description']."\" /></td>";

        echo "</tr>";
        echo "<tr><td></td>";
        echo '<td class="resint"><input type="submit" name="update" value="save" /></td></tr>
             </form>';
		}
	?>
</table>
<?php
break;
case 'save':
$update = $_REQUEST["update"] == "save";
    if ($update) {
$id = $_REQUEST["id"];
    if ($id) {
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    $max = $_REQUEST["max"];
    $price = $_REQUEST["price"];
    $status = $_REQUEST["status"];
    $description = $_REQUEST["description"];
	/* Get data. */
	$sql = "UPDATE  $tbl_name SET max = '".$max."', price = '".$price."', status = '".$status."', description = '".$description."' $where";
	$result = mysql_query($sql);

?>
<?php echo RECORDSAVED ?>
<?php
$date = $_REQUEST["date"];
    if ($date) {
    $where1 = "WHERE date = '".$date."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where1";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$pagelist = $_GET['pagelist'];
	if($pagelist)
		$start = ($pagelist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, date,time_start,time_end,max,price,status,description FROM $tbl_name $where1 LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($pagelist == 0) $pagelist = 1;					//if no page var is given, default to 1.
	$prev = $pagelist - 1;							//previous page is page - 1
	$next = $pagelist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($pagelist > 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$prev#dates\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $pagelist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($pagelist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $pagelist && $pagelist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $pagelist - $adjacents; $counter <= $pagelist + $adjacents; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
			}
		}

		//next button
		if ($pagelist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$next#dates\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo DATES ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND ?></td>
 <td class='resint'><?php echo MAXBOOKING ?></td>
 <td class='resint'><?php echo PRICE ?></td>
 <td class='resint'><?php echo STATUS ?></td>
 <td class='resint'><?php echo DESCRIPTION ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		  switch ($row['status']) {
		    case 0:
            $status = "not available";
		    break;
		    case 1:
            $status = "available";
		    break;
		    case 2:
            $status = "Resserved";
		    break;
		    default:
            $status = "available";
            break;
		  }
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['date']."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "<td class='resint'>".$row['max']."</td>";
        echo "<td class='resint'>".$row['price']."</td>";
        echo "<td class='resint'>".$status."</td>";
        echo "<td class='resint'>".$row['description']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=edit&id=".$row['id']."#dates\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=delete&del=yes&id=".$row['id']."#dates\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>

<?php } ?>
<?php }

break;
case 'delete':
$delete = $_REQUEST["del"] == "yes";
    if ($delete) {
$id = $_REQUEST["id"];
    if ($id) {
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    $max = $_REQUEST["max"];
    $price = $_REQUEST["price"];
    $status = $_REQUEST["status"];
    $description = $_REQUEST["description"];
	/* Get data. */
	$sql = "DELETE FROM $tbl_name $where";
	$result = mysql_query($sql);

?>
<?php echo RECORDDELETE ?>
<?php
$date = $_REQUEST["date"];
    if ($date) {
    $where1 = "WHERE date = '".$date."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where1";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$pagelist = $_GET['pagelist'];
	if($pagelist)
		$start = ($pagelist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, date,time_start,time_end,max,price,status,description FROM $tbl_name $where1 LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($pagelist == 0) $pagelist = 1;					//if no page var is given, default to 1.
	$prev = $pagelist - 1;							//previous page is page - 1
	$next = $pagelist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($pagelist > 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$prev#dates\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $pagelist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($pagelist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $pagelist && $pagelist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $pagelist - $adjacents; $counter <= $pagelist + $adjacents; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
			}
		}

		//next button
		if ($pagelist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$next#dates\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo DATES ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND ?></td>
 <td class='resint'><?php echo MAXBOOKING ?></td>
 <td class='resint'><?php echo PRICE ?></td>
 <td class='resint'><?php echo STATUS ?></td>
 <td class='resint'><?php echo DESCRIPTION ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		  switch ($row['status']) {
		    case 0:
            $status = "not available";
		    break;
		    case 1:
            $status = "available";
		    break;
		    case 2:
            $status = "Resserved";
		    break;
		    default:
            $status = "available";
            break;
		  }
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['date']."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "<td class='resint'>".$row['max']."</td>";
        echo "<td class='resint'>".$row['price']."</td>";
        echo "<td class='resint'>".$status."</td>";
        echo "<td class='resint'>".$row['description']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=edit&id=".$row['id']."#dates\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=delete&del=yes&id=".$row['id']."#dates\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>

<?php } ?>
<?php }

break;
}
?>
</div>
<div id="create">
<?php
$category = mysql_query("SELECT * FROM wp_resservation_cat");
//$rowcat = mysql_fetch_array($category)
?>
<form name="rangetime" action="admin.php?page=allbooking#create" method="POST">
<?php echo DATES ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="date1" type="text" id="date1" /><br /><br />
<?php echo MAXPLACE ?> &nbsp;<input name="max" type="text" id="max" /><br /><br />
<?php echo DESCRIPTION ?> <input name="description" type="text" id="description" /><br /><br />
<?php echo PRICE ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="price" type="text" id="price" /><br /><br />
<?php echo CATEGORY ?> &nbsp;&nbsp;&nbsp;<select name="category">
<?php
while($row1 = mysql_fetch_array($category))
        {
            /*** create the options ***/
            echo '<option value="'.$row1['id'].'"';
            echo '>'. $row1['name'] . '</option>'."\n";
        }

?>
         </select> <br /><br />
<input type="submit" name="Crea" value="create" />
</form>
<?php
if (!$_POST["create"] == "create") {
  if ($_POST["date1"]) {
  require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
$date1 = $_REQUEST["date1"];
$max = $_REQUEST["max"];
$description = $_REQUEST["description"];
$category = $_REQUEST["category"];

$settings = allbook_get_settings();

$categoryquery = mysql_query("SELECT * FROM wp_resservation_cat WHERE id= '".$category."'");
$rowcategory = mysql_fetch_array( $categoryquery );

$rgtime = $rowcategory['rangetime']." mins";
$rgtimedif = "+".$rowcategory['rangetime']." minutes";

$times = create_time_range($rowcategory['time_start_cat'], $rowcategory['time_end_cat'], $rgtime);
// esegue la query settando un errore personale in caso di fallimento
$ceck = mysql_query("SELECT * FROM wp_resservation_disp WHERE date= '".$date1."' AND category= ".$category."");
if(@mysql_num_rows($ceck) != 0)
	{
echo DATEEXIST;
} else {
print "<table>";
foreach ($times as $key => $time) {

$time_start = $times[$key] = date('H:i:s', $time);
$time_hours = $times[$key] = date("H:i",strtotime($rgtimedif,$time));
$time_min = $times[$key] = date("s",$time);
$time_end = $time_hours.":".$time_min;
$price = $_REQUEST["price"];
$status = "1";

    print "<tr>";
    echo "<td class='resint'>".$date1." - ".$times[$key] = date('H:i:s', $time)." - ".$time_end."<td>";
    echo "<td class='resint'>".$max." ".$avv." ".INSERT."</td>";
    print "</tr>";

    mysql_query("INSERT INTO wp_resservation_disp (id, date,time_start,time_end,max,price,status,description,category) VALUES('', '".$date1."', '".$time_start."','".$time_end."','".$max."','".$price."','".$status."','".$description."','".$category."') ") or die(mysql_error());
}
print "</table>";
}
}
}
?>
</div>
<div id="lisbook">
<form name="rangetime" action="admin.php?page=allbooking&pagebook=listbook#lisbook" method="POST">
<?php echo DATES ?> <input name="date2" type="text" id="date2" /><br />
<input type="submit" name="Search" value="search" />
</form>
<?php
$pagebook = $_GET['pagebook'];
switch ($pagebook) {
case 'listbook':
default:
    $date2 = $_REQUEST["date2"];
    if ($date) {
    $where = "WHERE data = '".$date2."'";
    }

	$tbl_name="wp_resservation_book";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagebook=listbook"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$booklist = $_GET['booklist'];
	if($booklist)
		$start = ($booklist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name $where LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($booklist == 0) $booklist = 1;					//if no page var is given, default to 1.
	$prev = $booklist - 1;							//previous page is page - 1
	$next = $booklist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($booklist > 1)
			$pagination.= "<a href=\"$targetpage&booklist=$prev#lisbook\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $booklist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($booklist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&booklist=$lpm1#lisbook\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=$lastpage#lisbook\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $booklist && $booklist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&booklist=1#lisbook\">1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=2#lisbook\">2</a>";
				$pagination.= "...";
				for ($counter = $booklist - $adjacents; $counter <= $booklist + $adjacents; $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&booklist=$lpm1#lisbook\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=$lastpage#lisbook\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&booklist=1#lisbook\">1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=2#lisbook\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
			}
		}

		//next button
		if ($booklist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&booklist=$next#lisbook\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo SURNAME ?></td>
 <td class='resint'><?php echo DATES ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND ?></td>
 <td class='resint'><?php echo INVOICENUMBER ?></td>
 <td class='resint'><?php echo NUMBER ?></td>
 <td class='resint'><?php echo Place ?></td>
 <td class='resint'><?php echo STATUS ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		  switch ($row['status']) {
		    case 0:
            $status = "not paid";
		    break;
		    case 1:
            $status = "Cash";
		    break;
		    case 2:
            $status = "Paid paypal";
		    break;
		    default:
            $status = "not paid";
            break;
		  }
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['nome']."</td>";
        echo "<td class='resint'>".$row['cognome']."</td>";
        echo "<td class='resint'>".$row['data']."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "<td class='resint'><a href='".PLUGIN_URL_ALLBOOK."/invoice.php?invoice=".$row['invoice']."' target='_blank'>".$row['invoice']."</a></td>";
        echo "<td class='resint'><a href='".PLUGIN_URL_ALLBOOK."/tiket.php?tiket=".$row['code']."' target='_blank'>".$row['code']."</a></td>";
        echo "<td class='resint'>".$row['price']."</td>";
        echo "<td class='resint'>".$status."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagebook=edit&id=".$row['id']."#lisbook\">".EDIT."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php
break;
case 'edit':
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
$id = $_REQUEST["id"];
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_book";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);
?>
 <table>
	<?php
		while($row = mysql_fetch_array($result))
		{
		echo '<form name="edit" action="admin.php?page=allbooking&pagebook=save#lisbook" method="post">';
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>ID</td>";
        echo "<td class='resint'><input name=\"id\" type=\"hidden\" value=\"".$row['id']."\" />".$row['id']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".DATES."</td>";
        echo "<td class='resint'>".$row['data']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMESTART."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMEEND."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".PAID."</td>";
        echo "<td class='resint'><select name=\"status\" size=\"1\">
        <option value=\"0\">".PAID1."</option>
        <option value=\"1\">".PAID2."</option>
        <option value=\"2\">".PAID3."</option>
        </select></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".NUMBER."</td>";
        echo "<td class='resint'>".$row['code']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".INVOICENUMBER."</td>";
        echo "<td class='resint'>".$row['invoice']."</td>";

        echo "</tr>";
        echo "<tr><td></td>";
        echo '<td class="resint"><input type="hidden" name="code" value="'.$row['code'].'" /><input type="submit" name="updatebook" value="save" /></td></tr>
             </form>';
		}
	?>
</table>
<?php
break;
case 'save':
$updatebook = $_REQUEST["updatebook"] == "save";
    if ($updatebook) {
$idupdatebook = $_REQUEST["id"];
    if ($idupdatebook) {
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
    if ($idupdatebook) {
    $wherebook = "WHERE id = '".$idupdatebook."'";
    }

	$tbl_name="wp_resservation_book";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    $status = $_REQUEST["status"];
    //$price = $_REQUEST["price"];
    //$status = $_REQUEST["status"];
	/* Get data. */
	$sql = "UPDATE  $tbl_name SET status = '".$status."' $wherebook";
	$result = mysql_query($sql);

?>
<?php echo RECORDSAVED ?>
<?php
$date2 = $_REQUEST["date2"];
    if ($date) {
    $where = "WHERE data = '".$date2."'";
    }

	$tbl_name="wp_resservation_book";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagebook=listbook"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$booklist = $_GET['booklist'];
	if($booklist)
		$start = ($booklist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name $where LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($booklist == 0) $booklist = 1;					//if no page var is given, default to 1.
	$prev = $booklist - 1;							//previous page is page - 1
	$next = $booklist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($booklist > 1)
			$pagination.= "<a href=\"$targetpage&booklist=$prev#lisbook\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $booklist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($booklist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&booklist=$lpm1#lisbook\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=$lastpage#lisbook\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $booklist && $booklist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&booklist=1#lisbook\">1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=2#lisbook\">2</a>";
				$pagination.= "...";
				for ($counter = $booklist - $adjacents; $counter <= $booklist + $adjacents; $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&booklist=$lpm1#lisbook\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=$lastpage#lisbook\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&booklist=1#lisbook\">1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=2#lisbook\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
			}
		}

		//next button
		if ($booklist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&booklist=$next#lisbook\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo SURNAME ?></td>
 <td class='resint'><?php echo DATES ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND ?></td>
 <td class='resint'><?php echo INVOICENUMBER ?></td>
 <td class='resint'><?php echo NUMBER ?></td>
 <td class='resint'><?php echo Place ?></td>
 <td class='resint'><?php echo STATUS ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		  switch ($row['status']) {
		    case 0:
            $status = "not paid";
		    break;
		    case 1:
            $status = "Cash";
		    break;
		    case 2:
            $status = "Paid paypal";
		    break;
		    default:
            $status = "not paid";
            break;
		  }
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['nome']."</td>";
        echo "<td class='resint'>".$row['cognome']."</td>";
        echo "<td class='resint'>".$row['data']."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "<td class='resint'><a href='".PLUGIN_URL_ALLBOOK."/invoice.php?invoice=".$row['invoice']."' target='_blank'>".$row['invoice']."</a></td>";
        echo "<td class='resint'><a href='".PLUGIN_URL_ALLBOOK."/tiket.php?tiket=".$row['code']."' target='_blank'>".$row['code']."</a></td>";
        echo "<td class='resint'>".$row['price']."</td>";
        echo "<td class='resint'>".$status."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagebook=edit&id=".$row['id']."#lisbook\">".EDIT."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php
}
}
break;
}
?>
</div>
<div id="category">
<?php echo NEWCATEGORY ?>
<form name="rangetime" action="admin.php?page=allbooking&pagecat=create#category" method="POST">
<?php echo NAME ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="name" type="text" id="name" /><br /><br />
<?php echo TIMESTART ?> &nbsp;<input name="time_start_cat" type="text" id="time_start_cat" /><br /><br />
<?php echo TIMEEND ?> &nbsp;&nbsp;&nbsp;<input name="time_end_cat" type="text" id="time_end_cat" /><br /><br />
<?php echo RANGECAT ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="range" type="text" id="range" /><br /><br />
<input type="submit" name="category" value="create" />
</form>
<?php
$pagecat = $_GET['pagecat'];
switch ($pagecat) {
case 'listcat':
default:

	$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagecat=listcat"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$categorylist = $_GET['categorylist'];
	if($categorylist)
		$start = ($categorylist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($categorylist == 0) $categorylist = 1;					//if no page var is given, default to 1.
	$prev = $categorylist - 1;							//previous page is page - 1
	$next = $categorylist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($categorylist > 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$prev#category\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $categorylist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($categorylist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $categorylist && $categorylist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $categorylist - $adjacents; $counter <= $categorylist + $adjacents; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
			}
		}

		//next button
		if ($categorylist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$next#category\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}

?>
<?php echo LISTCAT ?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND  ?></td>
 <td class='resint'><?php echo RANGECAT ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['name']."</td>";
        echo "<td class='resint'>".$row['time_start_cat']."</td>";
        echo "<td class='resint'>".$row['time_end_cat']."</td>";
        echo "<td class='resint'>".$row['rangetime']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=edit&id=".$row['id']."#category\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=delete&del=yes&id=".$row['id']."#category\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php

break;

case 'create':
if ($_POST["category"] == "create") {
  if ($_POST["name"]) {
  require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
$name = $_REQUEST["name"];
$time_start_cat = $_REQUEST["time_start_cat"];
$time_end_cat = $_REQUEST["time_end_cat"];
$range = $_REQUEST["range"];

$settings = allbook_get_settings();

// esegue la query settando un errore personale in caso di fallimento
$ceck = mysql_query("SELECT * FROM wp_resservation_cat WHERE name = '".$name."'");
if(@mysql_num_rows($ceck) != 0)
	{
echo "Data esistente";
} else {
print "<table>";

    print "<tr>";
    echo "<td class='resint'>".$name." - ".$time_start_cat." - ".$time_end_cat."<td>";
    echo "<td class='resint'>".$range." Inserito</td>";
    print "</tr>";

    //echo "INSERT INTO wp_resservation_cat (id, name,time_start_cat,time_end_cat,range) VALUES('', '".$name."', '".$time_start_cat."','".$time_end_cat."','".$range."'";

    mysql_query("INSERT INTO wp_resservation_cat (id, name,time_start_cat,time_end_cat,rangetime) VALUES('', '".$name."', '".$time_start_cat."','".$time_end_cat."','".$range."') ") or die(mysql_error());

print "</table>";
}
}
}
break;
case 'edit':
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
$id = $_REQUEST["id"];
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);
?>
 <table>
	<?php
		while($row = mysql_fetch_array($result))
		{
		echo '<form name="edit" action="admin.php?page=allbooking&pagecat=save#category" method="post">';
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>ID</td>";
        echo "<td class='resint'><input name=\"id\" type=\"hidden\" value=\"".$row['id']."\" />".$row['id']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".NAME."</td>";
        echo "<td class='resint'><input name=\"name\" type=\"text\" value=\"".$row['name']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMESTART."</td>";
        echo "<td class='resint'><input name=\"time_start_cat\" type=\"text\" value=\"".$row['time_start_cat']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMEEND."</td>";
        echo "<td class='resint'><input name=\"time_end_cat\" type=\"text\" value=\"".$row['time_end_cat']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".RANGECAT."</td>";
        echo "<td class='resint'><input name=\"rangetime\" type=\"text\" value=\"".$row['rangetime']."\" /></td>";

        echo "</tr>";
        echo "<tr><td></td>";
        echo '<td class="resint"><input type="submit" name="updatecategory" value="save" /></td></tr>
             </form>';
		}
	?>
</table>
<?php
break;
case 'save':
$updatecat = $_REQUEST["updatecategory"] == "save";
    if ($updatecat) {
$idupdatecat = $_REQUEST["id"];
    if ($idupdatecat) {
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
    if ($idupdatecat) {
    $wherecat = "WHERE id = '".$idupdatecat."'";
    }

	$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    $name = $_REQUEST["name"];
    $time_start_cat = $_REQUEST["time_start_cat"];
    $time_end_cat = $_REQUEST["time_end_cat"];
    $range = $_REQUEST["rangetime"];
	/* Get data. */
	$sql = "UPDATE  $tbl_name SET name = '".$name."', time_start_cat = '".$time_start_cat."', time_end_cat = '".$time_end_cat."', rangetime = '".$range."' $wherecat";
	$result = mysql_query($sql);

?>
<?php echo RECORDSAVED ?>
<?php
$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagecat=listcat"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$categorylist = $_GET['categorylist'];
	if($categorylist)
		$start = ($categorylist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($categorylist == 0) $categorylist = 1;					//if no page var is given, default to 1.
	$prev = $categorylist - 1;							//previous page is page - 1
	$next = $categorylist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($categorylist > 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$prev#category\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $categorylist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($categorylist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $categorylist && $categorylist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $categorylist - $adjacents; $counter <= $categorylist + $adjacents; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
			}
		}

		//next button
		if ($categorylist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$next#category\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}

?>
<br /><?php echo LISTCAT ?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND  ?></td>
 <td class='resint'><?php echo RANGECAT ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['name']."</td>";
        echo "<td class='resint'>".$row['time_start_cat']."</td>";
        echo "<td class='resint'>".$row['time_end_cat']."</td>";
        echo "<td class='resint'>".$row['rangetime']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=edit&id=".$row['id']."#category\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=delete&del=yes&id=".$row['id']."#category\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php
}
}
case 'delete':
$delete = $_REQUEST["del"] == "yes";
    if ($delete) {
$id = $_REQUEST["id"];
    if ($id) {
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    //$max = $_REQUEST["max"];
    //$price = $_REQUEST["price"];
    //$status = $_REQUEST["status"];
    //$description = $_REQUEST["description"];
	/* Get data. */
	$sql = "DELETE FROM $tbl_name $where";
	$result = mysql_query($sql);

?>
<?php echo RECORDDELETE ?>
<?php
$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagecat=listcat"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$categorylist = $_GET['categorylist'];
	if($categorylist)
		$start = ($categorylist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($categorylist == 0) $categorylist = 1;					//if no page var is given, default to 1.
	$prev = $categorylist - 1;							//previous page is page - 1
	$next = $categorylist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($categorylist > 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$prev#category\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $categorylist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($categorylist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $categorylist && $categorylist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $categorylist - $adjacents; $counter <= $categorylist + $adjacents; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
			}
		}

		//next button
		if ($categorylist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$next#category\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}

?>
<br /><?php echo LISTCAT ?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND  ?></td>
 <td class='resint'><?php echo RANGECAT ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['name']."</td>";
        echo "<td class='resint'>".$row['time_start_cat']."</td>";
        echo "<td class='resint'>".$row['time_end_cat']."</td>";
        echo "<td class='resint'>".$row['rangetime']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=edit&id=".$row['id']."#category\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=delete&del=yes&id=".$row['id']."#category\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php
}
}
break;
}
?>
</div>
<div id="help">
How it works:
<br /><br />
Once the plugin is installed you can see the "Allbooking" button on the sidebar of your wordpress administration panel, click it to open the options panel
<br /><br />
Configuration tab:
<br />
    * setup width and height of the calendar <br />
    * insert Paypal address, currencies and activate  <br />
    * Insert custom email messages to your customer <br />
    * Insert custom invoice details <br /><br />

Category tab: <br />

    * Create your new category and set time range (min 30 minutes)   <br />

Create availability tab:  <br /><br />

    * select date<br />
    * setup max places available <br />
    * Insert a description an the price <br />
    * select a category previously created<br /><br />

List date tab:<br /><br />

    * you can edit or delete the availability <br /><br />

History tab:<br /><br />

    * Monitor your reservation<br />

Insert the availability calendar in you page or post:<br />

Open a new page,write your content (if you want) and click on the "Code" button in the editor, publish the page and you can see the reservation system in action!
<br />
Simple and effective. <br />
</div>
</div>
<?php


	return 	$content;
}


function allbook_adm_settings()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_configuration()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_siteconf()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_menumanager()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_hotel()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_roomtype()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_seasons()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_booking()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_avv()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_history()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_stat()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_lang()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_tempemail()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_constants()
{
	global $wpdb;

	$content="";

	echo $content;
}


function allbook_treesort ($arr)
{

	for ($i=0;$i<count($arr)-1;$i++) {
		$flag=$i;
		for ($j=$i+1;$j<count($arr);$j++) {
			if ($arr[$j][1]==$arr[$i][0])
			{
				array_splice($arr,$flag+1,0,array(($arr[$j])));
				unset($arr[$j+1]);
				$arr = array_values($arr);
				$flag=$flag+1;
			}
			//echo $j."-".$arr[$j][0]."<br>";
		}
		//echo "<br>";

	}


$map=array();
	for ($k=0;$k<count($arr);$k++)
	{
		if ($arr[$k][1]==0) array_push($map,0);
		$z=1;
		foreach($arr as $key) {
			if($arr[$k][1]==$key[0]) {
				array_push($map,$z);
			}
			$z++;
		}

	}


	return array($arr, $map);
}

function allbook_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;

   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_allbook_tinymce_plugin");
     add_filter('mce_buttons', 'register_allbook_button');
   }
}

function register_allbook_button($buttons) {
   array_push($buttons, "separator", "allbook");
   return $buttons;
}

// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_allbook_tinymce_plugin($plugin_array) {
   $plugin_array['allbook'] = get_option('siteurl').'/wp-content/plugins/wp-allbooking/editor_plugin.js';
   return $plugin_array;
}

function allbook_change_tinymce_version($version) {
	return ++$version;
}
// Modify the version when tinyMCE plugins are changed.
add_filter('tiny_mce_version', 'allbook_change_tinymce_version');
// init process for button control
add_action('init', 'allbook_addbuttons');

?>