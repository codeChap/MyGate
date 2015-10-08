MyGate 1Click Enterprise Package
================================

## NB! Package is in development stage and not usable!

MyGate’s Tokenization – [Enterprise solution](https://developer.mygateglobal.com/integration-guides/1click-enterprise-page) eliminates merchant requirements to store card data. The solution utilizes a merchants hosted payment page. By replacing card detail with a unique token, merchants can reduce the risk of storing card data. Using the token, merchants can use a payment page that only requires the card holder to enter their CVV number.

## Disclaimer

Use this library at your own risk, I take no responsibility what so ever for the use of it!

## Money makes the world go round

Make sure you talk to myGate about the 1 Click enterprise option, there are setup costs that you or your client may want to consider before intergrating the 1 Click Enterprise option. 

## Usage

Optain your merchant_id, app_name and app_id from myGate. You need to be registered as a developer or as a merchant.

Use [composer](http://getcomposer.org) to install it within your framework using your composer.json file:

```
    "require": {
        "codechap/mygate": "dev-master"
    }
```

or simply incldue the required files:

```
    require("mygate/src/Mygate/connection.php");
    require("mygate/src/Mygate/card.php");
```


You can use the following test credit cards to test your implementation

```
    $testCards = array(

        // Visa Successful
        array(
            'cardholder' => 'Joe Soap',
            'cardnumber' => '4111111111111111',
            'expirymonth' => '10',
            'expiryyear' => '2015',
            'cvv' => '123'
        ),
        
        // Visa Declined
        array(
            'cardholder' => 'Joe Soap',
            'cardnumber' => '4242424242424242',
            'expirymonth' => '10',
            'expiryyear' => '2015',
            'cvv' => '123'
        )
    );
```

### Connect to myGate

Create the connection object to myGate

```
    // Open the gateway
    $connect = new Mygate\Connection(
        array(
            'merchant_id' => 'Your-merchant-id-here',
            'app_name' => 'Your-app-name-here', 
            'app_id' => 'Your-app-id-here'
        )
    );
```

### New and initial payment

Start by creating and tokenizing a credit card. You keep a record of the token in your database stored against a user. This token and the credit card holder's CVV number is all you need to collect  payments in the future

```
    // Create a new credit card and myGate connection details
    $card = new Mygate\Card($testCards[0], $connect);

    // Create and send the generated token to myGate and store the token in your database against a user.
    $token = $card->tokenize();

    // First time payment of R1200 over twelve months
    $card->pay("1200.00", "First Test Payment", "12");
```

### Second payment

Now that you have the token you can collect payments easer the next for the same user

```
    // Repeat or return customer payment.
    $card = new Mygate\Card($token, $connect);
    $card->setCvv('123'); // Client only needs to enter the CVV number
    $card->pay("100.00", "Second test payment");
```
    
### Deregistering a card from myGate

This will remove the card from myGate and tokenisation process will need to be repeated.

```
    $card = new Mygate\Card($token, $connect);
    $card->forget();
```