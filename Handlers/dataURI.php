<?php
class dataURI {
	var $config = array(
		//'combine_MHTML_dataURI' => true,
		'separate_dataURI' => true,
		'MHTML_path' => '',
		'expression_MHTML_path' => true,
		'images_basedir' => '',
        'separator' => "MY_BOUNDARY_SEPARATOR",
        'CRLF' => "\r\n",
		//'expires' => 'next year',
		//'separate_IE6_IE7' => false,
		//'UA_sniffing' => false
	);
	var $default_regex = '/
		(background(?:-[\w]+)?\s*\:[^;]+?)		#this will be saved#
		\/\*\s*inline\s*\*\/\s*					#this will be removed#
		(url\(\s*(\'|")?(.*?)\3?\s*\))			#this will be processed#
		([^;\}]*)								#this will be saved#
	/x';
	var $content = '';
	var $processed_content = '';
	var $files = array();
	function __construct($string, $config = array()) {
		$this->config = array_merge($this->config, $config);
		$this->setContent($string);
	}

	public function setContent($string) {
		$this->processed_content = '';
		$this->files = array();
		$this->dataURI = '';
		$this->content = $string;
		$this->processed_content = preg_replace_callback($this->default_regex, array( &$this, 'processContent'), $this->content);
	}

	public function getProcessedContent() {
		return $this->processed_content;
	}

	public function getMHTMLContent() {
		$MHTML_data = array();
		//$MHTML_data[] = $this->config['CRLF'];
		$MHTML_data[] = 'Content-Type: multipart/related; boundary="' . $this->config['separator'] . '"' . $this->config['CRLF'];
		foreach ($this->files as $file_name) {
			$base64_data = $this->file2Base64($file_name);
			$MHTML_part_name = md5($file_name);
			$mime = 'image/' . pathinfo($file_name, PATHINFO_EXTENSION);
			$MHTML_data[] = $this->getMHTMLPart($base64_data, $mime, $MHTML_part_name);
		}
		$MHTML_data[] = '--' . $this->config['separator'] . '--' . $this->config['CRLF'];
		return implode('', $MHTML_data);
	}

	public function saveMHTML($file_name) {
		file_put_contents($file_name, $this->getMHTMLContent());
	}

	public function setImagePathHandler($handler) {
	}

	private function getMHTMLDataPath() {
		if (!$this->config['expression_MHTML_path']) {
			return $this->config['MHTML_path'];
		}
		if (!$this->config['MHTML_path']) {
			$MHTML_path = '" + document.location + "';
			//$MHTML_path = 'http://localhost/resource-manager/examples/background-inline.php';
		} elseif (strpos($this->config['MHTML_path'], '/') !== 0) {
			$MHTML_path = '" + document.location.href.substr(0, document.location.href.lastIndexOf("/") + 1) + "' . $this->config['MHTML_path'];
		} else {
			$MHTML_path = '" + document.location.protocol + "//" + document.location.host + "' . $this->config['MHTML_path'];
		}
		return $MHTML_path;
	}

	private function file2Base64($file_name) {
		return base64_encode(file_get_contents($file_name));
	}

	/**
	 * @param  $matches
	 * @return string
	 * TODO: extract background-image to separate attribute for IE.
	 */
	private function processContent($matches) {
		$file_name = $this->config['images_basedir'] . $matches[4];
		$this->files[] = $file_name;
		$base64_data = $this->file2Base64($file_name);
		$MHTML_part_name = md5($file_name);
		$ie_hack_part = '; *' . $matches[1] . 'expression("url(mhtml:' . $this->getMHTMLDataPath() . '!' . $MHTML_part_name . ')")' . $matches[5];
		return $matches[1] . 'url(data:image/png;base64,' . $base64_data . ')' . $matches[5] . $ie_hack_part;
	}

	private function getMHTMLPart($base64, $mime, $name) {
		return $this->config['CRLF'] . '--' . $this->config['separator'] . $this->config['CRLF'] .
			'Content-Location:' . $name . $this->config['CRLF'] .
			'Content-Transfer-Encoding:base64' . $this->config['CRLF'] .
			'Content-Type:' . $mime . $this->config['CRLF'] .
			$this->config['CRLF'] .
			$base64 . $this->config['CRLF'];
	}

	protected function nativeImagePathHandler() {
	}
}
