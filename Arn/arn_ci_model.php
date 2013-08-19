<?php
Class ARN_Model
{
    public static function getResults($query)
    {
        if(ARN::$userConfig['useArnDb'])
            $dbConfig = ARN::$arnConfig['db'];
        else
            $dbConfig = ARN::$userConfig['db'];
                
        $CI = & get_instance();
        $ARNDB = $CI->load->database($dbConfig, true);
        $res = $ARNDB->query($query)->result();
        return $res;
    }
}