<?php

	interface ITemplateBase {
//		function getParserObject();
//		function getContextObject();
		function getTemplateSource($name); 		// liefert ITemplateSource anh. namen
//		function getSourceFileName($name);
//		function getParsedFileName($name);
	}

	interface ITemplateSource {
		function parseFile();		// returns cachefilename for context
	};
	
	interface IDebug {
		function getDebugInfo();	// returns usefull help for exceptionhandling
	}
		
?>