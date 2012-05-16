<?php
	require_once('../cfg.php');
	require_once(PATH_LIBS . 'common/cfg.php');
	require_once(PATH_LIB_COMMON . 'init.tools_debug.php');
	
	require_once('cfg.php');
	require_once('class.templateparser.php');
	require_once('class.templatecontext.php');

	class Base implements ITemplateBase, IDebug {
		var $name;
		
	// ITemplateBase
		function getTemplateSource($name) {
			$this->name = $name;
			return new File($this, $name);
		}
		
		function display($name) {
			try {
				$oFile = $this->getTemplateSource($name);
				$file = $oFile->parseFile();
			
				$oCtx = new TemplateContext($this);
				$oCtx->includeFile($file, $aVars);
			}
			catch(TemplateException $e) {
				$e->addObject($this);
				throw $e;
			}
		}
		
		function getDebugInfo() {
			return sprintf("template: %s", $this->name);
		}
	}
		
	class File implements ITemplateSource, IDebug {
		
	// ITemplateSource
		function getParseSource() {	
			return $this->oSource;
		}

		function __construct($oBase, $name) {
			$this->oBase = $oBase;
			$this->sourceFile = '_tmpl.'.$name.'.html';
			$this->parsedFile = '_cache.'.$name.'.php';
			$text = file_get_contents($this->sourceFile);
			$this->oSource = new ParseSource($text);
		}
		
		function parseFile() {
			try {
				$oParser = new TemplateParser($this->oBase);
				$parsed = $oParser->parse($this);
				file_put_contents($this->parsedFile, $parsed);
			}
			catch(TemplateException $e) {
				$e->addObject($this);
				throw $e;
			}
			return $this->parsedFile;
		}
		
		function getDebugObjects() {
			return $this->oSource;
		}
		function getDebugInfo() {
			return sprintf("source: %s\ntarget: %s", $this->sourceFile, $this->parsedFile);
		}
		
	}


	try {
		$oTmpl = new Base();
		$oTmpl->display('test');
		
		$oTmpl = new Base();
		$oTmpl->display('error');
	}
	catch(TemplateException $e) {
		echo $e->debug();
	}
?>