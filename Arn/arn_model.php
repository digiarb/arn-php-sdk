<?php
require_once(dirname(__FILE__) . '/DB/mysqldb.php');
require_once(dirname(__FILE__) . '/DB/mysqlresult.php');

Class ARN_Model
{
    private static $db;
    
    private static function initDB()
    {
        self::$db = MySqlDatabase::getInstance();
        try {
            if(ARN::$userConfig['useArnDb'])
                self::$db->connect(Arn::$arnConfig['db']['hostname'], Arn::$arnConfig['db']['username'], Arn::$arnConfig['db']['password'], Arn::$arnConfig['db']['database']);
            else
                self::$db->connect(Arn::$userConfig['db']['hostname'], Arn::$userConfig['db']['username'], Arn::$userConfig['db']['password'], Arn::$userConfig['db']['database']);
                
        } 
        catch (Exception $e) {
            throw new Arn_Error(Arn_language::getLabel('error_common'), $e->getMessage());
        }
    }
    
    public static function getResults($query)
    {
        self::initDB();
        
        $res = array();
        foreach (self::$db->iterate($query) as $row) {
            $res[] = $row;
        }
        
        $cntRes = self::$db->fetchOneRow('SELECT FOUND_ROWS() as found_rows');
        
        $res['totalCount'] = $cntRes->found_rows;
        return $res;
    }
}