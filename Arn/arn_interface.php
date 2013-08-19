<?
/*File config.php:

$use_arn_database = TRUE;
if $use_arn_database = FALSE user needs to specify his own DB to have an ability get extended data from DB:
$my_arn_database = array('host', 'username', 'password', 'DB_NAME')


also config.php can contain the default width options see with option in the methods)settings  separately for each method:

$default_options = array(
            'getAvailability' => array('airports','amenities','brand','brand_images'),
            'getProperties' => array('airport','descrition', 'meeting_room')
            );
*/
	/**
	 * @todo I would like to combine $default options so they are always the same. I do not think that the user will care whether default options are different for getAvailability or getProperties.
 	 */
/*    
 Also we will need internal config file to set default DB apriviledges and API path.
*/

interface iArn
{
    //--TripAuthority API SECTION
    
    /**
     * 
     * @param type $options
     * 
     * array(
     * inDate*
     * outDate*
     * rooms*
     * adults*
     * children*
     * hotels* (array of hotels IDs)
     * displayCurrency*
     * searchTimeout
     * )
     * 
     * @param type $filter array('name'=>string, 'countryCode'=>array|string, 'city'=>array|string, 'roomPrice'=>array(min, max)|max, 'totalAmount'=>array(min, max)|max, 'starRating'=>array(min, max)|max)
     * @param type $sort array('countryCode'=>'asc', 'name'=>'desc', 'city', 'price', 'starRating')
     * @param type $limit array(start, count)|count
     * 
     * returns the PHP object corresponding to ARN format (http://static.reservetravel.com/documents/trip-examples/AvailabilityResponse.xml)
     */
    public static function getAvailability($param, $filter, $sort, $limit);
    
    
    /**
     * 
     * @param type $options
     * 
     * array(
     * inDate*
     * outDate*
     * rooms*
     * adults*
     * children*
     * hotels* (array of hotels IDs)
     * displayCurrency*
     * 
     * @param type $filter array('name'=>string, 'countryCode'=>array|string, 'city'=>array|string, 'roomPrice'=>array(min, max)|max, 'totalAmount'=>array(min, max)|max, 'starRating'=>array(min, max)|max)
     * @param type $sort array('countryCode'=>'asc', 'name'=>'desc', 'city', 'price', 'starRating')
     * @param type $limit array(start, count)|count
     * 
     * @param type $details is array of additional entities which will be included into results
     * $details = array('meeting_room', 'airport', 'amenity', 'descrition', 'image', 'policy', 'supplier')
     * 
     * returns the PHP object corresponding to ARN format (http://static.reservetravel.com/documents/trip-examples/AvailabilityResponse.xml)
     * the additional entities like airport OR descrition will be added to the hotels properties as subobject.
     */

    public static function getAvailabilityWithDetails($param, $filter=array(), $sort=array(), $limit=array(), $details=array());
    
    
    
    /**
     * @param type $options
     * 
     * array(
     * inDate*
     * outDate*
     * rooms*
     * adults*
     * children*
     * displayCurrency*
     * hotelId*
     * ratePlanCode*
     * ratePlanGateway*
     * roomCode*
     * )
     * 
     * 
     * returns the PHP object corresponding to ARN format http://static.reservetravel.com/documents/trip-examples/DetailsResponse.xml
     */
    
    /*
     * Comments
     * Not sure we need to add any additional options for this method
     */
    public static function getParticularRateDetails($param);
    
    
    
    /**
     * 
     * @param type $detailsArray
     */
    public static function prepareReservation($detailsArray);
    
    
    
     /**
     * 
     * @param type $reservationData
     * 
     * $reservationData is result of Arn::prepareReservation()
     * 
     * @param type $userDetails
     * GuestsPrimaryTitle*
     * GuestsPrimaryFirstName*
     * GuestsPrimaryMiddleName*
     * GuestsPrimaryLastName*
     * GuestsPrimaryMessage*
     * GuestsPrimaryEmail*
     * GuestsPrimaryPhoneCountry*
     * GuestsPrimaryPhoneArea*
     * GuestsPrimaryPhoneNumber*
     * GuestsPrimaryPhoneExtension*
     * GuestsPrimaryAgeGroup*
     * GuestsAddressType*
     * GuestsAddressAddress*
     * GuestsAddressCity*
     * GuestsAddressRegion*
     * GuestsAddressPostalCode*
     * GuestsAddressCountryCode*
     * GuestsAddressExtraInfo*
     * CCType*
     * CCNumber*
     * CCExpiration*
     * CCCVV2*
     * CСHolder*
     * CCAddress*
     * CCCity*
     * CCRegion*
     * CCPostalCode*
     * CCCountryCode*
     * 
     * returns the PHP object corresponding to ARN format http://static.reservetravel.com/documents/trip-examples/ReservationResponse.xml
     * 
     */
    public static function reservation($reservationData, $userDetails);
    
    
    
    /**
     * 
     * @param type $options
     * array (
     * ItineraryID* int
     * ReservationID* int
     * )
     * returns the PHP object corresponding to ARN format http://static.reservetravel.com/documents/trip-examples/CancellationResponse.xml
     * 
     * 
     */
    public static function reservationCancellation($options);
    

    
    /**
     * 
     * @param type $inXML
     * 
     * return simplexml object
     */
    public static function getByXML($inXML);
    
    
    
    
    //---DB METHODS SECTION.
    //To have one style for all methods I propose use $option parameter which will be passed into method. We will have an ability to expand it in any time (add additional options etc. with backward compatibility)
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * typeId number|array
     * type string|sArray
     * )
     * 
     * @param type $options can be any from $filter keys (id, type)
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getPropertyTypes($filter, $sort, $limit);

    
    
    /**
     * 
     * @param type $filter    is array which can contains the folloving options:
     * 
     * array(
     * id number|array
     * brandCode string|array
     * name string|array
     * city string
     * state string
     * countryCode string
     * 
     * 
     * @param type $sort 
     * the selected entities will be sorted by this criteria. Can be any from $filter keys.
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results
     * 
     * @param type $details    used to add other property entity to the result
     * can be one or group of ('meeting_room', 'airport', 'amenity', 'descrition', 'image', 'policy', 'supplier')
     * 
     * For example: array('airport', 'descrition', 'meeting_room')
     * )
     * 
     */
    
    /*Comments
     * in SQL the criterias will be paste together with AND operand
     */
    public static function getProperties($filter, $sort, $limit, $details);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * id number|array
     * code string|array
     * name string|array
     * city string
     * state string
     * countryCode string
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getAirports($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * id number|array
     * type string|sArray
     * )
     * 
     * @param type $options can be any from $filter keys (id, type)
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getAmenityTypes($filter, $sort);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * id number|array
     * typeId number|array
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getAmenities($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * code string|array
     * name string|array
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getBrands($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * id integer|array
     * mame string|array
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getContinents($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * array(
     * code string|array
     * name string|array
     * continentId integer|array
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getCountries($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * id integer|array
     * city string|array
     * state string|array
     * countryCode string|array
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getLocations($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * id integer|array
     * PropertyId integer:array
     * withOccupancy bool
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getMeetingRooms($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * id integer|array
     * category string|array
     * name string|array
     * city string|array
     * state string|array
     * countryCode string|array
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getPOI($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * propertyID inteher|array
     * code string|array
     * distance numeric(max)
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getPropertyAirports($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * propertyID integer|array
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getPropertyAmenities($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * propertyID number|array
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getPropertyDescription($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * PropertyID number|array
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getPropertyImages($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * PropertyID number|array
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getPropertyPolicies($filter, $sort, $limit);
    
    
    
    /**
     * 
     * @param type $filter
     * 
     * array(
     * PropertyID number|array
     * )
     * 
     * @param type $options can be any from $filter keys
     * 
     * @param type $limit (array('from', 'count'))
     * the limitation of teh results 
     */
    public static function getPropertySuppliers($filter, $sort, $limit);
    
}