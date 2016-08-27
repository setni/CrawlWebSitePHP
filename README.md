About
=====

A simple crawl script (array of url) writen in PHP, I did for a friend in 2015, You can use as you want.

Usage
=====

BECAREFULL : Depend of the website and your connection, it could take long time. <br/>
You should use asynchronous PHP [a link](http://stackoverflow.com/questions/5905877/how-to-run-the-php-code-asynchronous)
<br/>
```php
<?php
require 'Crawler.class.php';

$url = 'http://www.example.net'
$fileArchi = 3; //set here the depth you want
$sameHost = true;

try {
	$crawler = new Main($url, $fileArchi, $sameHost);
	print_r($crawler->crawler());
} catch (Exception $e) {
	die($e->getMessage());
}

```



