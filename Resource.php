<?php
interface iResource {
	public function setString($data);
	public function setFile($file_name);
}

class Resource extends BaseResource implements iResource {

	var $data = false;
	var $file_name = false;

	public function __construct($driver = false) {
		parent::__construct($driver);
	}

	public function setString($source) {
		$this->data = $source;
		$this->file_name = false;
		return $this;
	}

	public function setFile($file_name) {
		$this->data = false;
		$this->file_name = false;
		if (!is_string($file_name) || !trim($file_name)) {
			trigger_error('file name must be a non empty string');
			return $this;
		}
		$file_name = trim($file_name);
		if (!is_readable($file_name)) {
			trigger_error('file ' . $file_name . 'does not exist or is not readable');
			return $this;
		}
		$this->file_name = $file_name;
		return $this;
	}

	protected function getString($handle_parent = false) {
		if ($this->data !== false) {
			$data = $this->data;
		} elseif ($this->file_name !== false) {
			$data = file_get_contents($this->file_name);
		}
		if (isset($data)) {
			return $this->handleData($data, $handle_parent);

		}
		return false;
	}

	public function flush() {
		//resource is empty? -- do not do anything.
		if ($this->data === false && $this->file_name === false) {
			return $this;
		}
		$driver = $this->getDriver();
		if (!$driver) {
			trigger_error('no output driver specified');
			$this->file_name = false;
			$this->data = false;
			return $this;
		}
		$error = false;
		//inline flushing
		if ($this->inline || ($this->data !== false && !$this->file)) {
			$data = $this->getString(true);
			if ($data !== false) {
				$content = $driver->flushInline($data, $this->getComputedParameters());
			} else {
				$error = 'no data specified';
			}
		//file flushing
		} else {
			$file_name = $this->getFinalFileName();
			if ($file_name) {
				if ($file_name === $this->file_name) {
					//TODO: make here getting full list of handlers including parent handlers
					if ($this->hasHandlers()) {
						$error = 'you can not use content processing handlers when using original file (' . $file_name . '); set new file name; file flushing aborted';
					}
				} else {
					//get string with applying all handlers to it
					$data = $this->getString(true);
					if ($data !== false) {
						file_put_contents($file_name, $data);
					} else {
						$error = 'no data specified';
					}
				}
			} else {
				$error = 'no output file name specified; file flushing aborted';
			}
			if ($error === false) {
				//driver can return false if e.g. file flushing is not supported (as in HTML driver)
				$content = $driver->flushFile($file_name, $this->getComputedParameters());
			}
		}
		$this->file_name = false;
		$this->data = false;
		if ($error === false && $content !== false) {
			echo implode("\n", $content);
		} elseif ($error) {
			trigger_error($error);
		}
		if ($this->collection) {
			$this->collection->removeResource($this);
		}
		return $this;
	}

	protected function getFinalFileName() {
		if (is_string($this->file)) {
			return $this->file;
		} elseif (!empty($this->file_name)) {
			return $this->file_name;
		}
		return 'foobar.js';
	}

}
