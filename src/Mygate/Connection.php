<?php

    /**
     * Set namespace
     */
    namespace Mygate;

    /**
     * MyGate SOAP Integration
     */
    class Connection
    {
        /**
         * @var     object Holds the instance
         */
        protected static $_instance = false;

        /**
         * @var     object Hold the card object
         */
        public $card = false;

        /**
         * @var     object Holds payment information
         */
        public $payment = false;

        /**
         * @var     array   Default config array
         */
        protected $config = array(
            'merchant_id' => '',        // Merchant id from MyGate
            'app_id' => '',             // Application ID from MyGate
            'secure_id' => '',          // 3D Secure ID from MyGate
            'gateway_id' => '01',       // 01 => Sandbox Test Bank, 21 => First National Bank, 22 => ABSA, 23 => Nedbank, 24 => Standard Bank
            'currency' => 'ZAR',        // Currency setting
            'mode' => '0'               // 0 => test mode, 1 => live mode
        );

        /**
         * @var     array   Array of url access points
         */
        public $urls = array(
            'service' => '',
            '3d' => ''    
        );

        /**
         * @var     string  Hold the 3D transaction id
         */
        public $three_d_transaction_id = false;

        /**
         * Sets up the class object. If a config is given, it will merge with the current config array.
         *
         * @param   array  $config  Optional config override
         * @return  void
         */
        public function __construct(array $config = array())
        {
            // Update or merge config data
            $this->config = $config + $this->config;

            // Bring in URLs
            $urls = require_once("resources".DIRECTORY_SEPARATOR."Urls.php");

            // Append URLS
            switch($this->config['mode']){
                case 1 :
                    $this->urls['service'] = $urls['live']['service'];
                    $this->urls['3d'] = $urls['live']['3d'];
                break;
                default :
                    $this->urls['service'] = $urls['test']['service'];
                    $this->urls['3d'] = $urls['test']['3d'];
                break;
            }

            // Done
            return $this;
        }

        /**
         * Sets a config value
         *
         * @param   string
         * @param   mixed
         * @return  Fieldset  this, to allow chaining
         */
        public function set_config($config, $value = null)
        {
            $config = is_array($config) ? $config : array($config => $value);
            foreach ($config as $key => $value)
            {
                if (strpos($key, '.') === false)
                {
                    $this->config[$key] = $value;
                }
                else
                {
                    \Arr::set($this->config, $key, $value);
                }
            }

            return $this;
        }

        /**
         * Get a single or multiple config values by key
         *
         * @param   string|array  a single key or multiple in an array, empty to fetch all
         * @param   mixed         default output when config wasn't set
         * @return  mixed|array   a single config value or multiple in an array when $key input was an array
         */
        public function get_config($key = null, $default = null)
        {
            if ($key === null){
                return $this->config;
            }

            if (is_array($key)){
                $output = array();
                foreach ($key as $k){
                    $output[$k] = $this->get_config($k, $default);
                }
                return $output;
            }

            if (strpos($key, '.') === false){
                return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
            }
            else{
                return \Arr::get($this->config, $key, $default);
            }
        }

        /**
         * Tokenise the card
         */
        public function tokenize()
        {
            // Check that we have what we need to create the token, we would only have two entries here if the token was already created and stored.
            if(
                $this->card->cc_name and
                $this->card->pan and
                $this->card->exp_month and
                $this->card->exp_year
            ){
                // Register token
                Soap\Service::fCreateTokenCC($this);

                // Done
                return true;
            }
        }

        /**
         * 3D SECURE CALLS
         * Check if a user has enrolled for 3D Secure, if so we return a form with post data to be send to the banks 3D secure page.
         * When the bank posts back, posted fields MD and PaRes will be available for capture and we can move to step 2
         */
        public function is_enrolled()
        {
            /** 
             * Step 2. 
             * Once the card holder has been posted back, the authenticate function gets invoked to verify the results. 
             * The resulting transaction index from this call needs to be passed to the "fpaynow" function.
             */
            if(isset($_POST['MD']) and isset($_POST['PaRes'])){
                $this->three_d_transaction_id = Soap\Threed::authenticate($this);
                return false; // We return false here as 3D secure is now complete and no longer an action required.
            }

            /** 
             * Step 1.
             * Check if a user has enrolled for 3D Secure.
             */
            if($results = Soap\Threed::tokenlookup($this)){

                // Find return URL
                $return_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                // Return a form to use, or create your own form
                return array(
                    'form_open' => '<form action="'.$results['acsurl'].'" method="post" accept-charset="utf-8"><input type="hidden" name="PaReq" value="'.$results['pareqmsg'].'" /><input type="hidden" name="TermUrl" value="'.$return_url.'" /><input type="hidden" name="MD" value="'.$results['transactionindex'].'" />',
                    'form_close' => '</form>',
                    'elements' => array(
                        'PaReq' => $results['pareqmsg'],
                        'MD' => $results['transactionindex'],
                        'TermUrl' => $return_url
                    )
                );  
            }

            // User is not enrolled, carry on as normal
            return false;
        }

        /**
         * Perform the actual payment
         */
        public function pay()
        {
            // Make payment
            if($index = Soap\Service::fpaynow($this)){
                return $index;
            }
        }

        /**
         * Forget about this cards token
         */
        public function forgetcard()
        {
            if(isset($this->card->token)){
                $results = Soap\Service::fDeregisterTokenCC($this);
            }

            return true;
        }
    }