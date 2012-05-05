<?php

$aFile['index'] = <<<TEXT
indexStart
#embed:embed1;
indexContent
#embed:embed2;
indexEnd
TEXT;

$aFile['embed1'] = <<<TEXT
embedContent1
TEXT;
	
$aFile['embed2'] = <<<TEXT
#embed:embed1;
embedContent2
TEXT;

$result = <<<TEXT
indexStart
embedContent1
indexContent
embedContent1
embedContent2
indexEnd
TEXT;

?>