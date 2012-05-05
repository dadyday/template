<?php

	class BlockStack {
		var $oParser;
		var $aBlock = array();
		
		function __construct($oParser) {
			$this->oParser = $oParser;
		}
		
		function &push($type, Block $obj = null) {
			$oBlock = is_null($obj) ? new Block() : $obj;
			$oBlock->type = $type;
			array_push($this->aBlock, $oBlock);
			return $oBlock;
		}
		function &get($type = null) {
			$oBlock = end($this->aBlock);
			if (!is_null($type) && $type != $oBlock->type) throw new TemplateException($this->oParser, 'block type "%s" found instead of "%s"', $oBlock->type, $type);
			return $oBlock;
		}
		function &pop($type = null) {
			$oBlock = array_pop($this->aBlock);
			if (!is_null($type) && $type != $oBlock->type) throw new TemplateException($this->oParser, 'block type "%s" found instead of "%s"', $oBlock->type, $type);
			return $oBlock;
		}
	}

	class Block {
		var $type = '';
	}
	
	class BlockLoop extends Block {
		static $lastVarNum = 0;
		static $nextIndent = 0;
		var $varNum = 0;
		var $indent = 0;
		
		var $init, $start, $end;
		
		var $aFeature = array();
		

		function __construct($init, $start, $end) {
			$this->varNum = ++self::$lastVarNum;
			$this->indent = self::$nextIndent++;
			
			$cr = "\n".str_repeat("\t", $this->indent);
			$this->init = $cr.$this->setVarNum($init);
			$this->start = $cr.$this->setVarNum($start);
			$this->end = $cr.$this->setVarNum($end);
		}
		
		function __destruct() {
			self::$lastVarNum--;
		}
	   
		function setVarNum($string) {
			$string = preg_replace('/\$__a\b/', '$__a'.$this->varNum, $string);
			return $string;
		}
		
		function getCondition($cond, $ifContent, $elseContent = null) {
			$cond = $this->setVarNum($cond);
			$cr = "\n".str_repeat("\t", $this->indent+1);
			$ret = '';
			if (!empty($ifContent)) {
				$ret .= $cr.'if('.$cond.') {'.$ifContent.'}';
				if (!empty($elseContent)) $ret .= $cr.'else {'.$elseContent.'}';
				$ret .= ';';
			}
			elseif (!empty($elseContent)) $ret .= $cr.'if(!('.$cond.')) {'.$elseContent.'};';
			return $ret;
		}
		function getCode($code) {
			$code = $this->setVarNum($code);
			return $code;
		}
		
		function addCondition($position, $cond, $ifContent, $elseContent = null) { 
			$oFeature = $this->getFeature();
			$oFeature->$position .= $this->getCondition($cond, $ifContent, $elseContent);
			//if ($oFeature->type == 'between') echo dump($oFeature, $position);
		}
		function addCode($position, $code) {
			$oFeature = $this->getFeature();
			$oFeature->$position .= $this->getCode($code);	
		}

		
		function build($content) {
			$init = ''; $start = ''; $before = ''; $after = ''; $end = '';
			
		// Loopfeatures einsetzen
			if ($this->aFeature) {
				while($oFeature = $this->popFeature()) {
					$init = 	$init.	$oFeature->init;
					$start = 	$start.	$oFeature->start;
					$before = 			$oFeature->before.	$before;
					$after = 	$after.	$oFeature->after;
					$end = 				$oFeature->end.		$end;
				}
			};

			$result = 
				$this->init.$init.
				$this->start.$start.
				$before.$content.$after.
				$end.$this->end;
				
			self::$nextIndent--;
			return $result;
		}
		
		function &addFeature($type = '') {
			$oFeature = new LoopFeature();
			$oFeature->type = $type;
			array_push($this->aFeature, $oFeature);
			return $oFeature;
		}
		function &getFeature() {
			$oFeature = end($this->aFeature);
			return $oFeature;
		}
		function &popFeature() {
			$oFeature = array_pop($this->aFeature);
			return $oFeature;
		}
	}

	class LoopFeature {
		var $type = '';
		var $init = '';
		var $start = '';
		var $before = '';
		var $after = '';
		var $end = '';
		
		var $_content = '';
		var $_beforeContent = '';
		var $_afterContent = '';
		var $_elseContent = '';
		var $_beforeElseContent = '';
		var $_afterElseContent = '';
	}
	
	

?>