<?php


	class TemplateFile implements ITemplateSource {

		var $oBase;
		var $name = '';
		
		var $sourceFileName = '';
		var $parsedFileName = '';
		
		var $oSource = null;
		var $parsed = null;
		
	// ITemplateSource
		function __construct(ITemplateBase $oBase, $name) {
			$this->oBase = $oBase;
			$this->name = $name;
		}
		
	// callback fuer class Parser
		function getParseSource() {
			if (!$this->oSource) {
				$source = $this->loadSource();
				$this->oSource = new ParseSource($source);	
			}
			return $this->oSource;
		}
		
		
	// processing and ITemplateBase hooking
		function loadSource() {
			$this->sourceFileName = $this->oBase->getSourceFileName($this->name);
			if (!file_exists($this->sourceFileName)) throw new TemplateException($this, 'source file "%s" not found', $this->sourceFileName);
			$source = file_get_contents($this->sourceFileName);
			return $source;
		}
		
		function parse() {
			try {
				$this->oParser = $this->oBase->getParserObject();
				$parsed = $this->oParser->parse($this);	
				return $parsed;
			}
			catch(TemplateException $e) {
				$e->addObject($this);
				throw $e;
			}
		}
		
		function saveParsed($parsed) {
			$this->parsedFileName = $this->oBase->getParsedFileName($this->name);
			file_put_contents($this->parsedFileName, $parsed);
		}
		
		function parseFile() {
			$parsed = $this->parse();
			$this->saveParsed($parsed);
			return $this->parsedFileName;
		}
		
		
	// error handling
		function debug(Exception $e) {
			echo dump($e);
			echo dump($this);
		}
		
		function getDebugInfo() {
			$file = $this->name;
			$src = $this->oSource->source;
			$pos = $this->oSource->matchPos;
			$len = $this->oSource->matchLen;
			
			$line = $this->oSource->getLine($pos, $row, $col);
			
			$l = substr($line, 0, $col); 
			$m = substr($line, $col, $len);
			$r = substr($line, $col+$len); 
			return sprintf("file: %s #%d\nline: %s%s%s\npos:  %s[%s] #%d\n", $file, $row+1, $l, $m, $r, str_repeat(' ',$col), str_repeat('-',$len-2), $col+1);
		}
		
	}

?>