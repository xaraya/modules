<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

    class BasicPayment implements iPayment
    {
        public $code;
        public $title;
        public $description;
        public $status;
        
        function __construct()
        {
        }

        function update_status(Array $args=array()) 
        {
            return false;
        }

        function javascript_validation() 
        {
            return false;
        }

        function selection() 
        {
            return false;
        }

        function pre_confirmation_check() 
        {
            return false;
        }

        function confirmation() 
        {
            return false;
        }

        function process_button() 
        {
            return false;
        }

        function before_process() 
        {
            return false;
        }

        function after_process() 
        {
            return false;
        }

        function output_error() 
        {
          return false;
        }

        function get_error() 
        {
            return false;
        }

        function check() 
        {
            return false;
        }

        function install() 
        {
            return false;
        }

        function remove() 
        {
            return false;
        }

        function keys() 
        {
            return false;
        }
    }

    interface iPayment
    {
        public function __construct();
        public function update_status(Array $args=array());
        public function javascript_validation();
        public function selection();
        public function pre_confirmation_check();
        public function confirmation();
        public function process_button();
        public function before_process();
        public function after_process();
        public function get_error();
        public function check();
        public function install();
        public function remove();
        public function keys();
    }
?>