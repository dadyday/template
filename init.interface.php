<?php

	interface ITemplateBase {
//		function getParserObject();
//		function getContextObject();
		function getTemplateSource($name); 		// liefert ITemplateSource anh. namen
//		function getSourceFileName($name);
//		function getParsedFileName($name);
	}

	interface ITemplateSource {
		//function parse();
		function getParseSource();
	};
		
?>