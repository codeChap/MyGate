<?php

    namespace CodeChap;

    /**
     * MyGate SOAP Integration
     */
    class Card
    {
        /**
         * Credit card holders name
         */
        var $cc_name = false;
        
        /**
         * Credit card number
         */
        var $pan = false;
        
        /**
         * Exp month of card
         */
        var $exp_month = false;
        
        /**
         * Exp year of card
         */
        var $exp_year  = false;

        /**
         * Pan exp of card
         */
        var $pan_exp = false;
        
        /**
         * CSV number of back of card
         */
        var $cvc = false;
        
        /**
         * The type of card
         */
        var $type = false;

        /**
         * Set the token
         */
        var $token = false;

        public function __construct(array $data = array())
        {
            // Update card holder name
            if(isset($data['cc_name']) and ! empty($data['cc_name'])){
                $this->cc_name = $data['cc_name'];
            }
            
            // Update card number
            if(isset($data['pan']) and ! empty($data['pan'])){
                $this->set_pan($data['pan']);
            }
            
            // Set expiry month
            if(isset($data['exp_month']) and ! empty($data['exp_month'])){
                $this->set_expmonth($data['exp_month']);
            }
            
            // Set expiry year
            if(isset($data['exp_year']) and ! empty($data['exp_year'])){
                $this->set_expyear($data['exp_year']);
            }

            // Set expiry pan number
            if($this->exp_month and $this->exp_year){
                $this->set_panexp();
            }
            
            // Sets csv number
            if(isset($data['cvc']) and ! empty($data['cvc'])){
                $this->set_cvc($data['cvc']);
            }

            // Find the type of card
            $this->set_type();

            // Set token
            $this->set_token($data);
        }

        /**
         * Sets and validates the credit card number given
         *
         * @param numeric $number Card number
         *  
         * https://developer.mygateglobal.com/downloads/credit-card-number-validation
         */
        private function set_pan($number)
        {            
            // Clean number and perform Luhn or MOD10 check.
            if($number = preg_replace('/\D/', '', $number) and is_numeric($number)){
                
                // Set the string length
                $number_length = strlen($number);

                // Set parity
                $parity = $number_length % 2;

                // Set total variable
                $total = 0;
                
                //Loop through each digit and perform checks
                for ($i = 0; $i < $number_length; $i++) {
                    $digit = $number[$i];
                    if ($i % 2 == $parity) {
                        $digit*=2;
                        if ($digit > 9) { 
                            $digit-=9;
                        }
                    }
                
                    // Total up the digits
                    $total+=$digit;
                }

                // If the total mod 10 equals 0, the number is valid. 
                // There can be instatnces where false credit cards will pass this function (test cards, etc).
                if($total % 10 == 0){
                    $this->pan = $number;
                    return true;
                }
            }
            
            // Card is invalid
            throw new \Exception("Card number: " . $number . " is invalid.");
        }

        /**
         * Sets the expire month
         * 
         * @param numeric 
         */
        private function set_expmonth($data)
        {
            if(is_numeric($data)){
                $this->exp_month = $data;
                return true;
            }

            throw new \Exception("Expiry month is invalid");
        }

        /**
         * Sets the expire yeath
         * 
         * @param numeric 
         */
        public function set_expyear($data)
        {
            if(is_numeric($data)){
            $this->exp_year = $data;
                return true;
            }
            
            throw new \Exception("Expiry year is invalid");
        }

        /**
         * Sets the panexp value
         */
        private function set_panexp()
        {
            if($this->exp_year and $this->exp_month){
                $this->pan_exp = substr($this->exp_year, 2) . $this->exp_month;
                return true;
            }

            throw new \Exception("Please set your expiry month and year.");
        }


        /**
         * Sets the credit card type
         *
         * https://developer.mygateglobal.com/downloads/credit-card-type-identification
         */
        private function set_type()
        {
            // Toekenised cards dont need this
            if( ! $this->pan){
                return true;
            }

            //Removes any spaces or hyphens on the card number before validation continues.
            $number = preg_replace('/\D/', '', $this->pan);

            //Checks to see whether the submitted value is numeric (After spaces and hyphens have been removed).
            if(is_numeric($number)) {
                
                //Splits up the card number into various identifying lengths.
                $firstOne = substr($number, 0, 1);
                $firstTwo = substr($number, 0, 2);
                $firstThree = substr($number, 0, 3);
                $firstFour = substr($number, 0, 4);
                $firstFive = substr($number, 0, 5);
                $firstSix = substr($number, 0, 6);

                // Visa
                if($firstOne == "4") {
                    $this->type = "Visa";
                    return true;
                }
            
                // Mastercard
                if($firstTwo >= "51" && $firstTwo <= "55") {
                    $this->type = "MasterCard";
                    return true;
                }
            
                // American Express
                if($firstTwo == "34" || $firstTwo == "37") {
                    $this->type = "American Express";
                    return true;
                }
                
                // Diners Club International
                if($firstTwo == "36") {
                    $this->type = "Diners Club International";
                    return true;
                }
            
                // Diners club EnRoute
                if($firstFour == "2014" || $firstFour == "2149") {
                    $this->type = "Diners Club enRoute";
                    return true;
                }
                
                // Diners Club Carte Blanche
                if($firstThree >= "300" && $firstThree <= "305") {
                    $this->type = "Diners Club Carte Blanche";
                    return true;
                }
                
                // Discovery
                if(($firstFour == "6011") || ($firstSix >= "622126" && $firstSix <= "622925") || ($firstThree >= "644" && $firstThree <= "649") || ($firstTwo == "65")) {
                    $this->type = "Discover Card";
                    return true;
                }
                
                // JCB
                if($firstTwo >= "35") {
                    $this->type = "JCB";
                    return true;
                }

                // If the above logic does not identify the card number, return this message.
                $this->type = "Other / Unknown Card Type";
                return true;
            }

            // If the incoming card number is not numeric, return this message.
            throw new \Exception("Your card number is invalid.");
        }

        /**
         * Sets the cards CVV number
         * 
         * @param numeric $data Four or three digit CVV number
         *
         * https://developer.mygateglobal.com/downloads/cvv-validation
         */
        private function set_cvc($cvc)
        {
            // Validation can only be done if the card number is present but since we use tokens to represent cards, we will just return true here.
            if($this->pan == false){
                $this->cvc = $cvc;
                return true;
            }

            // Get the card number
            $number = $this->pan;
            
            // Check the CVV
            if($cvc = preg_replace('/\D/', '', $cvc) and is_numeric($cvc)){
                
                // Splits up the card number into various identifying lengths.
                $firstOne = substr($number, 0, 1);
                $firstTwo = substr($number, 0, 2);

                //If the card is an American Express
                if($firstTwo == "34" || $firstTwo == "37") {
                    if ( ! preg_match("/^\d{4}$/", $cvc)){
                        
                        // The credit card is an American Express card but does not have a four digit CVV code
                        throw new \Exception("Card CVV number is invalid");
                    }
                }
                
                else if ( ! preg_match("/^\d{3}$/", $cvc)) {
                    
                    // The credit card is a Visa, MasterCard, or Discover Card card but does not have a three digit CVV code
                    throw new \Exception("Card CVV number is invalid");
                }

                // CVV is valid
                $this->cvc = $cvc;

                // Done
                return true;
            }

            // Not valid for card type
            throw new \Exception("Card cvc number is invalid.");
        }

        /**
         * Set the token on a card
         */
        public function set_token($data)
        {
            // The token has been provided
            if(isset($data['token']) and ! empty($data['token'])){
                $this->token = $data['token'];
            }
            else{
                $this->token = false;
            }

            // Done
            return true;
        }
    }