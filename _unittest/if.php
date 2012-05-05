<?php

$aFile['index'] = '
start
1:#if:$cond1;true#end;;
2:#if:!$cond1;true#end;;
3:#if:$cond0;true#else;false#end;;
4:#if:!$cond0;true#else;false#end;;
5:#if:$cond1;true#if:$cond0;true#else;false#end;#else;false#if:$cond0;true#else;false#end;#end;;
6:#if:!$cond1;true#if:$cond0;true#else;false#end;#else;false#if:$cond0;true#else;false#end;#end;;
7:#if:$cond0,true,$cond0;;
8:#if:$cond1,$cond1;;
end
';


$result = '
start
1:true;
2:;
3:false;
4:true;
5:truefalse;
6:falsefalse;
7:0;
8:1;
end
';

$cond0 = 0;
$cond1 = 1;

?>