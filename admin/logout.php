<?php

require_once __DIR__.'/../functions/registry.php';
require_once __DIR__.'/functions/registry.php';

$session = new \Custom\Sessions\session();
session_destroy();

?>
<html>
<head>
	<title>EVEOTS Admin Panel</title>
</head>
<body>
	<center>
		You are now logged out.<br />
		<br />
		<a href="index.php">Login</a><br />
	</center>
</body>
</html>
