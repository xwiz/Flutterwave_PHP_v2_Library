<?php 

namespace Flutterwave;

class Mpesa {
    public function __construct(){
        $this->payment = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }

    public function mpesa($array){

        $this->payment->setEventHandler(new MpesaEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("flwv3-pug/getpaidx/api/charge");
        //returns the value from the results
        return $this->payment->chargePayment($array);
    }

     /**you will need to verify the charge
         * After validation then verify the charge with the txRef
         * You can write out your function to execute when the verification is successful in the onSuccessful function
     ***/
    public function verifyTransaction($txRef){
        //verify the charge
        return $this->payment->verifyTransaction($txRef);//Uncomment this line if you need it
    }

}
