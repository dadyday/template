<?php
/******************************************************
Umgebung für Templates zur Laufzeit
verwaltet Template-Variablen, Formate, Makros, Slots
******************************************************/

	require_once('class.templateloopobject.php');

	class TemplateContext {
		static $oInstance;
		var $oBase;
		
		var $aVar = array();
		var $aFormat = array();
		var $aMacro = array();
		var $aSlot = array();
		
		function __construct(ITemplateBase $oBase) {
			TemplateContext::$oInstance = $this;
			$this->oBase = $oBase;
		}
		
		function initVars($aVar) {
			$this->aVar = VarStruct::convertItems($aVar);
		}
		
		function includeFile($includeFile, $aVars = null) {
			if (is_null($aVars)) $aVars = array_slice($GLOBALS,1);
			$this->initVars($aVars);
			$oldErr = error_reporting();
			error_reporting(($oldErr | E_NOTICE) - E_NOTICE);
			$this->inc($includeFile);
			error_reporting($oldErr);
		}
		private function inc($__inc) {
			$ctx = $this;
			extract($this->aVar);
			include($__inc);
		}
		
		function __get($name) {
			return isset($this->aVar[$name]) ? $this->aVar[$name] : '';
		}
		function __set($name, $value) {
			return $this->aVar[$name] = $value;
		}
		function __call($func, $aParams) {
			return call_user_func_array($func, $aParams);
		}
		
		function addFormat($name, $func) {
			$this->aFormat[$name] = $func;
		}
		
		function format($name, $value) {
			$func = isset($this->aFormat[$name]) ? $this->aFormat[$name] : $name.'Format';
			$aParams = array_slice(func_get_args(),1);
			return call_user_func_array($func, $aParams);
		}
		
		function addMacro($name, $func) {
			$this->aMacro[$name] = $func;
		}
		
		function macro($name, $value) {
			$func = isset($this->aMacro[$name]) ? $this->aMacro[$name] : $name.'Macro';
			$aParams = array_slice(func_get_args(),1);
			return call_user_func_array($func, $aParams);
		}
		
		function setSlot($name, $tmpl = null) {
			if (empty($tmpl)) $tmpl = $name;
			$this->aSlot[$name] = $tmpl;
		}
		
		function getSlot($name, $default = null) {
			if (empty($default)) $default = $name;
			$tmpl = empty($this->aSlot[$name]) ? $default : $this->aSlot[$name];
			return $this->getTemplate($tmpl);
		}
		
		function getTemplate($tmpl) {
			$oTmplFile = $this->oBase->getTemplateSource($tmpl);
			return $oTmplFile->parseFile();
			return $oTmplFile->parsedFileName;
		}
		
	}
	
	class VarStruct extends ArrayObject {
		
		static function convert($var) {
			if (is_scalar($var)) return $var;
			if (is_a($var, 'VarStruct')) return $var;
			if (is_object($var)) return new VarStruct($var);
			if (is_array($var)) return new VarStruct($var);
		}
		static function convertItems($var) {
			foreach($var as $key => $item) {
				$var[$key] = self::convert($item);
			}
			return $var;
		}

		private $_init = false;
		private $_aKey = array();
		
			
		public function __construct(&$obj) {
			$this->setFlags(ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
			parent::__construct($obj);
	    }
		
		public function __toString() {
			return 'struct';
		/*
			$i = 0; $ret = '';
			foreach($this as $item) {
				if ($ret) $ret .= ', ';
				$ret .= $item;
			}
			return $ret; //join('', (array) $this);
		*/
		}
		
		function initItems() {
			if ($this->_init) return;
			$this->_init = true;
			foreach($this as $key => $item) {
				if ($key{0} == '_') continue;
				$this->_aKey[] = $key;
				$this[$key] = self::convert($item);
			}
		}
		
		function getNumericOffset($offset) {
			$this->initItems();
			if (!is_numeric($offset)) return $offset;
			$key = array_slice($this->_aKey, $offset, 1);
			return $key[0];
		}
		
		public function offsetExists($offset) {
			$offset = $this->getNumericOffset($offset);
	        return parent::offsetExists($offset);
	    }
		public function offsetGet($offset) {
			$offset = $this->getNumericOffset($offset);
	        return parent::offsetGet($offset);
	    }
		public function offsetSet($offset, $value) {
			$offset = $this->getNumericOffset($offset);
			parent::offsetSet($offset, $value);
	    }
	    public function offsetUnset($offset) {
			$offset = $this->getNumericOffset($offset);
			parent::offsetUnset($offset);
	    }
	}	
?>