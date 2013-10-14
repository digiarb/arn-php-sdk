<?php
error_reporting(E_ALL);
require_once '../Arn/arn.php';

try{
    $res = Arn::getProperties(  array('city'=>'chicago', 'type'=>1),
                                array('city'),
                                20,
                                array('meeting_room', 'airport', 'amenity', 'descrition', 'image', 'policy', 'supplier')
            );
    print_r($res);
}
catch (Arn_Error $error) {
    print_r($error->getMessage());
    echo "<hr>";
    print_r($error->getErrorsDetails());
}