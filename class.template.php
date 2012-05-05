<?php

	require_once('class.templatefile.php');
	require_once('class.templatecontext.php');

	interface ITemplateBase {
		function getParserObject();
		function getContextObject();
		function getTemplateFile($name);
		function getSourceFileName($name);
		function getParsedFileName($name);
	}
    
	
		
	class TemplateBase implements ITemplateBase {
		
		function __construct($name) {
			$this->name = $name;
		}
		
		
		function getTemplateFile($name) {
			$oTmplFile = new TemplateFile($this, $name);
			$this->aFile[] = $oTmplFile;
			return $oTmplFile;
		}
		function getParserObject() {
			require_once('class.templateparser.php');
			return new TemplateParser($this);
		}
		function getContextObject() {
			return new TemplateContext($this);
		}
		
		
		function getSourceFileName($name) {
			return '_tmpl.'.$name.'.html';
		}
		
		function getParsedFileName($name) {
			return '_cache.'.$name.'.php';
		}
		
		function parseFile() {
			$oTemplateFile = $this->getTemplateFile($this->name);
			return $oTemplateFile->parseFile();
		}
		
		function display($aVars = array()) {
			$file = $this->parseFile();
			$oCtx = $this->getContextObject();
			$oCtx->includeFile($file, $aVars);
		}
		
	}


?>