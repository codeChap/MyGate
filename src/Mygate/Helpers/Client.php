<?php

    namespace Mygate\Helpers;

    class Client
    {
        /**
         * Get browser information
         */
        public static function get_http_user_agent()
        {
            //Get the browser header and user agent form the clients browser.
            return $_SERVER['HTTP_USER_AGENT'];
        }

        /**
         * Get browser information
         */
        public static function get_http_accept()
        {
            return $_SERVER['HTTP_ACCEPT'];
        }

        /**
         * Find the users IP address
         *
         * http://stackoverflow.com/questions/15699101/get-the-client-ip-address-using-php
         */
        public static function get_ip()
        {
            // Function to get the client IP address
            $ipaddress = '';
            if (getenv('HTTP_CLIENT_IP'))
                $ipaddress = getenv('HTTP_CLIENT_IP');
            else if(getenv('HTTP_X_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
            else if(getenv('HTTP_X_FORWARDED'))
                $ipaddress = getenv('HTTP_X_FORWARDED');
            else if(getenv('HTTP_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_FORWARDED_FOR');
            else if(getenv('HTTP_FORWARDED'))
               $ipaddress = getenv('HTTP_FORWARDED');
            else if(getenv('REMOTE_ADDR'))
                $ipaddress = getenv('REMOTE_ADDR');
            else
                $ipaddress = '';

            // Check for localhost
            if($ipaddress == '127.0.0.1' or $ipaddress == 'localhost'){
                $ipaddress = '';
            }

            return $ipaddress;
        }
    }