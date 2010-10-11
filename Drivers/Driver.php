<?php
interface iDriver {
	public static function flushInline($data, $parameters = array());
	public static function flushFile($file_name, $parameters = array());
}
abstract class Driver implements iDriver {

	public static function flushInline($data, $parameters = array()) {
		return array(
			$data,
			''
		);
	}

	public static function flushFile($file_name, $parameters = array()) {
		return array(
			$file_name,
			''
		);
	}

	protected static function getTagAttributes($allowed, $base, $override = array()) {
		$parameters = array_merge($base, $override);
		$parameters_filtered = array();
		foreach ($allowed as $parameter) {
			if (isset($parameters[$parameter])) {
				$parameters_filtered[$parameter] = $parameters[$parameter];
			}
		}
		$parameters_prepared = array();
		foreach ($parameters_filtered as $parameter => $value) {
			if ($value === true) {
				$value = $parameter;
			}
			if (is_string($value) && trim($value)) {
				$parameters_prepared[] = $parameter . '="' . htmlspecialchars($value) . '"';
			}
		}
		return $parameters_prepared ? (' ' . implode(' ', $parameters_prepared)) : '';
	}
}
