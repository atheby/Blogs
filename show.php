<?php

if(!isset($_SESSION['currentBlog']))
	$_SESSION['currentBlog'] = false;
if(!isset($_SESSION['currentPost']))
	$_SESSION['currentPost'] = false;
if(!isset($_GET['action']))
	$_GET['action'] = false;

$mysqli = connectDB();
$result = $mysqli->query("SELECT post_id FROM posts");
if($result->num_rows > 0) {
	if($_GET['action'] == 'prevPost') {
		for($i = 0; $i < count($_SESSION['postsArray']); $i++) {
			if(current($_SESSION['postsArray'])['post_id'] == $_SESSION['currentPost']['post_id'])
				break;
			else
				next($_SESSION['postsArray']);
		}
		$_SESSION['currentPost'] = next($_SESSION['postsArray']);
		if(!is_array($_SESSION['currentPost']))
			$_GET['action'] = false;
	}

	if($_GET['action'] == 'nextPost') {
		for($i = 0; $i < count($_SESSION['postsArray']); $i++) {
			if(current($_SESSION['postsArray'])['post_id'] == $_SESSION['currentPost']['post_id'])
				break;
			else
				next($_SESSION['postsArray']);
		}
		$_SESSION['currentPost'] = prev($_SESSION['postsArray']);
		if(!is_array($_SESSION['currentPost']))
			$_GET['action'] = false;
	}

	if(!$_GET['action'] || $_GET['action'] == 'nextBlog') {
		$blogsArray = getBlogsArray();
		do
			$nextBlog = $blogsArray[rand(0, count($blogsArray) - 1)]['blog_id'];
		while($_SESSION['currentBlog'] == $nextBlog);
		$_SESSION['currentBlog'] = $nextBlog;
		unset($_SESSION['postsArray']);
	}

	if(!isset($_SESSION['postsArray'])) {
		$result = $mysqli->query("SELECT p.post_id, p.body, p.title AS post_title, b.title AS blog_title, u.username, p.create_date AS post_date FROM posts p 
								  LEFT JOIN (SELECT blog_id, user_id, title FROM blogs) b ON p.blog_id=b.blog_id 
							      LEFT JOIN (SELECT username, user_id FROM users) u ON u.user_id=b.user_id 
							      WHERE b.blog_id='".$_SESSION['currentBlog']."' ORDER BY p.create_date DESC;");
		$postsArray = array();
		while($row = $result->fetch_object()) {
			array_push($postsArray, array("post_id" => $row->post_id,
										  "post_title" => $row->post_title,
										  "blog_title" => $row->blog_title,
										  "author" => $row->username,
										  "body" => $row->body,
										  "post_date" => $row->post_date));
		}
		$_SESSION['postsArray'] = $postsArray;
		$_SESSION['currentPost'] = current($_SESSION['postsArray']);
	}

	echo '<div class="bgdiv" style="width:80%; min-height:400px; margin:0 auto;">';
	echo '	<div style="width:80%; margin:0 auto; text-align:justify; padding-bottom:30px;">';
	echo '		<span style="float:right; font-style:italic; font-size:20px;">'.$_SESSION['currentPost']['author'].'</span>';
	echo '		<br />';
	echo '		<h3 style="float:left;">'.$_SESSION['currentPost']['blog_title'].'</h3>';
	echo '		<br />';
	echo '		<br />';
	echo '		<br />';
	echo '		<h5 style="float:left;">'.$_SESSION['currentPost']['post_title'].'</h5>';
	echo '		<h6 style="float:right;">'.@date('d-m-Y', strtotime($_SESSION['currentPost']['post_date'])).'</h6>';
	echo '		<br />';
	echo '		<br />';
	echo '		<br />';
	echo '		<hr />';
	echo '		<br />';
	echo 		nl2br($_SESSION['currentPost']['body']);
	echo '	<hr style="margin:0 auto; width:80%; margin-top:50px; margin-bottom:30px;" />';
	echo '	</div>';
	echo '</div>';
}
else
	showAlert("block", "No posts");

?>