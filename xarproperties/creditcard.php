<?php
/**
 * CreditCard Property
 * @package math
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

// PARK THIS HERE FOR NOW. IT WILL MOVE

sys::import('modules.dynamicdata.class.properties.base');

class CreditCardProperty extends DataProperty
{
    public $id         = 30097;
    public $name       = 'creditcard';
    public $desc       = 'CreditCard';
    public $reqmodules = array('shop');

    public $display_labels            = array();

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->tplmodule = 'shop';
        $this->template =  'creditcard';
        $this->filepath   = 'modules/shop/xarproperties';
    }

    public function checkInput($name = '', $value = null)
    {
        $name = empty($name) ? 'dd_'.$this->id : $name;
        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;
        if (!isset($value)) {
            $invalid = array();
            $validity = true;
            $value = array();
            $textbox = DataPropertyMaster::getProperty(array('name' => 'textbox'));
            $textbox->validation_min_length = 3;

            for ($i=1;$i<=$this->display_rows;$i++) {
                $isvalid = $textbox->checkInput($name . '_line_' . $i);
                if ($isvalid) {
                    $value['line_' . $i] = $textbox->value;
                } else {
                    $invalid[] = 'line_' . $i;
                }                
                $validity = $validity && $isvalid;
            }

            if ($this->display_show_city) {
                $isvalid = $textbox->checkInput($name . '_city');
                if ($isvalid) {
                    $value['city'] = $textbox->value;
                } else {
                    $invalid[] = 'city';
                }
                $validity = $validity && $isvalid;
            }

            if ($this->display_show_province) {
                $province = DataPropertyMaster::getProperty(array('name' => 'statelisting'));
                $isvalid = $province->checkInput($name . '_province');
                if ($isvalid) {
                    $value['province'] = $province->value;
                } else {
                    $invalid[] = 'province';
                }
                $validity = $validity && $isvalid;
            }

            if ($this->display_show_postal_code) {
                list($isvalid, $value['postal_code']) = $this->fetchValue($name . '_postal_code');
                $validity = $validity && $isvalid;
            }
            
            if ($this->display_show_country) {
                $country = DataPropertyMaster::getProperty(array('name' => 'countrylisting'));
                $isvalid = $country->checkInput($name . '_country');
                if ($isvalid) {
                    $value['country'] = $country->value;
                } else {
                    $invalid[] = 'country';
                }
                $validity = $validity && $isvalid;
            }
            
        }
        if (!empty($invalid)) $this->invalid = implode(',',$invalid);
        $this->value = serialize($value);
        return $validity;
    }

    public function getValue()
    {
        try {
            $valuearray = unserialize($this->value); 
        } catch (Exception $e) {
            $valuearray = array(); 
        }
        $valuearray['cc_name'] = !empty($valuearray['cc_name']) ? $valuearray['cc_name'] : '';
        $valuearray['cc_type'] = !empty($valuearray['cc_type']) ? $valuearray['cc_type'] : '';
        $valuearray['cc_number'] = !empty($valuearray['cc_number']) ? $valuearray['cc_number'] : '';
        $valuearray['cc_code'] = !empty($valuearray['cc_code']) ? $valuearray['cc_code'] : '';
        $valuearray['cc_expiration'] = !empty($valuearray['cc_expiration']) ? $valuearray['cc_expiration'] : '';
        return $valuearray;
    }

    public function showInput(Array $data = array())
    {
        $data = $this->assemble_creditcard($data);
        return parent::showInput($data);
    }
    public function showOutput(Array $data = array())
    {
        $data = $this->assemble_creditcard($data);
        return parent::showOutput($data);
    }

    private function assemble_creditcard(Array $data = array())
    {
        if (!isset($data['labels'])) $data['labels'] = $this->display_labels;;
        if (empty($data['value'])) $data['value'] = $this->getValue();
        return $data;
    }
}

?>