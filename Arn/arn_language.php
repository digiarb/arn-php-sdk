<?php
Class Arn_language
{
    static private $labels = array();
    public static function setLangCode($langCode)
    {
        Arn::$langCode = $langCode;
        $lang=array();
        require_once(dirname(__FILE__) . '/languages/'.strtolower(Arn::$langCode).'.php');
        self::$labels = $lang;
    }
    
    public static function getLangCode()
    {
        return Arn::$langCode;
    }
    
    public static function getLabel($label)
    {
        return isset(self::$labels[$label])? self::$labels[$label] : '';
    }
}