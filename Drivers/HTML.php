<?php
class DriverHTML extends Driver {

	public static function flushInline($data, $parameters = array()) {
		$attributes = self::getTagAttributes(
			array(),
			array(),
			$parameters
		);
		return array(
			$data
		);
	}

	public static function flushFile($file_name, $parameters = array()) {
		trigger_error('driver "HTML" do not support flushing as file');
		return false;
	}
}
