<?php

	class TemplateException extends Exception {
		var $aObject = array();
		
		function __construct() {                      
			$aParam = func_get_args();
			$message = '';
			$aMessageParams = array();
			$code = -1;
			foreach ($aParam as $param) {
				if (is_object($param) && is_a($param, 'IDebug')) {
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
		
		function addObject(IDebug $oObject) {                                           
			$this->aObject[] = $oObject;
			if (method_exists($oObject, 'getDebugObjects')) {
				$aObjects = $oObject->getDebugObjects();
				if (!is_array($aObjects)) $aObjects = array($aObjects);
				foreach($aObjects as $oObject) {
					if (is_a($oObject, 'IDebug')) $this->addObject($oObject);
				}
			}
		}
		
		function debug() {
			echo '<div style="border:solid 1px black; background: #fee; color: #000; margin: 10px; padding:10px;">';
			echo '<b style="font-size:120%">'.$this->getMessage().'</b>';
			echo '<hr>';
			
			$aObject = array_reverse($this->aObject);
			foreach($aObject as $oObject) {              
				echo '<b style="font-size:100%">'.get_class($oObject).'</b>';
				echo '<pre style="margin: 0 5px 5px 5px;">'.trim($oObject->getDebugInfo()).'</pre>';
			}
			
			echo '</div>';
		}
	}
?>