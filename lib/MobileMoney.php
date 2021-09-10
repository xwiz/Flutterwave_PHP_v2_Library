<?php

namespace Flutterwave;

class MobileMoney {
    protected $payment;
    public function __construct(){
        $this->payment = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }

    public function mobilemoney($array){

        $this->payment->setEventHandler(new CardEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("flwv3-pug/getpaidx/api/charge");
        //returns the value from the results
        $result = $this->payment->chargePayment($array);
        if($result['data']['authModelUsed']){
                $this->payment->setAuthModel($result["data"]["authModelUsed"]);
                return $result;
        }else if($result['message'] === 'Momo initiated'){
            header('Location:'.$result['data']['link']);
            }else{
            return json_decode(array(
                "error"=>"There was an error in charging this number"
            ),true);
            }
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

?>
