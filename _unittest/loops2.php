<?php

$aFile['index'] = '
(#for:$nn,0,4;
*#for:$n,1,$nn;
	#group:3;[#content;]#end;
	#fill:3,_,$n;
#end;#end;)
(#for:$nn,0,4;
*#for:$n,1,$nn;
	#group:2;[#content;]#end;
	#fill:3,_,$n;
#end;#end;)
(#for:$nn,0,4;
*#for:$n,1,$nn;
	#group:3;[#content;]#end;
	#fill:2,_,$n;
#end;#end;)
';


$result = '
(**[1__]*[12_]*[123]*[123][4__])
(**[1_][_]*[12][_]*[12][3]*[12][34][__])
(**[1_]*[12]*[123][_]*[123][4])
';

$a1 = array(1,2,3);
$a2 = array();

?>