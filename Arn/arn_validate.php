<?php

Class ARN_Validate
{
    public static function validate(&$data, $validateConfig)
    {
        $errors = array();
        
        foreach($validateConfig as $fieldName => $validationData)
        {
            $validationRules = array();
            $validationRulesIn = explode('|', $validationData['rules']);
            foreach($validationRulesIn as $validationRuleIn)
            {
                $validationRuleInTmp = explode(':', $validationRuleIn);
                if(count($validationRuleInTmp)==2)
                    $validationRules[$validationRuleInTmp[0]] = $validationRuleInTmp[1];
                else
                    $validationRules[$validationRuleIn] = true;
            }
            
            if(isset($validationRules['default']) && !isset($data[$fieldName]))
                $data[$fieldName] = $validationRules['default'];
            
            if(isset($validationRules['required']) && (!isset($data[$fieldName]) || $data[$fieldName]===''))
                $errors[$fieldName] = $fieldName.' is required';
            else
            {
                $types = explode('|', $validationData['type']);
                
                if(!isset($data[$fieldName]))
                    $data[$fieldName] = '';
                
                if($data[$fieldName]!=='')
                {
                    foreach($types as $type)
                    {
                        $typeValidated = false;
                        $errorMsg = '';
                        switch($type)
                        {
                            case "date":
                                if(preg_match("/^20\d\d-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[12][0-9])))$/", $data[$fieldName]))
                                    $typeValidated = true;
                                else
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_incorrect_date'),  $fieldName);
                            break;
                            
                            case "integer":
                                if(is_integer($data[$fieldName]) || (int)$data[$fieldName].'' === $data[$fieldName])
                                    $typeValidated = true;  
                            break;
                            
                            case "sArray":
                                if(is_array($data[$fieldName]))
                                    foreach($data[$fieldName] as $chVal)
                                        if(!is_string($chVal) && !is_numeric($chVal))
                                        {
                                            $errorMsg = sprintf(Arn_language::getLabel('validation_array_or_string'),  $fieldName);
                                            break;
                                        }
                                    if(!$errorMsg)
                                        $typeValidated = true;  
                            break;
                            
                            case "iArray":
                                if(is_array($data[$fieldName]))
                                    foreach($data[$fieldName] as $chVal)
                                        if(!is_integer($chVal) && (int)$chVal.'' !== $chVal)
                                        {
                                            $errorMsg = sprintf(Arn_language::getLabel('validation_array_with_int'),  $fieldName);
                                            break;
                                        }
                                    if(!$errorMsg)
                                        $typeValidated = true;  
                            break;
                            
                            case "nArray":
                                if(is_array($data[$fieldName]))
                                    foreach($data[$fieldName] as $chVal)
                                        if(!is_numeric($chVal))
                                        {
                                            $errorMsg = sprintf(Arn_language::getLabel('validation_array_with_num'),  $fieldName);
                                            break;
                                        }
                                    if(!$errorMsg)
                                        $typeValidated = true;  
                            break;
                            
                            case "string":
                                if(is_string($data[$fieldName]) || is_numeric($data[$fieldName]))
                                    $typeValidated = true;
                            break;
                            
                            case "arnString":
                                if(is_string($data[$fieldName]) && strpos($data[$fieldName], "\n")===false)
                                    $typeValidated = true;
                                else
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_arnString'),  $fieldName);
                            break;
                            
                            case "email":
                                if(preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $data[$fieldName]))
                                    $typeValidated = true;
                            break;
                            
                            case "arnGroupAge":
                                if(preg_match("/^(Adult|Senior|Youth|Child|Infant)$/i", $data[$fieldName]))
                                    $typeValidated = true;
                                else
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_arnGroupAge'),  $fieldName);
                            break;
                            
                            case "arnAddressType":
                                if(preg_match("/^(Home|Work|Other)$/i", $data[$fieldName]))
                                    $typeValidated = true;
                                else
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_arnAddressType'),  $fieldName);
                            break;
                            
                            case "arnCountryCode":
                                if(preg_match("/^(AD|AE|AF|AG|AI|AL|AM|AN|AO|AQ|AR|AS|AT|AU|AW|AZ|BA|BB|BD|BE|BF|BG|BH|BI|BJ|BM|BN|BO|BR|BS|BT|BU|BV|BW|BY|BZ|CA|CC|CF|CG|CH|CI|CK|CL|CM|CN|CO|CR|CU|CV|CX|CY|CZ|DE|DJ|DK|DM|DO|DZ|EC|EE|EG|EH|ER|ES|ET|FI|FJ|FK|FM|FO|FR|GA|GB|GD|GE|GF|GH|GI|GL|GM|GN|GP|GQ|GR|GS|GT|GU|GW|GY|HK|HM|HN|HR|HT|HU|ID|IE|IL|IN|IO|IQ|IR|IS|IT|JM|JO|JP|KE|KG|KH|KI|KM|KN|KP|KR|KW|KY|KZ|LA|LB|LC|LI|LK|LR|LS|LT|LU|LV|LY|MA|MC|MD|MG|MH|MK|ML|MM|MN|MO|MP|MQ|MR|MS|MT|MU|MV|MW|MX|MY|MZ|NA|NC|NE|NF|NG|NI|NL|NO|NP|NR|NU|NZ|OM|PA|PE|PF|PS|PG|PH|PK|PL|PM|PN|PR|PT|PW|PY|QA|RE|RO|RU|RW|SA|SB|SC|SD|SE|SG|SH|SI|SJ|SK|SL|SM|SN|SO|SR|ST|SV|SY|SZ|TC|TD|TF|TG|TH|TJ|TK|TM|TN|TO|TP|TR|TT|TV|TW|TZ|UA|UG|UM|US|UY|UZ|VA|VC|VE|VG|VI|VN|VU|WF|WI|WS|WX|WY|XA|XB|XH|XI|YE|YT|YU|ZA|ZM|ZR|ZW)$/i", $data[$fieldName]))
                                    $typeValidated = true;
                                else
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_arnCountryCode'),  $fieldName);
                            break;
                            
                            case "arnCcType":
                                if(preg_match("/^(AX|CA|VI|DS)$/i", $data[$fieldName]))
                                    $typeValidated = true;
                                else
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_arnCCType'),  $fieldName);
                            break;
                            
                            case "arnCreditCardExpirationDate":
                                if(preg_match("/^(\d{2})\/(\d{2})$/i", $data[$fieldName], $aMatch) && (int)$aMatch[1]>1 && (int)$aMatch[1]<13)
                                    $typeValidated = true;
                                else
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_arnCreditCardExpirationDate'),  $fieldName);
                            break;
                            
                            case "arnPrice":
                                if(is_numeric($data[$fieldName]))
                                    $typeValidated = true;
                                else
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_arnPrice'),  $fieldName);
                            break;
                        }
                        if($typeValidated)
                            break;
                    }

                    if(!$typeValidated)
                    {
                        $typeRules = str_replace('|', ' '.Arn_language::getLabel('validation_OR').' ', $validationData['type']);
                        $errors[$fieldName] = $errorMsg?$errorMsg:sprintf(Arn_language::getLabel('validation_specific_formats'),  $fieldName, $typeRules);
                    }

                    if(isset($validationRules['min']) && $data[$fieldName]<$validationRules['min'])
                        $errors[$fieldName] = sprintf(Arn_language::getLabel('validation_number_gt'), $fieldName, $validationRules['min']);

                    if(isset($validationRules['max']) && $data[$fieldName]>$validationRules['max'])
                        $errors[$fieldName] =  sprintf(Arn_language::getLabel('validation_number_lt'), $fieldName, $validationRules['max']);

                    if(isset($validationRules['gtThen']) && !($data[$fieldName]>$data[$validationRules['gtThen']]))
                        $errors[$fieldName] =  sprintf(Arn_language::getLabel('validation_greater_then'), $fieldName, $validationRules['gtThen']);
                    
                    if(isset($validationRules['unique']) && (in_array('iArray', $types) || in_array('sArray', $types)) && array_unique($data[$fieldName]) != $data[$fieldName])
                        $errors[$fieldName] =  sprintf(Arn_language::getLabel('validation_unique'), $fieldName);
                }
                
                //callback process
                foreach($validationRules as $rule => $val)
                    if(strpos($rule, 'callback_')===0)
                    {
                        $fObj = explode('_', $rule);
                        array_shift($fObj);
                        $fName = implode('_', $fObj);
                        
                        if(strpos($fName, '::')!==false)
                        {
                            $fName = explode('::', $fName);
                            $className = $fName[0];
                            $fName = $fName[1];
                        }
                        else
                        {
                            $className = __CLASS__;
                            $fName = $fName;
                        }
                            
                        
                        if(method_exists($className, $fName))
                        {
                            $error = call_user_func(array($className, $fName), $fieldName, $data[$fieldName]);
                            if($error!==true)
                                $errors[$fieldName] = $error;
                        }
                    }
            }
        }
        
        return $errors?$errors:true;
    }
    
    public static function opitonsValidate(&$filter, &$sort, &$limit, $source)
    {
        $cfg = Arn::$validateCfg[$source];
        
        $errors = array();
        
        $allovedFilters = $cfg['filtersValidate'];
        $filterValidation =  self::filterValidation($filter, $allovedFilters);
        
        if($filterValidation!==true)
            $errors = array_merge ($errors, $filterValidation);
            
        $allovedSortes = array_keys($cfg['sorts']);
        $sortValidation =  self::sortValidation($sort, $allovedSortes);
        
        if($sortValidation!==true)
            $errors = array_merge ($errors, $sortValidation);
            
        $limitValidation =  self::limitValidation($limit);
        
        if($limitValidation!==true)
            $errors = array_merge ($errors, $limitValidation);
        
        return $errors?$errors:true;
    }

    public static function filterValidation(&$filter, $allovedFilters)
    {
        if(!$filter)
            return true;
        
        $errors = array();
        
        if(!is_array($filter))
        {
            $errors['filterParameter'] = Arn_language::getLabel('validation_filter_array');
            return $errors;
        }
        
        foreach($filter as $key => &$filterValue)
        {
            if(is_string($filterValue) && trim($filterValue)==='' || is_bool($filterValue) && !$filterValue)
                continue;
            
            if(is_integer($key))
            {
                $errors['filterParameter'] = Arn_language::getLabel('validation_filter_assoc_array');
                return $errors;
            }
            
            if(!in_array($key, array_keys($allovedFilters)))
            {
                $errors['filterParameter'] = sprintf(Arn_language::getLabel('validation_filter_unallowed'), $key);
                return $errors;
            }
            
            $filterRules = $allovedFilters[$key];
            $filterRulesTmp = explode('|', $allovedFilters[$key]);

            $valid = false;
            $errorMsg = '';
            foreach($filterRulesTmp as $rule)
            {
                switch($rule)
                {
                    case "integer":
                        if(is_integer($filterValue) || (int)$filterValue.'' === $filterValue)
                            $valid = true;  
                    break;
                    
                    case "numeric":
                        if(is_numeric($filterValue))
                            $valid = true;  
                    break;
                    
                    case "sArray":
                        if(is_array($filterValue))
                            foreach($filterValue as $chVal)
                                if(!is_string($chVal) && !is_numeric($chVal))
                                {
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_array_or_string'),  $key);
                                    break;
                                }
                                
                            if(!$errorMsg)
                                $valid = true;
                    break;
                    
                    case "iArray":
                        if(is_array($filterValue))
                            foreach($filterValue as $chVal)
                                if(!is_integer($chVal) && (int)$chVal.'' !== $chVal)
                                {
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_array_with_int'),  $key);
                                    break;
                                }
                                
                            if(!$errorMsg)
                                $valid = true;  
                    break;
                    
                    case "nArray":
                        if(is_array($filterValue))
                            foreach($filterValue as $chVal)
                                if(!is_numeric($chVal))
                                {
                                    $errorMsg = sprintf(Arn_language::getLabel('validation_array_with_num'),  $key);
                                    break;
                                }
                            if(!$errorMsg)
                                $valid = true;  
                    break;
                    
                    case "string":
                        if(is_string($filterValue) || is_numeric($filterValue))
                            $valid = true;
                    break;
                    
                    case "location":
                        if(!is_array($filterValue) || count($filterValue)!=3 || !is_numeric($filterValue[0]) || !is_numeric($filterValue[1]) || !is_numeric($filterValue[2]))
                        {
                            $errorMsg = sprintf(Arn_language::getLabel('validation_location'),  $key);
                            break;
                        }
                    
                        $valid = true;
                    break;
                    
                    case "MMArray":
                        if(!is_array($filterValue) && !is_numeric($filterValue))
                            $errorMsg = sprintf(Arn_language::getLabel('validation_max_min_pair'), $key);

                        if(is_numeric($filterValue))
                            $filterValue = array(0, $filterValue);

                        if(count($filterValue)!=2 || !is_numeric($filterValue[0]) || !is_numeric($filterValue[1]))
                            $errorMsg = sprintf(Arn_language::getLabel('validation_max_min_pair'), $key);
                        
                        if(!$errorMsg)
                            $valid = true;
                    break;
                    
                    case "bool":
                        $filterValue = (bool)$filterValue;
                            $valid = true;
                    break;
                    
                    default:
                        $errorMsg = sprintf(Arn_language::getLabel('validation_filter_unallowed'), $key);
                    break;
                }
                
                
                if($valid)
                    break;
            }
            if(!$valid)
            {
                $typeRules = str_replace('|', ' OR ', $filterRules);
                $errors[$key] = $errorMsg?$errorMsg:sprintf(Arn_language::getLabel('validation_filter_specific_formats'),  $key, $typeRules);
            }
        }

        return $errors?$errors:true;
    }
    
    public static function sortValidation(&$sort, $allovedSortes)
    {
        if(!$sort)
            return true;
        
        $errors = array();
        
        $outSort = array();
        foreach($sort as $key => $sortValue)
        {
            if(!$sortValue)
                continue;
            
            if(is_integer($key))
                $outSort[$sortValue] = 'asc';
            else
            {
                $sortValue = strtolower($sortValue);
                
                if(!in_array($sortValue, array('asc', 'desc')))
                    $sortValue = 'asc';
                
                $outSort[$key] = $sortValue;
            }
        }

        $sort = $outSort;
        
        foreach($sort as $key => $sortValue)
        {
            if(!in_array($key, $allovedSortes))
                $errors['sortParameter'] = sprintf(Arn_language::getLabel('validation_sort_unallowed'), implode(', ', $allovedSortes));
        }
        
        return $errors?$errors:true;
    }
    
    public static function limitValidation(&$limit)
    {
        if(!$limit)
            return true;
        
        $errors = array();
        if( (!is_integer($limit) && !is_array($limit))
            || (is_array($limit) && 
                    (count($limit)!=2 || !is_integer($limit[0]) || !is_integer($limit[1]))
               )
          )
            $errors['limitParameter'] = Arn_language::getLabel('validation_limit_incorrect');
        
        if(is_integer($limit))
            $limit = array(0, $limit);
        
        if($limit[1]>100)
            $errors['limitParameter'] = Arn_language::getLabel('validation_limit_max');
        
        return $errors?$errors:true;
    }
    
    public static function detailsValidation($details)
    {
        if(!$details)
            return true;
        
        $errors = array();
       
        $allovedDetails = array('hotelDetails', 'meeting_room', 'airport', 'amenity', 'description', 'image', 'policy', 'supplier', 'type');
        foreach($details as $detailValue)
        {
            if(!in_array($detailValue, $allovedDetails))
                $errors['detailsParameter'] = Arn_language::getLabel('validation_unallowed_detail');
        }
        
        return $errors?$errors:true;
    }
    
    private static function future($field, $val)
    {
        if($val<date('Y-m-d'))
        {
            return sprintf(Arn_language::getLabel('validation_future_date'), $field);
        }
        else
            return true;
    }
    
    public static function validateXml($xml)
    {
        $outErrors = array();
        
        set_error_handler(array('Arn_validate', 'HandleXmlError'));
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        unset($doc);
        restore_error_handler();
    }
    
    public static function validateXmlByXsd($xml, $xsdURL)
    {
        $outErrors = array();
        
        set_error_handler(array('Arn_validate', 'HandleXmlError'));
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $doc->schemaValidate($xsdURL);
        unset($doc);
        restore_error_handler();
    }
    
    public static function HandleXmlError($errno, $errstr, $errfile, $errline)
    {
        if ($errno==E_WARNING && (substr_count($errstr,"DOMDocument::loadXML()")>0 || substr_count($errstr,"DOMDocument::schemaValidate()")>0))
        {
            throw new Arn_Error(Arn_language::getLabel('validation_XML_error'), array($errstr));
        }
        else
            return false;
    }
}