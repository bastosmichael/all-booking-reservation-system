<?php
define('WP_USE_THEMES', true);
require('../../wp-load.php');
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');

$checkout = $_REQUEST["checkout"];
    if ($checkout == "checkout") {
?>

<?php
	/*
		Place code to connect to your DB here.
	*/
	//require_once("libs/SQLManager.class.php");
    require_once("libs/class.book.php");
    //$db = new SQLManager(true, "", "log.txt", true);
    //$db->Open("localhost", "danese_wpbook", "gqdxz+E;!u*z", "danese_wpbook");
    $id = $_REQUEST["id"];
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, date,time_start,time_end,max,price,status FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);


$settings = allbook_get_settings();
?> <form name="bookingsave" action="<?php echo ''.$url.'/index.php?page_id='.$post_id.'&pager=conf' ?>" method="POST" onSubmit="return order_validate();">

 <table>
   <tr>
     <td><?php echo NAME ?></td>
     <td><input name="nome" id="nome" type="text" /></td>
   </tr>
   <tr>
     <td><?php echo SURNAME ?></td>
     <td><input name="cognome" id="cognome" type="text" /></td>
   </tr>
   <tr>
     <td><?php echo ADDRESS ?></td>
     <td><input name="indirizzo" id="indirizzo" type="text" /></td>
   </tr>
   <tr>
     <td><?php echo ZIP ?></td>
     <td><input name="cap" id="cap" type="text" /></td>
   </tr>
   <tr>
     <td><?php echo CITY ?></td>
     <td><input name="citta" id="citta" type="text" /></td>
   </tr>
   <tr>
     <td><?php echo PROVINCE ?></td>
     <td><input name="provincia" id="provincia" type="text" /></td>
   </tr>
   <tr>
     <td><?php echo COUNTRY ?></td>
     <td><input name="nazione" id="nazione" type="text" /></td>
   </tr>
   <tr>
     <td><?php echo PHONE ?></td>
     <td><input name="telefono" id="telefono" type="text" /></td>
   </tr>
   <tr>
     <td><?php echo EMAIL ?></td>
     <td><input name="email" id="email" type="text" /></td>
   </tr>
   <tr>
     <td><?php echo NOTE ?></td>
     <td><textarea name="note"></textarea></td>
   </tr>
   <tr>
     <td><?php echo PAYTYPE ?></td>
     <td>&nbsp;&nbsp;&nbsp;<?php echo CASH ?> <input type="radio" name="typepag" value="1" /> &nbsp;&nbsp;&nbsp;<?php echo PAYPAL ?> <input type="radio" name="typepag" value="2" /></td>
   </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		}
$data_array = $_REQUEST["id"];
$qty = $_REQUEST["qty"];
$date = $_REQUEST["date"];
$time_start = $_REQUEST["time_start"];
$time_end = $_REQUEST["time_end"];
$price = $_REQUEST["price"];
$total = $_REQUEST["total"];
foreach($data_array as $key => $value) {
$output = '<tr>';
$output .= '<td><input name="id['.$key.']" type="hidden" value="'.$value[$key].'" /></td>';
$output .= '<td><input name="qty['.$key.']" type="hidden" value="'.$qty[$key].'" /></td>';
$output .= '<td><input name="date['.$key.']" type="hidden" value="'.$date[$key].'" /></td>';
$output .= '<td><input name="time_start['.$key.']" type="hidden" value="'.$time_start[$key].'" /></td>';
$output .= '<td><input name="time_end['.$key.']" type="hidden" value="'.$time_end[$key].'" /></td>';
$output .= '<td><input name="price['.$key.']" type="hidden" value="'.$price[$key].'" /></td>';
$output .= '<td><input name="total" type="hidden" value="'.$total.'" /></td>';
$output .= '</tr>';
echo $output;
}
	?>
<tr>
<td></td>
<td><button class="button" type="submit" name="conferma" value="send" ><?php echo SEND ?></button></td>
</tr>
</table>
</form>
<script language="javascript">

function isEmpty(s) {
    return ((s == null) || (s.length == 0))
}

function order_validate(){
    var whitespace = " \t\n\r";

    if(isEmpty(document.getElementById("nome").value)){
        alert("<?php echo INSERTNAME ?>");
        return false;
    }
    if(isEmpty(document.getElementById("cognome").value)){
        alert("<?php echo INSERTSURNAME ?>");
        return false;
    }
    <?php
    if ($settings['allbook_indirizzo'] == 1) {
    ?>
    if(isEmpty(document.getElementById("indirizzo").value)){
        alert("<?php echo INSERTADDRESS ?>");
        return false;
    }
    <?php
    }
    ?>
     <?php
    if ($settings['allbook_cap'] == 1) {
    ?>
    if(isEmpty(document.getElementById("cap").value)){
        alert("<?php echo INSERTZIP ?>");
        return false;
    }
    <?php
    }
    ?>
    <?php
    if ($settings['allbook_citta'] == 1) {
    ?>
    if(isEmpty(document.getElementById("citta").value)){
        alert("<?php echo INSERTCITY ?>");
        return false;
    }
    <?php
    }
    ?>
    <?php
    if ($settings['allbook_provincia'] == 1) {
    ?>
    if(isEmpty(document.getElementById("provincia").value)){
        alert("<?php echo INSERTPROVINCE ?>");
        return false;
    }
    <?php
    }
    ?>
    <?php
    if ($settings['allbook_nazione'] == 1) {
    ?>
    if(isEmpty(document.getElementById("nazione").value)){
        alert("<?php echo INSERTCOUNTRY ?>");
        return false;
    }
    <?php
    }
    ?>
    <?php
    if ($settings['allbook_telefono'] == 1) {
    ?>
    if(isEmpty(document.getElementById("telefono").value)){
        alert("<?php echo INSERTPHONE ?>");
        return false;
    }
    <?php
    }
    ?>
    if(isEmpty(document.getElementById("email").value)){
        alert("<?php echo INSERTEMAIL ?>");
        return false;
    }

    var objRegExp  =/^\w+[\+\.\w-]*@([\w-]+\.)*\w+[\w-]*\.([a-z]{2,4}|\d+)$/i
    if (objRegExp.test(document.getElementById("email").value)==false) {
        alert('<?php echo INSERTEMAIL1 ?>');return false;
    }


    //document.getElementById("contbtn").disabled=true
    return true;
}
function LUNCheck(number) {
	var i, sum, weight;
	sum=0;
	for (i = 0; i < number.length - 1; i++) {
			weight = number.substr(number.length - (i + 2), 1) * (2 - (i % 2));
			sum += ((weight < 10) ? weight : (weight - 9));
	}

	if (parseInt(number.substr(number.length-1)) == ((10 - sum % 10) % 10)) {
			return (true);
	} else {
			return (false);
	}
}
function show_image(what, w, h) {
    w=window.open(what,"Image","width=" + w + ",height=" + h + ",scrollbars=YES,resizeable=YES",1);
}
function stripWhitespace (s) {
	return stripCharsInBag (s, " \t\n\r")
}
function stripCharsInBag (s, bag){
    var i;
    var returnString = "";
    for (i = 0; i < s.length; i++)
    {
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }

    return returnString;
}

</script>
<?php //$db->Close();  ?>

<?php } ?>