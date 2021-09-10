<?php
namespace Flutterwave;

class Subaccount {
    protected $subaccount;
    public function __construct(){
        $this->subaccount = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }
    public function subaccount($array){

        $this->subaccount->setEventHandler(new SampleEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v2/gpx/subaccounts/create");
        //returns the value from the results
        return $this->subaccount->createSubaccount($array);
    }
}

?>