<?php

namespace tykiv;

require_once('model.php');

/**
 * Example data access object
 *
 * Web Service Information:
 *     example: http://express.api.adstation.com/?token=1fdce93623c32d1b0b7a24de9b8ec813908f46bc39033c79c79f0495bdd84017&action=getTestData
 *     domain: http://express.api.adstation.com
 *     parameters:
 *         action: getTestData
 *         token: 1fdce93623c32d1b0b7a24de9b8ec813908f46bc39033c79c79f0495bdd84017
 */

class CandidateProjectDao
{
    /**
     * Retrieves data from the web service
     *
     * @return CandidateProjectModel[] $models
     */
    
    public $models;
    
    // requires trailing slash
    public static $api_base_url = 'http://express.api.adstation.com/';
    public static $user_agent = 'candidateProject-TylerKivari v0.1 -SSL';
        
    private $config = array(
        'action' => 'getTestData',
        'token' => '1fdce93623c32d1b0b7a24de9b8ec813908f46bc39033c79c79f0495bdd84017'
    );
    
    private $method;
    private $curl_opts = array();
    private $response = array();
    
    /*
     * @return void
     * configures curl options, allowing optional customized curlopts to override defaults
     * configures request URL and parameters 
     * configures request method (GET, POST)
     * 
     * @param Array $curl_config
     * @param Array $user_config
     */ 
    public function __construct($curl_config = array(), $user_config = array(), $method = 'GET') {
        $this->curl_opts = $this->config_curl_opts($curl_config);
        $this->config = array_merge($this->config, $user_config);
        $this->method = $method;
    }                
                
    public function getTestData() {
        // Array of object:CandidateProjectModel
        $models = array();
        
        $this->response = $this->curl_connect();
        
        // If the call was made and there were no errors
        if ($this->response['error_no'] == 0) {
            if ($this->response['curl_info']['http_code'] == 200) {
                $models = json_decode($this->response['data']);

                $this->models = \tykiv\CandidateProjectDao::getModels($models);
            }
            else { // non-200 HTTP response
                throw new \Exception(\tykiv\CandidateProjectDao::$api_base_url . " returned HTTP response: " . $this->response['curl_info']['http_code'], $this->response['error_no']);
            }
        }
        else {
            // If we get here, there was an error:
            throw new \Exception("Connecting to " . \tykiv\CandidateProjectDao::$api_base_url . " failed: " . $this->response['error'], $this->response['error_no']);
        }
        
        
    }
    
    /*
     * @return array $response
     * 
     * Set up appropriate curl options and make HTTP request.
     */
    private function curl_connect() {
        
        $url = \tykiv\CandidateProjectDao::$api_base_url;
        
        // If the request method is POST, add the config parameters as POST fields
        // If the request method is GET, append the config parameters to the end of the URL as a Query string
        switch($this->method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->config);
                break;
            case 'GET':
            default:
                $request_params = array();
                foreach ($this->config as $k => $v) {
                    $request_params[] = $k.'='.$v;
                }
                $url .= "?" . implode("&",$request_params);
        }
        
        $ch = curl_init($url);
        
        foreach($this->curl_opts as $name => $value) {
            curl_setopt($ch, constant($name), $value);
        }

        $response['data'] = curl_exec($ch);
        $response['curl_info'] = curl_getinfo($ch);
        $response['error_no'] = curl_errno($ch);
        $response['error'] = curl_error($ch);
        
        curl_close($ch);
        
        return $response;
    }
    
    
    /*
     * @return array $models
     * Converts JSON response from REST service into an array of CandidateProjectModel objects
     * 
     * @param array $data
     */ 
    public static function getModels($data) {
        
        $models = array();
        
        foreach ($data as $model) {
            $models[] = new \tykiv\CandidateProjectModel($model);
        }

        return $models;
    }
    
    
    private function config_curl_opts($curl_config) {
        return array_merge(array(
                // Don't use response header
                'CURLOPT_HEADER'            => false,
                // Return results as string
                'CURLOPT_RETURNTRANSFER'    => true,

                // Connection timeout, in seconds
                'CURLOPT_CONNECTTIMEOUT'    => 10,
                // Total timeout, in seconds
                'CURLOPT_TIMEOUT'           => 45,

                'CURLOPT_USERAGENT'         => \tykiv\CandidateProjectDao::$user_agent,

                // Follow Location: headers (HTTP 30x redirects)
                'CURLOPT_FOLLOWLOCATION'    => true,
                // Set a max redirect limit
                'CURLOPT_MAXREDIRS'         => 5,

                // Force connection close
                'CURLOPT_FORBID_REUSE'      => true,
                // Always use a new connection
                'CURLOPT_FRESH_CONNECT'     => true,

                'CURLOPT_SSL_VERIFYPEER'    => true,
                'CURLOPT_SSL_VERIFYHOST'    => true,

                // Allow all encodings
                'CURLOPT_ENCODING'          => '*/*',
                'CURLOPT_AUTOREFERER'       => true,
                
            ), $curl_config);
    }
    
}