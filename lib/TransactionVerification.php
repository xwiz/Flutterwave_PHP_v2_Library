<?php
namespace Flutterwave;

class TransactionVerification {
    protected $validate;

    public function __construct(){
        $this->validate = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }
    public function transactionVerify($txref){
    
            $this->validate->setEventHandler(new SampleEventHandler);
            //returns the value from the results
            return $this->validate->verifyTransaction($txref);
        }
    }
    
?>