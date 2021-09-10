<?php
namespace Flutterwave;

class Transactions{
    public function __construct(){
        $this->history = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
    }
    public function viewTransactions($array){

        $this->history->setEventHandler(new SampleEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v2/gpx/transactions/query");
        //returns the value from the results
        return $this->history->getAllTransactions($array);
    }

}
