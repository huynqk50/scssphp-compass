<?php

class scss_compass {
	protected $libFunctions = array("lib_compact",'font_files','font_url');
	
	static public $true = array("keyword", "true");
	static public $false = array("keyword", "false");

	public function __construct($scss) {
		$this->scss = $scss;
		$this->updateImportPath();
		$this->registerFunctions();
	}

	protected function updateImportPath() {
		$this->scss->addImportPath(__DIR__ . "/stylesheets/");
	}

	protected function registerFunctions() {
		foreach ($this->libFunctions as $fn) {
			$registerName = $fn;
			if (preg_match('/^lib_(.*)$/', $fn, $m)) {
				$registerName = $m[1];
			}
			$registerName = str_replace('_','-',$registerName);
			$this->scss->registerFunction($registerName, array($this, $fn));
		}
	}

	public function lib_compact($args) {
		list($list) = $args;
		if ($list[0] != "list") return $list;

		$filtered = array();
		foreach ($list[2] as $item) {
			if ($item != self::$false) $filtered[] = $item;
		}
		$list[2] = $filtered;
		return $list;
	}

	public function font_files()
	{
		$files = func_get_args();
		$files = $files[0];
		$list = "";
		while(!empty($files)) {
			$file = array_shift($files);

			$file = $this->concatenate($file[2]);
//			echo json_encode($file);die;
			$type = array_shift($files);
			$type = $this->concatenate($type[2]);
			$list .= $this->font_url($file)." format('$type')";
			if (!empty($files))
				$list .= ", ";
		}
		return $list;
	}

	public function font_url($path)
	{
		if(is_array($path)) {
			$args = $path[0];

			$path = $this->concatenate($args[2]);

		}
		return "url('$path')";
	}

	private function concatenate($data)
	{

		$string = array_shift($data);
		while(!empty($data)) {
			$s = array_shift($data);
			if($s[0]== "string") {
				$string .= $this->concatenate($s[2]);
			}elseif($s[0] == "interpolate"){
				$string .= $this->concatenate($s[1][2]);
			}elseif(is_string($s)){
				$string .= $s;
			}
		}
		return $string;

	}
}
