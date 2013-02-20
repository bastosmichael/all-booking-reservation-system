<?

define('WP_USE_THEMES', true);
require('../../../wp-load.php');
global $wp_query, $post;

require("ipn_cls.php");
$item_number = isset($_POST["item_number"]) ? $_POST["item_number"] : '';
$booking_id = isset($_POST["custom"]) ? $_POST["custom"] : "";
$item_name = isset($_POST["item_name"]) ? $_POST["item_name"] : "";


// --- Get PayPal response

$paypal_info = $_REQUEST;
$paypal_ipn = new paypal_ipn($paypal_info);
$msg = "";
$error = "";

$myFile = "testPayment.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
//
$stringData .= "<br>POST<br>";
foreach ($HTTP_POST_VARS as $key => $value) {
    $stringData .= $key . "=" . $value . "<br>\n";
}
$stringData .= "<br>===============<br>";
$stringData .= "<br>GET<br>";
foreach ($HTTP_GET_VARS as $key => $value) {
    $stringData .= $key . "=" . $value . "<br>\n";
}
$stringData .= "<br>===============<br>";
//$stringData .= "<br>SESSION<br>";
//foreach ($_SESSION as $key => $value) {
//    $stringData .= $key . "=" . $value . "<br>\n";
//}
//echo $stringData;
//exit();
//$myfile2="testSQL.txt";
//$fSQL = fopen($myFile2, 'a') or die("can't open file");
//fwrite($fh, $stringData);
//fclose($fh);
//fwrite($fh, "in payemnet<br>");


// Get payment status
$status = $paypal_ipn->get_payment_status();
if (trim($status) == "")  $status = isset($_REQUEST['st']) ? $_REQUEST['st'] : "";
    switch ($status)
    {
        case 'Pending':
            $pending_reason = $paypal_ipn->paypal_post_vars['pending_reason'];
            if ($pending_reason != "intl")
                $msg = "Pending Payment - $pending_reason";
            $insert_status = "1";
            break;
        case 'Completed':
            $amount = isset($paypal_ipn->paypal_post_vars['mc_gross']) ? floatval($paypal_ipn->paypal_post_vars['mc_gross']) : "";
            if (trim($amount) == "" || trim($amount) == "0")
                $amount = number_format(isset($_REQUEST['amt']) ? $_REQUEST['amt'] : "", 2, ".", "");
            // Check if amount is valid
            
            if ($amount == "" || $amount == "0" || ! is_numeric($amount)) {
                $error = "Incorrect payment amount!";
            } else {
                // Approve the customer subscription status        
                
            }
			$transaction_date = isset($paypal_ipn->paypal_post_vars['payment_date']) ? $paypal_ipn->paypal_post_vars['payment_date'] : "";           			
	        $transaction_number = isset($paypal_ipn->paypal_post_vars['txn_id']) ? $paypal_ipn->paypal_post_vars['txn_id'] : "";           
	        $payer_status = isset($paypal_ipn->paypal_post_vars['payer_status']) ? $paypal_ipn->paypal_post_vars['payer_status'] : "";           
	        $payment_instrument = ($payer_status == "verified") ? "1" : "2";
	        //0 - Unknown , 1 - PayPal, 2 - Credit Card
			
			break;
        case 'Updated':
            // Updated already
            $msg = "Thank you for your order!<br><br>";
            $insert_status = "3";
            break;
        case 'Failed':
            // This will only happen in case of echeck.
            $msg = "Payment Failed";
            $error = $msg;
            $insert_status = "4";
            break;
        case 'Denied':
            // Denied payment by us
            $msg = "Payment Denied";
            $error = $msg;
            $insert_status = "5";
            break;
        case 'Refunded':
            // Payment refunded by us
            $msg = "Payment Refunded";
            $insert_status = "6";
            break;
        case 'Canceled':
          /* Reversal cancelled
           mark the payment as dispute cancelled */
            $msg = "Cancelled reversal";
            $error = $msg;
            $insert_status = "7";
            break;
        default:
            // Order is not good
            $msg = "Unknown Payment Status - please try again."; // . $paypal_ipn->get_payment_status();
            $error = $msg;
            $insert_status = "8";
            break;
    }

if (!empty($item_number) && $status=="Completed") {
// Add payment details to the database
//fwrite($fh, "in if<br>");
//fclose($fSQL);
    $sql = "UPDATE wp_resservation_invoice SET
				trans_id='".$transaction_number."',
				trans_date='".date("Y-m-d H:i:s",strtotime($transaction_date))."',
				status=2
				WHERE invocie='".$item_number."'";
	echo $sql;
    mysql_query($sql);

    $sql1 = "UPDATE wp_resservation_book SET
				status=2
				WHERE invocie='".$item_number."'";
	echo $sql1;
    mysql_query($sql1);

}


?>