<?php
session_start();
define('WP_USE_THEMES', false);
require('../../../wp-load.php');
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');
$settings = allbook_get_settings();

function writeCart() {
$cart = $_SESSION['cart'];
if (!$cart) {
return '<p>You have no items in the cart</p>';
} else {
// analyze the cart session variable
$items = explode(',',$cart);
$s = (count($items) > 1) ? 's':'';
return '<p>You have <a href="cart.php">'.count($items).' item'.$s.' in the shopping cart</a></p>';
}
}

function showCart() {
//require_once("libs/SQLManager.class.php");
    require_once("libs/class.book.php");
    //$db = new SQLManager(true, "", "log.txt", true);
    //$db->Open("localhost", "danese_wpbook", "gqdxz+E;!u*z", "danese_wpbook");

$cart = $_SESSION['cart'];
if ($cart) {
$items = explode(',',$cart);
$contents = array();
foreach ($items as $item) {
$contents[$item] = (isset($contents[$item])) ? $contents[$item] + 1 : 1;
}
$output[] = '<form action="addajax.php?action=update" method="post" id="cart">';
$output[] = '<table class="tablecart">';
foreach ($contents as $id=>$qty) {
$sql = 'SELECT * FROM wp_resservation_disp WHERE id = '.$id;
$result = mysql_query($sql);
//$row = $result->getArray();
$row = mysql_fetch_array($result);


extract($row);
if ($max > $qty) {
$output[] = '<tr>';
$output[] = '<td class="tdcart"><a href="addajax.php?action=delete&id='.$id.'" class="r">'.REMOVE.'</a></td>';
$output[] = '<td class="tdcart">&nbsp;&nbsp;<span class="tablespan">'.HOURS.'</span> &nbsp;'.$time_start.' &nbsp;<span class="tablespan">'.DATES.':</span> '.$date.'</td>';
$output[] = '<td class="tdcart"><span class="tablespan">'.PRICE.'</span> &nbsp;$'.$price.'</td>';
$output[] = '<td class="tdcart"><span class="tablespan">'.NRPALACES.'</span> &nbsp;<input type="text" name="qty'.$id.'" value="'.$qty.'" size="3" maxlength="3" /></td>';
//$output[] = '<td class="tdcart">$'.($price * $qty).'</td>';
$total += $price * $qty;
$output[] = '</tr>';
} else {
$output[] = '<tr>';
$output[] = '<td class="tdcart"><a href="addajax.php?action=delete&id='.$id.'" class="r">'.REMOVE.'</a></td>';
$output[] = '<td class="tdcart">&nbsp;&nbsp;<span class="tablespan">'.HOURS.'</span> &nbsp;'.$time_start.' &nbsp;<span class="tablespan">'.DATES.': '.$date.'</td>';
$output[] = '<td class="tdcart"><span class="tablespan">'.PRICE.'</span> &nbsp;$'.$price.'</td>';
$output[] = '<td class="tdcart"><span class="tablespan">'.NRPALACES.'</span> &nbsp;<input type="text" name="qty'.$id.'" value="'.$max.'" size="3" maxlength="3" /></td>';
//$output[] = '<td class="tdcart">$'.($price * $max).'</td>';
$output[] = '<td class="tdcart"><span class="tablespan">&nbsp;'.ERRORQTY.'</span></td>';
$total += $price * $max;
$output[] = '</tr>';
}
}
$output[] = '</table>';
$output[] = '<p>'.GRTOTAL.': $'.$total.'</p>';
$output[] = '<div><button type="submit">'.UPDATECART.'</button> &nbsp;<button type="button" onclick="closeurl();" value="checkout">'.CHECKKOUT.'</button></div>';
$output[] = '<div></div>';
$output[] = '</form>';
} else {
$output[] = '<p>'.CARTEMPITY.'</p>';
}
return join('',$output);
}


$cart = $_SESSION['cart'];
$action = $_GET['action'];
switch ($action) {
case 'add':
if ($cart) {
$cart .= ','.$_GET['id'];
} else {
$cart = $_GET['id'];
}
break;
case 'delete':
if ($cart) {
$items = explode(',',$cart);
$newcart = '';
foreach ($items as $item) {
if ($_GET['id'] != $item) {
if ($newcart != '') {
$newcart .= ','.$item;
} else {
$newcart = $item;
}
}
}
$cart = $newcart;
}
break;

case 'update':
if ($cart) {
$newcart = '';
foreach ($_POST as $key=>$value) {
if (stristr($key,'qty')) {
$id = str_replace('qty','',$key);
$items = ($newcart != '') ? explode(',',$newcart) : explode(',',$cart);
$newcart = '';
foreach ($items as $item) {
if ($id != $item) {
if ($newcart != '') {
$newcart .= ','.$item;
} else {
$newcart = $item;
}
}
}
for ($i=1;$i<=$value;$i++) {
if ($newcart != '') {
$newcart .= ','.$id;
} else {
$newcart = $id;
}
}
}
}
}
$cart = $newcart;
break;

}
$_SESSION['cart'] = $cart;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>Cart</title>
  <link href="<?php echo PLUGIN_URL_ALLBOOK ?>/css/style/style.css" rel="stylesheet" type="text/css" />
 <script type="text/javascript">

function closeurl() {
url = 'myPage.html';
parent.jQuery.fancybox.close();
parent.window.location = '<?php echo $url ?>/index.php?page_id=<?php echo $settings["allbook_pageid"]; ?>&pager=cart';
};
</script>
</head>

<body>

<?php

echo showCart();

?>

<?php //$db->Close();  ?>
</body>

</html>