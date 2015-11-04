<?php

if($_SESSION['uid'] != false) {

	$mysqli = connectDB();

	if(!isset($_GET['action']))
		$_GET['action'] = false;
	if(!isset($_GET['check']))
		$_GET['check'] = false;
	if(!isset($_GET['confirmDel']))
		$_GET['confirmDel'] = false;
	if(!isset($_GET['blog']))
		$_GET['blog'] = false;
	if(!isset($_GET['post']))
		$_GET['post'] = false;
	if(!isset($_SESSION['warning']))
		$_SESSION['warning'] = false;
	
	echo '<div style="margin: 0 auto;width:90%;padding: 20px 20px;">';

	if($_GET['action'] == 'editBlog') {
		echo '<div class="bgdiv" style="min-height:300px; width:100%; margin-bottom:30px; padding:0px 0px 30px;">';
		echo '	<div class="lab_title" style="box-shadow:none;">';
		echo 		getTitle("blog", $_GET['blog_id']);
		echo '	</div>';
		echo '	<a href="?page=edit&amp;action=editBlog&amp;blog=changetitle&amp;blog_id='.$_GET['blog_id'].'"><input type="button" class="button" value="Change title" style="margin-bottom:10px;"/></a>';
		echo '	<a href="?page=edit&amp;action=editBlog&amp;blog=addPost&amp;blog_id='.$_GET['blog_id'].'"><input type="button" class="button" value="Add post" style="margin-bottom:10px;"/></a>';
		echo '	<a href="?page=edit&amp;action=editBlog&amp;blog=deleteBlog&amp;blog_id='.$_GET['blog_id'].'"><input type="button" class="button" value="Delete blog" style="margin-bottom:10px;"/></a>';

		if($_GET['blog'] == "changetitle") {

			echo '<hr style="width:80%;" />';

			echo '<form action="?page=edit&amp;action=editBlog&amp;blog=changetitleConfirm&amp;blog_id='.$_GET['blog_id'].'" method="post">';
			echo '	<table style="margin:0 auto;">';
			echo '		<tr>';
			echo '			<td style="vertical-align:middle;"><input class="text" style="width:120px; margin:0px;" type="text" name="title" value="'.getTitle("blog", $_GET['blog_id']).'" /></td>';
			echo '			<td style="vertical-align:middle;"><input type="submit" class="button" style="width:70px;" value="Change title" /></td>';
			echo '		</tr>';
			echo '	</table>';
			echo '</form>';
		}

		if($_GET['blog'] == "changetitleConfirm") {
			if($_POST['title'] == '') {
				echo '<hr style="width:80%;" />';
				showAlert("error", "Podaj tytuł");
			}
			if($_POST['title'] != '') {
				$title = $_POST['title'];
				$blogID = $_GET['blog_id'];
				if($result = $mysqli->query("SELECT title FROM blogs WHERE title='".$title."'")) {
					if($result->num_rows > 0) {
						echo '<hr style="width:80%;" />';
		 				showAlert("error", "Blog already exists");
					}
		 			else{
		 				if($stmt = $mysqli->prepare("UPDATE blogs SET title=? WHERE blog_id=?")) {
							$stmt->bind_param("ss", $title, $blogID);
							$stmt->execute();
							$stmt->close();
							echo '<hr style="width:80%;" />';
							showAlert("success", "Title has been changed");
						}
		 			}
		 		}
			}
		}

		if($_GET['blog'] == 'addPost') {

			if($_SESSION['warning']) {
				echo '<hr style="width:80%;" />';
				showAlert("error", "Fill out all the fields");
				$_SESSION['warning'] = false;
			}

			echo '<hr style="width:80%;" />';

			echo '<form action="?page=edit&amp;action=editBlog&amp;blog=addPostConfirm&amp;blog_id='.$_GET['blog_id'].'" method="post">';
			echo '	<table style="margin:0 auto;">';
			echo '		<tr>';
			echo '			<td style="line-height:20px;text-align:left;"><input type="text" class="text" name="postTitle" value="Title" /></td>';
			echo '		</tr>';
			echo '		<tr>';
			echo '			<td><textarea name="body" cols="40" rows="40" style="width:400px; height:300px;"></textarea></td>';
			echo '		</tr>';
			echo '		<tr>';
			echo '			<td><input type="submit" class="button" value="Add" /></td>';
			echo '		</tr>';
			echo '	</table>';
			echo '</form>';
		}

		if($_GET['blog'] == "addPostConfirm") {
			if($_POST['postTitle'] == '' || $_POST['body'] == '')
				$_SESSION['warning'] = true;
			if($_POST['postTitle'] != '' && $_POST['body'] != '') {
				$postID = generateID();
				$blogID = $_GET['blog_id'];
				$title = $_POST['postTitle'];
				$body = $_POST['body'];
				if($result = $mysqli->query("SELECT title FROM posts WHERE title='".$title."' AND blog_id='".$blogID."'")) {
		 			if($result->num_rows > 0) {
		 				echo '<hr style="width:80%;" />';
		 				showAlert("error", "Post with a given title already exists");
		 			}
		 			else {
						if($stmt = $mysqli->prepare("INSERT INTO posts (post_id, blog_id, title, body) VALUES (?, ?, ?, ?)")) {
							$stmt->bind_param("ssss", $postID, $blogID, $title, $body);
							$stmt->execute();
							$stmt->close();
							echo '<hr style="width:80%;" />';
							showAlert("success", "Post '".$title."' has been added");
						}
					}
				}
			}
			else
				header("Location: ?page=edit&action=editBlog&blog=addPost&blog_id=".$_GET['blog_id']);
		}

		if($_GET['blog'] == 'deleteBlog') {
			$result = $mysqli->query("SELECT title FROM blogs WHERE blog_id='".$_GET['blog_id']."'");
			$row = $result->fetch_object();
			$ask = true;
			if($_GET['confirmDel'] == 'true') {
				if($mysqli->query("DELETE FROM blogs WHERE blog_id='".$_GET['blog_id']."'")) {
					$ask = false;
					header("Location: ?page=edit");
				}
			}
			if($ask) {
				echo '<hr style="width:80%;" />';
				echo '<div style="">';
				echo '	<h5>Delete post:  '.$row->title.'?</h5><br />';
				echo '	<a href="?page=edit&amp;action=editBlog&amp;blog=deleteBlog&amp;blog_id='.$_GET['blog_id'].'&amp;confirmDel=true"><input style="width:80px;" class="button" type="submit" value="Delete" /></a>';
				echo '</div>';
			}
		}

		echo '<hr style="width:80%;" />';

		if($result = $mysqli->query("SELECT post_id, create_date, title FROM posts WHERE blog_id='".$_GET['blog_id']."' ORDER BY create_date DESC;"))
			if($result->num_rows > 0) {
				echo '<table style="width:80%; margin: 0 auto;">';
		 		echo '	<tr>';
		 		echo '		<th>Title</th>';
		 		echo '		<th style="width:160px;">Added</th>';
		 		echo '		<th style="width:40px;"></th>';
		 		echo '	</tr>';
		 		while($row = $result->fetch_object()) {
		 			echo '	<tr>';
		 			echo '		<td style="text-align:left;">'.$row->title.'</td>';
		 			echo '		<td>'.$row->create_date.'</td>';
		 			echo '		<td><a href="?page=edit&amp;action=editPost&amp;post_id='.$row->post_id.'&amp;blog_id='.$_GET['blog_id'].'"><input type="button" class="button" value="Edit" /></a></td>';
		 			echo '	</tr>';
		 		}
		 		echo '</table>';
		 	}
		 	else
		 		showAlert("block", "No posts");
		echo '</div>';
	}

	if($_GET['action'] == 'editPost') {
		echo '<div class="bgdiv" style="min-height:300px; width:100%; margin-bottom:30px; padding:0px 0px 30px;">';
		echo '	<div class="lab_title" style="box-shadow:none;">';
		echo 		getTitle("blog", $_GET['blog_id']).' - '.getTitle("post", $_GET['post_id']);
		echo '	</div>';
		echo '	<a href="?page=edit&amp;action=editBlog&amp;blog_id='.$_GET['blog_id'].'"><input type="button" class="button" value="Back" style="margin-bottom:10px;"/></a>';
		echo '	<a href="?page=edit&amp;action=editPost&amp;post=deletePost&amp;post_id='.$_GET['post_id'].'&amp;blog_id='.$_GET['blog_id'].'"><input type="button" class="button" value="Delete post" style="margin-bottom:10px;"/></a>';
		
		if($_GET['post'] == 'changePostConfirm') {
			if($_POST['postTitle'] == '' || $_POST['body'] == '') {
				echo '<hr style="width:80%;" />';
				showAlert("error", "Fill out all fields");
			}
			else{
				$title = $_POST['postTitle'];
				$body = $_POST['body'];
				$postID = $_GET['post_id'];
				if($stmt = $mysqli->prepare("UPDATE posts SET title=?, body=? WHERE post_id=?")) {
					$stmt->bind_param("sss", $title, $body, $postID);
					$stmt->execute();
					$stmt->close();
					echo '<hr style="width:80%;" />';
					showAlert("success", "Post has been changed");
				}
			}
		}

		if($_GET['post'] == 'deletePost') {
			$result = $mysqli->query("SELECT title FROM posts WHERE post_id='".$_GET['post_id']."'");
			$row = $result->fetch_object();
			$ask = true;
			if($_GET['confirmDel'] == 'true') {
				if($mysqli->query("DELETE FROM posts WHERE post_id='".$_GET['post_id']."'")) {
					$ask = false;
					header("Location: ?page=edit&action=editBlog&blog_id=".$_GET['blog_id']);
				}
			}
			if($ask) {
				echo '<hr style="width:80%;" />';
				echo '<div style="">';
				echo '	<h5>Delete post: '.$row->title.'?</h5><br />';
				echo '	<a href="?page=edit&amp;action=editPost&amp;post=deletePost&amp;blog_id='.$_GET['blog_id'].'&amp;post_id='.$_GET['post_id'].'&amp;confirmDel=true"><input style="width:80px;" class="button" type="submit" value="Delete" /></a>';
				echo '</div>';
			}
		}

		echo '<hr style="width:80%;" />';

		$result = $mysqli->query("SELECT title,body FROM posts WHERE post_id='".$_GET['post_id']."'");
		$row = $result->fetch_object();
		echo '	<form action="?page=edit&amp;action=editPost&amp;post=changePostConfirm&amp;post_id='.$_GET['post_id'].'&amp;blog_id='.$_GET['blog_id'].'" method="post">';
		echo '		<table style="margin:0 auto;">';
		echo '			<tr>';
		echo '				<td style="line-height:20px;text-align:left;"><input type="text" class="text" name="postTitle" value="'.$row->title.'" /></td>';
		echo '			</tr>';
		echo '			<tr>';
		echo '				<td><textarea name="body" cols="40" rows="40" style="width:400px; height:300px;">'.$row->body.'</textarea></td>';
		echo '			</tr>';
		echo '			<tr>';
		echo '				<td><input type="submit" class="button" value="Change" /></td>';
		echo '			</tr>';
		echo '		</table>';
		echo '	</form>';	
		echo '</div>';
	}

	$warning = false;
	if($_GET['action'] == 'createBlog') {
		if($_POST['title'] == '')
			$warning = true;
		else{
			$uid = $_SESSION['uid'];
			$id = generateID();
			$title = $_POST['title'];
			if($result = $mysqli->query("SELECT title FROM blogs WHERE title='".$title."'")) {
		 		if($result->num_rows > 0)
		 			showAlert("error", "Blog already exists");
		 		else{
					if($stmt = $mysqli->prepare("INSERT INTO blogs (blog_id, user_id, title) VALUES (?, ?, ?)")) {
						$stmt->bind_param("sss", $id, $uid, $title);
						$stmt->execute();
						$stmt->close();
						showAlert("success", "Blog '".$title."' has been added");
					}
				}
			}
		}
	}

	echo '<div class="bgdiv" style="min-height:300px; padding:0px 0px 30px;">';
	echo '	<div class="lab_title" style="box-shadow:none;">';
	echo '		List of blogs';
	echo '	</div>';
	echo '<form style="margin-bottom:30px;" action="?page=edit&amp;action=createBlog&amp;check=true" method="post">';
	echo '	<table style="margin: 0 auto; width: 220px;">';
	echo '		<tr>';
					if($warning)
						echo '<td style="vertical-align:middle;"><input class="text" style="outline:none;border-color:red;box-shadow:0 0 10px red; width:120px; margin:0px;" type="text" name="title" /></td>';
					else
						echo '<td style="vertical-align:middle;"><input class="text" style="width:120px; margin:0px;" type="text" name="title" /></td>';
	echo '			<td style="vertical-align:middle;"><input type="submit" class="button" style="width:70px;" value="Add blog" /></td>';
	echo '		</tr>';
	echo '	</table>';
	echo '</form>';

	echo '<hr style="width:80%;" />';

	if($result = $mysqli->query("SELECT a.title, a.blog_id, a.create_date, IFNULL(b.posts,0) AS posts FROM blogs a LEFT OUTER JOIN 
								(SELECT blog_id, count(*) AS posts FROM posts GROUP BY blog_id) b ON a.blog_id=b.blog_id WHERE a.user_id='".$_SESSION['uid']."' ORDER BY title;"))
		 if($result->num_rows > 0) {
		 	echo '<table style="width:80%; margin: 0 auto;">';
		 	echo '	<tr>';
		 	echo '		<th>Title</th>';
		 	echo '		<th style="width:160px;">Added</th>';
		 	echo '		<th style="width:60px;">Posts</th>';
		 	echo '		<th style="width:40px;"></th>';
		 	echo '	</tr>';
		 	while($row = $result->fetch_object()) {
		 		echo '	<tr>';
		 		echo '		<td style="text-align:left;">'.$row->title.'</td>';
		 		echo '		<td>'.$row->create_date.'</td>';
		 		echo '		<td>'.$row->posts.'</td>';
		 		echo '		<td><a href="?page=edit&amp;action=editBlog&amp;blog_id='.$row->blog_id.'"><input type="button" class="button" value="Edit" /></a></td>';
		 		echo '	</tr>';
		 	}
		 	echo '</table>';
		 }
		 else
		 	showAlert("block", "You don't have any blogs");
	echo '</div>';
	echo '</div>';
}
else
	header("Location: index.php");

?>