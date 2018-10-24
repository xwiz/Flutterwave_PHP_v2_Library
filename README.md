# Rave PHP SDK :wink:

> Class documentation can be found here [https://flutterwave.github.io/Flutterwave-Rave-PHP-SDK/packages/Default.html](https://flutterwave.github.io/Flutterwave-Rave-PHP-SDK/packages/Default.html)

Use this library to integrate your PHP app to Rave.

Edit the `paymentForm.php` and `processPayment.php` files to suit your purpose. Both files are well documented.

Simply redirect to the `paymentForm.php` file on your browser to process a payment.

The vendor folder is committed into the project to allow easy installation for those who do not have composer installed.
It is recommended to update the project dependencies using;

```shell
$ composer install
```

## Sample implementation

In this implementation, we are expecting a form encoded POST request to this script.
The request will contain the following parameters.

- payment_method `Can be card, account, both`
- description `Your transaction description`
- logo `Your logo url`
- title `Your transaction title`
- country `Your transaction country`
- currency `Your transaction currency`
- email `Your customer's email`
- firstname `Your customer's firstname`
- lastname `Your customer's lastname`
- phonenumber `Your customer's phonenumber`
- pay_button_text `The payment button text you prefer`
- ref `Your transaction reference. It must be unique per transaction.  By default, the Rave class generates a unique transaction reference for each transaction. Pass this parameter only if you uncommented the related section in the script below.`

```php
// Prevent direct access to this class
define("BASEPATH", 1);

include('lib/rave.php');
include('lib/raveEventHandlerInterface.php');

use Flutterwave\Rave;
use Flutterwave\Rave\EventHandlerInterface;

$URL = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
$getData = $_GET;
$postData = $_POST;
$publicKey = '****YOUR**PUBLIC**KEY****'; // Remember to change this to your live public keys when going live
$secretKey = '****YOUR**SECRET**KEY****'; // Remember to change this to your live secret keys when going live
$env = 'staging'; // Remember to change this to 'live' when you are going live
$prefix = 'MY_APP_NAME'; // Change this to the name of your business or app
$overrideRef = false;

// Uncomment here to enforce the useage of your own ref else a ref will be generated for you automatically
// if($postData['ref']){
//     $prefix = $postData['ref'];
//     $overrideRef = true;
// }

$payment = new Rave($publicKey, $secretKey, $prefix, $env, $overrideRef);


// This is where you set how you want to handle the transaction at different stages
class myEventHandler implements EventHandlerInterface{
    /**
     * This is called when the Rave class is initialized
     * */
    function onInit($initializationData){
        // Save the transaction to your DB.
        echo 'Payment started......'.json_encode($initializationData).'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called only when a transaction is successful
     * */
    function onSuccessful($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Comfirm that the transaction is successful
        // Confirm that the chargecode is 00 or 0
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction
        // Update the transaction to note that you have given value for the transaction
        // You can also redirect to your success page from here
        echo 'Payment Successful!'.json_encode($transactionData).'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called only when a transaction failed
     * */
    function onFailure($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here
        echo 'Payment Failed!'.json_encode($transactionData).'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    function onRequery($transactionReference){
        // Do something, anything!
        echo 'Payment requeried......'.$transactionReference.'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called a transaction requery returns with an error
     * */
    function onRequeryError($requeryResponse){
        // Do something, anything!
        echo 'An error occured while requeying the transaction...'.json_encode($requeryResponse).'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called when a transaction is canceled by the user
     * */
    function onCancel($transactionReference){
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution
        echo 'Payment canceled by user......'.$transactionReference.'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    function onTimeout($transactionReference, $data){
        // Get the transaction from your DB using the transaction reference (txref)
        // Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        // Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects
        echo 'Payment timeout......'.$transactionReference.' - '.json_encode($data).'<br />'; //Remember to delete this line
    }
}

if($postData['amount']){
    // Make payment
    $payment
    ->eventHandler(new myEventHandler)
    ->setAmount($postData['amount'])
    ->setPaymentMethod($postData['payment_method']) // value can be card, account or both
    ->setDescription($postData['description'])
    ->setLogo($postData['logo'])
    ->setTitle($postData['title'])
    ->setCountry($postData['country'])
    ->setCurrency($postData['currency'])
    ->setEmail($postData['email'])
    ->setFirstname($postData['firstname'])
    ->setLastname($postData['lastname'])
    ->setPhoneNumber($postData['phonenumber'])
    ->setPayButtonText($postData['pay_button_text'])
    ->setRedirectUrl($URL)
    // ->setMetaData(array('metaname' => 'SomeDataName', 'metavalue' => 'SomeValue')) // can be called multiple times. Uncomment this to add meta datas
    // ->setMetaData(array('metaname' => 'SomeOtherDataName', 'metavalue' => 'SomeOtherValue')) // can be called multiple times. Uncomment this to add meta datas
    ->initialize();
}else{
    if($getData['cancelled'] && $getData['txref']){
        // Handle canceled payments
        $payment
        ->eventHandler(new myEventHandler)
        ->requeryTransaction($getData['txref'])
        ->paymentCanceled($getData['txref']);
    }elseif($getData['txref']){
        // Handle completed payments
        $payment->logger->notice('Payment completed. Now requerying payment.');
        
        $payment
        ->eventHandler(new myEventHandler)
        ->requeryTransaction($getData['txref']);
    }else{
        $payment->logger->warn('Stop!!! Please pass the txref parameter!');
        echo 'Stop!!! Please pass the txref parameter!';
    }
}
```

# Support Direct Charges

## Account Charge Sample implementation

The following implementation shows how to initiate a direct bank charge
```php
require("Flutterwave-Rave-PHP-SDK/lib/AccountPayment.php");
use Flutterwave\Account;

    $array = array(
        "PBFPubKey" =>"****YOUR**PUBLIC**KEY****",
        "accountbank"=> "044",// get the bank code from the bank list endpoint.
        "accountnumber" => "0690000031",
        "currency" => "NGN",
        "payment_type" => "account",
        "country" => "NG",
        "amount" => "10",
        "email" => "eze@gmail.com",
       // passcode => "09101989",//customer Date of birth this is required for Zenith bank account payment.
        "bvn" => "12345678901",
        "phonenumber" => "0902620185",
        "firstname" => "temi",
        "lastname" => "desola",
        "IP" => "355426087298442",
        "txRef" => "MC-".time(), // merchant unique reference
        "device_fingerprint" => "69e6b7f0b72037aa8428b70fbe03986c"

    );
$account = new Account("****YOUR**PUBLIC**KEY****","****YOUR**SECRET**KEY****","staging");
$result = $account->accountCharge($array);
print_r($result);
```
## Card Charge Sample implementation

The following implementation shows how to initiate a direct card charge
```php
require("Flutterwave-Rave-PHP-SDK/lib/CardPayment.php");
use Flutterwave\Card;
    $array = array(
        "PBFPubKey" => "****YOUR**PUBLIC**KEY****",
        "cardno" =>"5438898014560229",
        "cvv" => "890",
        "expirymonth"=> "09",
        "expiryyear"=> "19",
        "currency"=> "NGN",
        "country"=> "NG",
        "amount"=> "2000",
        "pin"=>"3310",
         "payment_plan"=> "980",  
        "email"=> "eze@gmail.com",
        "phonenumber"=> "0902620185",
        "firstname"=> "temi",
        "lastname"=> "desola",
        "IP"=> "355426087298442",
        "txRef"=>"MC-".time(),// your unique merchant reference
        "meta"=>["metaname"=> "flightID", "metavalue"=>"123949494DC"],
        "redirect_url"=>"https://rave-webhook.herokuapp.com/receivepayment",
        "device_fingerprint"=> "69e6b7f0b72037aa8428b70fbe03986c"
    );
$card = new Card("****YOUR**PUBLIC**KEY****","****YOUR**SECRET**KEY****","staging");
$result = $card->cardCharge($array);
print_r($result);
```
## BVN Verification Sample implementation

The following implementation shows how to verify a Bank Verification Number
```php
require("Flutterwave-Rave-PHP-SDK/lib/Bvn.php");
use Flutterwave\Bvn;
$bvn = new Bvn("****YOUR**PUBLIC**KEY****","****YOUR**SECRET**KEY****","staging");
$result = $bvn->verifyBVN("123456789");
print_r($result);
```

## Create a Payment Plan Sample implementation

The following implementation shows how to create a payment plan on the rave dashboard
```php
require("Flutterwave-Rave-PHP-SDK/lib/PaymentPlan.php");
use Flutterwave\PaymentPlan;

$array = array(
    "amount" => "2000",
     "name"=> "The Premium Plan",
     "interval"=> "monthly",
     "duration"=> "12",
     "seckey" => "****YOUR**SECRET**KEY****"
);

$plan = new PaymentPlan("****YOUR**PUBLIC**KEY****","****YOUR**SECRET**KEY****","staging");
$result = $plan->createPlan($array);
print_r($result);
```

## Create a Subaccount Sample implementation

The following implementation shows how to create a subaccount on the rave dashboard
```php
require("Flutterwave-Rave-PHP-SDK/lib/Subaccount.php");
use Flutterwave\Subaccount;

$array = array(
        "account_bank"=>"044",
        "account_number"=> "0690000030",
        "business_name"=> "JK Services",
        "business_email"=> "jke@services.com",
        "business_contact"=> "Seun Alade",
        "business_contact_mobile"=> "090890382",
        "business_mobile"=> "09087930450",
        "meta" => ["metaname"=> "MarketplaceID", "metavalue"=>"ggs-920900"],
        "seckey"=> "****YOUR**SECRET**KEY****"
);

$subaccount = new Subaccount("****YOUR**PUBLIC**KEY****","****YOUR**SECRET**KEY****","staging");
$result = $subaccount->subaccount($array);
print_r($result);
```
## Create Transfer Recipient Sample implementation

The following implementation shows how to create a transfer recipient on the rave dashboard
```php
require("Flutterwave-Rave-PHP-SDK/lib/Recipient.php");
use Flutterwave\Recipient;

$array = array(
    "account_number"=>"0690000030",
	"account_bank"=>"044",
	"seckey"=>"****YOUR**SECRET**KEY****"
);

$recipient = new Recipient("****YOUR**PUBLIC**KEY****","****YOUR**SECRET**KEY****","staging");
$result = $recipient->recipient($array);
print_r($result);
```

## Create Refund Sample implementation

The following implementation shows how to initiate a refund
```php
require("Flutterwave-Rave-PHP-SDK/lib/Refund.php");
use Flutterwave\Refund;

$array = array(
    "ref"=>"txRef",//pass a transaction reference to initiate refund
	"seckey"=>"****YOUR**SECRET**KEY****"
);

$refund = new Refund("****YOUR**PUBLIC**KEY****","****YOUR**SECRET**KEY****","staging");
$result = $refund->refund($array);
print_r($result);
```

## Subscriptions Sample implementation

The following implementation shows how to activata a subscription, fetch a subscription, get all subscription
```php
require("Flutterwave-Rave-PHP-SDK/lib/Subscription.php");
use Flutterwave\Subscription;

$email = "eze@gmail.com";//email address of subscriber
$id = 1112 //Id of subscription plan

$subscription = new Subscription("****YOUR**PUBLIC**KEY****","****YOUR**SECRET**KEY****","staging");

$resultFetch = $subscription->fetchASubscription($email);
$resultGet = $subscription->getAllSubscription();
$resultActivate = $subscription->activateSubscription($id);
print_r($result);
```
You can also find the class documentation in the docs folder. There you will find documentation for the `Rave` class and the `EventHandlerInterface`.

Enjoy... :v:

## ToDo

- Write Unit Test
- Support Tokenized payment
