<?php
namespace Flutterwave;

class Recipient {
    protected $recipient;
    public function __construct(){
        $this->recipient = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }

    public function recipient($array){

        $this->recipient->setEventHandler(new SampleEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v2/gpx/transfers/beneficiaries/create");
        //returns the value from the results
        return $this->recipient->beneficiary($array);
    }
}
?>