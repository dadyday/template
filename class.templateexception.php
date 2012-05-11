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
			if (is_a($oObject, 'ITemplateSource')) {
				$this->oSource = $oObject;
			}
			if (is_a($oObject, 'ITemplateBase')) {
				$this->oBase = $oObject;
			}
			$this->aObject[] = $oObject;
		}
		
		function debug() {
			echo '<div style="border:solid 1px black; background: #fee; color: #000; margin: 10px; padding:10px;">';
			echo '<b style="font-size:120%">'.$this->getMessage().'</b>';
			if ($this->oSource && method_exists($this->oSource, 'getDebugInfo')) echo '<pre>'.$this->oSource->getDebugInfo().'</pre>';
			echo dump($this->aObject);
			echo '</div>';
		}
	}
?>