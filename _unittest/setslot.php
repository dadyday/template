<?php

$aFile['index'] = <<<TEXT
#slot:slot1;
#slot:slot1,slot2;
#setSlot:slot1,slot3;
#slot:slot1;
#slot:slot1,slot2;
TEXT;

$aFile['slot1'] = <<<TEXT
slotContent1
TEXT;
	
$aFile['slot2'] = <<<TEXT
slotContent2
TEXT;

$aFile['slot3'] = <<<TEXT
slotContent3
TEXT;

$result = <<<TEXT
slotContent1
slotContent2
slotContent3
slotContent3
TEXT;

?>