<?php
namespace Flutterwave;

class Account {
    protected $payment;

    public function __construct(){
        $this->payment = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }

    public function accountCharge($array){

        $this->payment->setEventHandler(new AccountEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("flwv3-pug/getpaidx/api/charge");
        //returns the value from the results
        //you can choose to store the returned value in a variable and validate within this function
        $this->payment->setAuthModel("AUTH");
        return $this->payment->chargePayment($array);
        /**you will need to validate and verify the charge
         * Validating the charge will require an otp
         * After validation then verify the charge with the txRef
         * You can write out your function to execute when the verification is successful in the onSuccessful function
         ***/
    }

    public function validateTransaction($otp,$authModel,$Ref){
            //validate the charge
        $this->payment->setEventHandler(new AccountEventHandler);

        if($authModel == 'PIN'){
        return $this->payment->validateTransaction($otp, $Ref);//Uncomment this line if you need it
        }
        return $this->payment->validateTransaction2($otp, $Ref);//Uncomment this line if you need it
    }
       
    public function verifyTransaction($txRef){
           //verify the charge
        $this->payment->setEventHandler(new AcountEventHandler);
        return $this->payment->verifyTransaction($txRef, fl_get_config('secret_key'));//Uncomment this line if you need it
    }
}
?>