<?php

	// --------- INCLUDES

	require('inc/config.php');
	require('inc/nano.php');
	require('inc/nano_helper.php');
	require('inc/db.php');
	require('inc/yt_helper.php');

	// ---------------------------------
	
	// YouTube
	DEFINE('VIDEO_ID', '-rwAiXX0w0k');
	DEFINE('TIP_PERCENTAGE_ACCOUNT', 0.05);
	DEFINE('MAX_TIP_PREFIX', '0.0');

	// ---------------------------------
	
	echo sprintf("Loading Comments\r\n");
	$last_comment_id = db_last_comment_id(VIDEO_ID);
	$comments = getAllComments(VIDEO_ID, null, 100, $last_comment_id);
	
	foreach($comments[0]['items'] as $comment)
	{
		tip_comment($comment['snippet']['topLevelComment']);
		if(isset($comment['snippet']['replies']))
			foreach($comment['snippet']['replies'] as $reply_comment)
				tip_comment($reply_comment);
	}
	
	function tip_comment($comment)
	{
		$video_id = $comment['snippet']['videoId'];
		$author = $comment['snippet']['authorDisplayName'];
		$text = $comment['snippet']['textOriginal'];
		$user_post_id = $comment['id'];
		
		echo sprintf("Processing Comment %s - %s\r\n", $author, $text);
		
		$tip_count = db_video_user_tips($author, $video_id);
		if($tip_count >= 1) // User already tipped
			return FALSE;
			
		preg_match('/nano_([a-zA-Z0-9]{60})/i', $text, $split);
		if(count($split) !== 2) // No nano address present
			return FALSE;
		
		$address_to = 'nano_'.$split[1];
		if(db_nano_sent_to_account($address_to, $video_id)) // Already sent to nano address
			return FALSE;
			
		$tip_amount = getTipAmount(TIP_PERCENTAGE_ACCOUNT);
		$result = sendTip($tip_amount, 'youtube_tip:'.$author.'-'.$video_id.'-'.rand(), $address_to);
		if($result['success'])
		{
			echo sprintf("Tipped: %s - %s - %s - %s\r\n", $video_id, $author, $text, $result['block']);
			db_insert_tip($tip_amount, $author, $video_id, 0, $user_post_id, $text, json_encode($comment), $address_to);
		}
		
		return TRUE;
	}
?>