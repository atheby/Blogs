<?php

echo '<div style="width:250px; height:40px; float:left; vertical-align:middle; text-align:center; font-size:20px; line-height:40px;">';
echo '	<a style="margin-right:10px;" href="index.php">Home</a>';
echo '</div>';

if($_SESSION['uid'] == false) {
	echo '<div style="width:250px; height:40px; float:right; vertical-align:middle;text-align: center;line-height:40px;">';
	echo '	<a style="margin-right:10px;" href="?page=user&amp;action=register">Register</a>';
	echo '	<a href="?page=user&amp;action=login">Log in</a>';
	echo '</div>';
}
else {
	echo '<div style="padding-right:20px; height:40px; float:right; vertical-align:middle;text-align: center;line-height:40px;">';
	echo '	Welcome, '.getUsername($_SESSION['uid']);
	echo '	<a style="margin-left:20px" href="?page=edit">Your blogs</a>';
	echo '	<a style="margin-left:20px" href="?page=user&amp;action=logout">Log out</a>';
	echo '</div>';
}
if($_GET['page'] == 'show') {
	echo '<div style="width:750px; height:40px; margin:0 auto; vertical-align:middle; font-size:20px; line-height:40px;">';
	echo '	<a style="margin-left:260px;" href="?page=show&amp;action=prevPost">&larr;post</a>';
	echo '	<a style="margin-left:100px;" href="?page=show&amp;action=nextPost">post&rarr;</a>';
	echo '	<a style="margin-left:130px;" href="?page=show&amp;action=nextBlog">blog&rarr;&rarr;</a>';
	echo '</div>';
}

?>