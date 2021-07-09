<?php

	function get_mysqli()
	{
		$mysqli = new mysqli(MYSQL_DB_HOST, MYSQL_DB_USER, MYSQL_DB_PASSWORD, MYSQL_DB_NAME);
		if ($mysqli->connect_errno)
		{
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}
		
		return $mysqli;
	}
	function db_last_comment_id($video_id)
	{
		$db = get_mysqli();
		$query = $db->query(sprintf("SELECT * FROM youtube_tips WHERE tip_type=%d AND video_id='%s' ORDER BY id DESC LIMIT 1", 
		0, mysqli_escape_string($db, $video_id)));
		
		if(!$query)
			printf($db->error);
			
		$result = $query->fetch_object();
		return $result ? $result->user_post_id : FALSE;
	}
	function db_video_user_tips($author, $video_id)
	{
		$db = get_mysqli();
		$query = $db->query(sprintf("SELECT count(1) as total FROM youtube_tips WHERE tip_type=%d AND author='%s' AND video_id='%s'", 
		0, mysqli_escape_string($db, $author), mysqli_escape_string($db, $video_id)));
		
		if(!$query)
			printf($db->error);
			
		return $query->fetch_object()->total;
	}
	function db_nano_sent_to_account($account, $video_id, $tip_type = 0)
	{
		$db = get_mysqli();
		$query = $db->query(sprintf("SELECT count(1) as total FROM youtube_tips WHERE tip_type=%d AND account='%s' AND video_id='%s'", 
		$tip_type, mysqli_escape_string($db, $account), mysqli_escape_string($db, $video_id)));
		
		if(!$query)
			printf($db->error);
			
		return $query->fetch_object()->total >= 1 ? TRUE : FALSE;
	}
	function db_insert_tip($amount, $author, $video_id, $tip_type, $user_post_id, $body_text, $event, $account = '')
	{
		$db = get_mysqli();
		$result = $db->query(sprintf("INSERT INTO youtube_tips 
		(amount, author, video_id, tip_type, user_post_id, 
		body_text, event, time_tipped, account
		) 
		VALUES ('%s', '%s', '%s', %d, '%s',
		'%s', '%s', %d, '%s'
		)", 
		mysqli_escape_string($db, $amount), mysqli_escape_string($db, $author), mysqli_escape_string($db, $video_id), $tip_type, mysqli_escape_string($db, $user_post_id), 
		mysqli_escape_string($db, $body_text), 	mysqli_escape_string($db, $event), time(), mysqli_escape_string($db, $account)));
		
		if(!$result)
			printf($db->error);
		
		return $result;
	}
?>