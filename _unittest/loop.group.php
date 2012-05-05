<?php

$aFile['index'] = <<<'TEXT'
(#for:$n,1,5;#group:3,[,];$n;#end;)
(#for:$n,1,5;#group:3;[#content;]#end;$n;#end;)
(#for:$n,1,5;#group:3;[#content;]#else;{#content;}#end;$n;#end;)
(#for:$n,1,8;
#group:6;{#content;}#end;
#group:3;[#content;]#end;
$n;#end;)
(#for:$n,1,8;
#group:3;{#content;}#end;
#group:2;[#content;]#end;
$n;#end;)
(#for:$n,1,8;
#group:2;[#content;]#end;
#group:3;{#content;}#end;
$n;#end;)
TEXT;


$result = <<<'TEXT'
([123][45])
([123][45])
([1}{2}{3][4}{5])
({[123][456]}{[78]})
({[12][3}{4][56]}{[78]})
([{12][3}{4][56}][{78}])
TEXT;

$a1 = array(1,2,3);
$a2 = array();

?>