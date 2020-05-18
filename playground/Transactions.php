<?php 
include('partials/header.php');//this is just to load the bootstrap and css. 

require("../library/Transactions.php");
use Flutterwave\Transactions;
//The data variable holds the payload
$data = array(
    
);

$history = new Transactions();
$transactions = $history->viewTransactions($data);
$transactionfee = $history->getTransactionFee();
$verifyTransaction = $history->verifyTransaction($fetch_data);
$timeline = $history->viewTimeline($update_data);

echo '<div class="alert alert-success role="alert">
        <h1>Subaccount Creation Result: </h1>
        <p><b> '.print_r($transactions, true).'</b></p>
      </div>';

echo '<div class="alert alert-primary role="alert">
        <h1>[Get Subaccounts] Result: </h1>
        <p><b> '.print_r($transactionfee, true).'</b></p>
      </div>';

echo '<div class="alert alert-primary role="alert">
      <h1>[Get Subaccounts] Result: </h1>
      <p><b> '.print_r($verifyTransaction, true).'</b></p>
    </div>';

echo '<div class="alert alert-primary role="alert">
    <h1>[Get Subaccounts] Result: </h1>
    <p><b> '.print_r($timeline, true).'</b></p>
  </div>';



include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


