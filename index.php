<?php

@session_start();

require('functions.php');

if(!isset($_SESSION['uid']))
	$_SESSION['uid'] = false;
if(!isset($_GET['page']))
	$_GET['page'] = 'show';

readfile('header.txt');

echo '	<body>';
echo '		<script src="http://code.jquery.com/jquery-latest.js" type="text/javascript"></script>';
echo '		<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>';
echo '		<div id="container">';
echo '          <div id="navi">';
					require('navi.php');
echo '         	</div>';
echo '         	<div id="main">';
					require($_GET['page'].'.php');
echo '         	</div>';
echo '			<div id="footer">';
					readfile('footer.txt');
echo '			</div>';
echo '		</div>';
echo '	</body>';
echo '</html>';

?>