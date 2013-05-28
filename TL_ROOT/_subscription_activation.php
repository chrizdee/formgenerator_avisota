<?php

// Config
$confirmation_page = '/newsletter-bestaetigung.html';
$not_found_page = '/404.html';

/* 	IMPORTANT:
	Add to your .htaccess mod_rewrite
	##
	# Double-opt-in / Newsletter subscription activation
	##
	RewriteRule a/(.+) /_subscription_activation.php?a=confirm&token=$1 [R=301,L]
*/


// Receive double-opt-in
session_start();
require_once('system/config/localconfig.php');
global $GLOBALS;

$db = mysql_connect($GLOBALS['TL_CONFIG']['dbHost'], $GLOBALS['TL_CONFIG']['dbUser'], $GLOBALS['TL_CONFIG']['dbPass']);

if (!$db)
{
	echo "No database connection!";
	exit;
}

if (!mysql_select_db($GLOBALS['TL_CONFIG']['dbDatabase']))
{
	echo "Can't select database!";
	exit;
}


switch($_GET['a'])
{
	case 'confirm':
		$token = str_replace(array("'", "\"","'", "`"),array(''), $_GET['token']);
		
		$sql = 'SELECT id 
				FROM tl_avisota_recipient 
				WHERE token="'.$token.'"';

		$rs = mysql_query($sql);
		$data = mysql_fetch_assoc($rs);

		if($data)
		{
			$sql = 'UPDATE tl_avisota_recipient 
					SET confirmed="1" 
					WHERE token="'.$token.'"';

			mysql_query($sql);
			Header('Location: '.$confirmation_page);
			exit;
		}
		else
		{
			Header('Location: '.$not_found_page); 
			exit;
		}
		break;
}

?>