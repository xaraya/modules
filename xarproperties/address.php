<?php
/**
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Shop module
 */

// PARK THIS HERE FOR NOW. IT WILL MOVE

/**
 * Address Property
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.base.xarproperties.textbox');

class AddressProperty extends TextBoxProperty
{
    public $id         = 30033;
    public $name       = 'address';
    public $desc       = 'Address';
    public $reqmodules = array('shop');

    public $display_show_city         = true;
    public $display_show_province     = true;
    public $display_show_postal_code  = true;
    public $display_show_country      = true;
    public $display_rows              = 2;

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->tplmodule = 'shop';
        $this->template =  'address';
        $this->filepath   = 'modules/shop/xarproperties';
    }

    public function checkInput($name = '', $value = null)
    {
        $name = empty($name) ? 'dd_'.$this->id : $name;
        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;
        if (!isset($value)) {
            $value = array();
            for ($i=1;$i<=$this->display_rows;$i++) {
                list($isvalid, $value['line_' . $i]) = $this->fetchValue($name . '_line_' . $i);
            }
            list($isvalid, $value['city']) = $this->fetchValue($name . '_city');
            list($isvalid, $value['province']) = $this->fetchValue($name . '_province');
            list($isvalid, $value['postal_code']) = $this->fetchValue($name . '_postal_code');
            list($isvalid, $value['country']) = $this->fetchValue($name . '_country');
        }

        $value = serialize($value);
        return $this->validateValue($value);
    }

    public function getValue()
    {
        try {
            $valuearray = unserialize($this->value); 
        } catch (Exception $e) {
            $valuearray = array(); 
        }
        for ($i=1;$i<=$this->display_rows;$i++)
            $valuearray['line_' . $i] = !empty($valuearray['line_' . $i]) ? $valuearray['line_' . $i] : '';
        $valuearray['city'] = !empty($valuearray['city']) ? $valuearray['city'] : '';
        $valuearray['province'] = !empty($valuearray['province']) ? $valuearray['province'] : '';
        $valuearray['postal_code'] = !empty($valuearray['postal_code']) ? $valuearray['postal_code'] : '';
        $valuearray['country'] = !empty($valuearray['country']) ? $valuearray['country'] : '';
        return $valuearray;
    }

    public function showInput(Array $data = array())
    {
        $data = $this->assemble_address($data);
        return DataProperty::showInput($data);
    }
    public function showOutput(Array $data = array())
    {
        $data = $this->assemble_address($data);
        return DataProperty::showOutput($data);
    }

    private function assemble_address(Array $data = array())
    {
        if (!empty($data['rows'])) $this->display_rows = $data['rows'];
        if (!empty($data['display_show_city'])) $this->display_show_city = $data['display_show_city'];
        if (!empty($data['display_show_province'])) $this->display_show_province = $data['display_show_province'];
        if (!empty($data['display_show_postal_code'])) $this->display_show_postal_code = $data['display_show_postal_code'];
        if (!empty($data['display_show_country'])) $this->display_show_country = $data['display_show_country'];
        if (empty($data['value'])) $data['value'] = $this->getValue();
        return $data;
    }
}

?>