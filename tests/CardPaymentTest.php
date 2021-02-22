<?php
require("lib/CardPayment.php");
use PHPUnit\Framework\TestCase;
use Flutterwave\Card;


class CardPaymentTest extends TestCase
{
    function test_authmodel_with_mastercard(){
        $card_details = [
            "PBFPubKey" => "FLWPUBK-4e581ebf8372cd691203b27227e2e3b8-X",
            "cardno" => "5531886652142950",
            "cvv" => "564",
            "expirymonth" => "09",
            "expiryyear" => "22",
            "currency" => "NGN",
            "country" => "NG",
            "amount" => "10",
            "email" => "user@gmail.com",
            "phonenumber" => "0902620185",
            "firstname" => "temi",
            "lastname" => "desola",
            "IP" => "355426087298442",
            "redirect_url" => "https://rave-webhook.herokuapp.com/receivepayment",
            "device_fingerprint" => "69e6b7f0b72037aa8428b70fbe03986c"
        ];

        $payment = new Card();

        $result = $payment->cardCharge($card_details);
        $this->assertEquals( 'PIN',$result['data']['suggested_auth']);

    }

    function test_card_payment_successful_with_mastercard(){
        $card_details = [
            "PBFPubKey" => "FLWPUBK-4e581ebf8372cd691203b27227e2e3b8-X",
            "cardno" => "5531886652142950",
            "cvv" => "564",
            "expirymonth" => "09",
            "expiryyear" => "22",
            "currency" => "NGN",
            "pin" => "3310",
            "country" => "NG",
            "amount" => "10",
            "email" => "user@gmail.com",
            "suggested_auth" => "PIN",
            "phonenumber" => "0902620185",
            "firstname" => "temi",
            "lastname" => "desola",
            "IP" => "355426087298442",
            "redirect_url" => "https://rave-webhook.herokuapp.com/receivepayment",
            "device_fingerprint" => "69e6b7f0b72037aa8428b70fbe03986c"
        ];
        
        $payment = new Card();
        $result = $payment->cardCharge($card_details);
        print_r($result);
        $this->assertEquals( '00',$result['data']['tx']['chargeResponseCode']);
    }
}

?>
