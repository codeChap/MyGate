<?php

    namespace CodeChap\Soap;

    /**
     * MyGate SOAP Integration
     */
    class Threed
    {
        /**
         * 3D SECURE CALL STEP 1.
         * Check if a user has enrolled for 3D Secure, if so return a form with post to be send to the banks 3d secure page. Step 2 will occur as soon as post data is available.
         */
        public static function tokenlookup($connection)
        {
            // Call, step one.
            $client = new \SoapClient($connection->urls['3d']);
            
            // Execute
            $request = $client->tokenlookup(
                $connection->get_config('merchant_id'),             // Merchant ID
                $connection->get_config('app_id'),                  // Application ID
                $connection->get_config('mode'),                    // Mode
                $connection->card->token,                           // Tokenised token
                $connection->payment->amount,                       // Amount to be paid
                \Mygate\Helpers\Client::get_http_user_agent(),      // Http user agent
                \Mygate\Helpers\Client::get_http_accept(),          // Http accept
                'merchant reference',                               // Reference
                'merchant description',                             // Description
                'N',                                                // Is the transaction recurring
                '',                                                 // The recurring frequency
                '',                                                 // Last debit date
                ''                                                  // Amount of months the recurring debits will continue
            );

            // Find results
            $results = \Mygate\Helpers\Terminal::process_results($request, true);

            // If enrolled, we must verify
            if(isset($results['enrolled']) and $results['enrolled'] == 'Y'){
                return $results;                         
            }

            // We do not need 3D secure verification
            return false;
        }

        /**
         * 3D SECURE CALL STEP 2.
         * Verify the 3D secure results from the data posted by the users banks 3d page
         */
        public static function authenticate($connection)
        {
            // Gather and filter post data
            $TransactionId = isset($_POST['MD']) ? filter_var($_POST['MD'], FILTER_SANITIZE_STRING) : null;
            $PAResPayload = isset($_POST['PaRes']) ? filter_var($_POST['PaRes'], FILTER_SANITIZE_STRING) : null;
            
            // Call
            $client = new \SoapClient($connection->urls['3d']);

            // Execute
            $request = $client->authenticate(
                $TransactionId,     // TransactionID
                $PAResPayload       // PaRes
            );

            // Regardless of the result, an authorisation should occur at this stage.
            return $TransactionId;
        }
    }