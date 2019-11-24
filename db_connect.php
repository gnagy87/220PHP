<?php
	$conn = mysqli_connect('localhost', 'test', 'test1234', 'testdb');

	if(!$conn) {
	echo 'connection error: ' . mysqli_connect_error();
	}
?>