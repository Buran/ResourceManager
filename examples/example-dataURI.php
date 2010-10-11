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
		return '/*' . "\r\n" . $dataURI->getMhtmlContent() . '*/' . $dataURI->getProcessedContent();
	})
	->inline()
	->flush()
;
?>
</head>
	<body>
		<div>This square should be gray</div>
	</body>
</html>
