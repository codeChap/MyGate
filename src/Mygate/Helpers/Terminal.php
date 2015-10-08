<?php

    namespace CodeChap\Mygate\Helpers;

    class Terminal
    {
        /**
         * Process results of the transaction
         *
         * @return boolean | array | exception
         */
        public static function process_results(array $results, $boolean = false)
        {
            // Loop each row of the result
            foreach($results as $result){

                // Break up key value pair
                if( is_string($result) and preg_match("/||/", $result) ){
                    try{
                        list($key, $val) = explode("||", $result);
                        $r[strtolower($key)] = $val;
                    }
                    catch(\Exception $e){
                        \Log::debug($e);
                    }
                }
            }

            // Return new array
            if(isset($r)){

                // If ok return the transaction index
                if($r['result'] == "0"){
                    return $r;  
                }

                // Return only false
                if($boolean){
                    return $r;
                }

                // One up
                $path = realpath(__DIR__.'/../resources/Codes.php');

                // Bring in Error codes
                $codes = require_once($path);

                // Result
                if($r['result'] == "-1"){
                    $array_keys = array_keys($r);
                    $code = $array_keys[2];
                }

                // The different type of errors we get
                if(isset($r['errorcode']) or isset($r['errorno'])){
                    
                    // Try find the code
                    $code = isset($r['errorcode']) ? $r['errorcode'] : $r['errorno'];
                }

                // Set description
                $description = isset($codes[$code]) ? $codes[$code] : "Unknown error";

                // Show error
                throw new \Exception($code . " - " . $description . ".");
            }

            // Something went wrong
            throw new \Exception("No result from call.");
        }
    }