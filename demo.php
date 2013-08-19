<?php
error_reporting(E_ALL);
require_once 'Arn/arn.php';

//allowed tests
//getPropertyTypes, getPropertyAirports, getPOI, getMeetingRooms, getCountries, getContinents, getBrands, getAmenities, getAmenityTypes, getAirports,
//getProperties, getAvailability, getAvailabilityWithDetails, getParticularRateDetails, getByXML, reservationCancellation

test('getPropertyTypes');


///
///
///
///
///




function test($mthName)
{
    try{
        if('getPropertyTypes' == $mthName) {
            $res = Arn::getPropertyTypes( array('typeId'=>'1'),
                                array()
                                );
            print_r($res);die;
        }

        if('getPropertyAirports' == $mthName) {
            $res = Arn::getPropertyAirports( array('propertyId'=>'42747', 'distance'=>5),
                                array()
                                );
            print_r($res);die;
        }

        if('getPOI' == $mthName) {
            $res = Arn::getPOI( array(),
                                array()
                                );
            print_r($res);die;
        }

        if('getMeetingRooms' == $mthName) {
            $res = Arn::getMeetingRooms(  array('id'=>865, 'withOccupancy'=>0, 'meetingRoomOccupancyId'=>'4090'),
                                        array());
            print_r($res);die;
        }

        if('getCountries' == $mthName) {
            $res = Arn::getCountries(  array(),
                                        array());
            print_r($res);die;
        }

        if('getContinents' == $mthName) {
            $res = Arn::getContinents(  array(),
                                        array());
            print_r($res);die;
        }

        if('getBrands' == $mthName) {
            $res = Arn::getBrands(  array('code'=>array('A3','AA')),
                                        false);
            print_r($res);die;
        }

        if('getAmenities' == $mthName) {
            $res = Arn::getAmenities(  array('id'=>array('5','17'), 'typeId'=>8),
                                        false);
            print_r($res);die;
        }

        if('getAmenityTypes' == $mthName) {
            $res = Arn::getAmenityTypes(  array('id'=>array('1','2'), 'type'=>'123'),
                                        false);
            print_r($res);die;
        }

        if('getAirports' == $mthName) {
            $res = Arn::getAirports(  array('city'=>'chicago', 'airportCode'=>'MDW'),
                                        array('city'),
                                        20);
            print_r($res);die;
        }

        if('getProperties' == $mthName) {
            $res = Arn::getProperties(  array('city'=>'chicago', 'name'=>'hotel'),
                                        array('city'),
                                        20,
                                        array('meeting_room', 'airport', 'amenity', 'descrition', 'image', 'policy', 'supplier')
                    );
            print_r($res);die;
        }

        if('getAvailability' == $mthName) {
            $res = Arn::getAvailability(array(   
                                                    'inDate' => date('Y-m-d'), 
                                                    'outDate' => date('Y-m-d', strtotime('tomorrow')),
                                                    'rooms' => 1,
                                                    'adults' => 1,
                                                    'children' =>0,
                                                    'hotels'=>array('123')
                                                ),
                                                array('city'=>'chicago', 'name'=>'hotel'),
                                                array('price'),
                                                20
                                        );
            print_r($res);die;
        }

        if('getAvailabilityWithDetails' == $mthName) {
            $res = Arn::getAvailabilityWithDetails(array(   
                                                    'inDate' => date('Y-m-d'),
                                                    'outDate' => date('Y-m-d', strtotime('tomorrow')),
                                                    'rooms' => 1,
                                                    'adults' => 1,
                                                    'children' =>0,
                                                    'hotels'=>array('123')
                                                ),
                                                array('city'=>'chicago', 'name'=>'hotel'),
                                                array(),
                                                10,
                                                array('meeting_room', 'airport', 'amenity', 'descrition', 'image', 'policy', 'supplier')
                                        );
            print_r($res);die;
        }

        if ('getParticularRateDetails' == $mthName) {
            $detailsData = Arn::getParticularRateDetails(array(   
                                                    'inDate' => date('Y-m-d'), 
                                                    'outDate' => date('Y-m-d', strtotime('tomorrow')),
                                                    'rooms' => 1,
                                                    'adults' => 1,
                                                    'children' => 0,
                                                    'hotelId' => 274659,
                                                    'ratePlanCode' => '0RAC',
                                                    'ratePlanGateway' => 4,
                                                    'roomCode' => 'Z30'
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
                //'ccNumber' => '4321432143214327',
                //'ccNumber' => '5454545454545454',
                'ccNumber' => '4111111111111111',
                'ccExpiration' => '12/15',
                'ccCVV2' => '',
                'ccHolder' => 'holder',
                'ccAddress' => 'holder',
                'ccCity' => 'holder',
                'ccRegion' => '',
                'ccPostalCode' => '',
                'ccCountryCode' => 'US'
            );

            $reservation = Arn::reservation($reservationDetails, $userDetails);

            print_r($reservation);die;
        }

        if('getByXML' == $mthName) {
            $xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <ArnRequest>
        <Availability DisplayCurrency=\"USD\" SearchTimeout=\"15\">
            <HotelAvailability InDate=\"".date('Y-m-d')."\" OutDate=\"".date('Y-m-d', strtotime('tomorrow'))."\" Rooms=\"1\" Adults=\"2\" Children=\"0\">
                <Hotel HotelID=\"274659\"/>
            </HotelAvailability>
        </Availability>
    </ArnRequest>";

            $result = Arn::getByXML($xml_string);
            print_r($result);
        }

        if ('reservationCancellation' == $mthName) {
            $result = Arn::reservationCancellation(array(   
                                                    'itineraryID' => '111',
                                                    'reservationID' => '111'
                                                    )
                                                );

            print_r($result);die;
        }
    }
    catch (Arn_Error $error) {
        print_r($error->getMessage());
        echo "<hr>";
        print_r($error->getErrorsDetails());
        die;
    }  
}