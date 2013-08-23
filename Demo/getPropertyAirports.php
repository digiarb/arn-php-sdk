<?php
error_reporting(E_ALL);
require_once '../Arn/arn.php';

try{
    $res = Arn::getPropertyAirports( array('propertyId'=>'42747', 'distance'=>5),
                        array()
                        );
    print_r($res);

}
catch (Arn_Error $error) {
    print_r($error->getMessage());
    echo "<hr>";
    print_r($error->getErrorsDetails());

}