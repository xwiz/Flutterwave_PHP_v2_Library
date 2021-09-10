<?php

namespace Flutterwave;

class Bill {
    protected $payment;

    public function __construct(){
        $this->payment = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }
   
    public function payBill($array){

        $this->payment->setEventHandler(new BillEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v2/services/confluence");
    
        return $this->payment->bill($array);
    }  
}
