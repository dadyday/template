<?php

$aFile['index'] = '
#each:$a,$o;
	#between;;
#end;
	#each:$o,$name,$p;
		#around;x = { #content;}#end;
		#between;, #end; 
		#each:$p,$item;
			#around;$name;: [ #content; ]#end;
			#between;,#end;
			#every;$item;#end;
		#end;
	#end;
#end;
';


$result = '
x = { p0: [ 1,2,3 ], p1: [ 1,2 ], p2: [ 1 ], };
x = { p1: [ 1,2,3 ], };
x = { }
';

$o1 = new stdClass();
$o1->p0 = array(1,2,3);
$o1->p1 = array(1,2);
$o1->p2 = array(1);
$o1->p3 = array();

$o2 = new stdClass();
$o2->p1 = array(1,2,3);
$o2->p3 = array();

$o3 = new stdClass();
$o3->p3 = array();

$a = array($o1, $o2, $o3);

?>