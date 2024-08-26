<?php

require_once 'db_config.php';

$title = 'Log Out';

$user->logout();

$content = '
<h2 >Logout</h2>
<p> You are now logged out</p>
<a href="login.php">Login</a><br>
<a href="index.php">Back to home</a>
';

require 'layout.php';
?>
