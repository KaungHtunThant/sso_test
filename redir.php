<?php
	session_start();

	var_dump($_SESSION['input']);  //Debug print
	echo "<hr>";
	var_dump($_SESSION['get1']);  //Debug print
	echo "<hr>";
	var_dump($_SESSION['get2']);  //Debug print
	echo "<hr>";
	var_dump($_SESSION['authdata']);  //Debug print
	echo "<hr>";
	var_dump($_SESSION['userdata']);  //Debug print
?>