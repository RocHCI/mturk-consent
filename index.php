<?php
$base_url = "http://codingthecrowd.com/mturk/passthru.php";
?>
<html>
<head>
<style>
html,body {
	font-family: helvetica;
}
textarea,input[type="submit"] {
	font-size: 2em;
}
textarea {
	width: 60%;
}

</style>
<title>MTurk Passthru URL Preparation</title>
</head>
<body>
<?php
if(isset($_REQUEST["url"])) {
?>
<h1><?php
echo $base_url . "?taskurl=" . base64_encode($_REQUEST["url"]) . "\n";
?>
</h1>
<?php
} else {
?>
<form>
<p>
Please enter the URL you would like to set up for use with MTurk:<br>
<textarea rows="5" cols="80" name="url"></textarea><br>
<input type="submit" value="SUBMIT">
</p>
</form>
<?php
}
?>

</body>
</html>