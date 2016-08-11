<!doctype html>
<html>
<head>

<link rel="stylesheet" href="./ftree_style.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.js" type="text/javascript"></script>
</head>
<body>
<div id="navmenu">
	<ul>
 		<li> Setup Helper </li>
	</ul>
</div>
<?php if (isset($_REQUEST["required"])): ?>
<p>You've been redirect here, because you must first create a "config.php" file.</p>
<p>This form will help you to create it.</p>
<? endif; ?>
<div id="setup_form">
<form action="setup.php" method="post" accept-charset="utf-8">
	<label>
	Username
	<input type="text/" name="username" value="" placeholder="username">
	</label>
	<br/>
	<label>
	Password
	<input type="text/password" name="password" value="">
	</label>
	<br/>
	<label>
	Prefix URL
	<input type="text/" name="username" value="" placeholder="http://example.com/downloads">
	</label>
	<br/>
	<label>
	Downloads DIR
	<input type="text/" name="username" value="" placeholder="/home/username/downloads">
	</label>
	<br>
	<input type="submit" value="Submit">
</form>
<?php if (isset($_POST['username'])): ?>
<pre contenteditable="true">
<?

?>

<? endif; ?>
</pre>
</div>
</body>
</html>