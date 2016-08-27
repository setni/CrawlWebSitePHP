<?php

//Launch file

require 'Crawler.class.php';

$url = 'http://www.movingimage24.com';
$fileArchi = 3;
$sameHost = true;

try {
    
	$crawler = new Crawler($fileArchi, $url, $sameHost);
	print_r($crawler->crawler());
    
} catch (Exception $e) {
    
	die($e->getMessage());
}