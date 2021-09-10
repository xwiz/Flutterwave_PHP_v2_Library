<?php
namespace Flutterwave;

class PaymentPlan{
    protected $plan;
    public function __construct(){
        $this->plan = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }
    public function createPlan($array){
    
            $this->plan->setEventHandler(new SampleEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v2/gpx/paymentplans/create");
            //returns the value from the results
            return $this->plan->createPlan($array);
        }
    }
?>