<?php
function CSS2JavaScript($string) {
	$css = explode("\n", $string);
	$css_as_js = '[' . implode(",\n", array_map(function($v) {
		return '\'' . str_replace('\'', '\\\'', trim($v)) . '\'';
	}, $css)) . ']';
	ob_start();
?>
(function() {
	cssText = <?php echo $css_as_js ?>.join('');
	var ua = navigator.userAgent.toLowerCase(), doc = document, ss;
	var head = doc.getElementsByTagName('head')[0];
	var rules = doc.createElement('style');
	rules.setAttribute('type', 'text/css');
	if ((ua.indexOf('opera') === -1) && (ua.indexOf('msie') > -1)) {//isIE
		head.appendChild(rules);
		ss = rules.styleSheet;
		ss.cssText = cssText;
	} else {
		try {
			rules.appendChild(doc.createTextNode(cssText));
		} catch(e) {
			rules.cssText = cssText;
		}
		head.appendChild(rules);
		ss = rules.styleSheet ? rules.styleSheet : (rules.sheet || doc.styleSheets[doc.styleSheets.length - 1]);
	}
})();
<?php
    $r = ob_get_clean() . "\n";
    return $r;
}
