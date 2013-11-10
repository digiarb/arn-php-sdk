<?php
require_once(dirname(__FILE__) . '/arn_client.php');
require_once(dirname(__FILE__) . '/arn_interface.php');
require_once(dirname(__FILE__) . '/arn_util.php');
require_once(dirname(__FILE__) . '/arn_validate.php');
require_once(dirname(__FILE__) . '/arn_error.php');
require_once(dirname(__FILE__) . '/arn_language.php');

if (function_exists('get_instance'))
    require_once(dirname(__FILE__) . '/arn_ci_model.php');
else
    require_once(dirname(__FILE__) . '/arn_model.php');

Class ARN implements iArn
{
    public static $arnConfig;
    public static $validateCfg;
    public static $userConfig;
    public static $langCode = 'en';
    
    public static function init()
    {
        require_once(dirname(__FILE__) . '/config/arn_config.php');
        require_once(dirname(__FILE__) . '/config/arn_u_config.php');

        self::$arnConfig = $arnConfig;
        self::$validateCfg = $validateCfg;
        self::$userConfig = $userConfig;
        
        Arn_language::setLangCode(self::$langCode);
    }
    
    public static function getAvailability($param, $filter=array(), $sort=array(), $limit=array())
    {
        return self::getAvailabilityIn($param, $filter, $sort, $limit);
    }
    
    private static function getAvailabilityIn(&$param, $filter=array(), $sort=array(), $limit=array())
    {
        $validation = ARN_Validate::validate($param, self::$validateCfg['getAvailability']);
        if($validation!==true)
            throw new Arn_Error(Arn_language::getLabel('error_common'), $validation);
        
        $optionsValidation = ARN_Validate::opitonsValidate($filter, $sort, $limit, 'availabilityProperties');
        if($optionsValidation!==true)
            throw new Arn_Error(Arn_language::getLabel('error_common'), $optionsValidation);
        
        if(empty($param['hotels']))
            $param['hotels'] = array();
        
        if(empty($param['hotels']) && !$filter)
            throw new Arn_Error(Arn_language::getLabel('error_common'), array(Arn_language::getLabel('hotels_param_required')));
        
        if($param['hotels'] && is_numeric($param['hotels']))
            $param['hotels'] = array($param['hotels']);
        
        if($filter)
        {
            if($param['hotels'])
                $filter['PropertyID'] = $param['hotels'];
            
            //search hotels by filters
            $hotels = self::getAvailabilityProperties($filter, $sort, $limit);
            $totalCount = $hotels['totalCount'];
            unset($hotels['totalCount']);
            
            $hotelsIds = array();
            foreach ($hotels as $hotel)
                $hotelsIds[] = $hotel->PropertyID;

                $param['hotels'] = $hotelsIds;
        }
        else
            $totalCount = count($param['hotels']);
        
        if(!$param['hotels'])
            return array('totalCount'=>0);

        $results = array();
        $block_count = self::$arnConfig['availabilityHotelNumberPerRequest'];
        $client = Client::getInstance(self::$arnConfig);
        for($i=0;$i<ceil(count($param['hotels'])/$block_count);$i++)
        {
            $hotelIDs = array_slice($param['hotels'], $i*$block_count, $block_count);
            $xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<ArnRequest>
    <Availability DisplayCurrency=\"{$param['displayCurrency']}\" SearchTimeout=\"{$param['searchTimeout']}\">
        <HotelAvailability InDate=\"{$param['inDate']}\" OutDate=\"{$param['outDate']}\" Rooms=\"{$param['rooms']}\" Adults=\"{$param['adults']}\" Children=\"{$param['children']}\">";
            foreach($hotelIDs as $hotelID)
                $xml_string .="
            <Hotel HotelID=\"{$hotelID}\"/>";
            
            $xml_string .= "
        </HotelAvailability>
    </Availability>
</ArnRequest>";

            $result_xml = ARN_Util::sendRequest($client, $xml_string);
            Arn_util::checkXMLError($result_xml);
            
            $results[] = $result_xml;
        }
        
        $result_array = ARN_Util::simpleXmlObjectToArray($results[0]);
        $result_array = ARN_Util::availabilityStandartisation($result_array);

        for($i=1; $i<count($results); $i++)
        {
            $results[$i] = ARN_Util::simpleXmlObjectToArray($results[$i]);
            $results[$i] = ARN_Util::availabilityStandartisation($results[$i]);
            $result_array['Availability']['HotelAvailability']['Hotel'] = array_merge($result_array['Availability']['HotelAvailability']['Hotel'], $results[$i]['Availability']['HotelAvailability']['Hotel']);
        }
        
        $result_array = self::applyPriceFilter($result_array, $filter);
        $result_array = self::applyPriceSort($result_array, $sort);
        
        $result_array['totalCount'] = $totalCount;
        return $result_array;
    }
    
    public static function getAvailabilityWithDetails($param, $filter=array(), $sort=array(), $limit=array(), $details=array())
    {
        $detailsValidation = ARN_Validate::detailsValidation($details);
        if($detailsValidation!==true)
            throw new Arn_Error(Arn_language::getLabel('error_common'), $detailsValidation);
        
        $res = self::getAvailabilityIn($param, $filter, $sort, $limit);
        $res = self::addDetails2XMLRes($param, $res, $details);
        
        return $res;
    }
    
    public static function getParticularRateDetails($param)
    {
        $validation = ARN_Validate::validate($param, self::$validateCfg['getParticularRateDetails']);
        
        if($validation!==true)
            throw new Arn_Error(Arn_language::getLabel('error_common'), $validation);
        
        $client = Client::getInstance(self::$arnConfig);
        
        $xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<ArnRequest>
    <RateDetails DisplayCurrency=\"{$param['displayCurrency']}\">
        <HotelRateDetails InDate=\"{$param['inDate']}\" OutDate=\"{$param['outDate']}\" Rooms=\"{$param['rooms']}\" Adults=\"{$param['adults']}\" Children=\"{$param['children']}\">
            <Hotel HotelID=\"{$param['hotelId']}\">
                <RatePlan Code=\"{$param['ratePlanCode']}\" Gateway=\"{$param['ratePlanGateway']}\">
                    <Room Code=\"{$param['roomCode']}\"/>
                </RatePlan>
            </Hotel>
        </HotelRateDetails>
    </RateDetails>
</ArnRequest>";
        
        $result_xml = Arn_util::sendRequest($client, $xml_string);
        Arn_util::checkXMLError($result_xml);
        
        $result_array = ARN_Util::simpleXmlObjectToArray($result_xml);
        $result_array = ARN_Util::detailsStandartisation($result_array);
        
        return $result_array;
    }
    
    public static function prepareReservation($detailsData)
    {
        //print_r($detailsData);die;
        $data = array();

        foreach(self::$validateCfg['reservationParams'] as $key => $value)
            $data[lcfirst($key)] = ARN_Util::getAElement($detailsData, $value);
        
        return $data;
    }
    
    
    public static function reservation($reservationData, $userDetails, $additionalData=array())
    {
        $validation = ARN_Validate::validate($reservationData, self::$validateCfg['reservationRValidate']);
        
        if($validation!==true)
            throw new Arn_Error(Arn_language::getLabel('error_common'), $validation);
        
        $validation = ARN_Validate::validate($userDetails, self::$validateCfg['reservationUValidate']);
        
        if($validation!==true)
            throw new Arn_Error(Arn_language::getLabel('error_common'), $validation);
        
        $additionalData = array();
        $reservationAParams = array('ItineraryID', 'CampaignCode', 'RecordLocator', 'AgentId', 'ProfileId', 'AgentProfileId');
        foreach($reservationAParams as $key)
            if(isset($additionalData[$key]))
                $additionalData[$key] = $additionalData[$key];
            
        $client = Client::getSecureInstance(self::$arnConfig);
            
        $xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<ArnRequest>
    <Reservation DisplayCurrency=\"{$reservationData["displayCurrency"]}\" CampaignCode=\"".(isset($additionalData['campaignCode'])?$additionalData['campaignCode']:'')."\" RecordLocator=\"".(isset($additionalData['recordLocator'])?$additionalData['recordLocator']:'')."\" AgentId=\"".(isset($additionalData['agentId'])?$additionalData['agentId']:'')."\">
        <HotelReservation InDate=\"{$reservationData["inDate"]}\" OutDate=\"{$reservationData["outDate"]}\" Rooms=\"{$reservationData["rooms"]}\" Adults=\"{$reservationData["adults"]}\" Children=\"{$reservationData["children"]}\">
            <Hotel HotelID=\"{$reservationData["hotelID"]}\">
                <RatePlan Code=\"{$reservationData["ratePlanCode"]}\" Gateway=\"{$reservationData["ratePlanGateway"]}\">
                    <Room Code=\"{$reservationData["roomCode"]}\"/>
                </RatePlan>
            </Hotel>
            <Guests>
                <Primary Title=\"{$userDetails["guestsPrimaryTitle"]}\" FirstName=\"{$userDetails["guestsPrimaryFirstName"]}\" MiddleName=\"{$userDetails["guestsPrimaryMiddleName"]}\" LastName=\"{$userDetails["guestsPrimaryLastName"]}\" Message=\"{$userDetails["guestsPrimaryMessage"]}\" Email=\"{$userDetails["guestsPrimaryEmail"]}\" PhoneCountry=\"{$userDetails["guestsPrimaryPhoneCountry"]}\" PhoneArea=\"{$userDetails["guestsPrimaryPhoneArea"]}\" PhoneNumber=\"{$userDetails["guestsPrimaryPhoneNumber"]}\" PhoneExtension=\"{$userDetails["guestsPrimaryPhoneExtension"]}\" AgeGroup=\"{$userDetails["guestsPrimaryAgeGroup"]}\">
                    <Address Type=\"{$userDetails["guestsAddressType"]}\" Address=\"{$userDetails["guestsAddressAddress"]}\" City=\"{$userDetails["guestsAddressCity"]}\" Region=\"{$userDetails["guestsAddressRegion"]}\" PostalCode=\"{$userDetails["guestsAddressPostalCode"]}\" CountryCode=\"{$userDetails["guestsAddressCountryCode"]}\" ExtraInfo=\"{$userDetails["guestsAddressExtraInfo"]}\"/>
                </Primary>
            </Guests>
            <RoomCost Price=\"{$reservationData["roomCostPrice"]}\" TaxAmount=\"{$reservationData["roomCostTaxAmount"]}\" GatewayFee=\"{$reservationData["roomCostGatewayFee"]}\" TotalAmount=\"{$reservationData["roomCostTotalAmount"]}\" CurrencyCode=\"{$reservationData["roomCostCurrencyCode"]}\"/>
            <BookingFee Amount=\"{$reservationData["bookingFeeAmount"]}\" CurrencyCode=\"{$reservationData["bookingFeeCurrencyCode"]}\"/>
        </HotelReservation>
        <CreditCard Type=\"{$userDetails["ccType"]}\" Number=\"{$userDetails["ccNumber"]}\" Expiration=\"{$userDetails["ccExpiration"]}\" CVV2=\"{$userDetails["ccCVV2"]}\" Holder=\"{$userDetails["ccHolder"]}\" Address=\"{$userDetails["ccAddress"]}\" City=\"{$userDetails["ccCity"]}\" Region=\"{$userDetails["ccRegion"]}\" PostalCode=\"{$userDetails["ccPostalCode"]}\" CountryCode=\"{$userDetails["ccCountryCode"]}\"/>
    </Reservation>
</ArnRequest>";
        
        $result_xml = Arn_util::sendRequest($client, $xml_string);
        Arn_util::checkXMLError($result_xml);
        
        $result_array = ARN_Util::simpleXmlObjectToArray($result_xml);
        
        return $result_array;
    }

    
    public static function reservationCancellation($param)
    {
        $validation = ARN_Validate::validate($param, self::$validateCfg['reservationCancellation']);
        
        if($validation!==true)
            throw new Arn_Error(Arn_language::getLabel('error_common'), $validation);
        $client = Client::getSecureInstance(self::$arnConfig);
            
        $xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<ArnRequest>
    <Cancellation ItineraryID=\"{$param["itineraryID"]}\">
        <HotelCancellation ReservationID=\"{$param["reservationID"]}\"/>
    </Cancellation>
</ArnRequest>";
        
        $result_xml = Arn_util::sendRequest($client, $xml_string);
        Arn_util::checkXMLError($result_xml);
        
        $result_array = ARN_Util::simpleXmlObjectToArray($result_xml);
        
        return $result_array;
    }
    
    public static function getByXML($xml_string)
    {
        try {
            ARN_Validate::validateXmlByXsd($xml_string, self::$arnConfig['apiXSD']);
        }
        catch(Arn_Error $e) {
            throw new Arn_Error(Arn_language::getLabel('error_common'), $e->getErrorsDetails());
        }
        
        $client = Client::getInstance(self::$arnConfig);
        $result_xml = ARN_Util::sendRequest($client, $xml_string);

        return $result_xml;
    }
    
    public static function getPropertyTypes($filter=array(), $sort=array(), $limit=array())
    {
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getProperties($filter=array(), $sort=array(), $limit=array(), $details=array())
    {
        $detailsValidation = ARN_Validate::detailsValidation($details);
        if($detailsValidation!==true)
            throw new Arn_Error(Arn_language::getLabel('error_common'), $detailsValidation);
        
        $properties = self::getByOptions($filter, $sort, $limit, __METHOD__);
        $totalCount = $properties['totalCount'];
        unset($properties['totalCount']);
        $properties = self::addDetails($properties, $details);
        $properties['totalCount'] = $totalCount;
        
        return $properties;
    }
    
    public static function getAirports($filter, $sort=array(), $limit=array())
    {
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    
    public static function getAmenityTypes($filter, $sort=array()) {
        $limit = array();
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getAmenities($filter, $sort=array(), $limit=array())
    {
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getAttributes($filter, $sort=array(), $limit=array())
    {
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getBrands($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getContinents($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getCountries($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    
    public static function getLocations($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getMeetingRooms($filter, $sort=array(), $limit=array()){
        if(!empty($filter['meetingRoomOccupancyId']) && empty($filter['withOccupancy']))
            $filter['withOccupancy']= true;
            
        $source = explode('::', __METHOD__);
        $source = $source[1];
        $optionsValidation = ARN_Validate::opitonsValidate($filter, $sort, $limit, $source);
        if($optionsValidation!==true)
            throw new Arn_Error(Arn_language::getLabel('error_common'), $optionsValidation);
        
        $options = array();
        if(!empty($filter['withOccupancy']))
            $options[] = array('left join',  'arn_meeting_room_occupancy ON arn_meeting_room.MeetingRoomId = arn_meeting_room_occupancy.MeetingRoomId');
        
        $query = ARN_Util::prepareSQL($filter, $sort, $limit, $source, $options);
        
        $results = Arn_model::getResults($query);
        return $results;
    }
    
    public static function getPOI($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getPropertyInfo($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getPropertyAirports($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getPropertyAmenities($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getPropertyDescription($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getPropertyImages($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getPropertyPolicies($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function getPropertySuppliers($filter, $sort=array(), $limit=array()){
        return self::getByOptions($filter, $sort, $limit, __METHOD__);
    }
    
    public static function setLangCode($langCode)
    {
        Arn_language::setLangCode($langCode);
    }
    
    public static function getLangCode()
    {
        return Arn_language::getLangCode();
    }
    
    
    /*Private section*/
    private static function getByOptions($filter, $sort=array(), $limit=array(), $source)
    {
        $source = explode('::', $source);
        $source = $source[1];
        $optionsValidation = ARN_Validate::opitonsValidate($filter, $sort, $limit, $source);
        if($optionsValidation!==true)
            throw new Arn_Error(Arn_language::getLabel('error_common'), $optionsValidation);
        
        $query = ARN_Util::prepareSQL($filter, $sort, $limit, $source);
        
        $results = Arn_model::getResults($query);
        return $results;
    }
    
    private static function getAvailabilityProperties($filter=array(), $sort=array(), $limit=array())
    {
        $query = ARN_Util::prepareSQL($filter, $sort, $limit, 'availabilityProperties');
        $properties = Arn_model::getResults($query);
        return $properties;
    }
    
    private static function applyPriceFilter($result_array, $filter)
    {
        if(empty($filter['roomPrice']) && empty($filter['totalAmount']))
            return $result_array;
        
        $hotelsData = &$result_array['Availability']['HotelAvailability']['Hotel'];
        foreach($hotelsData as $hotel_key => $hotelData)
        {
            if(empty($hotelData['RatePlan']))
                continue;
            
            foreach($hotelData['RatePlan'] as $rateKey => $ratePlan)
                foreach($ratePlan['Room'] as $roomKey=>$room)
                {
                    $roomPrice = null;
                    $totalAmount = null;
                    
                    if(!empty($room['NightlyRate']))
                        $roomPrice = $room['NightlyRate'][0]['@attributes']['Price'];
                    elseif(!empty($room['FirstNightRate']))
                        $roomPrice = $room['FirstNightRate']['@attributes']['Price'];
                    
                    $totalAmount = $room['Total']['@attributes']['Amount'];
                                       
                    if(!empty($filter['roomPrice']) && $roomPrice)
                        if($roomPrice<$filter['roomPrice'][0] || $roomPrice>$filter['roomPrice'][1])
                            unset($hotelsData[$hotel_key]['RatePlan'][$rateKey]['Room'][$roomKey]);
                    if(!empty($filter['totalAmount']) && $totalAmount)
                        if($totalAmount<$filter['totalAmount'][0] || $totalAmount>$filter['totalAmount'][1])
                            unset($hotelsData[$hotel_key]['RatePlan'][$rateKey]['Room'][$roomKey]);
                }
                
            //remove empty rooms
            foreach($hotelsData as $hotel_key => $hotelData)
            {
                if(empty($hotelData['RatePlan']))
                    continue;

                foreach($hotelData['RatePlan'] as $rateKey => $ratePlan)
                {
                    if(!$ratePlan['Room'])
                        unset($hotelsData[$hotel_key]['RatePlan'][$rateKey]);
                }
            }
            
            //remove empty ratePlans
            foreach($hotelsData as $hotel_key => $hotelData)
                if(isset($hotelData['RatePlan']) && !$hotelData['RatePlan'])
                    unset($hotelsData[$hotel_key]['RatePlan']);
            
        }
        
        return $result_array;
    }
    
    private static function applyPriceSort($result_array, $sort)
    {
        if(empty($sort['price']))
            return $result_array;
        
        $emptyHotels = array();
        $hotelsData = &$result_array['Availability']['HotelAvailability']['Hotel'];
        
        //Move empty hotels to the separate array
        foreach($hotelsData as $hotel_key => $hotelData)
        {
            if(empty($hotelData['RatePlan']))
            {
                $emptyHotels[] = $hotelData;
                unset($hotelsData[$hotel_key]);
            }
        }
        
        
        //Sort rooms by TOTAL inside Hotels RoomPlans
        foreach($hotelsData as $hotel_key => &$hotelData)
            foreach($hotelData['RatePlan'] as $rateKey => &$ratePlan)
                usort($ratePlan['Room'], function($a, $b)
                {
                    if($a['Total'] > $b['Total'])
                        return 1;
                }
                );
                
        
        //Add empty hotels to the result list.
        $hotelsData = array_merge($hotelsData, $emptyHotels);
        return $result_array;
    }
    
    private static function addDetails($properties, $details)
    {
        $properties = ARN_Util::addPropertyKeys($properties);
        $propDetails = self::getPropertiesDetailsSRC(array_keys($properties), $details);
        
        foreach($propDetails as $propId=>$pDetails)
            foreach($pDetails as $detailsName=>$detailsData)
                $properties[$propId]->$detailsName = $detailsData;

        return $properties;
    }
    
    private static function addDetails2XMLRes($param, $res, $details)
    {
        if(empty($res) || !$res['totalCount'])
            return ($res);
        
        $propDetails = self::getPropertiesDetailsSRC($param['hotels'], $details);
        
        foreach($res['Availability']['HotelAvailability']['Hotel'] as &$hotel)
            if(isset($propDetails[$hotel['@attributes']['HotelID']]))
                $hotel = array_merge($hotel, $propDetails[$hotel['@attributes']['HotelID']]);
            
        return $res;
    }
    
    private static function getPropertiesDetailsSRC($propList, $details)
    {
        $map = self::$arnConfig['propertyDetailsMap'];
        $tmpData = array();
        
        foreach($details as $pDetails)
        {
            $keyIdf = $map[$pDetails][1];
            $detailData = call_user_func_array(array('ARN', $map[$pDetails][0]), array(array('propertyId'=> $propList), array(), array(0, -1)));
            unset($detailData['totalCount']);

            foreach($detailData as $data)
                if($pDetails=='hotelDetails')
                    $tmpData[$data->$keyIdf][$pDetails] = $data;
                else
                    $tmpData[$data->$keyIdf][$pDetails][] = $data;
        }
        
        return $tmpData;
    }
}

//ARN configs initialization
Arn::init();