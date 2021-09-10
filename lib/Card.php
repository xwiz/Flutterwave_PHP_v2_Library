<?php
namespace Flutterwave;

class Card {
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

    public function cardCharge($array){

        $final_result = [];

        //set the endpoint for the api call
        $this->payment->setEndPoint("flwv3-pug/getpaidx/api/charge");
        //returns the value from the results
        //$result = $this->payment->chargePayment($array);
        $result = $this->payment->chargePayment($array);
            //check the value of the returned data for the suggested_auth response
            if(isset($result["data"]["suggested_auth"])){
                if($result["data"]["suggested_auth"] === "PIN"){
                    //validates the pin on the request data
                    $this->payment->setAuthModel("PIN");
                    $final_result =  $this->payment->chargePayment($array);
                }
                if($result["data"]["suggested_auth"] === "NOAUTH_INTERNATIONAL"){

                $this->payment->setAuthModel("NOAUTH_INTERNATIONAL");
                $final_result =  $this->payment->chargePayment($array);
    
                    //TODO: Update $this->options with the billing addres details
                    //$this->chargePayment($this->options) //uncomment this function when charging international cards
                }
                if($result["data"]["suggested_auth"] === "AVS_VBVSECURECODE"){

                $this->payment->setAuthModel("AVS_VBVSECURECODE");
                $final_result =  $this->payment->chargePayment($array);
    
                    //TODO: Update $this->options with the billing addres details
                    //$this->chargePayment($this->options) //uncomment this function when charging international cards
                }
                
            }else{
                // $array["suggested_auth"] = "NOAUTH_INTERNATIONAL";
            //   $this->payment->setAuthModel($result["data"]["authModelUsed"]);
                $final_result = $result;
            }
        return $final_result;
    }

        /**you will need to validate and verify the charge
         * Validating the charge will require an otp
         * After validation then verify the charge with the txRef
         * You can write out your function to execute when the verification is successful in the onSuccessful function
     ***/

    public function validateTransaction($otp, $Ref){
            //validate the charge
        return $this->payment->validateTransaction($otp, $Ref);//Uncomment this line if you need it
    }
    public function verifyTransaction($txRef){
        //verify the charge
        return $this->payment->verifyTransaction($txRef, fl_get_config('secret_key'));//Uncomment this line if you need it
    }
    

}

?>
