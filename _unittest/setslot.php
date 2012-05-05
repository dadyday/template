<?php

$aFile['index'] = '
#slot:slot1;
#slot:slot1,slot2;
#setSlot:slot1,slot3;
#slot:slot1;
#slot:slot1,slot2;
';

$aFile['slot1'] = 'slotContent1';
	
$aFile['slot2'] = 'slotContent2';

$aFile['slot3'] = 'slotContent3';

$result = 'slotContent1
slotContent2
slotContent3
slotContent3';

?>