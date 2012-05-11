<?php
	require_once('../cfg.php');
	require_once(PATH_LIBS . 'common/cfg.php');
	require_once(PATH_LIB_COMMON . 'init.tools_debug.php');
	
	require_once('cfg.php');
	require_once('class.templateparser.php');
	require_once('class.templatecontext.php');

	class Base implements ITemplateBase {
		
	// ITemplateBase
		function getTemplateSource($name) {
			return new File($this, $name);
		}
		
		function display($name) {
			try {
				$oFile = $this->getTemplateSource($name);
				$file = $oFile->parse();
			
				$oCtx = new TemplateContext($this);
				$oCtx->includeFile($file, $aVars);
			}
			catch(TemplateException $e) {
				$e->addObject($this);
				throw $e;
			}
		}
	}
		
	class File implements ITemplateSource {
		
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
		
		function parse() {
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
		
		function getDebugInfo() {
			return $this->sourceFile;
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