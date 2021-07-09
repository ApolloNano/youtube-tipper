<?php

	function nano_curl ($post) {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, NODE_ADDRESS);
		curl_setopt($ch, CURLOPT_HEADER, 1); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
        //curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-length: '.strlen($post)));

		$output = curl_exec($ch);
		if (curl_errno($ch)) {
			var_dump(curl_error($ch));
		}
		curl_close($ch);
		
		return $output;
	}
	function nano_send_money($nano_address, $nano_amount, $uniq_id = '')
	{
		if($uniq_id != '')
			$uniq_id = md5($uniq_id);
		
		$work = false;
		$raw_amount = NanoHelper::den2raw($nano_amount, 'NANO');
		$post = '"action": "send", "wallet": "'.NANO_MAIN_WALLET.'", "source": "'.NANO_MAIN_ACCOUNT.'", "destination": "'.$nano_address.'", "amount": "'.$raw_amount.'", "id": "'.$uniq_id.'"';
		if($work)
			$post .= ', "work":"'.$work.'"';
		$post = '{'.$post.'}';
		$result = json_decode(nano_curl($post));
		
		return $result;
	}
	function nano_get_balance($account)
	{
		$post = '{"action": "account_balance", "account":"'.$account.'"}';
		$balance = json_decode(nano_curl($post));
		
		return $balance;
	}
?>