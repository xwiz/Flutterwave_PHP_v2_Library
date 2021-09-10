<?php
namespace Flutterwave;

use Flutterwave\Rave;
use Flutterwave\EventHandlerInterface;

class TokenizedCharge {
    protected $payment;

    public function __construct($rave = null){
        if (!$rave) {
            $this->payment = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
        } else {
            $this->payment = $rave;
        }
        if (!$this->payment->getEventHandler()) {
            $ev = new CardEventHandler();
            $this->payment->setEventHandler($ev);
        }
    }
    public function tokenCharge($array){
        //set the endpoint for the api call
        $this->payment->setEndPoint("flwv3-pug/getpaidx/api/tokenized/charge");
        //returns the value from the results
        //you can choose to store the returned value in a variable and validate within this function
        return $this->payment->postURL($array);          
    }

    public function updateEmailTiedToToken($data){
        //set the endpoint for the api call
        $this->payment->setEndPoint("v2/gpx/tokens/embed_token/update_customer");
        //returns the value from the results
        //you can choose to store the returned value in a variable and validate within this function
        return $this->payment->postURL($data);

    }

    public function bulkCharge($data){
            //https://api.ravepay.co/flwv3-pug/getpaidx/api/tokenized/charge_bulk
         //set the endpoint for the api call
         $this->payment->setEndPoint("flwv3-pug/getpaidx/api/tokenized/charge_bulk");

         $this->payment->bulkCharges($data);

    }

    public function bulkChargeStatus($data)
    {
        //https://api.ravepay.co/flwv3-pug/getpaidx/api/tokenized/charge_bulk
         //set the endpoint for the api call
         $this->payment->setEndPoint("flwv3-pug/getpaidx/api/tokenized/charge_bulk");

         $this->payment->bulkCharges($data);
    }
}
    
?>
