<?php

function connectDB() {
	$host = 'localhost';
	$user = 'guest';
	$pass = 'guest';
	$database = 'blogs';

	@$mysqli = new mysqli($host, $user, $pass, $database);
	if ($mysqli->connect_errno)
		showAlert("error", "Database error");
	return $mysqli;
}

function generateID() {
	$id = sha1(mt_rand());
	$mysqli = connectDB();
	$result = $mysqli->query("SELECT users.user_id, blogs.blog_id, posts.post_id FROM users,blogs,posts 
							  WHERE users.user_id='".$id."' OR blogs.blog_id='".$id."' OR posts.post_id='".$id."';");
	if($result->num_rows == 0)
		return $id;
	else
		generateID();
}

function getBlogsArray() {
	$mysqli = connectDB();
	$blogsArray = array();
	$result=$mysqli->query("SELECT a.title, a.blog_id, c.username, IFNULL(b.posts,0) AS posts FROM blogs a 
							LEFT JOIN (SELECT blog_id, count(*) AS posts FROM posts GROUP BY blog_id) b ON a.blog_id=b.blog_id 
							LEFT JOIN (SELECT username,user_id FROM users) c ON a.user_id=c.user_id WHERE posts>0;");
	while($row = $result->fetch_object())
		array_push($blogsArray, array("blog_id" => $row->blog_id,
									  "title" => $row->title,
								  	  "author" => $row->username));
	return $blogsArray;
}

function getUsername($user_id) {
	$mysqli = connectDB();
	$result = $mysqli->query("SELECT username FROM users WHERE user_id = '".$user_id."'");
	$row = $result->fetch_object();
	return $row->username;
}

// string $type [blog, post]
function getTitle($type, $id) {
	$mysqli = connectDB();
	$result = $mysqli->query("SELECT title FROM ".$type."s WHERE ".$type."_id = '".$id."'");
	$row = $result->fetch_object();
	return $row->title;
}

// string $type [error, success, block, info]
function showAlert($type, $msg) {
	echo '<div class="alert alert-'.$type.'" style="margin:0 auto;margin-bottom:20px;padding:5px;width:250px;font-weight:bold;">';
	echo 	$msg;
	echo '</div>';
}

?>