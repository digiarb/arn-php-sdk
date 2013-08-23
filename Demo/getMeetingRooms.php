<?php
error_reporting(E_ALL);
require_once '../Arn/arn.php';

try{
    $res = Arn::getMeetingRooms(  array('id'=>865, 'withOccupancy'=>0, 'meetingRoomOccupancyId'=>'4090'),
                                array());
    print_r($res);
}
catch (Arn_Error $error) {
    print_r($error->getMessage());
    echo "<hr>";
    print_r($error->getErrorsDetails());
}