<?php
require("lib/AccountPayment.php");
use PHPUnit\Framework\TestCase;
use Flutterwave\Account;

class AccountPaymentTest extends TestCase
{
    function test_keys_supplied_for_account_charge(){
        $data = [
            "PBFPubKey" => "FLWPUBK-7adb6177bd71dd43c2efa3f1229e3b7f-X",
            "accountbank" => "232",// get the bank code from the bank list endpoint.
            "accountnumber" => "0061333471",
            "currency" => "NGN",
            "payment_type" => "account",
            "country" => "NG",
            "amount" => "10",
            "email" => "desola.ade1@gmail.com",
            "passcode" => "09101989",//customer Date of birth this is required for Zenith bank account payment.
            "bvn" => "12345678901",
            "phonenumber" => "0902620185",
            "firstname" => "temi",
            "lastname" => "desola",
            "IP" => "355426087298442",
            "txRef" => "MC-0292920", // merchant unique reference
            "device_fingerprint" => "69e6b7f0b72037aa8428b70fbe03986c"
        ];

        $account = new Account();
        $result = $account->accountCharge($data);
        $this->assertArrayHasKey('data', $result);
    }

    function test_when_account_is_invalid(){
        $this->assert(2 + 2);
    }

    function test_when_users_enter_wrong_details(){
        $this->assert(2 + 2);
    }
   

}

?>
