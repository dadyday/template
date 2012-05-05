<?php

$aFile['index'] = '
---#wrap:wrap0;www#end;---
---#wrap:wrap0;#wrap:wrap0;#wrap:wrap2;xxx#end;#end;#end;---
---#wrap:wrap1;yyy#end;---
---#wrap:wrap1;#wrap:wrap2;zzz#end;#end;---
';

$aFile['wrap0'] = '[#content;]';

$aFile['wrap1'] = '#wrap:wrap0;(#content;)#end;';
	
$aFile['wrap2'] = '{#content;}';


$result = '
---[www]---
---[[{xxx}]]---
---[(yyy)]---
---[({zzz})]---
';

?>