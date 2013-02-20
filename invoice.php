<?php
define('WP_USE_THEMES', false);
require('../../../wp-load.php');
$invoice = $_REQUEST["invoice"];
    if ($invoice) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title><?php echo TITLEINVOICE ?></title>
</head>

<body>
<link type="text/css" href="http://lavori.joomlaskin.it/resservation/calendar/calendar/css/smoothness/jquery-ui-1.8.9.front.css" rel="Stylesheet" />
<script type="text/javascript" src="http://lavori.joomlaskin.it/resservation/calendar/calendar/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="http://lavori.joomlaskin.it/resservation/calendar/calendar/js/jquery-ui-1.8.9.custom.min.js"></script>
<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/
jQuery.noConflict();
jQuery(function() {

jQuery('#date').datepicker({ dateFormat: 'yy-mm-dd' });

});

/*]]>*/
</script>


<?php
	/*
		Place code to connect to your DB here.
	*/
	//require_once("libs/SQLManager.class.php");
    require_once("libs/class.book.php");
    //$db = new SQLManager(true, "", "log.txt", true);
    //$db->Open("localhost", "danese_wpbook", "gqdxz+E;!u*z", "danese_wpbook");
    $invoice = $_REQUEST["invoice"];
    if ($invoice) {
    $where = "WHERE invocie = '".$invoice."'";
    }

	$tbl_name="wp_resservation_invoice";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);

		while($row = mysql_fetch_array($result))
		{
    $where1 = "WHERE invoice = '".$invoice."'";
    $sql1 = "SELECT * FROM wp_resservation_book $where1 LIMIT 1";
	$result1 = mysql_query($sql1);

		while($row1 = mysql_fetch_array($result1))
		{

?>

 <table>
    <tr>
     <td><?php echo INVOICENR ?></td>
     <td> <?php echo $row1['invoice']  ?></td>
   </tr>
   <tr>
     <td><?php echo NAME ?></td>
     <td><?php echo $row1['nome'] ?></td>
   </tr>
   <tr>
     <td><?php echo SURNAME ?></td>
     <td><?php echo $row1['cognome'] ?></td>
   </tr>
   <tr>
     <td><?php echo ADDRESS ?></td>
     <td><?php echo $row1['indirizzo'] ?></td>
   </tr>
   <tr>
     <td><?php echo ZIP ?></td>
     <td><?php echo $row1['cap'] ?></td>
   </tr>
   <tr>
     <td><?php echo CITY ?></td>
     <td><?php echo $row1['citta'] ?></td>
   </tr>
   <tr>
     <td><?php echo PROVINCE ?></td>
     <td><?php echo $row1['prov'] ?></td>
   </tr>
   <tr>
     <td><?php echo COUNTRY ?></td>
     <td><?php echo $row1['nazione'] ?></td>
   </tr>
   <tr>
     <td><?php echo PHONE ?></td>
     <td><?php echo $row1['telefono'] ?></td>
   </tr>
   <tr>
     <td><?php echo EMAIL ?></td>
     <td><?php echo $row1['email'] ?></td>
   </tr>
   <tr>
     <td><?php echo NOTE ?></td>
     <td><?php echo $row1['note'] ?></td>
   </tr>
   <tr>
     <td><?php echo PAYTYPE ?></td>
     <?php
     switch ($row1['status']) {
		    case 0:
            $status = PAID1;
		    break;
		    case 1:
            $status = PAID2;
		    break;
		    case 2:
            $status = PAID3;
		    break;
		    default:
            $status = PAID1;
            break;
		  }
     ?>
     <td><?php echo $status  ?></td>
   </tr>
	<?php
    $where2 = "WHERE invoice = '".$invoice."'";
    $sql2 = "SELECT * FROM wp_resservation_book $where2";
	$result2 = mysql_query($sql2);

		while($row2 = mysql_fetch_array($result2))
		{
$date_book = date('Y-m-d', strtotime($row2['data']));
$id = $row2['id'];
$output = '<tr>';
$output .= '<td>'.TIMESTART.' '.$row2['time_start'].' '.TIMEEND.' '.$row2['time_end'].' '.DATES.': '.$date_book.' '.CODEBOOK.': <a href="'.PLUGIN_URL_ALLBOOK.'/tiket.php?tiket='.$row2['code'].'">'.$row2['code'].'</a></td>';
$output .= '<td><input name="id['.$id.']" type="hidden" value="'.$row2['id'].'" /></td>';
$output .= '<td><input name="date['.$id.']" type="hidden" value="'.$row2['date'].'" /></td>';
$output .= '<td><input name="time_start['.$id.']" type="hidden" value="'.$row2['time_start'].'" /></td>';
$output .= '<td><input name="time_end['.$id.']" type="hidden" value="'.$row2['time_end'].'" /></td>';
$output .= '</tr>';
echo $output;
}
}
echo '<tr><td>Total price $ '.$row['price'].'</td></tr>';
echo '<tr><td></td></tr>';
}
//echo "<tr><td>Total $ ".$total."</td><td></td></tr>";

//mail("francodanese60@gmail.com", "Nuova prenotazione", "Nuova prenotazione inserita ");
//mail("info@lucazone.net", "Nuova prenotazione", "Nuova prenotazione inserita");
	?>
<tr>
<td></td>
<td></td>
</tr>
</table>

<?php //$db->Close();  ?>
</body>

</html>
<?php } ?>