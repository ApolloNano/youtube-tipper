<?php
	function getAllComments($videoId,$pageToken=null,$maxResults=100,$max_id=FALSE)
	{
        $url = "https://www.googleapis.com/youtube/v3/commentThreads";

        static $all =[];
        $params =[
            'key' => API_KEY,
            'part' => 'snippet',
            'maxResults' => $maxResults,
            'videoId' => $videoId,
            'pageToken' => $pageToken
        ];

        $call = $url.'?'.http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $call);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $data = NULL;
        $data = json_decode($output,true);
        $all[] = $data;
		
		if($max_id)
		{
			if(isset($data['items']))
			{
				foreach($data['items'] as $comment)
				{
					if($comment['id'] == $max_id)
					{
						return $all;
					}
				}
			}
		}
		
        if(isset($data['nextPageToken'])){
            if($data['nextPageToken'] != NULL ){
                $pageToken = $data['nextPageToken'];
                getAllComments($videoId,$pageToken,$maxResults);
            }
        }
        curl_close($ch);
        return $all;
    }

	// Calculates a percentage tip amount based on the overall available balance.
	function getTipAmount($percentage)
	{
		$main_balance = nano_get_balance(NANO_MAIN_ACCOUNT);
		$tip_amount = floatval(NanoHelper::raw2den($main_balance->balance, 'NANO')) / 100 * $percentage;
		
		$zero_count = strspn(number_format($tip_amount, 10), "0", strpos(number_format($tip_amount, 10), ".")+1);
		if($tip_amount >= 0.0 && $tip_amount <= 1.0)
			$tip_amount = number_format($tip_amount, $zero_count + 3); // Reduce everything to 2 digit fractions 0.0012 0.000051 0.14
		
		if(strpos($tip_amount, MAX_TIP_PREFIX) === false) // Max amount
			$tip_amount = MAX_TIP_PREFIX.'1';
			
		return $tip_amount;
	}
	
	function sendTip($tip_amount, $uniq_id, $address_to)
	{
		$data = FALSE;
		$data['success'] = FALSE;
		$data['error'] = '';
		$data['block'] = '';
		
		$block = nano_send_money($address_to, $tip_amount, $uniq_id);
		if(!$block)
			return $data;
			
		if(isset($block->error))
		{		
			if($block->error === 'Insufficient balance')
				$data['error'] = 'Sorry. The donation wallet is empty. Try again another time.';
			else
				$data['error'] = 'Sending money failed!';
				
		}
		
		$data['block'] = isset($block->block) ? $block->block : FALSE;
		$data['success'] = isset($block->block);
			
		return $data;
	}
?>