<?php

	class TemplateException extends Exception {
		var $aObject = array();
		
		function __construct() {                      
			$aParam = func_get_args();
			$message = '';
			$aMessageParams = array();
			$code = -1;
			foreach ($aParam as $param) {
				if (is_object($param)) {
					$this->addObject($param);
				}
				elseif (empty($message)) {
					if (is_string($param)) $message = $param;
					if (is_numeric($param)) $code = $param;
				}
				else {
					$aMessageParams[] = $param;
				}
				
			}
			$message = vsprintf($message, $aMessageParams);
			parent::__construct($message, $code);
		}
		
		function addObject($oObject) {
			if (is_a($oObject, 'TemplateParser')) {
				$this->oParser = $oObject;
			}
			$this->aObject[] = $oObject;
		}
		
		function debugSourceLine() {
			
					
			$file = $this->oTemplate->oParser->oSrc->source;
			$src = $this->oParser->oParser->oSrc->source;
			$pos = $this->oParser->oParser->oMatch->pos;
			$len = strlen($this->oParser->oParser->oMatch->match);
			
			getCurrentLine($src, $pos, $line, $row, $col);
			
			$l = substr($line, 0, $col); 
			$m = substr($line, $col, $len);
			$r = substr($line, $col+$len); 
			return sprintf("#%d: %s<b>%s</b>%s\n%s^ #%d", $row, $l, $m, $r, str_repeat('-',$col+strlen($row)+3), $col);
		}
		
		function debug() {
			
			echo '<div style="border:solid 1px black; background: #fee; color: #000; margin: 10px; padding:10px;">';
			echo '<b style="font-size:120%">'.$this->getMessage().'</b>';
			echo '<pre>'.$this->oParser->oTemplate->debugSourceLine().'</pre>';
			echo dump($this->aObject);
			echo '</div>';
		}
	}
?>