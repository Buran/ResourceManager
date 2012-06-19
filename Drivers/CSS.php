<?php
class DriverCSS extends Driver {

	private $base_uri;

	public function __construct($options) {
		$this->base_uri = $options['base-uri'] || '';
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
		$attributes = self::getTagAttributes(
			array('type', 'rel', 'href', 'media', 'id'),
			array('rel' => 'stylesheet', 'href' => $file_name),
			$parameters
		);

		if (!empty($this->base_uri) && strpos($attributes['href'], '/') !== 0) {
			$attributes['href'] = $this->base_uri . $attributes['href'];
		}

		return array(
			'<link' . $attributes . ' />',
			''
		);
	}
}
