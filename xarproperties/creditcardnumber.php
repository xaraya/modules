<?php
/**
 * CreditCardNumber Property
 * @package math
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

// PARK THIS HERE FOR NOW. IT WILL MOVE

    sys::import('modules.base.xarproperties.textbox');

    class CreditCardNumberProperty extends TextBoxProperty
    {
        public $id         = 30071;
        public $name       = 'creditcardnumber';
        public $desc       = 'Credit Card Number';
        public $reqmodules = array('shop');

        public $creditcardtype;

        public $initialization_transform     = 0;

        public $validation_ccnumber          = null;
        public $validation_ccnumber_invalid;

        function __construct(ObjectDescriptor $descriptor)
        {
            parent::__construct($descriptor);

            $this->tplmodule = 'shop';
            $this->filepath   = 'modules/shop/xarproperties';

            // check validation for allowed values
            if (!empty($this->validation)) {
                $this->parseValidation($this->validation);
            }
        }

        public function validateValue($value = null)
        {
            if (!parent::validateValue($value)) return false;

            if (!isset($value)) $value = $this->value;

            $value = ereg_replace('[^0-9]', '', $value);

            // Check if the number itself is valid
            if(!$this->validnumber($value)) {
                if (!empty($this->validation_ccnumber_invalid)) {
                    $this->invalid = xarML($this->validation_ccnumber_invalid);
                } else {
                    $this->invalid = xarML('#(1) #(2): is not a valid credit card', $this->name, $this->desc);
                }
                $this->value = null;
                return false;
            }
            return true;

        }

        public function setValue($value=null)
        {
            if ($this->initialization_transform == true) {
                if (empty($value)) {
                    $this->value = null;
                } else {
                    // ?
                    $this->value = '**** **** **** ' . substr($value,-4);
                
                }
            
            } else {
                $this->value = $value;
            }
          
        }
        
        function validnumber($value) 
        {
            $cardNumber = strrev($value);
            $numSum = 0;

            for ($i = 0; $i < strlen($cardNumber); $i ++) {
                $currentNum = substr($cardNumber, $i, 1);

                // Double every second digit
                if ($i % 2 == 1) {
                    $currentNum *= 2;
                }

                // Add digits of 2-digit numbers together
                if ($currentNum > 9) {
                    $firstNum = $currentNum % 10;
                    $secondNum = ($currentNum - $firstNum) / 10;
                    $currentNum = $firstNum + $secondNum;
                }

                $numSum += $currentNum;
            }

            // If the total has no remainder it's OK
            return ($numSum % 10 == 0);
        }
    }
?>