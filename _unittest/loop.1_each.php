<?php

$aFile['index'] = '
(#each:$aTest,$index,$value;$index;$value;#end;)
(#each:$aTest,$value;$value;#end;)
(#each:$oTest,$index,$value;$index;$value.e1;#end;)
(#each:$oTest.aTest,$index,$value;$index;$value;#end;)
';


$result = '
(e1Entry1e2Entry2e3Entry3)
(Entry1Entry2Entry3)
(aTestEntry1)
(e1Entry1e2Entry2e3Entry3)
';

$aTest = array(
	'e1' => 'Entry1',
	'e2' => 'Entry2',
	'e3' => 'Entry3',
);

$oTest = new stdClass();
$oTest->aTest = $aTest;

?>