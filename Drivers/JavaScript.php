<?php
class DriverJavaScript extends Driver {

	private $base_uri;

	public function __construct($options = array()) {
		$this->base_uri = !empty($options['base-uri']) ? $options['base-uri'] : '';
	}

	public function flushInline($data, $parameters = array()) {
		$attributes = self::getTagAttributes(
			array('type', 'language', 'src', 'type', 'id', 'defer', 'event', 'for', 'lang'),
			array(),
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

	public function flushFile($file_name, $parameters = array()) {
		if (!empty($this->base_uri) && strpos($file_name, '/') !== 0) {
			$file_name = $this->base_uri . $file_name;
		}

		$attributes = self::getTagAttributes(
			array('language', 'src', 'type', 'id', 'defer', 'event', 'for', 'lang', 'charset', 'async'),
			array('src' => $file_name),
			$parameters
		);

		return array(
			'<script' . $attributes . '></script>',
			''
		);
	}
}
