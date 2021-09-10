<?php

namespace Flutterwave;

class Ebill {
    public function __construct(){
        $this->eb = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }
    public function order($array){
        $this->eb->setEventHandler(new SampleEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("flwv3-pug/getpaidx/api/ebills/generateorder/");
        //returns the value of the result.
       return $this->eb->createOrder($array); 
    }

    public function updateOrder(){

       $this->eb->setEventHandler(new SampleEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("flwv3-pug/getpaidx/api/ebills/update/");
        //returns the value of the result.
       return $this->eb->updateOrder($array); 
    }
}
