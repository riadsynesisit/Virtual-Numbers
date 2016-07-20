<?php

/**
 * @package    Joomla
 * @subpackage    DIDWW
 * author DIDWW
 * copyright Copyright (C) 2010 DIDWW.com. All Rights Reserved.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Websites: http://open.didww.com
 * Technical Support: http://didww.com/support
 */

class DidwwApi {

    static $_errorCodes =
            array(
        "100" => "Access denied",
        "150" => "Server error when validating an API client request",
        "151" => "Array has invalid data",
        "200" => "Server error when processing an API client request",
        "300" => "Type not valid",
        "301" => "Protocol not valid",
        "302" => "Unsupported format for this type",
        "303" => "PSTN prefix not supported",
        "400" => "API Order ID not found or invalid",
        "401" => "API Order ID not in valid status",
        "405" => "Transaction refused",
        "410" => "Transaction out of balance",
        "411" => "Account balance is disabled/suspened/has not enough amount for purchases",
        "430" => "Customer: Prepaid Balance disabled or not exist",
        "500" => "Region(s) not found or invalid",
        "501" => "City not found",
        "505" => "DIDs not available for this region",
        "600" => "DID Number not found or invalid",
        "601" => "DID Number not found in Reserved Pool",
        "602" => "DID Number expired. Please renew"
    );

    /**
     * @var SoapClient
     */
    private $_client;

    /**
     * @var SoapFault
     */
    private $_errorString;
    private $_errorCode;
    private $_authstr;
    private $_callback;

    function setCallback($callback) {
        if (!is_string($callback) && !is_array($callback)) {
            return false;
        }

        $this->_callback = $callback;
        return true;
    }

    function getClient() {
        return $this->_client;
    }

    function getErrorCode() {
        return $this->_errorCode;
    }

    function getErrorString() {
        return $this->_errorString;
    }

    function getError() {
        if ($this->_errorString) {
            return "Error: (code: {$this->_errorCode}, message: {$this->_errorString})";
        }
        return NULL;
    }



    function setCredentials($wsdl_url, $user, $pass, $test = false)
    {
        //$wsdl_url = DidwwConfigHelper::getParams()->get('DIDWW_API_WSDL'.($test?'_TEST':''));
    	$this->_client = new SoapClient($wsdl_url);
        $this->_authstr = sha1($user . $pass  .  ($test ? 'sandbox'  :''));        
    }

    function __construct($wsdl_url, $user, $pass, $test = false) {
        $this->setCredentials($wsdl_url, $user, $pass, $test);
    }

    function getAvailableMethods() {
        if (!isset($this->_client))
            return null;

        $soapFunctions = $this->_client->__getFunctions();
        for ($i = 0; $i < count($soapFunctions); $i++) {
            preg_match("/[\s\S]*?(didww_[\s\S]*?)\([\s\S]*?/", $soapFunctions[$i], $matche);
            $soapFunctions[$i] = $matche[1];
        }
        return $soapFunctions;
    }

    private function _handleQuery($method, $params = array()) {


        if (!isset($this->_client))
            return null; // client undefined if missed internet connection

        $params = array_merge(array('auth_string' => $this->_authstr), $params);
        $timeStart = microtime(true);
        try{
            $this->_errorCode = null;
            $this->_errorString = null;
            $method = 'didww_' . $method;
            //time measure
            $result = $this->_client->__soapCall($method, $params);

            
            
            
           if(is_null($result)){
               
                throw new Exception('Undefined API result');
              
           }
        }
        catch(SoapFault $e){
            $this->_errorCode = $e->faultcode;
            $this->_errorString = $e->faultstring;
            $result = null;
        }
        catch(Exception $e){
            $this->_errorCode = $e->getCode();
            $this->_errorString = $e->getMessage();
            $result = null;
        }

        $timeFinish = microtime(true);
        // If result contains error field trying to resolve error text by error code
        if (isset($result->error) && $result->error > 0) {
            $result->error = isset(self::$_errorCodes[$result->error]) ? JText::_(self::$_errorCodes[$result->error]) :
                    'Unknown error with code : ' . $result->error;
        }


        if ($this->_callback) {

            call_user_func_array($this->_callback, array(
                "result" => $result,
                "method" => $method,
                "params" => $params,
                "error" => $this->_errorString,
                "code" => $this->_errorCode,
                "seconds" => $timeFinish - $timeStart
            ));
        }
        return $result;
    }

    function callhistory_invoices($customer_id = 0, $from_date = '', $to_date = '') {
        return $this->_handleQuery(
                        'callhistory_invoices', array(
                    'customer_id' => $customer_id,
                    'from_date' => $from_date,
                    'to_date' => $to_date
                        )
        );
    }

    ////didww_getsmslog($auth, $customer_id, $from_date, $to_date, $destination, $source,  $success,  $limit, $offset, $order, $order_Dir ){
    function smslog($customer_id, $from_date, $to_date, $destination, $source, $success, $limit, $offset, $order, $order_Dir) {
        return $this->_handleQuery(
                        'getsmslog', array(
                    'customer_id' => $customer_id,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'destination' => $destination,
                    'source' => $source,
                    'success' => $success,
                    'limit' => $limit,
                    'offset' => $offset,
                    'order' => $order,
                    'order_Dir' => $order_Dir
                        )
        );
    }

    /**
     * Get CDRlog with pagination
     * 
     * @param int $customer_id
     * @param string $did_number
     * @param string $from_date
     * @param string $to_date
     * @param int $limit
     * @param int $offset
     * @param string $order
     * @param string $order_Dir
     * @return array
     */
    function cdrlog($customer_id, $did_number, $from_date, $to_date, $limit, $offset, $order, $order_Dir) {
        return $this->_handleQuery(
            'getcdrlog', array(
                'customer_id' => $customer_id,
                'did_number' => $did_number,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'limit' => $limit,
                'offset' => $offset,
                'order' => $order,
                'order_Dir' => $order_Dir
            )
        );
    }

    /**
     * Get PSTN traffic for statistic
     *
     * @param string $from_date
     * @param string $to_date
     * @return array
     */
    function pstnTraffic($from_date = NULL, $to_date = NULL) {
        $api_traffic = $this->_handleQuery(
                'pstn_traffic', array(
            'from_date' => $from_date,
            'to_date' => $to_date,
                )
        );

        return $api_traffic;
    }

    function getDetails() {
        return $this->_handleQuery('getdidwwapidetails');
    }

    function getservicedetails($customer_id = 0, $api_order_id = 0, $did_number = 0) {
        return $this->_handleQuery('getservicedetails', array('customer_id' => $customer_id, 'api_order_id' => $api_order_id, 'did_number' => $did_number));
    }

    function getRegions($iso = 0, $city_prefix = 0, $last_request_gmt = 0) {
        return $this->_handleQuery(
                        'getdidwwregions', array(
                    'country_iso' => $iso,
                    'city_prefix' => $city_prefix,
                    'last_request_gmt' => $last_request_gmt
                        )
        );
    }

    function getCity($iso, $city_prefix) {
        return $this->getRegions($iso, $city_prefix);
    }

    function getCountry($iso) {
        return $this->getRegions($iso);
    }

    function didrestore($customer_id = 0, $did_number = '', $period = 1, $renew = 0, $uniq_id) {
        return $this->_handleQuery(
                        'didrestore', array(
                    'customer_id' => (int) $customer_id,
                    'did_number' => $did_number,
                    'period' => $period,
                    'uniq_hash' => md5($uniq_id),
                    'renew' => $renew
                        )
        );
    }

    function getPstnRates($iso = 0, $pstn_prefix = 0) {
        return $this->_handleQuery('getdidwwpstnrates', array('country_iso_code' => $iso, 'pstn_prefix' => $pstn_prefix));
    }

    /**
     * Update pstn rates on DIDWW side
     * @param array $rates
     * @return array
     */
    function setPstnRates($rates) {
        return $this->_handleQuery('updatepstnrates', array('rates' => $rates));
    }

    function checkPstnNumber($number) {
        return $this->_handleQuery('checkpstnnumber', array('pstn_number' => $number));
    }

    function orderautorenew($user_id, $did_number, $period, $uniq_id) {
        return $this->_handleQuery('orderautorenew', array(
                    'customer_id' => $user_id,
                    'did_number' => $did_number,
                    'period' => $period,
                    'uniq_hash' => md5($uniq_id)
                        )
        );
    }

    /**
     *
     * @param string $user_id
     * @param string $iso
     * @param string $city_prefix
     * @param int $period
     * @param array $map_data
     * @param string $prepaid_funds
     * @param int $uniq_id
     * @param int $city_id
     * @return Object
     */
    function createOrder($user_id, $iso, $city_prefix, $period, $map_data, $prepaid_funds, $uniq_id, $city_id) {
        $map_data = $this->convertMapData($map_data);
        return $this->_handleQuery('ordercreate', array('customer_id' => $user_id,
                    'country_iso_code' => $iso,
                    'city_prefix' => $city_prefix,
                    'period' => $period,
                    'map_data' => $map_data,
                    'prepaid_funds' => $prepaid_funds,
                    'uniq_hash' => md5($uniq_id),
                    'city_id' => $city_id
                ));
    }

    /**
     * Will cancel order which in valid status (Completed, Pending)
     * Cancelation will not refunds any money and all services will be removed immediately
     * @param string $user_id
     * @param string $did_number
     */
    function cancelOrder($user_id, $did_number) {
        return $this->_handleQuery('ordercancel', array('customer_id' => $user_id,
                    'did_number' => $did_number
                ));
    }

    function updateMapping($user_id, $did_number, $map_data) {
        $map_data = $this->convertMapData($map_data);

        return $this->_handleQuery('updatemapping', array(
                    'customer_id' => $user_id,
                    'did_number' => $did_number,
                    'map_data' => $map_data
                ));
    }

    /**
     * TODO: move this logic to forwarding lib
     * @param $mapData
     * @return array
     */
    private function convertMapData($mapData) {
        //$mapData['map_type'] == 'ITSP' ||
        if ($mapData['map_type'] == 'CITSP' || $mapData['map_type'] == 'VOIP')
            $mapData['map_type'] = 'URI';

        elseif ($mapData['map_type'] == 'PSTN')
            $mapData['map_detail'] = str_replace('-', '', $mapData['map_detail']);

        elseif ($mapData['map_type'] == 'ACF') {
            didwwImport('DidwwACF', 'lib/forwarding');
            $mapData['map_type'] = DidwwForwardingACF::DEFAULT_ACF_MAP_TYPE;
            $mapData['map_detail'] = DidwwForwardingACF::DEFAULT_ACF_MAP_DETAILS;
            $mapData['map_proto'] = DidwwForwardingACF::DEFAULT_ACF_MAP_PROTO;
        }
        elseif($mapData['map_type'] == 'LINPHONE'){
            didwwImport('DidwwLINPHONE', 'lib/forwarding');
            $mapData['map_proto'] = DidwwForwardingLINPHONE::API_MAP_PROTO;
            $mapData['map_type'] = DidwwForwardingLINPHONE::API_MAP_TYPE;
            $mapData['map_detail'] = DidwwForwardingLINPHONE::API_HOST."/".$mapData['map_detail'];
        }

        return $mapData;
    }

    function updatePrepaidBalance($customer_id, $prepaid_funds, $operation_type, $system_id) {
        return $this->_handleQuery('updateprepaidbalance', array('customer_id' => $customer_id,
                    'prepaid_funds' => $prepaid_funds,
                    'operation_type' => $operation_type,
                    'uniq_hash' => md5($operation_type . '.' . $system_id)
                        )
        );
    }

    function decreaseBalance($customer_id, $prepaid_funds, $system_id, $entropy = '') {

        if ($prepaid_funds > 0) {
            $prepaid_funds = -$prepaid_funds;
        }

        $operation = '2';
        if ($entropy) {
            $system_id.='_' . (string) $entropy;
        }

        return $this->updatePrepaidBalance($customer_id, $prepaid_funds, $operation, $system_id);
    }

    function increaseBalance($customer_id, $prepaid_funds, $system_id, $entropy = '') {

        if ($prepaid_funds < 0) {
            $prepaid_funds = - $prepaid_funds;
        }

        $operation = '1';
        if ($entropy) {
            $system_id.='_' . (string) $entropy;
        }

        return $this->updatePrepaidBalance($customer_id, $prepaid_funds, $operation, $system_id);
    }

    function getPrepaidBalance($customer_id = 0) {
        return $this->_handleQuery('getprepaidbalancelist', array('customer_id' => $customer_id));
    }

    function getCdrLog($customer_id, $last_requested_date = NULL) {

        if (!$last_requested_date)
            $last_requested_date = '2010-01-01 00:00:00';
        return $this->_handleQuery('getfullcdrlog', array(
                    'customer_id' => $customer_id,
                    'last_requested_date' => $last_requested_date
                        )
        );
    }

    function getDidwwApiDetails() {
        return $this->_handleQuery('getdidwwapidetails');
    }
    
	function getservicelist($customer_id = 0) {
        return $this->_handleQuery('getservicelist', array('customer_id' => $customer_id));
    }

}

?>