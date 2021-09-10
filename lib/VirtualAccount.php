<?php

namespace Flutterwave;

class VirtualAccount{

    public function __construct(){
        $this->va = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }

    /**
     * Creating the VirtualAccount
     */
    public function virtualAccount($userdata){

        $this->va->setEventHandler(new SampleEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v2/banktransfers/accountnumbers");
        
        //returns the value of the result.
       return $this->va->createVirtualAccount($userdata);
    }
   
}
