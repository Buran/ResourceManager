<?php
class DriverCSS extends Driver {

	public static function flushInline($data, $parameters = array()) {
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

	public static function flushFile($file_name, $parameters = array()) {
		$attributes = self::getTagAttributes(
			array('type', 'rel', 'href', 'media', 'id'),
			array('type' => 'text/css', 'rel' => 'stylesheet', 'href' => $file_name),
			$parameters
		);
		return array(
			'<link' . $attributes . ' />',
			''
		);
	}
}
