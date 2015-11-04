<?php

if(!isset($_GET['check']))
	$_GET['check'] = false;

if($_GET['action'] == 'register') {	
	$success = false;
	echo '<div class="bgdiv" style="margin:0 auto; width:80%; height: 400px; padding:0px 0px 30px;">';
	echo '<div class="lab_title" style="box-shadow:none; margin-bottom:50px;">';
	echo '	Registration';
	echo '</div>';
	
	if($_GET['check'] == true && ($_POST['login'] == '' || $_POST['pass'] == '' || $_POST['repass'] == '' || $_POST['pass'] != $_POST['repass']))
		showAlert("error", "Fill out all required fields");
	else
		if($_GET['check'] == true) {
			$login = $_POST['login'];
			$pass = sha1($_POST['pass']);
			$id = generateID();
			$mysqli = connectDB();
			$result = $mysqli->query("SELECT username FROM users WHERE username = '".$login."'");
			if($result->num_rows == 0) {
				if($stmt = $mysqli->prepare("INSERT INTO users (user_id, username, password) VALUES (?, ?, ?)")) {
					$stmt->bind_param("sss", $id, $login, $pass);
					$stmt->execute();
					$stmt->close();
					$success = true;
				}
			}
			else
				showAlert("error", "Username already exists");
		}
	
	if($success == true) {
		showAlert("success", "Thank you for your registration");
		$success = false;
	}
	else{
		echo '<div class="bgdiv" style="width:350px; padding:0px;margin: 0 auto;">';
		echo '	<div class="lab_title" style="box-shadow:none;">';
		echo '		Fill out the form';
		echo '	</div>';
		echo '	<form action="?page=user&amp;action=register&amp;check=true" method="post">';
		echo '		<table style="margin: 0 auto;width: 300px; text-align: left;">';
		echo '			<tr>';
		echo '				<td style="width:50%">';
		echo '  				Username<span style="color:red;">*</span>';
		echo '              	<br />';
		echo '              	<input class="text" type="text" name="login" style="width:80%" />';
		echo '          	</td>';
		echo '			</tr>';
		echo '      	<tr>';
		echo '          	<td style="width:50%">';
		echo '  				Password<span style="color:red;">*</span>';
		echo '              	<br />';
		echo '             		<input class="text" type="password" name="pass" style="width:80%" />';
		echo '          	</td>';
		echo '          	<td>';
		echo '  				Repeat password<span style="color:red;">*</span>';
		echo '              	<br />';
		echo '              	<input class="text" type="password" name="repass" style="width:80%" />';
		echo '          	</td>';
		echo '			</tr>';
		echo '      	<tr>';
		echo '      		<td colspan="2" style="text-align:center;">';
		echo '          		<input class="button" type="submit" value="Send" style="width:60px; height: 25px;" />';
		echo '          	</td>';
		echo '      	</tr>';
		echo '		</table>';
		echo '	</form>';
		echo '</div>';
	}
	echo '</div>';
}

if($_GET['action'] == 'login') {
	$success = false;

	if($_GET['check'] == true && ($_POST['login'] == '' || $_POST['pass'] == ''))
		showAlert("error", "Podaj nazwę użytkownika i hasło");
	else
		if($_GET['check'] == true) {
			$login = $_POST['login'];
			$pass = sha1($_POST['pass']);
			$mysqli = connectDB();
			$checkUser = $mysqli->query("SELECT user_id, password FROM users WHERE username = '".$login."'");
			if($checkUser->num_rows > 0)
				$row = $checkUser->fetch_object();
			if(isset($row) && $row->password == $pass) {
				$_SESSION['uid'] = $row->user_id;
				unset($_GET['page']);
				$success = true;
				header("Location: index.php");
			}
			else
				showAlert("error", "Wrong username or password");			
		}
	if(!$success) {
		echo '<div class="bgdiv" style="width:280px;padding:0px;margin: 0 auto;">';
		echo '	<div class="lab_title" style="box-shadow:none;">';
		echo '		Log in';
		echo '	</div>';
		echo '	<form action="?page=user&amp;action=login&amp;check=true" method="post">';
		echo '		<table style="margin: 0 auto;width: 180px; text-align: left;">';
		echo '			<tr>';
		echo '				<td style="width:50%">';
		echo '  				Username';
		echo '              	<br />';
		echo '              	<input class="text" type="text" name="login" style="width:92%" />';
		echo '          	</td>';
		echo '			</tr>';
		echo '      	<tr>';
		echo '          	<td style="width:50%">';
		echo '  				Password';
		echo '              	<br />';
		echo '             		<input class="text" type="password" name="pass" style="width:92%" />';
		echo '          	</td>';
		echo '			</tr>';
		echo '      	<tr>';
		echo '      		<td colspan="2" style="text-align:center;">';
		echo '          		<input class="button" type="submit" value="Log in" style="width:60px; height: 25px;" />';
		echo '          	</td>';
		echo '      	</tr>';
		echo '		</table>';
		echo '	</form>';
		echo '</div>';
	}
}

if($_GET['action'] == 'logout') {
	$_SESSION['uid'] = false;
	header("Location: index.php");
}

?>