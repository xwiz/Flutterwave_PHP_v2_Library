<?php
namespace Flutterwave;

class Subscription{
    protected $subscription;
    public function __construct(){
        $this->subscription = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }

    public function activateSubscription($id){

        $endPoint = 'v2/gpx/subscriptions/'.$id.'/activate';
        $this->subscription->setEventHandler(new SampleEventHandler)
        //set the endpoint for the api call
        ->setEndPoint($endPoint);
        //returns the value from the results
        return $this->subscription->activateSubscription();
    }

    public function getAllSubscription(){
    
            $this->subscription->setEventHandler(new SampleEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v2/gpx/subscriptions/query");
            //returns the value from the results
            return $this->subscription->getAllSubscription();
        }

    public function fetchASubscription($data){
    
            $this->subscription->setEventHandler(new SampleEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v2/gpx/subscriptions/query");
            //returns the value from the results
            return $this->subscription->fetchASubscription($data);
        }
    }
?>