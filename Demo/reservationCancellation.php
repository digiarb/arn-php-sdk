<?php
error_reporting(E_ALL);
require_once '../Arn/arn.php';

try{
    $result = Arn::reservationCancellation(array(   
                                            'itineraryID' => '111',
                                            'reservationID' => '111'
                                            )
                                        );

    print_r($result);
}
catch (Arn_Error $error) {
    print_r($error->getMessage());
    echo "<hr>";
    print_r($error->getErrorsDetails());
}