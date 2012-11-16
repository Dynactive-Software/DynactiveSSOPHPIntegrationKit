<?php
$status = $_GET['status'];
$redirect = $_GET['redirect'];

if ($status == 'OK' && $redirect != null)
{
	header("Location: $redirect");
}
else
{
	var_dump($_GET);
}
?>
