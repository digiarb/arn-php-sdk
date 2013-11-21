<?php
$arnConfig['apiWSDL']       = 'http://tripauthority.com/hotel.asmx?WSDL';
$arnConfig['apiSecureURL']  = 'https://tripauthority.com/hotel.asmx';
$arnConfig['apiXSD']        = 'http://static.reservetravel.com/documents/TripAuthorityRequest.xsd';

$arnConfig['siteID']        = '';
$arnConfig['username']      = '';
$arnConfig['password']      = '';

$arnConfig['availabilityHotelNumberPerRequest'] = 50;

$arnConfig['db']['hostname'] = 'arnphpsdk.c8d7gm5s02xx.us-west-1.rds.amazonaws.com';
$arnConfig['db']['username'] = 'arnro';
$arnConfig['db']['password'] = 'arnSDKP$$';
$arnConfig['db']['database'] = 'arnphpsdk';

//CI specific settings
$arnConfig['db']['dbdriver'] = 'mysqli';
$arnConfig['db']['dbprefix'] = '';
$arnConfig['db']['pconnect'] = TRUE;
$arnConfig['db']['db_debug'] = TRUE;
$arnConfig['db']['cache_on'] = FALSE;
$arnConfig['db']['cachedir'] = '';
$arnConfig['db']['char_set'] = 'utf8';
$arnConfig['db']['dbcollat'] = 'utf8_general_ci';
$arnConfig['db']['swap_pre'] = '';
$arnConfig['db']['autoinit'] = TRUE;
$arnConfig['db']['stricton'] = FALSE;

$arnConfig['propertyDetailsMap'] = array(
    'hotelDetails' => array('getPropertyInfo', 'PropertyID'),
    'meeting_room' => array('getMeetingRooms', 'PropertyId'),
    'airport' => array('getPropertyAirports', 'PropertyID'),
    'amenity' => array('getPropertyAmenities', 'PropertyID'),
    'description'=> array('getPropertyDescription', 'PropertyID'),
    'image' => array('getPropertyImages', 'PropertyID'),
    'policy' => array('getPropertyPolicies', 'PropertyID'),
    'supplier' => array('getPropertySuppliers', 'PropertyID'));

$validateCfg['getAvailability'] = array(
    'inDate'=>array('type'=>'date', 'rules'=>'required|callback_Arn_validate::future'),
    'outDate'=>array('type'=>'date', 'rules'=>'required|gtThen:inDate'),
    'rooms'=>array('type'=>'integer', 'rules'=>'required|min:1|max:99'),
    'adults'=>array('type'=>'integer', 'rules'=>'required|min:0|max:99'),
    'children'=>array('type'=>'integer', 'rules'=>'required|min:0|max:99|default:0'),
    'hotels'=>array('type'=>'integer|iArray', 'rules'=>'unique'),
    'displayCurrency'=>array('type'=>'string', 'rules'=>'required|default:USD'),
    'searchTimeout'=>array('type'=>'integer', 'rules'=>'required|default:15|min:0')
    );

$validateCfg['getParticularRateDetails'] = array(
    'inDate'=>array('type'=>'date', 'rules'=>'required|callback_Arn_validate::future'),
    'outDate'=>array('type'=>'date', 'rules'=>'required|gtThen:inDate'),
    'rooms'=>array('type'=>'integer', 'rules'=>'required|min:1|max:99'),
    'adults'=>array('type'=>'integer', 'rules'=>'required|min:0|max:99'),
    'children'=>array('type'=>'integer', 'rules'=>'required|min:0|max:99|default:0'),
    'hotelId'=>array('type'=>'integer', 'rules'=>''),
    'displayCurrency'=>array('type'=>'string', 'rules'=>'required|default:USD'),
    'ratePlanCode'=>array('type'=>'string', 'rules'=>'required'),
    'ratePlanGateway'=>array('type'=>'integer', 'rules'=>'required'),
    'roomCode'=>array('type'=>'string', 'rules'=>'required')
    );

$validateCfg['reservationParams'] = array(
    'DisplayCurrency' => "['RateDetails']['@attributes']['DisplayCurrency']",
    'InDate' => "['RateDetails']['HotelRateDetails']['@attributes']['InDate']",
    'OutDate' => "['RateDetails']['HotelRateDetails']['@attributes']['OutDate']",
    'Rooms' => "['RateDetails']['HotelRateDetails']['@attributes']['Rooms']",
    'Adults' => "['RateDetails']['HotelRateDetails']['@attributes']['Adults']",
    'Children' => "['RateDetails']['HotelRateDetails']['@attributes']['Children']",
    'HotelID' => "['RateDetails']['HotelRateDetails']['Hotel']['@attributes']['HotelID']",
    'RatePlanCode' => "['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['@attributes']['Code']",
    'RatePlanGateway' => "['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['@attributes']['Gateway']",
    'RoomCode' => "['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['Room']['@attributes']['Code']",
    'RoomCostPrice' => "SUM:['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['Room']['NightlyRate'][X]['@attributes']['Price']",
    'RoomCostTaxAmount' => "['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['Room']['Tax']['@attributes']['Amount']",
    'RoomCostGatewayFee' => "['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['Room']['GatewayFee']['@attributes']['Amount']",
    'RoomCostTotalAmount' => "['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['Room']['Total']['@attributes']['Amount']",
    'RoomCostCurrencyCode' => "['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['Room']['@attributes']['CurrencyCode']",
    'BookingFeeAmount' => "['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['Room']['BookingFee']['@attributes']['Amount']",
    'BookingFeeCurrencyCode' => "['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['Room']['BookingFee']['@attributes']['CurrencyCode']");

$validateCfg['reservationRValidate'] = array(   
    'displayCurrency'=>array('type'=>'string', 'rules'=>'required|default:USD'),
    'inDate'=>array('type'=>'date', 'rules'=>'required|callback_Arn_validate::future'),
    'outDate'=>array('type'=>'date', 'rules'=>'required|gtThen:inDate'),
    'rooms'=>array('type'=>'integer', 'rules'=>'required|min:1|max:99'),
    'adults'=>array('type'=>'integer', 'rules'=>'required|min:0|max:99'),
    'children'=>array('type'=>'integer', 'rules'=>'required|min:0|max:99|default:0'),
    'hotelID'=>array('type'=>'integer', 'rules'=>'required:min:1'),
    'ratePlanCode'=>array('type'=>'arnString', 'rules'=>'required'),
    'ratePlanGateway'=>array('type'=>'integer', 'rules'=>'required|min:1'),
    'roomCode'=>array('type'=>'arnString', 'rules'=>'required'),
    'roomCostPrice'=>array('type'=>'arnPrice', 'rules'=>'required|min:0'),
    'roomCostTaxAmount'=>array('type'=>'arnPrice', 'rules'=>'required|min:0'),
    'roomCostGatewayFee'=>array('type'=>'arnPrice', 'rules'=>'required|min:0'),
    'roomCostTotalAmount'=>array('type'=>'arnPrice', 'rules'=>'required|min:0'),
    'roomCostCurrencyCode'=>array('type'=>'string', 'rules'=>'required'),
    'bookingFeeAmount'=>array('type'=>'arnPrice', 'rules'=>'required|min:0'),
    'bookingFeeCurrencyCode'=>array('type'=>'string', 'rules'=>'required')
    );

$validateCfg['reservationUValidate'] = array(   
    'guestsPrimaryTitle'=>array('type'=>'string', 'rules'=>''),
    'guestsPrimaryFirstName'=>array('type'=>'string', 'rules'=>'required'),
    'guestsPrimaryMiddleName'=>array('type'=>'string', 'rules'=>''),
    'guestsPrimaryLastName'=>array('type'=>'string', 'rules'=>'required'),
    'guestsPrimaryMessage'=>array('type'=>'string', 'rules'=>''),
    'guestsPrimaryEmail'=>array('type'=>'email', 'rules'=>'required|email'),
    'guestsPrimaryPhoneCountry'=>array('type'=>'string', 'rules'=>''),
    'guestsPrimaryPhoneArea'=>array('type'=>'string', 'rules'=>''),
    'guestsPrimaryPhoneNumber'=>array('type'=>'string', 'rules'=>'required'),
    'guestsPrimaryPhoneExtension'=>array('type'=>'string', 'rules'=>''),
    'guestsPrimaryAgeGroup'=>array('type'=>'arnGroupAge', 'rules'=>'required'),
    'guestsAddressType'=>array('type'=>'arnAddressType', 'rules'=>'required'),
    'guestsAddressAddress'=>array('type'=>'arnString', 'rules'=>'required'),
    'guestsAddressCity'=>array('type'=>'arnString', 'rules'=>'required'),
    'guestsAddressRegion'=>array('type'=>'string', 'rules'=>''),
    'guestsAddressPostalCode'=>array('type'=>'arnString', 'rules'=>'required'),
    'guestsAddressCountryCode'=>array('type'=>'arnCountryCode', 'rules'=>'required'),
    'guestsAddressExtraInfo'=>array('type'=>'string', 'rules'=>''),
    'ccType'=>array('type'=>'arnCcType', 'rules'=>'required'),
    'ccNumber'=>array('type'=>'arnString', 'rules'=>'required'),
    'ccExpiration'=>array('type'=>'arnCreditCardExpirationDate', 'rules'=>'required'),
    'ccCVV2'=>array('type'=>'string', 'rules'=>''),
    'ccHolder'=>array('type'=>'arnString', 'rules'=>'required'),
    'ccAddress'=>array('type'=>'arnString', 'rules'=>'required'),
    'ccCity'=>array('type'=>'arnString', 'rules'=>'required'),
    'ccRegion'=>array('type'=>'string', 'rules'=>''),
    'ccPostalCode'=>array('type'=>'string', 'rules'=>''),
    'ccCountryCode'=>array('type'=>'arnCountryCode', 'rules'=>'required'),
    );

$validateCfg['reservationCancellation'] = array(   
    'itineraryID'=>array('type'=>'integer', 'rules'=>'required'),
    'reservationID'=>array('type'=>'integer', 'rules'=>'required')
    );
    

$validateCfg['availabilityProperties'] = array (
    'table' => 'arn_property_active',
    'filtersValidate' => array('name'=>'string', 'countryCode'=>'sArray|string', 'city'=>'sArray|string', 'postal'=>'string', 'address'=>'string', 'roomPrice'=>'MMArray', 'totalAmount'=>'MMArray', 'starRating'=>'iArray|integer', 'type'=>'iArray|integer', 'location'=>'location', 'locationId'=>'iArray|integer'),
    'filtersDB' => array('name'=>'PropertyName:like', 'countryCode'=>'CountryCode:in', 'city'=>'City:in', 'postal'=>'Postal:in', 'address'=>'Address1:in', 'starRating'=>'PriceClassId:between', 'type'=>'ProprtyTypeId:in', 'location'=>'PropertyID:location', 'locationId'=>'LocationID:in', 'PropertyID'=>'PropertyID:in'),
    'sorts' => array('name'=>'PropertyName', 'countryCode'=>'CountryCode', 'city'=>'City', 'price'=>false, 'starRating'=>'PriceTypeId')
);

$validateCfg['getPropertyTypes'] = array (
    'table' => 'arn_property_type',
    'filtersValidate' => array('typeId'=>'iArray|integer', 'type'=>'sArray|string'),
    'filtersDB' => array('typeId'=>'PropertyTypeId:in', 'type'=>'PropertyType:in'),
    'sorts' => array('typeId'=>'PropertyTypeId', 'type'=>'PropertyType')
);

$validateCfg['getProperties'] = array (
    'table' => 'arn_property_active',
    'filtersValidate' => array('id'=>'iArray|integer', 'brandCode'=>'sArray|string', 'name'=>'sArray|string', 'city'=>'string', 'postal'=>'string', 'address'=>'string', 'state'=>'string', 'countryCode'=>'string', 'type'=>'iArray|integer', 'locationId'=>'iArray|integer'),
    'filtersDB' => array('id'=>'PropertyID:in', 'name'=>'PropertyName:inLike', 'city'=>'City:in', 'postal'=>'Postal:in', 'address'=>'Address1:in', 'state'=>'StateCode:in', 'countryCode'=>'CountryCode:in', 'type'=>'PropertyTypeId:in', 'locationId'=>'LocationID:in'),
    'sorts' => array('id'=>'PropertyId', 'brandCode'=>'BrandCode', 'name'=>'PropertyName', 'city'=>'City', 'state'=>'State', 'countryCode'=>'CountryCode')
);

$validateCfg['getAirports'] = array (
    'table' => 'arn_airport',
    'filtersValidate' => array('id'=>'iArray|integer', 'code'=>'sArray|string', 'name'=>'sArray|string', 'city'=>'string', 'state'=>'string', 'countryCode'=>'string'),
    'filtersDB' => array('id'=>'AirportID:in', 'code'=>'AirportCode:in', 'name'=>'AirportName:inLike', 'city'=>'City:in', 'state'=>'StateCode:in', 'countryCode'=>'CountryCode:in'),
    'sorts' => array('id'=>'AirportId', 'code'=>'AirportCode', 'name'=>'AirportName', 'city'=>'City', 'state'=>'State', 'countryCode'=>'CountryCode')
);

$validateCfg['getAmenityTypes'] = array (
    'table' => 'arn_amenity_type',
    'filtersValidate' => array('id'=>'iArray|integer', 'type'=>'sArray|string'),
    'filtersDB' => array('id'=>'AmenityTypeId:in', 'type'=>'AmenityType:in'),
    'sorts' => array('id'=>'AmenityTypeId', 'type'=>'AmenityType')
);

$validateCfg['getAmenities'] = array (
    'table' => 'arn_amenity',
    'filtersValidate' => array('id'=>'iArray|integer', 'typeId'=>'iArray|integer'),
    'filtersDB' => array('id'=>'AmenityID:in', 'typeId'=>'AmenityTypeID:in'),
    'sorts' => array('id'=>'AmenityTypeID', 'Type'=>'AmenityTypeID')
);

$validateCfg['getAttributes'] = array (
    'table' => 'arn_attribute',
    'filtersValidate' => array('id'=>'iArray|integer'),
    'filtersDB' => array('id'=>'AmenityID:in'),
    'sorts' => array('id'=>'AttributeID')
);

$validateCfg['getBrands'] = array (
    'table' => 'arn_brand_images',
    'filtersValidate' => array('code'=>'sArray|string', 'name'=>'sArray|string'),
    'filtersDB' => array('code'=>'BrandCode:in', 'name'=>'BrandName:in'),
    'sorts' => array('code'=>'BrandCode', 'name'=>'BrandName')
);

$validateCfg['getContinents'] = array (
    'table' => 'arn_continent',
    'filtersValidate' => array('id'=>'iArray|integer', 'name'=>'sArray|string'),
    'filtersDB' => array('id'=>'ContinentID:in', 'name'=>'Name:in'),
    'sorts' => array('id'=>'ContinentID', 'name'=>'Name')
);

$validateCfg['getCountries'] = array (
    'table' => 'arn_country',
    'filtersValidate' => array('code'=>'sArray|string', 'name'=>'sArray|string', 'continentId'=>'iArray|integer'),
    'filtersDB' => array('code'=>'CountryCode:in', 'name'=>'CountryName:in', 'continentId'=>'ContinentID:in'),
    'sorts' => array('code'=>'CountryCode', 'name'=>'CountryName', 'continentId'=>'ContinentID')
);

$validateCfg['getLocations'] = array (
    'table' => 'arn_location',
    'filtersValidate' => array('id'=>'iArray|integer', 'city'=>'string', 'state'=>'string', 'countryCode'=>'string'),
    'filtersDB' => array('id'=>'LocationID:in', 'city'=>'City:inStartLike', 'state'=>'State:in', 'countryCode'=>'Country:in'),
    'sorts' => array('id'=>'LocationId', 'city'=>'City', 'state'=>'State', 'countryCode'=>'Country')
);

$validateCfg['getMeetingRooms'] = array (
    'table' => 'arn_meeting_room',
    'filtersValidate' => array('id'=>'iArray|integer', 'propertyId'=>'iArray|integer', 'withOccupancy'=>'bool', 'meetingRoomOccupancyId'=>'integer|iArray'),
    'filtersDB' => array('id'=>'arn_meeting_room.MeetingRoomID:in', 'propertyId'=>'PropertyID:in', 'meetingRoomOccupancyId'=>'MeetingRoomOccupancyId:in'),
    'sorts' => array('id'=>'MeetingRoomID', 'propertyId'=>'PropertyID')
);

$validateCfg['getPOI'] = array (
    'table' => 'arn_poi',
    'filtersValidate' => array('id'=>'iArray|integer', 'category'=>'sArray|atring', 'name'=>'sArray|string', 'city'=>'string', 'state'=>'string', 'countryCode'=>'string'),
    'filtersDB' => array('id'=>'PoiID:in', 'category'=>'Categiry:in', 'name'=>'Name:inLike', 'city'=>'City:in', 'state'=>'StateCode:in', 'countryCode'=>'CountryCode:in'),
    'sorts' => array('id'=>'PoiID', 'category'=>'Category', 'name'=>'Name', 'city'=>'City', 'state'=>'State', 'countryCode'=>'CountryCode')
);

$validateCfg['getPropertyInfo'] = array (
    'table' => 'arn_property_active',
    'filtersValidate' => array('propertyId'=>'iArray|integer'),
    'filtersDB' => array('propertyId'=>'PropertyID:in'),
    'sorts' => array('propertyId'=>'ProperyID')
);

$validateCfg['getPropertyAirports'] = array (
    'table' => 'arn_property_airport',
    'filtersValidate' => array('propertyId'=>'iArray|integer', 'code'=>'aString|string', 'distance'=>'numeric'),
    'filtersDB' => array('propertyId'=>'PropertyID:in', 'code'=>'AirportCode:in', 'distance'=>'DistanceFromAirport:lte'),
    'sorts' => array('propertyId'=>'ProperyID', 'code'=>'AirportCode', 'distance'=>'DistanceFromAirport')
);

$validateCfg['getPropertyAmenities'] = array (
    'table' => 'arn_property_amenity',
    'filtersValidate' => array('propertyId'=>'iArray|integer'),
    'filtersDB' => array('propertyId'=>'PropertyID:in'),
    'sorts' => array('propertyId'=>'ProperyID')
);

$validateCfg['getPropertyDescription'] = array (
    'table' => 'arn_property_description',
    'filtersValidate' => array('propertyId'=>'iArray|integer'),
    'filtersDB' => array('propertyId'=>'PropertyID:in'),
    'sorts' => array('propertyId'=>'ProperyID')
);

$validateCfg['getPropertyImages'] = array (
    'table' => 'arn_property_image',
    'filtersValidate' => array('propertyId'=>'iArray|integer'),
    'filtersDB' => array('propertyId'=>'PropertyID:in'),
    'sorts' => array('propertyId'=>'ProperyID')
);

$validateCfg['getPropertyPolicies'] = array (
    'table' => 'arn_property_policy',
    'filtersValidate' => array('propertyId'=>'iArray|integer'),
    'filtersDB' => array('propertyId'=>'PropertyID:in'),
    'sorts' => array('propertyId'=>'ProperyID')
);

$validateCfg['getPropertySuppliers'] = array (
    'table' => 'arn_property_supplier',
    'filtersValidate' => array('propertyId'=>'iArray|integer'),
    'filtersDB' => array('propertyId'=>'PropertyID:in'),
    'sorts' => array('propertyId'=>'ProperyID')
);

if(file_exists(__DIR__.'/local_config.php'))
    require_once __DIR__.'/local_config.php';