<?php
Header('Content-Type: text/html; charset=utf-8');
error_reporting((E_ALL | E_RECOVERABLE_ERROR) ^ E_STRICT);
ini_set('display_errors', true);
include dirname(__FILE__) . '/../Handlers/dataURI.php';
include dirname(__FILE__) . '/../Handlers/CSS2JavaScript.php';
include dirname(__FILE__) . '/../Handlers/JavaScriptPacker.php';
include dirname(__FILE__) . '/../Handlers/CSSExpander.php';
include dirname(__FILE__) . '/../Drivers/Driver.php';
include dirname(__FILE__) . '/../Drivers/CSS.php';
include dirname(__FILE__) . '/../Drivers/JavaScript.php';
include dirname(__FILE__) . '/../BaseResource.php';
include dirname(__FILE__) . '/../ResourceCollection.php';
include dirname(__FILE__) . '/../Resource.php';
