<?php

$aFile['index'] = '
indexStart
#slot:slot1;
indexContent1
#slot:slot1;
indexContent2
#slot:slot2;
indexEnd
';

$aFile['slot1'] = 'slotContent1';
	
$aFile['slot2'] = 'slotContent2';

$result = '
indexStart
slotContent1
indexContent1
slotContent1
indexContent2
slotContent2
indexEnd
';

?>