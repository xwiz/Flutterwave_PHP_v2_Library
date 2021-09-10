<?php
namespace Flutterwave;

class Refund {
    protected $refund;
    public function __construct(){
        $this->refund = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }
    public function refund($array){
    
            $this->refund->setEventHandler(new SampleEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("gpx/merchant/transactions/refund");
            //returns the value from the results
            return $this->refund->refund($array);
        }
    }
?>