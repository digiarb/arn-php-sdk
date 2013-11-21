<?php
Class ARN_Util
{
    private static $lastAPIRequest = '';
    private static $lastAPIResponse = '';
    
    public static function getLastAPIRequest()
    {
        return self::$lastAPIRequest;
    }
    
    public static function getLastAPIResponse()
    {
        return self::$lastAPIResponse ;
    }
    
    /**
     * 
     * @param type $xmlObject
     * @param type $out
     * @return type
     * 
     * SimpleXML object to array.
     */
    public static function simpleXmlObjectToArray ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) || is_array($node) )
            ? self::simpleXmlObjectToArray ( $node )
            : $node;

        return $out;
    }
    
    /**
     * 
     * @param type $result_array
     * @return type
     * 
     * This method converts all necessary colletions of single elements into array, to get a standard structure of common results array.
     */
    public static function availabilityStandartisation($result_array)
    {
        $tArrKeys = array_keys($result_array['Availability']['HotelAvailability']['Hotel']);
        if($tArrKeys[0] === '@attributes')
            $result_array['Availability']['HotelAvailability']['Hotel'] = array($result_array['Availability']['HotelAvailability']['Hotel']);
            
        $hotelsData = &$result_array['Availability']['HotelAvailability']['Hotel'];
        foreach ($hotelsData as &$hotelData)
        {
            if(empty($hotelData['RatePlan']))
                continue;
            
            $tArrKeysR = array_keys($hotelData['RatePlan']);
            if($tArrKeysR[0] === '@attributes')
                $hotelData['RatePlan'] = array($hotelData['RatePlan']);
            
            foreach ($hotelData['RatePlan'] as &$hotelDataRatePlan)
            {
                $tArrKeysD = array_keys($hotelDataRatePlan['Room']);
                if($tArrKeysD[0] === '@attributes')
                    $hotelDataRatePlan['Room'] = array($hotelDataRatePlan['Room']);
                
                foreach ($hotelDataRatePlan['Room'] as &$room)
                {
                    if(empty($room['NightlyRate']))
                        continue;
                    $tArrKeysN = array_keys($room['NightlyRate']);
                    if($tArrKeysN[0] === '@attributes')
                        $room['NightlyRate'] = array($room['NightlyRate']);
                }
            }
        }
        return $result_array;
    }
    
    /**
     * 
     * @param type $result_array
     * @return type
     * 
     * This method converts all necessary colletions of single elements into array, to get a standard structure.
     */
    public static function detailsStandartisation($result_array)
    {
        $roomData = &$result_array['RateDetails']['HotelRateDetails']['Hotel']['RatePlan']['Room'];
        $tArrKeys = array_keys($roomData['NightlyRate']);
        if($tArrKeys[0] === '@attributes')
            $roomData['NightlyRate'] = array($roomData['NightlyRate']);
        
        return $result_array;
    }
    
    public static function getAElement($array, $path)
    {
        preg_match_all('/\[(\'|")?(.+?)(\\1)?\]+/', $path, $pathKeys);
        $pathKeys = $pathKeys[2];

        $result = $array;
        
        if(count($pathKeys) > 0)
            foreach ($pathKeys as $key)
                if (isset($result[$key]))
                        $result = $result[$key];
                else {
                    $result = false;
                    break;
                }
        else
        {
            $result = false;
        }
        
        
        if($result===false)
        {
            $trace=debug_backtrace();
            $caller=$trace[1]['function'];
            //throw new Arn_Error(Arn_language::getLabel('error_common'), array($caller.'->getAElement Illegal path to element.'));
            throw new Arn_Error(Arn_language::getLabel('error_common_in').' '.$caller, array(Arn_language::getLabel('required_element_missing').' '.$path));
        }
        
        return $result;
        
        //foreach ($pathKeys as $key)
    }
    
    public static function checkXMLError($result_xml)
    {
        if($result_xml->Error)
        {
            if($result_xml->Error->attributes())
            {
                $err = $result_xml->Error->attributes();
                $code = $err['Code'];
                $descr = $err['Description'];
                throw new Arn_Error(Arn_language::getLabel('error_common'), array((string)$code=>(string)$descr));
            }
            else
                throw new Arn_Error(Arn_language::getLabel('error_common'), (string)$result_xml->Error->Message);
        }
    }
    
    public static function sendRequest($client, $xml_string)
    {
        ARN_Validate::validateXml($xml_string);
        $xml = simplexml_load_string($xml_string);
        self::$lastAPIRequest = $xml;
        //print_r($xml);die;
        
        $params = array();
        $params["siteID"] = Arn::$arnConfig["siteID"];
        $params["username"] = Arn::$arnConfig["username"];
        $params["password"] = Arn::$arnConfig["password"];
        $params["xmlFormattedString"] = $xml->asXML();
        //print_r($xml->asXML());die;
        
        try {
            $result = $client->SubmitRequestRpc(
                $params["siteID"],
                $params["username"],
                $params["password"],
                $params["xmlFormattedString"]
            );
        }
        catch (Exception $e) {
            throw new Arn_Error('ARN API error occured', $e->getMessage());
        }
        
        $result_xml = simplexml_load_string($result);
        
        self::$lastAPIResponse = $result_xml;
        return $result_xml;
    }
    
    public static function prepareSQL($filter, $sort, $limit, $source, $options=array())
    {
        $cfg = Arn::$validateCfg[$source];
        
        if(!$limit)
            $limit = array(0, 100);
        
        if($limit[1]!=-1)
            $limit = ' limit '.$limit[0].', '.$limit[1];
        else
            $limit='';
        
        $joins = '';
        foreach($options as $option)
        {
            switch ($option[0])
            {
                case 'left join':
                    $joins.= ' LEFT JOIN '.$option[1].' ';
                break;
            }
        }
        
        $slqWhere = '';
        foreach($filter as $filterName => $filterData)
        {
            if(array_key_exists($filterName, $cfg['filtersDB']))
            {
                $rule = explode(':', $cfg['filtersDB'][$filterName]);
                self::$rule[1]($slqWhere, $rule[0], $filterData);
            }
        }
        
        if(substr($slqWhere, 0, 3)=='AND')
            $slqWhere = substr($slqWhere, 4);
        else
            return 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$cfg['table'].$joins.$limit;
        
        
        $slqWhere = 'WHERE '.$slqWhere;
        
        $sortRes = array();
       
        if($sort)
            foreach($sort as $sortOption => $sortType)
                if(array_key_exists($sortOption, $cfg['sorts']) && $cfg['sorts'][$sortOption])
                    $sortRes[] = $cfg['sorts'][$sortOption].' '.$sortType;
        
        $query = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$cfg['table'].' '.$joins.' '.$slqWhere;
        
        if($sortRes)
            $query.= 'ORDER BY '.implode(', ',$sortRes);
            
        $query.= $limit;
        
        return $query;
    }
    
    private static function equal(&$slqWhere, $fieldName, $value)
    {
        $slqWhere.='AND '.$fieldName.' = '.$value.' ';
    }
    
    private static function gt(&$slqWhere, $fieldName, $value)
    {
        $slqWhere.='AND '.$fieldName.' > '.$value.' ';
    }
    
    private static function gte(&$slqWhere, $fieldName, $value)
    {
        $slqWhere.='AND '.$fieldName.' >= '.$value.' ';
    }
    
    private static function lt(&$slqWhere, $fieldName, $value)
    {
        $slqWhere.='AND '.$fieldName.' < '.$value.' ';
    }
    
    private static function lte(&$slqWhere, $fieldName, $value)
    {
        $slqWhere.='AND '.$fieldName.' <= '.$value.' ';
    }
    
    private static function in(&$slqWhere, $fieldName, $value)
    {
        if(is_array($value))
            $value = implode('\',\'', $value);

        $slqWhere.='AND '.$fieldName.' in (\''.$value.'\') ';
    }
    
    private static function between(&$slqWhere, $fieldName, $value)
    {
        $slqWhere.='AND '.$fieldName.' BETWEEN '.$value[0].' AND '.$value[0].' ';
    }
    
    private static function like(&$slqWhere, $fieldName, $value)
    {
        $slqWhere.='AND '.$fieldName.' like \'%'.$value.'%\' ';
    }
    
    private static function inLike(&$slqWhere, $fieldName, $value)
    {
        if(is_array($value))
        {
            $slqWhere.='AND (';
            $i=0;
            foreach($value as $val)
            {
                if($i++)
                    $slqWhere.=' OR ';
                $slqWhere.=$fieldName.' like \'%'.$val.'%\' ' ;
            }
            $slqWhere.=')';
        }
        else
            $slqWhere.='AND '.$fieldName.' like \'%'.$value.'%\' ';
    }
    
    private static function inStartLike(&$slqWhere, $fieldName, $value)
    {
        if(is_array($value))
        {
            $slqWhere.='AND (';
            $i=0;
            foreach($value as $val)
            {
                if($i++)
                    $slqWhere.=' OR ';
                $slqWhere.=$fieldName.' like \''.$val.'%\' ' ;
            }
            $slqWhere.=')';
        }
        else
            $slqWhere.='AND '.$fieldName.' like \''.$value.'%\' ';
    }
    
    private static function location(&$slqWhere, $fieldName, $value)
    {
        //format Lat, Long, Dist
        $dist = $value[2]; // miles!!!

        //print_r($cvbPropertiesList);die;
        $resPOI = array();

        $lon1 = $value[1] - $dist / abs(cos(deg2rad($value[0])) * 69);
        $lon2 = $value[1] + $dist / abs(cos(deg2rad($value[0])) * 69);
        $lat1 = $value[0] - ($dist / 69);
        $lat2 = $value[0] + ($dist / 69);

        $query = "
        SELECT p.Latitude, p.Longitude, p.PropertyName, p.PropertyID,
        3956 * 2 * ASIN(SQRT( POWER(SIN((" . $value[0] . " - p.Latitude) *  pi()/180 / 2), 2) +
                COS(" . $value[0] . " * pi()/180) * COS(p.Latitude * pi()/180) * POWER(SIN((" . $value[1] . " -p.Longitude) * pi()/180 / 2), 2) ))
        as distance
        FROM arn_property_active p
        WHERE
            p.latitude between " . $lat1 . " AND " . $lat2 . " AND
            p.longitude between " . $lon1 . " AND " . $lon2 . "
        HAVING distance < " . $dist . "
        ORDER BY Distance
        ";
        
        $res = array();
        $results = Arn_model::getResults($query);
        foreach($results as $prop)
            $res[] = $prop->PropertyID;
        
        $res = implode('\',\'', $res);
        
        $slqWhere.='AND '.$fieldName.' in (\''.$res.'\') ';
    }
    
    public static function addPropertyKeys($properties)
    {
        $res = array();
        foreach($properties as $property)
            $res[$property->PropertyID] = $property;
        
        return $res;
    }
}