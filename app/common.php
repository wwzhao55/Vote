<?php
$entrance =  $_SERVER['PHP_SELF'];
if(!strstr($entrance,'index.php')){
	Header("Location: ../index.php");
}

