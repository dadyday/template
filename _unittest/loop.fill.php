<?php

$aFile['index'] = '
(#for:$n,0,2;#fill:3,_,$n;#end;)
(#for:$n,0,3;#fill:3,_,$n;#end;)
(#for:$n,0,4;#fill:3,_,$n;#end;)
(#for:$n,0,5;#fill:3,_,$n;#end;)
(#for:$n,0,6;#fill:3,_,$n;#end;)
(#for:$n,0,6;#fill:3;_#else;$n;#end;#end;)
(#for:$n,0,5;#fill:3;_#else;$n;#end;#end;)
(#for:$n,0,0;#fill:3;_#else;$n;#end;#end;)
(#for:$n,0,-1;#fill:3;_#else;$n;#end;#end;)
';


$result = '
(012)
(0123__)
(01234_)
(012345)
(0123456__)
(0123456__)
(012345)
(0__)
()
';

$a1 = array(1,2,3);
$a2 = array();

?>