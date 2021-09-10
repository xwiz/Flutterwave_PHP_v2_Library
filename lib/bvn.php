<?php
namespace Flutterwave;

class Bvn {
    protected $bvn;
    public function __construct(){
        $this->bvn = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }

    public function verifyBVN($bvn){

        $this->bvn->setEventHandler(new SampleEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v2/kyc/bvn");
        //returns the value from the results
        return $this->bvn->bvn($bvn);
    }
}

?>