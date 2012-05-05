<?php

$aFile['index'] = <<<'TEXT'
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
TEXT;

$result = <<<TEXT
indexStart
String
STRING
Property
property
another string
another property
indexEnd
TEXT;

$string = 'String';
$oObj = new stdClass();
$oObj->property = 'Property';

?>