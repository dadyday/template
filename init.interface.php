<?php

	interface ITemplateBase {
//		function getParserObject();
//		function getContextObject();
		function getTemplateSource($name); 		// liefert ITemplateSource anh. namen
//		function getSourceFileName($name);
//		function getParsedFileName($name);
	}

	interface ITemplateSource {
		//function __construct(ITemplateBase $oBase, $name);
		//function parseFile();
		//function parse();
		function getParseSource();
	};
		
?>