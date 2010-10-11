<?php
interface iBaseResource {
	public function setDriver($name);
	public function inline($flag = true);
	public function handler($handler);
	public function file($flag = true);
	public function parameter($name, $value = true);
	public function flush();
//	protected function postponed($flag = true);
//	protected function setPriority($priority = false);
}

abstract class BaseResource implements iBaseResource {

	protected $driver = false;
	protected $inline = false;
	protected $handlers = array();
	protected $file = false;
	protected $parameter = array();
	protected $collection = false;

	abstract protected function getString();
	abstract protected function getFinalFileName();

	public function __construct($driver) {
		$this->setDriver($driver);
	}

	public function setDriver($name) {
		if ($name === false) {
			$this->driver = false;
		} elseif (is_object($name)) {
			$this->driver = $name;
		} else {
			$class_name = 'Driver' . $name;
			if (class_exists($class_name)) {
				$this->driver = new $class_name();
			} else {
				$this->driver = false;
				trigger_error('there is no driver "' + $name + '"');
			}
		}
		return $this;
	}

	public function driver($name) {
		return $this->setDriver($name);
	}

	public function inline($flag = true) {
		$this->inline = (bool) $flag;
		$this->file = false;
		return $this;
	}

	public function handler($handler) {
		$this->handlers[] = $handler;
		return $this;
	}

	public function file($flag = true) {
		$this->file = $flag;
		$this->inline = false;
		return $this;
	}

	public function parameter($name, $value = true) {
		if ($value === null) {
			unset($this->parameter[$name]);
		} else {
			$this->parameter[$name] = $value;
		}
		return $this;
	}
	
	protected function handleData($data, $handle_parent = false) {
		$parameter = $this->getComputedParameters();
		foreach ($this->handlers as $handler) {
			$data = $handler($data, $parameter);
		}
		if ($handle_parent && $this->collection) {
			$data = $this->collection->handleData($data, true);
		}
		return $data;
	}

	protected function hasHandlers() {
		if (!$this->handlers && $this->collection) {
			return $this->collection->hasHandlers();
		}
		return $this->handlers ? true : false;
	}

	protected function getComputedParameters() {
		$parameter = $this->parameter;
		if ($this->collection) {
			$parameter = array_merge($parameter, $this->collection->getComputedParameters());
		}
		return $parameter;
	}

	protected function getDriver() {
		if (!$this->driver && $this->collection) {
			return $this->collection->getDriver();
		}
		return $this->driver;
	}

}