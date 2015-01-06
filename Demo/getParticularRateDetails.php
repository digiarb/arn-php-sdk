<?php
error_reporting(E_ALL);
require_once '../Arn/arn.php';

try{
    $detailsData = Arn::getParticularRateDetails(array(   
                                            'inDate' => date('Y-m-d'), 
                                            'outDate' => date('Y-m-d', strtotime('tomorrow')),
                                            'rooms' => 1,
                                            'adults' => 1,
                                            'children' => 0,
                                            'hotelId' => 2617,
                                            'ratePlanCode' => 'dfru7fkjylcwjz2200cb2itk',
                                            'ratePlanGateway' => 28,
                                            'roomCode' => '1i0v7gmxkt25pcbptcvdknd9'
                                            )
                                        );

    $reservationDetails = Arn::prepareReservation($detailsData);

    $userDetails = array(
        'guestsPrimaryFirstName' => 'FN',
        'guestsPrimaryMiddleName' => '',
        'guestsPrimaryLastName' => 'LN',
        'guestsPrimaryMessage' => '',
        'guestsPrimaryEmail' => 'email@com.com',
        'guestsPrimaryPhoneCountry' => '',
        'guestsPrimaryPhoneArea' => '',
        'guestsPrimaryPhoneNumber' => '123',
        'guestsPrimaryPhoneExtension' => '',
        'guestsPrimaryAgeGroup' => 'Senior',
        'guestsAddressType' => 'Home',
        'guestsAddressAddress' => 'adr',
        'guestsAddressCity' => 'city',
        'guestsAddressRegion' => '',
        'guestsAddressPostalCode' => 'post',
        'guestsAddressCountryCode' => 'US',
        'guestsAddressExtraInfo' => '',
        'ccType' => 'CA',
        /*
         * You can use two fake card numbers to force credit card failures to ensure you handle them properly. 
         * 4321432143214327 will cause an authorization declined error and 5454545454545454 will cause an amount
         * that must be charged is different from amount authorized error. 4111111111111111 can be used on the
         * test gateways to book a successful reservation. 
         */
        //'ccNumber' => '4321432143214327',
        //'ccNumber' => '5454545454545454',
        'ccNumber' => '4111111111111111',
        'ccExpiration' => '03/15',
        'ccCVV2' => '',
        'ccHolder' => 'holder',
        'ccAddress' => 'holder',
        'ccCity' => 'holder',
        'ccRegion' => '',
        'ccPostalCode' => '',
        'ccCountryCode' => 'US'
    );

    $reservation = Arn::reservation($reservationDetails, $userDetails);

    print_r($reservation);
}
catch (Arn_Error $error) {
    print_r($error->getMessage());
    echo "<hr>";
    print_r($error->getErrorsDetails());
}