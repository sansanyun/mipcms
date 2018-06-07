<?php

$url = $_SERVER['REQUEST_URI'];
$url = str_replace("?","&",$url);
header('HTTP/1.1 302 Moved Permanently');
header('Location:index.php?s='.$url);