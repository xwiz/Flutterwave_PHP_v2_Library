<?php
namespace Flutterwave;


require_once('Rave.php');

use Flutterwave\Rave;

class VirtualCard {
    protected $vc;
    //initialise the constructor
    public function __construct(){
        $this->vc = new Rave(fl_get_config('public_key'), fl_get_config('secret_key'), fl_get_config('environment'));
        $this->vc->setEventHandler(new SampleEventHandler);
    }

    //create card function
    public function create($array){
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/new");
            return $this->vc->vcPostRequest($array);
        }
    //get the detials of a card using the card id
    public function get($array){
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/get");
            return $this->vc->vcPostRequest($array);
        }
    //list all the virtual cards on your profile
    public function list($array){
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/search");
            return $this->vc->vcPostRequest($array);
        }
    //terminate a virtual card on your profile
    public function terminate($array){
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/".$array['id']."/terminate");
            return $this->vc->vcPostRequest($array);
        }
    //fund a virtual card
    public function fund($array){
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/fund");
            return $this->vc->vcPostRequest($array);
        }
   // list card transactions
    public function transactions($array){
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/transactions");
            return $this->vc->vcPostRequest($array);
        }
    //withdraw funds from card
    public function withdraw($array){
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/withdraw");
            return $this->vc->vcPostRequest($array);
        }
        
    }
?>