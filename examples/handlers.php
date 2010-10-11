<?php
include 'init.php';
?>
<html>
<head>
	<title>Example of using handlers</title>
</head>
<body>
<?php
$handleBorderRadius = function($string) {
		$replace_pairs = array(
			'border-radius' => array('/
					(border\-radius\s*:\s*)
					(\d+[a-z]*)
					([;|}])
				/ixs', '-webkit-border-radius: $2; -moz-border-radius: $2; $0'),
			'cursor' => array('/
						(cursor\s*:\s*)
						(pointer)
						([;|}])
			/ixs', '$1$2; $1hand$3'),
			'opacity' => array('/
						(opacity\s*:\s*)
						(\d*(?:\.\d*)?)
						([;|}])
			/ixs', function($matches) {
				return
					$matches[1] . $matches[2] . '; ' .
					'filter: alpha(opacity=' . round(floatval($matches[2]) * 100) . ');' .
					'-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=' . round(floatval($matches[2]) * 100) . ')"' . $matches[3];
			}, true)
		);
		foreach ($replace_pairs as $rule) {
			if (!empty($rule[2])) {
				$string = preg_replace_callback($rule[0], $rule[1], $string);
			} else {
				$string = preg_replace($rule[0], $rule[1], $string);
			}
		}
		return $string;
};

$CSS = new Resource('CSS');
$CSS
	->setFile('css/w3-correct.css')
	->handler($handleBorderRadius)
	->inline()
	->handler(function($string) {
		return '/* Author: Anton Ruban */' . "\r\n" . $string;
	})
	->flush()
;

$JS = new Resource();
$JS->driver('JavaScript')->setFile('js/example.js')->handler(function($string) {
		$packer = new JavaScriptPacker($string);
		return $packer->pack();
	})->inline()->file('tmp/example.js')->flush();
?>
<div id="foobar" style="border: 1px solid #D0D0D0; padding: 20px; width: 300px;">Hello, world</div>
</body>
</html>
