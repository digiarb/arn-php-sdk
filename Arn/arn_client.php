<?php

class Client
{
    protected static $soapClient = null;
    protected static $secureSoapClient = null;
   
    protected function __construct()
    {
    }
    
    public static function getInstance($arnConfig)
    {
        if (!isset(self::$soapClient))
            self::$soapClient = new SoapClient($arnConfig['apiWSDL'], array('location'=>$arnConfig['apiURL'], 'encoding'=>'utf-8', 'trace' => false, 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP)); 
        
        return self::$soapClient;
    }
    
    public static function getSecureInstance($arnConfig)
    {
        if (!isset(self::$secureSoapClient))
            //self::$secureSoapClient = new SoapClient($arnConfig['apiWSDL'], array('location'=>$arnConfig['apiSecureURL'], 'encoding'=>'utf-8', 'trace' => false , 'compression' => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP ), 'ssl_method' => SOAP_SSL_METHOD_SSLv3));
            self::$secureSoapClient = new SoapClient($arnConfig['apiWSDL'], array('location'=>$arnConfig['apiSecureURL'], 'encoding'=>'utf-8', 'trace' => false , 'compression' => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP )));
        
        return self::$secureSoapClient;
    }
}

?>