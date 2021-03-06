<?php
error_reporting(E_ALL);
require_once '../Arn/arn.php';

try{
    $res = Arn::getAvailability(    array(   
                                        'inDate' => date('Y-m-d'), 
                                        'outDate' => date('Y-m-d', strtotime('tomorrow')),
                                        'rooms' => 1,
                                        'adults' => 1,
                                        'children' =>0,
                                        //'hotels'=>array('123')
                                    ),
                                    array('city'=>'chicago', 'name'=>'hotel'),
                                    array('price'),
                                    20
                                );
    print_r($res);
}
catch (Arn_Error $error) {
    print_r($error->getMessage());
    echo "<hr>";
    print_r($error->getErrorsDetails());
}