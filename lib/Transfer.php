<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php
define("CREATE_TRANSFER_ENDPOINT", "v2/gpx/transfers/create");
define("BULK_TRANSFER_ENDPOINT", "v2/gpx/transfers/create_bulk");
define("GET_TRANSFER_ENDPOINT", "v2/gpx/transfers");
define("TRANSFER_APPLICABLE_FEE", "v2/gpx/transfers/fee");
define("TRANSFER_BALANCE", "v2/gpx/balance");
define("TRANSFER_RETRY_ENDPOINT", "v2/gpx/transfers/retry");
define("WALLET_TO_WALLET_TRANSFER", "v2/gpx/transfers/wallet");

require_once('rave.php');
require_once('raveEventHandlerInterface.php');

use Flutterwave\Rave;
use Flutterwave\EventHandlerInterface;

class transferEventHandler implements EventHandlerInterface{
    /**
     * This is called only when a transaction is successful 
     * @param array
     * */
    function onSuccessful($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Comfirm that the transaction is successful
        // Confirm that the chargecode is 00 or 0
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction
        // Update the transaction to note that you have given value for the transaction
        // You can also redirect to your success page from here
    }
    
    /**
     * This is called only when a transaction failed
     * */
    function onFailure($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here
       
    }
    
    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    function onRequery($transactionReference){
        // Do something, anything!
    }
    
    /**
     * This is called a transaction requery returns with an error
     * */
    function onRequeryError($requeryResponse){
        // Do something, anything!
    }
    
    /**
     * This is called when a transaction is canceled by the user
     * */
    function onCancel($transactionReference){
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution
       
    }
    
    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    function onTimeout($transactionReference, $data){
        // Get the transaction from your DB using the transaction reference (txref)
        // Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        // Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects
      
    }
}

class Transfer {
    protected $transfer;
    function __construct(){
        $this->transfer = new Rave($_ENV['PUBLIC_KEY'], $_ENV['SECRET_KEY'], $_ENV['ENV']);
    }


    //initiating a single transfer
    function singleTransfer($array){
        //set the payment handler 
        $this->transfer->eventHandler(new transferEventHandler)
        //set the endpoint for the api call
        ->setEndPoint(constant("CREATE_TRANSFER_ENDPOINT"));
        //returns the value from the results
        return $this->renderResult($this->transfer->transferSingle($array));

    }

     //initiating a bulk transfer
    function bulkTransfer($array){
        //set the payment handler 
        $this->transfer->eventHandler(new transferEventHandler)
        //set the endpoint for the api call
        ->setEndPoint(constant("BULK_TRANSFER_ENDPOINT"));
        //returns the value from the results
        return $this->renderResult($this->transfer->transferBulk($array));
    }

    function listTransfers($array){

        //set the payment handler 
        $this->transfer->eventHandler(new transferEventHandler)
        //set the endpoint for the api call
        ->setEndPoint(constant("GET_TRANSFER_ENDPOINT"));

        return $this->renderResult($this->transfer->listTransfers($array));
        
    }

    function fetchATransfer($array){

        //set the payment handler 
        $this->transfer->eventHandler(new transferEventHandler)
        //set the endpoint for the api call
        ->setEndPoint(constant("GET_TRANSFER_ENDPOINT"));

        return $this->renderResult($this->transfer->fetchATransfer());
        
    }

    function bulkTransferStatus($array){

         //set the payment handler 
         $this->transfer->eventHandler(new transferEventHandler)
         //set the endpoint for the api call
         ->setEndPoint(constant("GET_TRANSFER_ENDPOINT"));

         return $this->renderResult($this->transfer->bulkTransferStatus($array));
    }

    function getApplicableFees($array){

         //set the payment handler 
         $this->transfer->eventHandler(new transferEventHandler)
         //set the endpoint for the api call
         ->setEndPoint(constant("TRANSFER_APPLICABLE_FEE"));

         return $this->renderResult($this->transfer->applicableFees($array));
         
    }

    function getTransferBalance($array){

        //set the payment handler 
        $this->transfer->eventHandler(new transferEventHandler)
        //set the endpoint for the api call
        ->setEndPoint(constant("TRANSFER_BALANCE"));

        if(!isset($array['currency'])){
            $array['currency'] = 'NGN';
        }

        return $this->renderResult($this->transfer->getTransferBalance($array));

    }

    function verifyAccount($array){

        //set the payment handler 
        $this->transfer->eventHandler(new transferEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("flwv3-pug/getpaidx/api/resolve_account");

        return $this->renderResult($this->transfer->verifyAccount($array));
        
    }

    function getBanksForTransfer($data = array("country" => 'NG')){
        
           //set the payment handler 
           $this->transfer->eventHandler(new transferEventHandler)
           //set the endpoint for the api call

           ->setEndPoint("v2/banks/".$data['country']."/");
        

        return $this->renderResult($this->transfer->getBanksForTransfer());
    }

    function transferRetry($id){
        $this->transfer->eventHandler(new transferEventHandler)
        ->setEndPoint(constant("TRANSFER_RETRY_ENDPOINT"));

        return $this->renderResult($this->transfer->retryTransfer($id));
        
    }

    function fetchTransferRetries($id){

                //set the payment handler 
                $this->transfer->eventHandler(new transferEventHandler)
                //set the endpoint for the api call
                ->setEndPoint("v2/gpx/transfers/".$id."/retries");
        
                return $this->renderResult($this->transfer->fetchTransferRetries());
    }


    function walletToWalletTransfer($data){

        //set the payment handler 
        $this->transfer->eventHandler(new transferEventHandler)
        //set the endpoint for the api call
        ->setEndPoint(constant("WALLET_TO_WALLET_TRANSFER"));
    
         function checkPayload($data){
             $error = '';
            if(array_key_exists('amount', $data) || empty($data['amount'])){
                if(array_key_exists('currency', $data) || empty($data['currency'])){
                    if(array_key_exists('merchant_id', $data) || empty($data['merchant_id'])){

                        return $this->renderResult($this->transfer->merchantTransfer($data));
                        
                    }else{
                        $error .=  'Please add a "merchant_id" to the payload <br />';
                    }
                }else{
                    $error .= 'Please add currency code to the payload <br/>';
                }
            }else{
                $error .= 'Please add an amount value to the payload';
            }

            return $error;
         } 


         checkPayload($data);
       

        
    }

    function verifyTransaction($txRef){
        //verify the charge
        return $this->renderResult($this->transfer->verifyTransaction($txRef, $_ENV['SECRET_KEY']));  
    }

    function renderResult($result){
        
        $result = json_decode($result, TRUE);
        if($result['status'] == 'error'){

            return $result['message'];
        }
        return $result;
    }





}

?>