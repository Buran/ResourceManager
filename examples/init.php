<?php
Header('Content-Type: text/html; charset=utf-8');
error_reporting((E_ALL | E_RECOVERABLE_ERROR) ^ E_STRICT);
ini_set('display_errors', true);
include '../Handlers/dataURL.php';
include '../Handlers/CSS2JavaScript.php';
include '../Handlers/JavaScriptPacker.php';
include '../Drivers/Driver.php';
include '../Drivers/CSS.php';
include '../Drivers/JavaScript.php';
include '../BaseResource.php';
include '../ResourceCollection.php';
include '../Resource.php';
