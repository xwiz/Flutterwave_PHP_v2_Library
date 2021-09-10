<?php
namespace Flutterwave;

class Transfer {
    protected $transfer;
    protected $ev;

    public function __construct($eventHandler = null){
        define("CREATE_TRANSFER_ENDPOINT", "v2/gpx/transfers/create");
        define("BULK_TRANSFER_ENDPOINT", "v2/gpx/transfers/create_bulk");
        define("GET_TRANSFER_ENDPOINT", "v2/gpx/transfers");
        define("TRANSFER_APPLICABLE_FEE", "v2/gpx/transfers/fee");
        define("TRANSFER_BALANCE", "v2/gpx/balance");
        define("TRANSFER_RETRY_ENDPOINT", "v2/gpx/transfers/retry");
        define("WALLET_TO_WALLET_TRANSFER", "v2/gpx/transfers/wallet");

        $this->transfer = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
        if (!$eventHandler) {
            $this->ev = new SampleEventHandler;
        }
        $this->transfer->setEventHandler($this->ev);
    }


    //initiating a single transfer
    public function singleTransfer($array){
        //set the endpoint for the api call
        $this->transfer->setEndPoint(constant("CREATE_TRANSFER_ENDPOINT"));
        //returns the value from the results
        return $this->renderResult($this->transfer->transferSingle($array));
    }

     //initiating a bulk transfer
    public function bulkTransfer($array){
        //set the endpoint for the api call
        $this->transfer->setEndPoint(constant("BULK_TRANSFER_ENDPOINT"));
        //returns the value from the results
        return $this->renderResult($this->transfer->transferBulk($array));
    }

    public function listTransfers($array){

        //set the endpoint for the api call
        $this->transfer->setEndPoint(constant("GET_TRANSFER_ENDPOINT"));

        return $this->renderResult($this->transfer->listTransfers($array));
        
    }

    public function fetchATransfer($array){
        //set the endpoint for the api call
        $this->transfer->setEndPoint(constant("GET_TRANSFER_ENDPOINT"));
        
        return $this->renderResult($this->transfer->fetchATransfer());
        
    }
    
    public function bulkTransferStatus($array){        
        //set the endpoint for the api call
         $this->transfer->setEndPoint(constant("GET_TRANSFER_ENDPOINT"));

         return $this->renderResult($this->transfer->bulkTransferStatus($array));
    }

    public function getApplicableFees($array){
        //set the endpoint for the api call
         $this->transfer->setEndPoint(constant("TRANSFER_APPLICABLE_FEE"));

         return $this->renderResult($this->transfer->applicableFees($array));
         
    }

    public function getTransferBalance($array){
        //set the endpoint for the api call
        $this->transfer->setEndPoint(constant("TRANSFER_BALANCE"));

        if(!isset($array['currency'])){
            $array['currency'] = 'NGN';
        }

        return $this->renderResult($this->transfer->getTransferBalance($array));

    }

    public function verifyAccount($array){
        //set the endpoint for the api call
        $this->transfer->setEndPoint("flwv3-pug/getpaidx/api/resolve_account");

        return $this->renderResult($this->transfer->verifyAccount($array));
    }

    public function getBanksForTransfer($data = array("country" => 'NG')){
        
        //set the endpoint for the api call
        $this->transfer->setEndPoint("v2/banks/".$data['country']."/");

        return $this->renderResult($this->transfer->getBanksForTransfer());
    }

    public function transferRetry($id){
        //set the endpoint for the api call
        $this->transfer->setEndPoint(constant("TRANSFER_RETRY_ENDPOINT"));

        return $this->renderResult($this->transfer->retryTransfer($id));
        
    }

    public function fetchTransferRetries($id){
        //set the endpoint for the api call
        $this->transfer->setEndPoint("v2/gpx/transfers/".$id."/retries");

        return $this->renderResult($this->transfer->fetchTransferRetries());
    }


    public function walletToWalletTransfer($data){
        //set the endpoint for the api call
        $this->transfer->setEndPoint(constant("WALLET_TO_WALLET_TRANSFER"));
    
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

    public function verifyTransaction($txRef){
        //verify the charge
        return $this->renderResult($this->transfer->verifyTransaction($txRef, fl_get_config('secret_key')));  
    }

    public function renderResult($result){
        
        $result = json_decode($result, TRUE);
        if($result['status'] == 'error'){

            return $result['message'];
        }
        return $result;
    }

}

?>