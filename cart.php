<?php
session_start();
define('WP_USE_THEMES', true);
require('../../wp-load.php');
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');

function writeCart() {
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');
$cart = $_SESSION['cart'];
if (!$cart) {
return '<p>You have no items in the cart</p>';
} else {
// analyze the cart session variable
$items = explode(',',$cart);
$s = (count($items) > 1) ? 's':'';
return '<p>You have <a href="'.$url.'/index.php?page_id='.$post_id.'&pager=cart">'.count($items).' item'.$s.' in the shopping cart</a></p>';
}
}

function showCart() {
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');
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
$output[] = '<form action="'.$url.'/index.php?page_id='.$post_id.'&pager=cart&action=update" method="post" id="cart">';
$output[] = '<table class ="tablecart">';
foreach ($contents as $id=>$qty) {
$sql = 'SELECT * FROM wp_resservation_disp WHERE id = '.$id;
$result = mysql_query($sql);
//$row = $result->getArray();
$row = mysql_fetch_array($result);


extract($row);
if ($max > $qty) {
$output[] = '<tr>';
$output[] = '<td class="tdcart"><a href="'.$url.'/index.php?page_id='.$post_id.'&pager=cart&action=delete&id='.$id.'" class="r">'.REMOVE.'</a></td>';
$output[] = '<td class="tdcart">&nbsp;&nbsp;<span class="tablespan">'.HOURS.'</span> &nbsp;'.$time_start.' &nbsp;&nbsp;<span class="tablespan">'.DATES.':</span> '.$date.'</td>';
$output[] = '<td class="tdcart"><span class="tablespan">'.PRICE.'</span> &nbsp;$'.$price.'</td>';
$output[] = '<td class="tdcart"><span class="tablespan">'.NRPALACES.'</span> &nbsp;<input type="text" name="qty'.$id.'" value="'.$qty.'" size="3" maxlength="3" /></td>';
//$output[] = '<td class="tdcart">$'.($price * $qty).'</td>';
$total += $price * $qty;
$output[] = '</tr>';
} else {
$output[] = '<tr>';
$output[] = '<td class="tdcart"><a href="'.$url.'/index.php?page_id='.$post_id.'&pager=cart&action=delete&id='.$id.'" class="r">'.REMOVE.'</a></td>';
$output[] = '<td class="tdcart">&nbsp;&nbsp;<span class="tablespan">'.HOURS.'</span> &nbsp;'.$time_start.' &nbsp;&nbsp;'.DATES.': '.$date.'</td>';
$output[] = '<td class="tdcart"><span class="tablespan">'.PRICE.' $</span>'.$price.'</td>';
$output[] = '<td class="tdcart"><span class="tablespan">'.NRPALACES.'</span> &nbsp;<input type="text" name="qty'.$id.'" value="'.$max.'" size="3" maxlength="3" /></td>';
//$output[] = '<td class="tdcart">$'.($price * $max).'</td>';
$output[] = '<td class="tdcart"><span class="tablespan">&nbsp;'.ERRORQTY.'</span></td>';
$total += $price * $max;
$output[] = '</tr>';
}
}
$output[] = '</table>';
$output[] = '<p>'.GRTOTAL.': $'.$total.'</p>';
$output[] = '<p style="text-align:left"><button type="submit" class="update">'.UPDATECART.'</button></p>';
$output[] = '</form>';
$output[] = '<form action="'.$url.'/index.php?page_id='.$post_id.'&pager=checkout" method="post" id="cart">';
foreach ($contents as $id=>$qty) {
$sql = 'SELECT * FROM wp_resservation_disp WHERE id = '.$id;
$result = mysql_query($sql);
//$row = $result->getArray();
$row = mysql_fetch_array($result);


extract($row);
if ($max > $qty) {
$output[] = '<td><input name="id['.$id.']" type="hidden" value="'.$id.'" /></td>';
$output[] = '<td><input name="qty['.$id.']" type="hidden" value="'.$qty.'" /></td>';
$output[] = '<td><input name="date['.$id.']" type="hidden" value="'.$date.'" /></td>';
$output[] = '<td><input name="time_start['.$id.']" type="hidden" value="'.$time_start.'" /></td>';
$output[] = '<td><input name="time_end['.$id.']" type="hidden" value="'.$time_end.'" /></td>';
$output[] = '<td><input name="price['.$id.']" type="hidden" value="'.$price.'" /></td>';
$total1 += $price * $qty;
} else {
$output[] = '<td><input name="id['.$id.']" type="hidden" value="'.$id.'" /></td>';
$output[] = '<td><input name="qty['.$id.']" type="hidden" value="'.$max.'" /></td>';
$output[] = '<td><input name="date['.$id.']" type="hidden" value="'.$date.'" /></td>';
$output[] = '<td><input name="time_start['.$id.']" type="hidden" value="'.$time_start.'" /></td>';
$output[] = '<td><input name="time_end['.$id.']" type="hidden" value="'.$time_end.'" /></td>';
$output[] = '<td><input name="price['.$id.']" type="hidden" value="'.$price.'" /></td>';
$total1 += $price * $max;
}
}
$output[] = '<td><input name="total" type="hidden" value="'.$total1.'" /></td>';
$output[] = '<p style="text-align:left"><button class="button" id="submit" type="submit" name="checkout" value="checkout"><span>'.CHECKKOUT.'</span></button></p>';
$output[] = '</form>';
} else {
$output[] = '<p>You shopping cart is empty.</p>';
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

<?php

echo showCart();

?>

<?php //$db->Close();  ?>
