<?php
$wpconfig = realpath("../../../wp-config.php");
if (!file_exists($wpconfig))  {
	echo "Could not found wp-config.php. Error in path :\n\n".$wpconfig ;
	die;
}
require_once($wpconfig);
require_once(ABSPATH.'/wp-admin/admin.php');
global $wpdb;
//$settings = jsvideo_get_settings();
$category = mysql_query("SELECT * FROM wp_resservation_cat");
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Insert All booking</title>
<!-- 	<meta http-equiv="Content-Type" content="<?php// bloginfo('html_type'); ?>; charset=<?php //echo get_option('blog_charset'); ?>" /> -->
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-content/plugins/wp-allbooking/tinymce.js"></script>
    <base target="_self" />
</head>
		<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="allbook" action="#">
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
			<td nowrap="nowrap"><label for="allbook_main"><?php _e("Select Tag", 'allbook_main'); ?></label></td>
			<td><select id="allbook_lang" name="allbook_main" style="width: 200px">
            <option value="ALLBOOK"><?php _e("All booking", 'allbook_main'); ?></option>
            <?php
            while($row1 = mysql_fetch_array($category))
            {
            /*** create the options ***/
            echo '<option value="CATBOOK '.$row1['id'].'"';
            echo '>'. $row1['name'] . '</option>'."\n";
            }

?>
            </select></td>
          </tr>
          <tr>
			<td nowrap="nowrap" valign="top"><label for="showtype"><?php _e("Show Line Number", 'allbook_main'); ?></label></td>
            <td><label><input name="showtype" id='allbook_linenumbers' type="checkbox" checked="checked" /></label></td>
          </tr>
        </table>
	<div class="mceActionPanel">
		<div style="float: left">
			    <input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'allbook_main'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
				<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'allbook_main'); ?>" onclick="insertallbookcode();" />
		</div>
	</div>
</form>
</body>
</html>