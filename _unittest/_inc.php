<?php

$aFile['index'] = <<<TEXT
indexStart
#inc:inc1;
indexContent
#inc:inc$n;
#inc:$inc;$n;
indexEnd
TEXT;

$aFile['inc1'] = <<<TEXT
incContent1
TEXT;
	
$aFile['inc2'] = <<<TEXT
#inc:inc1;
incContent2
TEXT;

$result = <<<TEXT
indexStart
incContent1
indexContent
incContent1
incContent2
indexEnd
TEXT;

$n = 2;
$inc = 'inc';

?>