<?php

namespace Flutterwave;

class Settlement {
    public function __construct(){
        $this->settle = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
        //set the event handler
        $this->settle->setEventHandler(new SampleEventHandler);
    }

    public function fetchSettlement($array){
        //set the endpoint for the api call
        $this->settle->setEndPoint("v2/merchant/settlements/".$array['id']);
        //returns the value from the results
        return $this->settle->fetchASettlement();
    }

    public function listAllSettlements(){
        //set the endpoint for the api call
        $this->settle->setEndPoint("v2/merchant/settlements");
        //returns the value from the results
        return $this->settle->getAllSettlements();
    }

}