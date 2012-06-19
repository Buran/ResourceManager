<?php
include 'init.php';

$JS = new Resource(new DriverJavaScript(array('base-uri' => '/')));
$JS->setFile('js/example.js');
$JS->flush();
