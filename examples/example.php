<?php
set_include_path('../src/' . PATH_SEPARATOR . get_include_path());

require_once 'Services/Eniro.php';

$eniro = new Services_Eniro(1108);
$return = $eniro->query('telefon', '51906011');
print_r($return);