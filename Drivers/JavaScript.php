<?php
class DriverJavaScript extends Driver {

	public static function flushInline($data, $parameters = array()) {
		$attributes = self::getTagAttributes(
			array('type', 'language', 'src', 'type', 'id', 'defer', 'event', 'for', 'lang'),
			array('type' => 'text/javascript'),
			$parameters
		);
		return array(
			'<script' . $attributes . '>',
			'//<![CDATA[',
			$data,
			'//]]>',
			'</script>',
			''
		);
	}

	public static function flushFile($file_name, $parameters = array()) {
		$attributes = self::getTagAttributes(
			array('type', 'language', 'src', 'type', 'id', 'defer', 'event', 'for', 'lang'),
			array('type' => 'text/javascript', 'src' => $file_name),
			$parameters
		);
		return array(
			'<script' . $attributes . '></script>',
			''
		);
	}
}
