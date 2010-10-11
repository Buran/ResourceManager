<?php
include 'init.php';
?>
<html>
	<head>
		<title>Inline images testing</title>
<?php
$Resources = new ResourceCollection('CSS');
$Resources
	->addFile('css/background-inline.css')
	->handler(function($string, $config) {
		$dataURI = new dataURI($string);
		$GLOBALS['tmp'] = $dataURI->getMhtmlContent();
		return $dataURI->getProcessedContent();
	})
	->handler(function($string) {
		return '/*' . "\r\n" . $GLOBALS['tmp'] . '*/' . CSS2JavaScript($string);
	})
	->inline();

$Resources = new ResourceCollection('JavaScript');
$CSS = $Resources
	->driver('CSS')
	->addFile('css/background-inline.css')
	->handler(function($string, $config) {
//		$dataURI = new dataURI($string, array('MHTML_path' => 'tmp/data.css'));
//		$dataURI->saveMHTML('tmp/data.css');
		$dataURI = new dataURI($string);
//		$dataURI->saveMHTML('tmp/data.css');
		$GLOBALS['tmp'] = $dataURI->getMhtmlContent();
		return $dataURI->getProcessedContent();
		//return '/*' . $dataURI->getMhtmlContent() . '*/' . $dataURI->getProcessedContent();
	})
	->handler(function($string) {
		return '/*' . "\r\n" . $GLOBALS['tmp'] . '*/' . CSS2JavaScript($string);
	})
	->file('tmp/background-inline.cache.css')
	->inline()
	->driver('JavaScript');


$Resources = new ResourceCollection('CSS');
$Resources
	->driver('JavaScript')
	->addFile('css/background-inline.css')
	->handler(function($string) {
		$dataURI = new dataURI($string, array('MHTML_path' => 'tmp/css-inline.cache.js'));//path relative to page (not CSS or JS file) URL
		return '/*' . "\r\n" . $dataURI->getMhtmlContent() . '*/' . CSS2JavaScript($dataURI->getProcessedContent());
	})
	->file('tmp/css-inline.cache.js');
//	->inline();

$Resources->flush();
?>
</head>
	<body>
		<div>This square should be gray</div>
	</body>
</html>
