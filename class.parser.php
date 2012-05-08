<?php

	interface IParseSource {
		function __construct($source);
		function getSource();
		function getPos();
		function seekTo($pos);
		function seekBy($delta);
	}

	class ParseSource implements IParseSource {    
		var $source = '';
		var $pos = 0;
		
		function __construct($source) {
			$this->source = (string) $source;
			$this->pos = 0;
		}
		
		function getSource() 	{ return $this->source; }
		function getPos() 		{ return $this->pos; }
		function seekTo($pos)	{ $this->pos = $pos; }
		function seekBy($delta)	{ $this->pos += $delta; }
		
		function getLine($pos = null, &$row = null, &$col = null) {
			$text = $this->source;
			$pos = is_null($pos) ? $this->pos : $pos; 
			
			$left = substr($text, 0, $pos);
			$right = substr($text, $pos);
			$a = strrpos($left, "\n")+1; if ($a === false) $a = 0;
			$e = strpos($right, "\n"); if ($e === false) $e = strlen($right);
			
			$row = substr_count($left, "\n");
			$col = $pos-$a;
			
			$line = substr($left, $a).substr($right, 0, $e);
			//echo $text, ' ', $pos, ' ', $a, ' ', $e, ' ', $line, ' ', $row, ' ', $col;
			return $line;
		}
		
	}

	class Parser {
		
		static function getSourceObj($param) {
			if (is_object($param) && is_a($param, 'IParseSource')) return $param;
			return new ParseSource($param);
		}
		
	
		
		var $aTokenDef = array();
		var $aHandlerDef = array('_text', '_text');
		var $oHandlerObj = null;
		
		function __construct($oHandlerObj = null) {
			$this->setHandlerObject($oHandlerObj);
		}
		
		function setHandlerObject($oHandlerObj = null) {
			$this->oHandlerObj = $oHandlerObj ? $oHandlerObj : $this;
		}

		function addTokenHandler($regExp, $handlerDef) {
			if (!is_array($handlerDef)) $handlerDef = array(null, $handlerDef);
			$this->addToken($regExp, $handlerDef[1]); 
			$this->addHandler($handlerDef[1], $handlerDef); 
		}
		
		function addToken($regExp, $name) {
			$oTokenDef = new stdClass();
			$oTokenDef->regExp = $regExp;
			$oTokenDef->name = $name;
			$this->aTokenDef[] = $oTokenDef; 
		}
		
		function addHandler($name, $handlerDef) {
			if (!is_array($handlerDef)) $handlerDef = array(null, $handlerDef); 
			$this->aHandlerDef[$name] = $handlerDef;
		}
		
		function getHandlerDef($tokenName) {
			$handlerDef = null;
			if (isset($this->aHandlerDef[$tokenName])) {
				$handlerDef = $this->aHandlerDef[$tokenName];
				if ($this->oHandlerObj) $handlerDef[0] = $this->oHandlerObj;
				if (is_null($handlerDef[0])) $handlerDef = $handlerDef[1];
			}
			else if ($this->oHandlerObj && method_exists($this->oHandlerObj, $tokenName)) {
				$handlerDef = array($this->oHandlerObj, $tokenName);
			}
			else if (function_exists($tokenName)) {
				$handlerDef = $tokenName;
			}
			return $handlerDef;
		}
		
		function handle($tokenName, $aParams = array()) {
			$handler = $this->getHandlerDef($tokenName);
			
			if ($tokenName{0} != '_') $d =& $this->_debugToken($tokenName, $handler, $aParams);
			
			if ($handler) $result = call_user_func_array($handler, $aParams);
			else $result = join('', $aParams);
			
			if ($tokenName{0} != '_') $this->_debugResult($d, $result);
			return $result;
		}
		
		
        static function parseText($text) {
			$oParser = new self();
			return $oParser->parse($text);
		}
		
		function init() {
			return '';
		}
		function concat($target, $part) {
			return $target . $part;
		}
		
		function parse($param = null) {
			$this->_stop = false;
			$oSrc = $this->oSrc = self::getSourceObj($param);
			$parsed = $this->init();
			while (1) {
				$oMatch = $this->getNextMatch($oSrc);
				if (!$oMatch) break;
				
				$text = substr($oSrc->source, $oSrc->pos, $oMatch->pos - $oSrc->pos);
				$oSrc->pos = $oMatch->pos;
				
				$p = $this->handle('_text', array($text));
				if ($p === false) return $parsed;
				if ($p !== '') $parsed = $this->concat($parsed, $p);
				
				$oSrc->match = $oMatch->match;
				$oSrc->matchPos = $oMatch->pos;
				$oSrc->matchLen = strlen($oMatch->match);
				
				$oSrc->pos += $oSrc->matchLen;
				$p = $this->handle($oMatch->name, $oMatch->aParams);
				
				if ($p === false) return $parsed;
				if ($p !== '') $parsed = $this->concat($parsed, $p);
				
				if ($this->_stop) return $parsed;
			}
			$text = substr($oSrc->source, $oSrc->pos);
			$oSrc->pos = strlen($oSrc->source);
			
			$p = $this->handle('_text', array($text));
			if ($p !== '') $parsed = $this->concat($parsed, $p);
			
			return $parsed;			
		}
		
		var $_stop = false;
		function stop() {
			$this->_stop = true;
		}
		
		function getNextMatch(IParseSource $oSrc) {
			$oMatch = false;
			foreach ($this->aTokenDef as $oTokenDef) {
				$oM = $this->getMatch($oTokenDef, $oSrc);
				if ($oM and (!$oMatch or $oMatch->pos >= $oM->pos)) $oMatch = $oM;	
			};
			$this->oMatch = $oMatch;
			return $oMatch;
		}
				
		function getMatch($oTokenDef, IParseSource $oSrc) {
			$ok = preg_match($oTokenDef->regExp, $oSrc->getSource(), $aMatch, PREG_OFFSET_CAPTURE, $oSrc->getPos());
			//if ($aMatch[2][0] == 'button') echo dump($aMatch);
			if (!$ok) return false;
			$oMatch = new stdClass();
			$oMatch->match = $aMatch[0][0];
			$oMatch->pos = $aMatch[0][1];
			$oMatch->name = $oTokenDef->name;
			$oMatch->aParams = array();
			for($i = 1; $i < count($aMatch); $i++) $oMatch->aParams[] = $aMatch[$i][0];  
			return $oMatch;
		}
		
		
	// debug helper
		protected $aDebug = array();
		protected function &_debugToken($tokenName, $handler, $aParams) {
			$aEntry = array($tokenName, $handler, $aParams, '');
			$this->aDebug[] = &$aEntry;
			return $aEntry;
		}
		protected function _debugResult(&$aEntry, $parsed) {
			$aEntry[3] = $parsed;
		}
		
		function debugText() {
			function _elipsed($str, $len) {
				if (!is_string($str)) return $str;
				$str = preg_replace('/\s+/', ' ', $str);
				return strlen($str) > $len ? substr($str, 0, $len) . '...' : $str;
			};
		
			$result = "";
			foreach ($this->aDebug as $aEntry) {
				list($token, $handler, $aParams, $parsed) = $aEntry;
				
				if (is_array($handler)) $handler = sprintf('%s::%s', get_class($handler[0]), $handler[1]);
				else $handler = sprintf('(global) %s', $handler);
				
				foreach($aParams as $i => $param) $aParams[$i] = '"' . _elipsed($param, 10) . '"';
				$params = join(', ', $aParams);
				
				$parsed = _elipsed($parsed, 20);
				
				$result .= sprintf("%s: %s(%s) -> \"%s\"\n", $token, $handler, $params, $parsed);
			}
			return $result;
		}
		
	};
		
?>