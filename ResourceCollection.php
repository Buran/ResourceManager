<?php
interface iResourceCollection {
	public function addFile();
	public function addString();
	public function addSingleFile();
	public function addSingleString();
	public function append($Resource);
}
class ResourceCollection extends BaseResource implements iResourceCollection {

	private $resources = array();

	public function __construct($driver = false) {
		parent::__construct($driver);
	}

	private function addResource($type, $file_names, $single = false) {
		if ($single) {
			if (count($file_names) > 1) {
				$file_names = array($file_names[0]);
			}
			$Resource = new Resource();
			$type == 'file'
				? $Resource->setFile($file_names[0])
				: $Resource->setString($file_names[0]);
			/*$Resource->setDriver($this->driver);*/
			$this->resources[] = $Resource;
			$Resource->collection = $this;
			return $Resource;
		} else {
			$class = __CLASS__;
			$Collection = new $class(/*$this->driver*/);
			$Collection->collection = $this;
			$this->resources[] = $Collection;
			foreach ($file_names as $data) {
				if (is_string($data)) {
					$type == 'file'
							? $Collection->addSingleFile($data)
							: $Collection->addSingleString($data);
				} else {
					$type == 'file'
							? $Collection->addFile($data)
							: $Collection->addString($data);
				}
			}
			return $Collection;
		}
	}

	public function addFile() {
		$file_names = func_get_args();
		return $this->addResource('file', $file_names);
	}

	public function addString() {
		$strings = func_get_args();
		return $this->addResource('string', $strings);
	}

	public function addSingleFile() {
		$file_names = func_get_args();
		return $this->addResource('file', $file_names, true);
	}

	public function addSingleString() {
		$strings = func_get_args();
		return $this->addResource('string', $strings, true);
	}

	public function removeResource($Resource) {
		foreach ($this->resources as $k => $r) {
			if ($Resource === $r) {
				unset($this->resources[$k]);
				//$Resource->collection = false;
				break;
			}
		}
	}

	public function flush() {
		if (!count($this->resources)) {
			return;
		}
		if ($this->file || $this->inline) {
			$driver = $this->getDriver();
			if (!$driver) {
				trigger_error('no output driver specified');
				return $this;
			}
			if (!$this->resourceDriverCompatible()) {
				//some of resources in collection differs, when flushing collection in MERGE (not ASIS) mode their resources need have the same driver
				trigger_error('resources driver incompatible');
				return $this;

			}
			$data = $this->getString(true);
			if ($data === false) {
				trigger_error('');
				return $this;
			}
			if ($this->inline) {
				$content = $driver->flushInline($data, $this->getComputedParameters());
			} else {
				//TODO: make here protection from overwriting original file!
				$file_name = $this->getFinalFileName();
				if (!$file_name) {
					trigger_error('');
					return $this;
				}
				file_put_contents($file_name, $data);
				$content = $driver->flushFile($file_name, $this->getComputedParameters());
			}
			echo implode("\n", $content);
		} else {
			foreach ($this->resources as $Resource) {
				$Resource->flush();
			}
		}
//		foreach ($this->resources as $k => $Resource) {
//			$Resource->collection = false;
//			unset($this->resources[$k]);
//		}
		if ($this->collection) {
			$this->collection->removeResource($this);
		}
		return $this;
	}

	protected function getString($handle_parent = false) {
		$data = '';
		foreach ($this->resources as $k => $Resource) {
			$data .= $Resource->getString();
		}
		return $this->handleData($data, $handle_parent);
	}

	/**
	 * Append resource or collection of resources
	 * @return
	 * @param $resource Object
	 */
	public function append($Resource) {
		if ($Resource->collection && $Resource->collection !== $this) {
			$Resource->collection->removeResource($Resource);
			trigger_error('resource binding reassigned, but it has been already appended to another collection');
		}
		$this->resources[] = $Resource;
		$Resource->collection = $this;
		return $this;
	}

	/**
	 * Detach resource or collection of resources
	 * @return
	 * @param $resource Object
	 */
	public function detach($Resource) {
		foreach ($this->resources as $k => $R) {
			if ($R === $Resource) {
				unset($this->resources[$k]);
			}

		}
		return $this;
	}

	protected function getFinalFileName() {
		if (is_string($this->file)) {
			return $this->file;
		}
		return 'foobar.js';
	}

	protected function resourceDriverCompatible() {
		foreach ($this->resources as $Resource) {
			//print_r(get_class($Resource->driver));
			$compatible = !$Resource->driver || (get_class($this->driver) === get_class($Resource->driver));
			if (!$compatible) {
				print_r($Resource->driver);
				print_r($this->driver);
			}
			if ($compatible && ($Resource instanceof ResourceCollection)) {
				$compatible = $Resource->resourceDriverCompatible();
			}
			if (!$compatible) {
				return false;
			}
		}
		return true;
	}

}