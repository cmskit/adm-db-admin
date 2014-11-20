<?php
// perform access control
require_once dirname(dirname(__DIR__)) . '/inc/php/session.php';
if(!isset($_SESSION[$_SESSION['___']]['root'])) exit('you are not allowed to access this service!');

// this is the container to activate adminer's plugin system
function adminer_object()
{
	include __DIR__ . '/plugins.php';
	return new AdminerPlugin( 
		array(
			new AdminerFrames,
			new AdminerEditTextarea,
			new AdminerDumpXml,
			new AdminerDumpJson,
		)
	);
}
// include adminer itself
include __DIR__ . '/adminer.inc';
