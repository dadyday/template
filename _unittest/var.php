<?php

$aFile['index'] = '
indexStart
$string;
$string:upper;
$oObj.property;
$oObj.property:lower;
#set:$string,another string;
#set:$oObj.property,another property;
$string;
$oObj.property;
indexEnd
';

$result = '
indexStart
String
STRING
Property
property
another string
another property
indexEnd
';

function upperFormat($value) { return strtoupper($value); };
function lowerFormat($value) { return strtolower($value); };

$string = 'String';
$oObj = new stdClass();
$oObj->property = 'Property';

?>