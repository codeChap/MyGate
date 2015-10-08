<?php

    /**
     * MyGate test cards version 1.2.1
     * Note: The below test cards will not work for testing 3D Secure transactions. Please refer to 3D Secure Test Cases
     */

    return array(

        "visa" => array(

            // Visa Successful
            "successful" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '4111111111111111',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123'
            ),
            
            // Visa Declined
            "declined" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '4242424242424242',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123'
            ),

            // 3D Successfull
            "threed_successful" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '4111111111111111',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123',
                'pin' => '12345'
            ),

            // 3D Failed
            "threed_failed" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '4111111111111111',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123',
                'pin' => '54321'
            ),

            // 3D Unable to verify
            "threed_unable" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '4012001038488884',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123',
                'pin' => ''
            ),

            // 3D Attempt acknowledged
            "threed_attempt" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '4012001037141112',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123',
                'pin' => ''
            ),

            // 3D Enrolled but invalid response from ACS
            "threed_invalid" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '4012001036853337',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123',
                'pin' => ''
            ),

            // 3D Directory Service Unavailable
            "threed_service" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '4012001036853337',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123',
                'pin' => ''
            )
        ),

        "mastercard" => array(

            // Mastercard Successfull
            "successful" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '5100080000000000',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123'
            ),

            // Mastercard declined
            "declined" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '5404000000000001',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123'
            ),
        ),

        "amex" => array(

            // Amex successfull
            "successful" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '370000200000000',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123'
            ),

             // Amex failed
            "declined" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '374200000000004',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123'
            ),
        ),

        "diners" => array(

            // Diners successfull
            "successful" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '362135898197781',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123'
            ),

             // Diners failed
            "declined" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '360569309025904',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123'
            ),
        ),

        "maestro" => array(

            // Maestro successfull
            "successful" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '6759649826438453',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123'
            ),

             // Maestro failed
            "declined" => array(
                'cardholder' => 'Joe Soap',
                'cardnumber' => '6700649826438453',
                'expirymonth' => '09',
                'expiryyear' => date('Y', strtotime("+2 years")),
                'cvv' => '123'
            ),
        )
    );