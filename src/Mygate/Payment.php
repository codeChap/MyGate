<?php

    /**
     * Set namespace
     */
    namespace CodeChap\Mygate;

    /**
     * MyGate SOAP Integration
     */
    class Payment
    {
        /**
         * @var The amount to be paid
         */
        var $amount = 0;

        /**
         * @var The reference or description of the payment
         */
        var $reference = false;

        /**
         * Construct
         */
        public function __construct($data = array())
        {
            $this->amount = $data['amount'];
            $this->reference = $data['reference'];
        }
    }