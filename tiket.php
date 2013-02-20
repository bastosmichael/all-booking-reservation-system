<?php
define('WP_USE_THEMES', false);
require('../../../wp-load.php');
$tiket = $_REQUEST["tiket"];
    if ($tiket) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title><?php echo TITLEBOOK ?></title>
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
    $tiket = $_REQUEST["tiket"];
    if ($tiket) {
    $where = "WHERE code = '".$tiket."'";
    }

	$tbl_name="wp_resservation_book";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);

		while($row = mysql_fetch_array($result))
		{

?>

 <table>
    <tr>
     <td><?php echo BOOKSUMMARY ?> </td>
     <td> <?php echo $row['code']  ?></td>
   </tr>
   <tr>
     <td><?php echo NAME ?></td>
     <td><?php echo $row['nome'] ?></td>
   </tr>
   <tr>
     <td><?php echo SURNAME ?></td>
     <td><?php echo $row['cognome'] ?></td>
   </tr>
   <tr>
     <td><?php echo ADDRESS ?></td>
     <td><?php echo $row['indirizzo'] ?></td>
   </tr>
   <tr>
     <td><?php echo ZIP ?></td>
     <td><?php echo $row['cap'] ?></td>
   </tr>
   <tr>
     <td><?php echo CITY ?></td>
     <td><?php echo $row['citta'] ?></td>
   </tr>
   <tr>
     <td><?php echo PROVINCE ?></td>
     <td><?php echo $row['prov'] ?></td>
   </tr>
   <tr>
     <td><?php echo COUNTRY ?></td>
     <td><?php echo $row['nazione'] ?></td>
   </tr>
   <tr>
     <td><?php echo PHONE ?></td>
     <td><?php echo $row['telefono'] ?></td>
   </tr>
   <tr>
     <td><?php echo EMAIL ?><</td>
     <td><?php echo $row['email'] ?></td>
   </tr>
   <tr>
     <td><?php echo NOTE ?></td>
     <td><?php echo $row['note'] ?></td>
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
$date_book = date('Y-m-d', strtotime($row['data']));
$output = '<tr>';
$output .= '<td>'.TIMESTART.' '.$row['time_start'].' '.TIMEEND.''.$row['time_end'].' '.DATES.': '.$date_book.' '.CODEBOOK.': '.$row['code'].'</td>';
$output .= '<td><input name="id" type="hidden" value="'.$row['id'].'" /></td>';
$output .= '<td><input name="date" type="hidden" value="'.$row['date'].'" /></td>';
$output .= '<td><input name="time_start" type="hidden" value="'.$row['time_start'].'" /></td>';
$output .= '<td><input name="time_end" type="hidden" value="'.$row['time_end'].'" /></td>';
$output .= '</tr>';
$output .= '<tr><td>'.CODEBOOKBAR.'</td></tr>';
$output .= '<tr><td><img src="'.PLUGIN_URL_ALLBOOK.'/libs/classphp/generate.php?idcode='.$row['code'].'" alt="" /></td></tr>';
echo $output;
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