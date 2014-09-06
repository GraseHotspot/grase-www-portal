<?php

/* Copyright 2012 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://grasehotspot.org/

    GRASE Hotspot is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GRASE Hotspot is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GRASE Hotspot.  If not, see <http://www.gnu.org/licenses/>.
*/

class Paypal extends PaymentGateway
{
    private $sandboxendpoint = 'https://api-3t.sandbox.paypal.com/nvp';
    private $sandboxsite = 'https://www.sandbox.paypal.com/webscr?';
    private $livesite = 'https://www.paypal.com/webscr?';
    private $liveendpoint = 'https://api-3t.paypal.com/nvp';
    
    private $endpoint;
    private $siteurl;
    
    private $APIpassword;
    private $APIusername;
    private $APIsignature;
    
    public function __construct()
    {
        $this->chooseEndpoint();
    }
    
    public function APIsettings($Settings)
    {
        // $Settings should be of class Settings/SettingsMySQL
        // $Settings can also be array
        if(is_object($Settings)){
            $this->APIpassword = $Settings->getSetting('PayPalAPIpassword');
            $this->APIusername = $Settings->getSetting('PayPalAPIusername');
            $this->APIsignature = $Settings->getSetting('PayPalAPIsignature');
        }else{ // Assume array instead (error check? TODO)
            $this->APIpassword = $Settings['APIpassword'];
            $this->APIusername = $Settings['APIusername'];
            $this->APIsignature = $Settings['APIsignature'];
        }
    }
    
    public function chooseEndpoint($endpoint = 'sandbox')
    {
        if($endpoint == 'live')
        {
            $this->endpoint = $this->liveendpoint;
            $this->siteurl = $this->livesite;
        }else{
            $this->endpoint = $this->sandboxendpoint;
            $this->siteurl = $this->sandboxsite;
        }
    }
    
    public function siteurl()
    {
        return $this->siteurl;
    }
    
    // paypalApiRequest based on http://www.techfounder.net/2011/04/23/breaking-down-the-paypal-api/
    public function paypalApiRequest($method, $params = array() ) {
        // Need valid API method
        if ( empty($method) ) return false;
        
        // Setup request parameters
        $requestParams = array(
            'METHOD' => $method,
            'VERSION' => '87.0',
            'PWD' => $this->APIpassword,
            'USER' => $this->APIusername,
            'SIGNATURE' => $this->APIsignature);
            
        // Build NVP query string
        $request = http_build_query($requestParams + $params);
        
        // Setup cURL options (TODO: Package needs to depend on php-curl/php5-curl
    	$curlOptions = array (
	        CURLOPT_URL => $this->endpoint,
	        CURLOPT_VERBOSE => 1,
	        CURLOPT_SSL_VERIFYPEER => true,
	        CURLOPT_SSL_VERIFYHOST => 2,
	        CURLOPT_CAPATH => '/etc/ssl/certs/',
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_POST => 1,
	        CURLOPT_POSTFIELDS => $request
    	);
    	
    	$ch = curl_init();
    	if($ch == FALSE){
    	    \Grase\ErrorHandling::fatalError(T_('PHP cURL extension failed to initalise'));
    	}
    	curl_setopt_array($ch, $curlOptions);
    	
    	// Send request and get API response
    	$response = curl_exec($ch);
    	
    	// Check for cURL errors
    	if (curl_errno($ch)) {
    	    // TODO: Log in admin log more details of request?
    	    \Grase\ErrorHandling::fatalError(T_('cURL error occured') . ' '. curl_error($ch));
    	}else{
    	    curl_close($ch);
    	    $responseArray = array();
    	    parse_str($response, $responseArray); // (Break NVP string into array)
    	    return $responseArray;
    	}
    }
    
    
    
    
}
?>
