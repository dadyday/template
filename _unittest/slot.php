<?php

$aFile['index'] = <<<TEXT
indexStart
#slot:slot1;
indexContent1
#slot:slot1;
indexContent2
#slot:slot2;
indexEnd
TEXT;

$aFile['slot1'] = <<<TEXT
slotContent1
TEXT;
	
$aFile['slot2'] = <<<TEXT
slotContent2
TEXT;

$result = <<<TEXT
indexStart
slotContent1
indexContent1
slotContent1
indexContent2
slotContent2
indexEnd
TEXT;

?>