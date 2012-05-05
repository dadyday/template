<?php

	interface ITemplateFile {
		function __construct(ITemplateBase $oBase, $name);
		function parseFile();
		function parse();
		function getSource();
	};

	class TemplateFile implements ITemplateFile {

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
		
		function parseFile() {
			try {
				$this->loadSource();
				$this->parsed = $this->parse();
				$this->saveParsed();
			}
			catch(Exception $e) {
				throw $e;
				//echo dump($this);
			}
			return $this->parsedFileName;
		}
		
		function parse() {                                  
			$this->oParser = $this->oBase->getParserObject();
            $parsed = $this->oParser->parseFile($this);
			return $parsed;
		}
		
		function getSource() {
			if (is_null($this->oSource)) {
				$source = $this->loadSource();
				$this->oSource = new ParseSource($source);
			} 
			return $this->oSource;
		}
		
		function loadSource() {
			$this->sourceFileName = $this->oBase->getSourceFileName($this->name);
			if (!file_exists($this->sourceFileName)) throw new Exception(sprintf('source file "%s" not found', $this->sourceFileName));
			$source = file_get_contents($this->sourceFileName);
			//$this->oSource = $source;
			return $source;
		}
		
		function saveParsed() {
			$this->parsedFileName = $this->oBase->getParsedFileName($this->name);
			file_put_contents($this->parsedFileName, $this->parsed);
		}
		
	}

?>