<?php


	class TemplateFile implements ITemplateSource {

		var $oBase;
		var $name = '';
		
		var $sourceFileName = '';
		var $parsedFileName = '';
		
		var $oSource = null;
		var $parsed = null;
		
		function __construct(ITemplateBase $oBase, $name) {
			$this->oBase = $oBase;
			$this->name = $name;
		}
		
	// processing and ITemplateBase hooking
		function loadSource() {
			$this->sourceFileName = $this->oBase->getSourceFileName($this->name);
			if (!file_exists($this->sourceFileName)) throw new TemplateException($this, 'source file "%s" not found', $this->sourceFileName);
			$source = file_get_contents($this->sourceFileName);
			return $source;
		}
		
		function parse() {                                  
			$this->oParser = $this->oBase->getParserObject();
			$parsed = $this->oParser->parse($this);	
			return $parsed;
		}
		
		function saveParsed($parsed) {
			$this->parsedFileName = $this->oBase->getParsedFileName($this->name);
			file_put_contents($this->parsedFileName, $parsed);
		}
		
		function parseFile() {
			try {
				$parsed = $this->parse();
				$this->saveParsed($parsed);
			}
			catch(Exception $e) {
				$this->oBase->handleException($e);
				throw $e;
				//echo dump($this);
			}
			return $this->parsedFileName;
		}
		
	// callback fuer class Parser
		function getParseSource() {
			if (!$this->oSource) {
				$source = $this->loadSource();
				$this->oSource = new ParseSource($source);	
			}
			return $this->oSource;
		}
		
	// error handling
		function debug(Exception $e) {
			echo dump($e);
			echo dump($this);
		}
		
	}

?>