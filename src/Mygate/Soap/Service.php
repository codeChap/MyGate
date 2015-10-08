<?php

    namespace CodeChap\Mygate\Soap;

    class Service
    {
        /**
         * Checks that a token is associated with credt card detils
         */
        public static function fGetToken($connection)
        {
            // Soap call
            $client = new \SoapClient($connection->urls['service']);

            // Result
            $request = $client->fGetToken(
                $connection->get_config('merchant_id'),
                $connection->get_config('app_id'),
                $connection->card->token
            );

            // Process result and show errors
            $result = \CodeChap\Mygate\Helpers\Terminal::process_results($request, true);

            // Record found
            if($result){
                return true;
            }

            // Record not found
            return false;
        }

        /**
         * Registers a new token with myGate
         */
        public static function fCreateTokenCC($connection)
        {
            // Soap call
            $client = new \SoapClient($connection->urls['service']);

            // Result
            $request = $client->fCreateTokenCC(
                $connection->get_config('merchant_id'),
                $connection->get_config('app_id'),
                $connection->card->token,
                $connection->card->cc_name,
                $connection->card->pan,
                $connection->card->exp_month,
                $connection->card->exp_year
            );

            // Process result and show errors
            $result = \CodeChap\Mygate\Helpers\Terminal::process_results($request);

            // Return it
            return true;
        }

        /**
         * Removes a registered token from mygate
         */
        public static function fDeregisterTokenCC($connection)
        {
            // Soap call
            $client = new \SoapClient($connection->urls['service']);

            // Result
            $request = $client->fDeregisterTokenCC(
                $connection->get_config('merchant_id'),
                $connection->get_config('app_id'),
                $connection->card->token
            );

            // Process result and show errors
            $result = \CodeChap\Mygate\Helpers\Terminal::process_results($request);

            // Done
            return true;
        }

        /**
         * Process a payment
         *
         * @var client object
         * @var credit card obejct
         */
        public static function fPayNow($connection)
        {
            // Soap call
            $soap = new \SoapClient($connection->urls['service']);
            
            // Result          
            $request = $soap->fPayNow(
                $connection->get_config('merchant_id'),
                $connection->get_config('app_id'),
                $connection->three_d_transaction_id,
                $connection->card->token,
                $connection->card->cvc,
                $connection->payment->amount,
                $connection->get_config('mode'),
                $connection->payment->reference,
                '0', // On budget
                '', // Budget period
                '', // UCI 
                \CodeChap\Mygate\Helpers\Client::get_ip(),
                '' // Shipping Country Code
            );

            // Process result and show errors
            $result = \CodeChap\Mygate\Helpers\Terminal::process_results($request);

            // Return it
            return $result['transactionindex'];
        }
    }