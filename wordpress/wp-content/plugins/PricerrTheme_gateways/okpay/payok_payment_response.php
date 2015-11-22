<?php

function payok_listing_response()
{
	
	
	/* Check IPN and process payment */
	error_reporting(E_ALL ^ E_NOTICE); 
	
	// Read the post from OKPAY and add 'ok_verify' 
	$request = 'ok_verify=true'; 
	
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$request .= "&$key=$value";
	}
	
	$fsocket = false;
	$result = false;
	
	// Try to connect via SSL due sucurity reason
	if ( $fp = @fsockopen('ssl://www.okpay.com', 443, $errno, $errstr, 30) ) {
		// Connected via HTTPS
		$fsocket = true;
	} elseif ($fp = @fsockopen('www.okpay.com', 80, $errno, $errstr, 30)) {
		// Connected via HTTP
		$fsocket = true;
	}
	
	// If connected to OKPAY
	if ($fsocket == true) {
		$header = 'POST /ipn-verify.html HTTP/1.0' . "\r\n" .
				  'Host: www.okpay.com'."\r\n" .
				  'Content-Type: application/x-www-form-urlencoded' . "\r\n" .
				  'Content-Length: ' . strlen($request) . "\r\n" .
				  'Connection: close' . "\r\n\r\n";
		
		@fputs($fp, $header . $request);
		$string = '';
		while (!@feof($fp)) {
			$res = @fgets($fp, 1024);
			$string .= $res;
			// Find verification result in response
			if ( $res == 'VERIFIED' || $res == 'INVALID' || $res == 'TEST') {
				$result = $res;
				break;
			}
		}
		@fclose($fp);
	}
	
	if ($result == 'VERIFIED') {
		// check the "ok_txn_status" is "completed"
		// check that "ok_txn_id" has not been previously processed
		// check that "ok_receiver_email" is your OKPAY email
		// check that "ok_txn_gross"/"ok_txn_currency" are correct
		// process payment
		
		
		if(isset($_POST['ok_invoice'])):
		
		$cust 					= $_POST['ok_invoice'];
		$cust 					= explode("|",$cust);
		
		$pid				= $cust[0];
		$uid				= $cust[1];
		$datemade 			= $cust[2];		
		$xtra1 				= $cust[3];
		$xtra2 				= $cust[4];
		$xtra3 				= $cust[5];
		
		$xtra4 				= $cust[6];
		$xtra5 				= $cust[7];
		$xtra6 				= $cust[8];
		$xtra7 				= $cust[9];
		$xtra8 				= $cust[10];
		$xtra9 				= $cust[11];
		$xtra10				= $cust[12];
		
		//---------------------------------------------------
		
		
		$my_arr = array();
		$my_arr['extra1'] = 0;
		$my_arr['extra2'] = 0;
		$my_arr['extra3'] = 0;
		
		$my_arr['extra4'] = 0;
		$my_arr['extra5'] = 0;
		$my_arr['extra6'] = 0;
		$my_arr['extra7'] = 0;
		$my_arr['extra8'] = 0;
		$my_arr['extra9'] = 0;
		$my_arr['extra10'] = 0;
		
		if(!empty($xtra1)) $my_arr['extra' . $xtra1] = 1;
		if(!empty($xtra2)) $my_arr['extra' . $xtra2] = 1;
		if(!empty($xtra3)) $my_arr['extra' . $xtra3] = 1;
		
		if(!empty($xtra4)) $my_arr['extra' . $xtra4] = 1;
		if(!empty($xtra5)) $my_arr['extra' . $xtra5] = 1;
		if(!empty($xtra6)) $my_arr['extra' . $xtra6] = 1;
		if(!empty($xtra7)) $my_arr['extra' . $xtra7] = 1;
		if(!empty($xtra8)) $my_arr['extra' . $xtra8] = 1;
		if(!empty($xtra9)) $my_arr['extra' . $xtra9] = 1;
		if(!empty($xtra10)) $my_arr['extra' . $xtra10] = 1;

		
		$xtra1 		= $my_arr['extra1'];
		$xtra2 		= $my_arr['extra2'];
		$xtra3 		= $my_arr['extra3'];
		
		$xtra4 		= $my_arr['extra4'];
		$xtra5 		= $my_arr['extra5'];
		$xtra6 		= $my_arr['extra6'];
		$xtra7 		= $my_arr['extra7'];
		$xtra8 		= $my_arr['extra8'];
		$xtra9 		= $my_arr['extra9'];
		$xtra10 		= $my_arr['extra10'];
		

		
		//---------------------------------------------------
		
		$payment_status = $_POST['payment_status'];
		
		if(1): //$payment_status == "Completed"):
		
		$price = get_post_meta($pid, 'price', true);
		if(empty($price)) $price = get_option('pricerrTheme_price');
		
		$mc_gross 				= $_POST['amount_net']; // - $_POST['mc_fee'];

		//-----------------------------------------------------
		global $wpdb;
		$pref = $wpdb->prefix;
		
		$s1 = "select * from ".$pref."job_orders where pid='$pid' AND uid='$uid' AND date_made='$datemade'";
		$r1 = $wpdb->get_results($s1);
					
		
		if(count($r1) == 0)
		{
		
			$nts = addslashes($nts);
			$s1 = "insert into ".$pref."job_orders (pid,uid,date_made, mc_gross, notes_to_seller, extra1, extra2, extra3, extra4, extra5, extra6, extra7, extra8, extra9, extra10) 
			values('$pid','$uid','$datemade','$mc_gross', '$nts','$xtra1','$xtra2','$xtra3','$xtra4','$xtra5','$xtra6','$xtra7','$xtra8','$xtra9','$xtra10')";
			$wpdb->query($s1);		
			

			//--------------
			
			$s1 = "select * from ".$pref."job_orders where pid='$pid' AND uid='$uid' AND date_made='$datemade'";
			$r1 = $wpdb->get_results($s1);
			$orderid = $r1[0]->id;
			//------------------------
			
			
			$g1 = "insert into ".$pref."job_chatbox (datemade, uid, oid, content) values('$datemade','0','$orderid','$ccc')";
			$wpdb->query($g1);
			
			//--------------
			
			$s1 = "insert into ".$pref."job_ratings (orderid) values('$orderid')";
			$wpdb->query($s1);	
			
			$sales = get_post_meta($pid,'sales',true);
			if(empty($sales)) $sales = 1; else $sales = $sales + 1;
			
			update_post_meta($pid,'sales',$sales);
			
			//---------------
			// email to the owner of the job
			$post 	= get_post($pid);
			
			PricerrTheme_send_email_when_job_purchased_4_buyer($orderid, $pid, $uid, $post->post_author);
			PricerrTheme_send_email_when_job_purchased_4_seller($orderid, $pid, $post->post_author, $uid);			
			
			
			//---------------
			
			$instant = get_post_meta($pid,'instant',true);
			
			if($instant == "1")
			{
				$tm = current_time('timestamp',0);
				$s = "update ".$wpdb->prefix."job_orders set done_seller='1', date_finished='$tm' where id='$orderid' ";
				$wpdb->query($s);
				
				$ccc = __('Delivered','PricerrTheme');
				
				$g1 = "insert into ".$wpdb->prefix."job_chatbox (datemade, uid, oid, content) values('$tm','-1','$orderid','$ccc')";
				$wpdb->query($g1);	
				
				PricerrTheme_send_email_when_job_delivered($orderid, $pid, $uid);				
				
			}
			
			
			//---------------
			
			$admin_email 	= get_bloginfo('admin_email');
			$message = sprintf(__('A new job has bee purchased on your site: <a href="%s">%s</a>', 'PricerrTheme'), 
			get_permalink($pid), $post->post_title);
					
			PricerrTheme_send_email($admin_email, sprintf(__('New Job Purchased on your site - %s', 'PricerrTheme'), $post->post_title), $message);	
		}
			

			endif;	endif;	
		
		
	} elseif($result == 'INVALID') {
		// If 'INVALID': log for manual investigation. 
		
	} elseif($result == 'TEST') {
		// If 'TEST': do something
		
	} else {
		// IPN not verified or connection errors
		// If status != 200 IPN will be repeated later
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	
	
		
	
}

?>