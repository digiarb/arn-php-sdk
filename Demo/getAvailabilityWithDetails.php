<?php
error_reporting(E_ALL);
require_once '../Arn/arn.php';

try{
    $res = Arn::getAvailabilityWithDetails(array(   
                                            'inDate' => date('Y-m-d'), 
                                            'outDate' => date('Y-m-d', strtotime('tomorrow')),
                                            'rooms' => 1,
                                            'adults' => 2,
                                            'children' =>0,
                                            'hotels'=>array('31458'),
                                            'displayCurrency' => 'USD',
                                            'searchTimeout' => '30'
                                        )
                                        /*array('city'=>'chicago', 'name'=>'hotel'),
                                        array(),
                                        10,
                                        array('hotelDetails', 'meeting_room', 'airport', 'amenity', 'description', 'image', 'policy', 'supplier')*/
                                );
    print_r($res);
}
catch (Arn_Error $error) {
    print_r($error->getMessage());
    echo "<hr>";
    print_r($error->getErrorsDetails());
}