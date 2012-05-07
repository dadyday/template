<?php

$aFile['index'] = '
indexStart
#inc:inc1;
indexContent
#inc:inc$n;
#inc:$inc$n;
indexEnd';

$aFile['inc1'] = 'incContent1';
	
$aFile['inc2'] = '#inc:inc1;
incContent2';

$result = '
indexStart
incContent1
indexContent
incContent1
incContent2
incContent1
incContent2
indexEnd';

$n = 2;
$inc = 'inc';

?>