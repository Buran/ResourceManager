<?php
class DriverCSS extends Driver {

	private $base_uri;

	public function __construct($options = array()) {
		$this->base_uri = !empty($options['base-uri']) ? $options['base-uri'] : '';
	}

	public function flushInline($data, $parameters = array()) {
		$attributes = self::getTagAttributes(
			array('type', 'media', 'id'),
			array('type' => 'text/css'),
			$parameters
		);
		return array(
			'<style' . $attributes . '>',
			$data,
			'</style>',
			''
		);
	}

	public function flushFile($file_name, $parameters = array()) {
		if (!empty($this->base_uri) && strpos($attributes['href'], '/') !== 0) {
			$attributes['href'] = $this->base_uri . $attributes['href'];
		}

		$attributes = self::getTagAttributes(
			array('type', 'rel', 'href', 'media', 'id'),
			array('rel' => 'stylesheet', 'href' => $file_name),
			$parameters
		);

		return array(
			'<link' . $attributes . ' />',
			''
		);
	}
}
