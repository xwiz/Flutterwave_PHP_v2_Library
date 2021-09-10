<?php 
namespace Flutterwave;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Unirest\Request;
use Unirest\Request\Body;

/**
 * Flutterwave's Rave payment gateway PHP SDK
 * @author Olufemi Olanipekun <iolufemi@ymail.com>
 * @author Emereuwaonu Eze <emereuwaonueze@gmail.com>
 * @version 1.0
 **/

class Rave {
    //Api keys
    protected $publicKey;
    protected $secretKey;
    protected $txref;
    protected $integrityHash;
    protected $payButtonText = 'Make Payment';
    protected $redirectUrl;
    protected $meta = array();
    // protected $env;
    protected $transactionPrefix;
   // public $logger;
    protected $handler;
    // protected $stagingUrl = 'https://ravesandboxapi.flutterwave.com';
    protected $liveUrl = 'https://api.ravepay.co';
    protected $baseUrl;
    protected $transactionData;
    protected $overrideTransactionReference;
    protected $requeryCount = 0;

    //Payment information
    protected $account;
    protected $accountno;
    protected $key;
    protected $pin;
    protected $json_options;
    protected $post_data;
    protected $options;
    protected $card_no;
    protected $cvv;
    protected $expiry_month;
    protected $expiry_year;
    protected $amount;
    protected $paymentOptions = Null;
    protected $customDescription;
    protected $customLogo;
    protected $customTitle;
    protected $country;
    protected $currency;
    protected $customerEmail;
    protected $customerFirstname;
    protected $customerLastname;
    protected $customerPhone;

    //EndPoints 
    protected $end_point ;
    protected $authModelUsed;
    protected $flwRef;
    protected $txRef;

    /**
     * Construct
     * @param string $publicKey Your Rave publicKey. Sign up on https://rave.flutterwave.com to get one from your settings page
     * @param string $secretKey Your Rave secretKey. Sign up on https://rave.flutterwave.com to get one from your settings page
     * @param string $prefix This is added to the front of your transaction reference numbers
     * @param string $env This can either be 'staging' or 'live'
     * @param boolean $overrideRefWithPrefix Set this parameter to true to use your prefix as the transaction reference
     * @return object
     * */
    public function __construct($publicKey, $secretKey, $prefix = 'RV', $overrideRefWithPrefix = false){
        define("APPLICATION_FORMAT", "application/json");
        define("SECKEY_QUERY_PARAM", "?seckey=");
        Request::verifyPeer(false);
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
        // $this->env = $env;
        $this->transactionPrefix = $overrideRefWithPrefix ? $prefix : $prefix.'_';
        $this->overrideTransactionReference = $overrideRefWithPrefix;
        // create a log channel
        $log = new Logger('flutterwave/rave');
        $this->logger = $log;
        $log->pushHandler(new RotatingFileHandler('rave.log', 90, Logger::DEBUG));

        $this->createReferenceNumber();

        // if($this->env === 'staging'){
        //     $this->baseUrl = $this->stagingUrl;
        // }elseif($this->env === 'live'){
        //     $this->baseUrl = $this->liveUrl;
        // }else{
        //     $this->baseUrl = $this->stagingUrl;
        // }

        // set the baseurl
        $this->baseUrl = $this->liveUrl;

        $this->logger->notice('Rave Class Initializes....');
        return $this;
    }
    
     /**
     * Generates a checksum value for the information to be sent to the payment gateway
     * @return object
     * */
    public function createCheckSum(){
        $this->logger->notice('Generating Checksum....');
        $payload = array( 
            "PBFPubKey" => $this->publicKey, 
            "amount" => $this->amount, 
            "customer_email" => $this->customerEmail, 
            "customer_firstname" => $this->customerFirstname, 
            "txref" => $this->txref, 
            "payment_options" => $this->paymentOptions, 
            "customer_lastname" => $this->customerLastname, 
            "country" => $this->country, 
            "currency" => $this->currency, 
            "custom_description" => $this->customDescription, 
            "custom_logo" => $this->customLogo, 
            "custom_title" => $this->customTitle, 
            "customer_phone" => $this->customerPhone,
            "pay_button_text" => $this->payButtonText,
            "redirect_url" => $this->redirectUrl,
            "hosted_payment" => 1
        );
        
        ksort($payload);
        
        $this->transactionData = $payload;
        
        $hashedPayload = '';
        
        foreach($payload as $key => $value){
            $hashedPayload .= $value;
        }
        $completeHash = $hashedPayload.$this->secretKey;
        $hash = hash('sha256', $completeHash);
        
        $this->integrityHash = $hash;
        return $this;
    }

    /**
     * Generates a transaction reference number for the transactions
     * @return object
     * */
    public function createReferenceNumber(){
        $this->logger->notice('Generating Reference Number....');
        if($this->overrideTransactionReference){
            $this->txref = $this->transactionPrefix;
        }else{
            $this->txref = uniqid($this->transactionPrefix);
        }
        $this->logger->notice('Generated Reference Number....'.$this->txref);
        return $this;
    }
    
    /**
     * gets the current transaction reference number for the transaction
     * @return string
     * */
    public function getReferenceNumber(){
        return $this->txref;
    }
    
    /**
     * Sets the transaction amount
     * @param integer $amount Transaction amount
     * @return object
     * */
    public function setAmount($amount){
        $this->amount = $amount;
        return $this;
    }

    /**
     * Sets the transaction amount
     * @param integer $amount Transaction amount
     * @return object
     * */
    public function setAccount($account){
        $this->account = $account;
        return $this;
    }
    /**
     * Sets the transaction amount
     * @param integer $amount Transaction amount
     * @return object
     * */
    public function setAccountNumber($accountno){
        $this->accountno = $accountno;
        return $this;
    }

    /**
     * Sets the transaction transaction card number
     * @param integer $card_no Transaction card number
     * @return object
     * */
    public function setCardNo($card_no){
        $this->card_no = $card_no;
        return $this;
    }

    /**
     * Sets the transaction transaction CVV
     * @param integer $CVV Transaction CVV
     * @return object
     * */
    public function setCVV($cvv){
        $this->cvv = $cvv;
        return $this;
    }
    /**
     * Sets the transaction transaction expiry_month
     * @param integer $expiry_month Transaction expiry_month
     * @return object
     * */
    public function setExpiryMonth($expiry_month){
        $this->expiry_month= $expiry_month;
        return $this;
    }

    /**
     * Sets the transaction transaction expiry_year
     * @param integer $expiry_year Transaction expiry_year
     * @return object
     * */
    public function setExpiryYear($expiry_year){
        $this->expiry_year = $expiry_year;
        return $this;
    }
    /**
     * Sets the transaction transaction end point
     * @param string $end_point Transaction expiry_year
     * @return object
     * */
    public function setEndPoint($end_point){
        $this->end_point = $end_point;
        return $this;
    }


     /**
     * Sets the transaction authmodel
     * @param string $authmodel 
     * @return object
     * */
    public function setAuthModel($authmodel){
        $this->authModelUsed = $authmodel;
        return $this;
    }
    
    
    /**
     * gets the transaction amount
     * @return string
     * */
    public function getAmount(){
        return $this->amount;
    }
    
    /**
     * Sets the allowed payment methods
     * @param string $paymentOptions The allowed payment methods. Can be card, account or both 
     * @return object
     * */
    public function setPaymentOptions($paymentOptions){
        $this->paymentOptions = $paymentOptions;
        return $this;
    }
    
    /**
     * gets the allowed payment methods
     * @return string
     * */
    public function getPaymentOptions(){
        return $this->paymentOptions;
    }
    
    /**
     * Sets the transaction description
     * @param string $customDescription The description of the transaction
     * @return object
     * */
    public function setDescription($customDescription){
        $this->customDescription = $customDescription;
        return $this;
    }
    
    /**
     * gets the transaction description
     * @return string
     * */
    public function getDescription(){
        return $this->customDescription;
    }
    
    /**
     * Sets the payment page logo
     * @param string $customLogo Your Logo
     * @return object
     * */
    public function setLogo($customLogo){
        $this->customLogo = $customLogo;
        return $this;
    }
    
    /**
     * gets the payment page logo
     * @return string
     * */
    public function getLogo(){
        return $this->customLogo;
    }
    
    /**
     * Sets the payment page title
     * @param string $customTitle A title for the payment. It can be the product name, your business name or anything short and descriptive 
     * @return object
     * */
    public function setTitle($customTitle){
        $this->customTitle = $customTitle;
        return $this;
    }
    
    /**
     * gets the payment page title
     * @return string
     * */
    public function getTitle(){
        return $this->customTitle;
    }
    
    /**
     * Sets transaction country
     * @param string $country The transaction country. Can be NG, US, KE, GH and ZA
     * @return object
     * */
    public function setCountry($country){
        $this->country = $country;
        return $this;
    }
    
    /**
     * gets the transaction country
     * @return string
     * */
    public function getCountry(){
        return $this->country;
    }
    
    /**
     * Sets the transaction currency
     * @param string $currency The transaction currency. Can be NGN, GHS, KES, ZAR, USD, EUR and GBP
     * @return object
     * */
    public function setCurrency($currency){
        $this->currency = $currency;
        return $this;
    }
    
    /**
     * gets the transaction currency
     * @return string
     * */
    public function getCurrency(){
        return $this->currency;
    }
    
    /**
     * Sets the customer email
     * @param string $customerEmail This is the paying customer's email
     * @return object
     * */
    public function setEmail($customerEmail){
        $this->customerEmail = $customerEmail;
        return $this;
    }
    
    /**
     * gets the customer email
     * @return string
     * */
    public function getEmail(){
        return $this->customerEmail;
    }
    
    /**
     * Sets the customer firstname
     * @param string $customerFirstname This is the paying customer's firstname
     * @return object
     * */
    public function setFirstname($customerFirstname){
        $this->customerFirstname = $customerFirstname;
        return $this;
    }
    
    /**
     * gets the customer firstname
     * @return string
     * */
    public function getFirstname(){
        return $this->customerFirstname;
    }
    
    /**
     * Sets the customer lastname
     * @param string $customerLastname This is the paying customer's lastname
     * @return object
     * */
    public function setLastname($customerLastname){
        $this->customerLastname = $customerLastname;
        return $this;
    }
    
    /**
     * gets the customer lastname
     * @return string
     * */
    public function getLastname(){
        return $this->customerLastname;
    }
    
    /**
     * Sets the customer phonenumber
     * @param string $customerPhone This is the paying customer's phonenumber
     * @return object
     * */
    public function setPhoneNumber($customerPhone){
        $this->customerPhone = $customerPhone;
        return $this;
    }
    
    /**
     * gets the customer phonenumber
     * @return string
     * */
    public function getPhoneNumber(){
        return $this->customerPhone;
    }
    
    /**
     * Sets the payment page button text
     * @param string $payButtonText This is the text that should appear on the payment button on the Rave payment gateway.
     * @return object
     * */
    public function setPayButtonText($payButtonText){
        $this->payButtonText = $payButtonText;
        return $this;
    }
    
    /**
     * gets payment page button text
     * @return string
     * */
    public function getPayButtonText(){
        return $this->payButtonText;
    }
    
    /**
     * Sets the transaction redirect url
     * @param string $redirectUrl This is where the Rave payment gateway will redirect to after completing a payment
     * @return object
     * */
    public function setRedirectUrl($redirectUrl){
        $this->redirectUrl = $redirectUrl;
        return $this;
    }
    
    /**
     * gets the transaction redirect url
     * @return string
     * */
    public function getRedirectUrl(){
        return $this->redirectUrl;
    }
    
    /**
     * Sets the transaction meta data. Can be called multiple time to set multiple meta data
     * @param array $meta This are the other information you will like to store with the transaction. It is a key => value array. eg. PNR for airlines, product colour or attributes. Example. array('name' => 'femi')
     * @return object
     * */
    public function setMetaData($meta){
        array_push($this->meta, $meta);
        return $this;
    }
    
    /**
     * gets the transaction meta data
     * @return string
     * */
    public function getMetaData(){
        return $this->meta;
    }
    
    /**
     * Sets the event hooks for all available triggers
     * @param object $handler This is a class that implements the Event Handler Interface
     * @return object
     * */
    public function setEventHandler($handler){
        $this->handler = $handler;
        return $this;
    }
    
    /**
     * Requerys a previous transaction from the Rave payment gateway
     * @param string $referenceNumber This should be the reference number of the transaction you want to requery
     * @return object
     * */
    public function requeryTransaction($referenceNumber){
        $this->txref = $referenceNumber;
        $this->requeryCount++;
        $this->logger->notice('Requerying Transaction....'.$this->txref);
        if(isset($this->handler)){
            $this->handler->onRequery($this->txref);
        }

        $data = array(
            'txref' => $this->txref,
            'SECKEY' => $this->secretKey,
            'last_attempt' => '1'
            // 'only_successful' => '1'
        );

        // make request to endpoint using unirest.
        $headers = array('Content-Type' => constant("APPLICATION_FORMAT"));
        $body = Body::json($data);
        $url = $this->baseUrl.'/flwv3-pug/getpaidx/api/xrequery';

        // Make `POST` request and handle response with unirest
        $response = Request::post($url, $headers, $body);
  
        //check the status is success
        if ($response->body && $response->body->status === "success") {
            if($response->body && $response->body->data && $response->body->data->status === "successful"){
               $this->logger->notice('Requeried a successful transaction....'.json_encode($response->body->data));
                // Handle successful
                if(isset($this->handler)){
                    $this->handler->onSuccessful($response->body->data);
                }
            }elseif($response->body && $response->body->data && $response->body->data->status === "failed"){
                // Handle Failure
                $this->logger->warn('Requeried a failed transaction....'.json_encode($response->body->data));
                if(isset($this->handler)){
                    $this->handler->onFailure($response->body->data);
                }
            }else{
                // Handled an undecisive transaction. Probably timed out.
                $this->logger->warn('Requeried an undecisive transaction....'.json_encode($response->body->data));
                // I will requery again here. Just incase we have some devs that cannot setup a queue for requery. I don't like this.
                if($this->requeryCount > 4){
                    // Now you have to setup a queue by force. We couldn't get a status in 5 requeries.
                    if(isset($this->handler)){
                        $this->handler->onTimeout($this->txref, $response->body);
                    }
                }else{
                   $this->logger->notice('delaying next requery for 3 seconds');
                    sleep(3);
                   $this->logger->notice('Now retrying requery...');
                    $this->requeryTransaction($this->txref);
                }
            }
        }else{
           // $this->logger->warn('Requery call returned error for transaction reference.....'.json_encode($response->body).'Transaction Reference: '. $this->txref);
            // Handle Requery Error
            if(isset($this->handler)){
                $this->handler->onRequeryError($response->body);
            }
        }
        return $this;
    }
    
    /**
     * Generates the final json to be used in configuring the payment call to the rave payment gateway
     * @return string
     * */
    public function initialize(){
        $this->createCheckSum();
        $this->transactionData = array_merge($this->transactionData, array('integrity_hash' => $this->integrityHash), array('meta' => $this->meta));
        
        $json = json_encode($this->transactionData);
        echo '<html>';
        echo '<body>';
        echo '<center>Proccessing...<br /><img src="ajax-loader.gif" /></center>';
        echo '<script type="text/javascript" src="'.$this->baseUrl.'/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>';
        echo '<script>';
	    echo 'document.addEventListener("DOMContentLoaded", function(event) {';
        echo 'var data = JSON.parse(\''.$json.'\');';
        echo 'getpaidSetup(data);';
        echo '});';
        echo '</script>';
        echo '</body>';
        echo '</html>';
        return $json;
    }

    /**
     * this is the getKey function that generates an encryption Key for you by passing your Secret Key as a parameter.
     * @param string
     * @return string
     * */
    
    public function getKey($seckey){
        $hashedkey = md5($seckey);
        $hashedkeylast12 = substr($hashedkey, -12);

        $seckeyadjusted = str_replace("FLWSECK-", "", $seckey);
        $seckeyadjustedfirst12 = substr($seckeyadjusted, 0, 12);
        return $seckeyadjustedfirst12.$hashedkeylast12;

    }

    /**
     * this is the encrypt3Des function that generates an encryption Key for you by passing your transaction Data and Secret Key as a parameter.
     * @param string
     * @return string
     * */

    public function encrypt3Des($data, $key)
    {
        $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
        return base64_encode($encData);
    }
    /**
     * this is the encryption function that combines the getkey() and encryptDes().
     * @param string
     * @return string
     * */

    public function encryption($options){
         //encrypt and return the key using the secrekKey
         $this->key = $this->getkey($this->secretKey);
         //set the data to transactionData
         $this->transactionData = $options;
         //encode the data and the 
        return $this->encrypt3Des( $this->transactionData,  $this->key);
    }

     /**
     * makes a post call to the api 
     * @param array
     * @return object
     * */

    public function postURL($data){
        // make request to endpoint using unirest.
        $headers = array('Content-Type' => constant("APPLICATION_FORMAT"));
        $body = Body::json($data);
        $url = $this->baseUrl.'/'.$this->end_point;
        $response = Request::post($url, $headers, $body);
        return $response->raw_body;    // Unparsed body
     }

     
     /**
     * makes a get call to the api 
     * @param array
     * @return object
     * */

     public function getURL($url){
        // make request to endpoint using unirest.
        $headers = array('Content-Type' => constant("APPLICATION_FORMAT"));
        //$body = Body::json($data);
        $path = $this->baseUrl.'/'.$this->end_point;
        $response = Request::get($path.$url, $headers);
        return $response->raw_body;    // Unparsed body
     }
     /**
     * verify the transaction before giving value to your customers
     *  @param string
     *  @return object
     * */
    public function verifyTransaction($txRef, $seckey){
        $this->logger->notice('Verifying transaction...');
        $this->setEndPoint("flwv3-pug/getpaidx/api/v2/verify");
        $this->post_data =  array( 
            'txref' => $txRef,
            'SECKEY' => $seckey
            );
        $result  = $this->postURL($this->post_data);
             
        return json_decode($result,true);
      
    }


     /**
     * Validate the transaction to be charged
     *  @param string
     *  @return object
     * */
    public function validateTransaction($otp,$Ref){

        //pin
                $this->logger->notice('Validating otp...');
                $this->setEndPoint("flwv3-pug/getpaidx/api/validatecharge");
                $this->post_data = array(
                    'PBFPubKey' => $this->publicKey,
                    'transaction_reference' => $Ref,
                    'otp' => $otp);
                
                return $this->postURL($this->post_data);

    }

    public function validateTransaction2($otp, $Ref){
        
        $this->logger->notice('Validating otp...');
                $this->setEndPoint("flwv3-pug/getpaidx/api/validate");
                $this->post_data = array(
                    'PBFPubKey' => $this->publicKey,
                    'transactionreference' => $Ref,
                    'otp' => $otp);
                 
                return $this->postURL($this->post_data);
    }

      /**
     * Get all Transactions
     *  @return object
     * */
    public function getAllTransactions($array){

        $this->logger->notice('Getting all Transactions...');
        return $this->postURL($array); 

    }

      /**
     * Get all Settlements
     *  @return object
     * */
    public function getAllSettlements(){

        $this->logger->notice('Getting all Subscription...');
        $url = constant("SECKEY_QUERY_PARAM").$this->secretKey;
        return $this->getURL($url);

    }

     /**
     * Validating your bvn
     *  @param string
     *  @return object
     * */
    public function bvn($bvn){
        $this->logger->notice('Validating bvn...');
        $url = "/".$bvn.constant("SECKEY_QUERY_PARAM").$this->secretKey;
        return $this->getURL($url);
     } 

     /**
     * Get all Subscription
     *  @return object
     * */
    public function getAllSubscription(){
        //getALl Subscription
        $this->logger->notice('Getting all Subscription...');
        $uri = constant("SECKEY_QUERY_PARAM").$this->secretKey;
        return $this->getURL($uri);
     } 

        /**
     * Get a Subscription
     * @param $id,$email
     *  @return object
     * */
    public function fetchASubscription($data){
        $this->logger->notice('Fetching a Subscription...');
        $url = constant("SECKEY_QUERY_PARAM").$this->secretKey."&transaction_id=".$data['transaction_id'];
        return $this->getURL($url);
     }
     
        /**
     * Get a Settlement
     * @param $id,$email
     *  @return object
     * */
    public function fetchASettlement(){
        $this->logger->notice('Fetching a Subscription...');
        $url = constant("SECKEY_QUERY_PARAM").$this->secretKey;
        return $this->getURL($url);
     } 

      /**
     * activating  a subscription
     *  @return object
     * */
    public function activateSubscription(){
        $this->logger->notice('Activating Subscription...');
        $data = array(
            "seckey"=>$this->secretKey
        );
        return $this->postURL($data);
     } 

      /**
     * Creating a payment plan
     *  @param array
     *  @return object
     * */

    public function createPlan($array){
        $this->logger->notice('Creating Payment Plan...');
        return $this->postURL($array);
     } 

       /**
     * Creating a beneficiaries
     *  @param array
     *  @return object
     * */

    public function beneficiary($array){
        $this->logger->notice('Creating beneficiaries ...');
        return $this->postURL($array);
     }

     /**
     * transfer payment api 
     *  @param array
     *  @return object
     * */

     public function transferSingle($array){
        $this->logger->notice('Processing transfer...');
         return $this->postURL($array);
     }


     /**
     * bulk transfer payment api 
     *  @param array
     *  @return object
     * */

    public function transferBulk($array){
        $this->logger->notice('Processing bulk transfer...');
         return $this->postURL($array);
     }

      /**
     * Refund payment api 
     *  @param array
     *  @return object
     * */

    public function refund($array){
        $this->logger->notice('Initiating a refund...');
         return $this->postURL($array);
     }


    /**
     * Generates the final json to be used in configuring the payment call to the rave payment gateway api
     *  @param array
     *  @return object
     * */

     public function chargePayment($array){
        $this->options = $array;
        $this->json_options = json_encode($this->options);
        
        $this->logger->notice('Checking payment details..');
        //encrypt the required options to pass to the server
        $this->integrityHash = $this->encryption($this->json_options);

        $this->post_data = array(
            'PBFPubKey' => $this->publicKey,
            'client' => $this->integrityHash,
            'alg' => '3DES-24');

        $result  = $this->postURL($this->post_data);
        
        $this->logger->notice('Payment requires validation..'); 
        // the result returned requires validation
        $result = json_decode($result, true);

        if(isset($result['data']['authModelUsed'])){
            $this->logger->notice('Payment requires otp validation...');
            $this->authModelUsed = $result['data']['authModelUsed'];
            $this->flwRef = $result['data']['flwRef'];
            $this->txRef = $result['data']['txRef'];
        }
        //passes the result to the suggestedAuth function which re-initiates the charge 
        return $result;
     }

     /**
     * sends a post request to the virtual APi set by the user
     *  @param array
     *  @return object
     * */
     public function vcPostRequest($array){
        $this->post_data = $array;
        //post the data to the API
        $result  = $this->postURL($this->post_data);
        //decode the response 
        $result = json_decode($result, true);
        //return result
        print_r($result);
     }

    /**
         * Used to create sub account on the rave dashboard
         *  @param array
         *  @return object
         * */
     public function createSubaccount($array){
        $this->options = $array;
        $this->logger->notice('Creating Sub account...');
        //pass $this->options to the postURL function to call the api
        return $this->postURL($this->options);
     }

    /**
     * Handle canceled payments with this method
     * @param string $referenceNumber This should be the reference number of the transaction that was canceled
     * @return object
     * */
    public function paymentCanceled($referenceNumber){
        $this->txref = $referenceNumber;
        $this->logger->notice('Payment was canceled by user..'.$this->txref);
        if(isset($this->handler)){
            $this->handler->onCancel($this->txref);
        }
        return $this;
    }

/**
 * This is used to create virtual account for a merchant.
 */
    public function createVirtualAccount($array){
        $this->options = $array;
        $this->logger->notice('creating virtual account..'); 
        return $this->postURL($this->options);
    }

     /**
     * Create an Order with this method
     * @param string $array
     * @return object
     * */

    public function createOrder($array){
        $this->logger->notice('creating Ebill order for customer with email: '.$array['email']); 

        if(empty($array['narration'])){
            $array['narration'] = '';
        }else if(empty($array['IP'])){
            $array['IP'] = '10.30.205.3';

        }else if(!isset($array['custom_business_name']) || empty($array['custom_business_name'])){
            $array['custom_business_name'] = '';
        }

        $data = array(
            // 'SECKEY' => $array['SECKEY'],
            'SECKEY' => $this->secretKey,
            'narration' => $array['narration'],
            'numberofunits' => $array['numberofunits'],
            'currency' => $array['currency'],
            'amount' => $array['amount'],
            'phonenumber' => $array['phonenumber'],
            'email' => $array['email'],
            'txRef' => $array['txRef'],
            'IP' => $array['IP'],
            'country' => $array['country'],
            'custom_business_name' => $array['custom_business_name']
        );

        return $this->postURL($data);
    }

     /**
     * Update an Order with this method
     * @param string $array
     * @return object
     * */
    public function updateOrder($array){
        $this->logger->notice('updating Ebill order..');
        
        $data = array(
            'SECKEY' => $this->secretKey,
            'reference' => $array['flwRef'],
            'currency' => 'NGN',
            'amount' => $array['amount'],
        );

        return $this->postURL($data);
    }

     /**
     * pay bill or query bill information with this method
     * @param string $array
     * @return object
     * */
    public function bill($array){
        $this->logger->notice(' billing ...');

        $data = array();
        if($array["service"] == 'fly_buy'){
        $this->logger->notice('fly_buy bill...');
            $data["service_payload"]["Country"] = $array["service_payload"]["Country"];
            $data["service_payload"]["CustomerId"] = $array["service_payload"]["CustomerId"];
            $data["service_payload"]["Reference"] = $array["service_payload"]["Reference"];
            $data["service_payload"]["Amount"] = $array["service_payload"]["Amount"];
            $data["service_payload"]["IsAirtime"] = $array["service_payload"]["IsAirtime"];
            $data["service_payload"]["BillerName"] = $array["service_payload"]["BillerName"];
      
        } else if($array["service"] == 'fly_buy_bulk'){
            $this->logger->notice('fly_buy_bulk bill...');

            $data["service_payload"]["BatchReference"] = $array["service_payload"]["BatchReference"];
            $data["service_payload"]["CallBackUrl"] = $array["service_payload"]["CallBackUrl"];
            $data["service_payload"]["Requests"] = $array["service_payload"]["Requests"];//an array
        } else if($array["service"] == 'fly_history'){
            $this->logger->notice('fly_history bill...');
            $data["service_payload"]["FromDate"] = $array["service_payload"]["FromDate"];
            $data["service_payload"]["ToDate"] = $array["service_payload"]["ToDate"];
            $data["service_payload"]["PageSize"] = $array["service_payload"]["PageSize"];
            $data["service_payload"]["PageIndex"] = $array["service_payload"]["PageIndex"];
            $data["service_payload"]["Reference"] = $array["service_payload"]["Reference"];
        } else if($array["service"] == 'fly_recurring_cancel'){
            $this->logger->notice('fly_recurring cancel bill...');

            $data["service_payload"]["CustomerMobile"] = $array["service_payload"]["CustomerMobile"];
            $data["service_payload"]["RecurringPayment"] = $array["service_payload"]["RecurringPayment"];//Id of the recurring payment to be cancelled.
        } else if($array["service"] == 'fly_remita_create-order'){
            $this->logger->notice('fly_remita_create-order...');

            $data["service_payload"]["billercode"] = $array["service_payload"]["billercode"];
            $data["service_payload"]["productcode"] = $array["service_payload"]["productcode"];
            $data["service_payload"]["amount"] = $array["service_payload"]["amount"];
            $data["service_payload"]["transactionreference"] = $array["service_payload"]["transactionreference"];
            $data["service_payload"]["payer"] = $array["service_payload"]["payer"];
            $data["service_payload"]["fields"] = $array["service_payload"]["fields"];
        } else if($array["service"] == 'fly_remita_pay-order'){
            $this->logger->notice('fly_remita_pay-order...');

            $data["service_payload"]["orderreference"] = $array["service_payload"]["orderreference"];
            $data["service_payload"]["paymentreference"] = $array["service_payload"]["paymentreference"];
            $data["service_payload"]["amount"] = $array["service_payload"]["amount"];
        }

        $data["secret_key"] = $this->secretKey;
        $data["service"] = $array["service"];
        $data["service_method"] = $array["service_method"];
        $data["service_version"] = $array["service_version"];
        $data["service_channel"] = "rave";
    
        return $this->postUrl($data);
    }

    public function bulkCharges($data){
        $this->logger->notice('bulk charging...');
        if(isset($data['title'])){
        $url = constant("SECKEY_QUERY_PARAM").$this->secretKey."&&title=".$data['title'];

        }elseif(isset($data['batch_id'])){
            $url = constant("SECKEY_QUERY_PARAM").$this->secretKey."&batch_id=".$data['batch_id'];
        }else{
        $url = constant("SECKEY_QUERY_PARAM").$this->secretKey;

        }
        return $this->getURL($url);
     }

      /**
     * List of all transfers with this method
     * @param string $data
     * @return object
     * */

     public function listTransfers($data){
        $this->logger->notice('Fetching list of transfers...');
        if(isset($data['page'])){
        $url = constant("SECKEY_QUERY_PARAM").$this->secretKey."&page=".$data['page'];

        }else if(isset($data['page']) && isset($data['status'])){
            $url = constant("SECKEY_QUERY_PARAM").$this->secretKey."&page".$data['page']."&status".$data['status'];
        }else if(isset($data['status'])){
        $url = constant("SECKEY_QUERY_PARAM").$this->secretKey."&status=".$data['status'];

        }else{
            $url = constant("SECKEY_QUERY_PARAM").$this->secretKey;

        }
        return $this->getURL($url);
     }

      /**
     * Fetch a transfer and its details with this method
     * @param string $data
     * @return object
     * */

     public function fetchATransfer(){
        $this->logger->notice('Fetching a transfer and its details...');
        $url = constant("SECKEY_QUERY_PARAM").$this->secretKey;

        return $this->getURL($url);
     }

      /**
     * Check  a bulk transfer status with this method
     * @param string $data
     * @return object
     * */

     public function bulkTransferStatus($data){

        $this->logger->notice('Checking bulk transfer status...');
        $url = constant("SECKEY_QUERY_PARAM").$this->secretKey."&batch_id=".$data['batch_id'];
        return $this->getURL($url);
     }

     /**
     * This helps you retry a failed transfer attempt.
     * @param string $id
     * @return object
     * */
     public function retryTransfer($id){

        $data = [
            'id' => $id,
            'seckey' => $this->secretKey
        ];

        $this->logger->notice('Retrying Transfer with Id..'.$id.'...');

        return $this->postURL($data);
     }

    /**
     * Fetch transfer retries.
     * @return object
     * */
     public function fetchTransferRetries(){

        $this->logger->notice('fetching tranfer retries for the id ...');
        $url = '';
        return $this->getURL($url);
     }

    /**
    * wallet to wallet merchant transfer.
    * @param string $data
    * @return object
    * */

    public function merchantTransfer($data){

        $payload = [
            'merchant_id' => $data['merchant_id'],
            'amount' => $data['amount'],
            'seckey' => $this->secretKey,
            'currency' => $data['currency']
        ];

        $this->logger->notice('wallet to wallet transfer initiated to merchant id:'.$payload['merchant_id']);
        return $this->postURL($payload);

    }

      /**
     * Check applicable fees with this method
     * @param string $data
     * @return object
     * */

     public function applicableFees($data){

        $this->logger->notice('Fetching applicable fees...');
        $url = constant("SECKEY_QUERY_PARAM").$this->secretKey."&currency=".$data['currency']."&amount=".$data['amount'];
        return $this->getURL($url);
     }

      /**
     * Retrieve Transfer balance with this method
     * @param string $array
     * @return object
     * */

     public function getTransferBalance($array){
        $this->logger->notice('Fetching Transfer Balance...');
        if(empty($array['currency']) || !isset($array['currency'])){
            $array['currency'] = 'NGN';
        }
        $data = array(
            "seckey"=>$this->secretKey,
            "currency" => $array['currency']
        );
        return $this->postURL($data);
     } 

      /**
     * Verify an Account to Transfer to with this method
     * @param string $array
     * @return object
     * */

     public function verifyAccount($array){

        $this->logger->notice('Verifying transfer recipents account...');
        if(empty($array['currency']) && empty($array['country'])){
            $array['currency'] = '';
            $array['country'] = '';
        }

        $data = array(
            "recipientaccount"=> $array['recipientaccount'],
            "destbankcode"=> $array['destbankcode'],
            "PBFPubKey"=>$this->publicKey,
            "currency" => $array['currency'],
            "country" => $array['country']
            
        );
        return $this->postURL($data);
     }

      /**
     * Lists banks for Transfer with this method
     * @return object
     * */

     public function getBanksForTransfer(){
        $this->logger->notice('Fetching banks available for Transfer...');

          //get banks for transfer
        $url = "?public_key=".$this->publickey;

        return  $this->getURL($url);
     }

      /**
     * Captures funds this method
     * @param string $array
     * @return object
     * */

     public function captureFunds($array){
        $this->logger->notice('capturing funds for flwRef: '.$array['flwRef'].' ...');
        $data = array(
            "seckey"=> $this->secretkey,
            "flwRef"=> $array['flwRef'],
            "amount"=> $array['amount']
            
        );
        return $this->postURL($data);

     }

      /**
     * Refund or Void a fund with this method
     * @param string $array
     * @return object
     * */

     public function refundOrVoid($array){
        $this->logger->notice($array['action'].'ing a captured fund with the flwRef='.$array['flwRef']);

        $data = array(
            "ref"=> $array['flwRef'],
            "action"=> $array['action'],
            "SECKEY"=> $this->secretkey  
        );
        return $this->postURL($data);
     }    
}

// silencio es dorado
?>

