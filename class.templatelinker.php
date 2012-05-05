<?php

	require_once('class.parser.php');
	
	class TemplateLinker extends Parser {
		static $_debugMode = 0;
	 
		var $currentMode = 'h';
		var $lastMode = 'h';
		var $aMode = array();
		
		function __construct() {
			parent::__construct();
			
			//$this->addTokenHandler('/\r?\n/', '_nl');
			$this->addTokenHandler('/<%(\w)/', '_mode');
			$this->addTokenHandler('/%>/', '_end');
		}
		
		static function run($text) {
			if (self::$_debugMode == 2) return $text;
			$oParser = new self();
			return $oParser->parse($text);
		}
		
		function parse($text) {
			return parent::parse($text) . $this->transite();
		}
		
		function _nl() {
			switch($this->currentMode) {
				case 'p': return '';
				case 'h': return "\n";
				case 'e': return '';
			};
			return '';
		}
		
		function _mode($mode) {
			array_push($this->aMode, $this->currentMode);
			$this->currentMode = $mode;
			return '';
		}
		
		function _end() {
			$last = array_pop($this->aMode);
			if (empty($last)) throw new Exception("too much ending"); 
			$this->currentMode = $last;
			return '';
		}
		
		function _text($text) {
			if (empty($text)) return '';
			$ret = $this->transite();
			if (preg_match('/\?\>\r?\n/', $ret . $text)) $ret .= "\n";
			return $ret . $text;
		}
		
		function transite() {
			if (self::$_debugMode) $ret = $this->getTransitionDebug($this->lastMode, $this->currentMode);
			else $ret = $this->getTransition($this->lastMode, $this->currentMode);
			$this->lastMode = $this->currentMode;
			return $ret;
		}
			
		function getTransition($from, $to) {             
			$ret = '';
			switch($from . $to) {
				case 'hp':	$ret = '<?php '; break;   
				case 'he':	$ret = '<?php echo '; break;   
				case 'ph':	$ret = '?>'; break;   
				case 'pe':	$ret = 'echo '; break;   
				case 'ep':	$ret = ';'; break;   
				case 'eh':	$ret = '; ?>'; break;   
				case 'hh':	break;
				case 'ee':	$ret = '.'; break;
				case 'pp':	$ret = ' '; break;   
				default:
					throw new Exception("$from $to not found");
			}
			return $ret;
		}
		
		function getTransitionDebug($from, $to) {             
			$ret = '';
			if ($from == $to) return '';
			switch($to) {
				case 'p':	$ret = '##php:'; break;   
				case 'e':	$ret = '##echo:'; break;   
				case 'h':	$ret = '##html:'; break;
				default:	$ret = '##__'.$to.':'; break;   
			}
			return $ret;
		}
		
		
	// ausgabehelper
		static function toEcho($param) {
			if (is_null($param) || $param === '') return '';
			return '<%e'.$param.'%>';
		}
		static function toPhp($param) {
			if (is_null($param) || $param === '') return '';
			return '<%p'.$param.'%>';
		}		
		static function toHtml($param) {
			if (is_null($param) || $param === '') return '';
			return '<%h'.$param.'%>';
		}
		
	// helper
		static function linkTemplate($parsed) {
		   	$parsed = TemplateLinker::run($parsed);
			
		//*
			$parsed = preg_replace('/\?'.'>(\s+)<\?php\s+/smx', '?'.'><?php ', $parsed);
		//	$parsed = preg_replace('/\?'.'>(.{1,10})<\?php/smx', 'echo "\\1";', $parsed);
		//	$parsed = preg_replace('/echo ([^;]*); \?'.'>(.{1,10})<\?php/smx', 'echo \\1 . "\\2";', $parsed);
			$parsed = preg_replace('/\s*\?'.'><\?php\s+/smx', "\n", $parsed);
		//*/
			return $parsed;
		}


		
	}


?>