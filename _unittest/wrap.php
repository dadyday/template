<?php

$aFile['index'] = '
---#wrap:wrap0;www#end;---
---#wrap:wrap0;#wrap:wrap0;#wrap:wrap2;xxx#end;#end;#end;---
---#wrap:wrap1;yyy#end;---
---#wrap:wrap1;#wrap:wrap2;zzz#end;#end;---
';

$aFile['wrap0'] = '[0#content;]';

$aFile['wrap1'] = '#wrap:wrap0;(1#content;)#end;';
	
$aFile['wrap2'] = '{2#content;}';


$result = '
---[0www]---
---[0[0{2xxx}]]---
---[0(1yyy)]---
---[0(1{2zzz})]---
';

?>