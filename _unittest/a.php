<?php

$aFile['index'] = '
a$a;$o.a;$o.o.a.0;
#testCont:a;#testCont:$a;#testCont:$o.a;#testCont:$o.o.a.0;
#testCont:a$a;#testCont:a $a z;#testCont:a{$a}z;
';


$result = '
aAOAOOA0
aAOAOOA0
aAa A zaAz
';

$aTest = array('A0', 'A1', 'A2');
$a = 'A';
$o = new stdClass();
$o->a = 'OA';
$o->o = new stdClass();
$o->o->a = array('OOA0', 'OOA1', 'OOA2');

?>