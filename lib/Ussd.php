<?php
namespace Flutterwave;

class Ussd {
    protected $ussd;
    public function __construct(){
        $ussd = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }

    public function ussd($array){

        $this->ussd->setEventHandler(new SampleEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("flwv3-pug/getpaidx/api/v2/hosted/pay");
        //returns the value from the results
        return $this->ussd->pay($array);
    }
}
?>