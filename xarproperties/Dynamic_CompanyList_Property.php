<?php
/**
 * Dynamic Select property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Base module
 */
include_once "modules/base/xarproperties/Dynamic_Select_Property.php";

/**
 * @author mikespub <mikespub@xaraya.com>
 */
class Dynamic_CompanyList_Property extends Dynamic_Select_Property
{
    var $options;
    var $override = false; // allow values other than those in the options

    function Dynamic_CompanyList_Property($args)
    {
        $this->Dynamic_Select_Property($args);
        if (!isset($this->options)) {
            $this->options = array();
        }
    }

    function validateValue($value = null)
    {
        if (isset($value)) {
            $this->value = $value;
        }
        if (!empty($this->value)) {
            return true;
        }
        // check if we allow values other than those in the options
        if ($this->override) {
            return true;
        }
        $this->invalid = xarML('company');
        $this->value = null;
        return false;
    }

    function showInput($args = array())
    {
        extract($args);
        $data=array();

        if (!isset($value)) {
            $data['value'] = $this->value;
        } else {
            $data['value'] = $value;
        }
        if (!isset($options) || count($options) == 0) {
            $data['options'] = xarModAPIFunc('addressbook', 'user', 'getcompanies');
        } else {
            $data['options'] = $options;
        }
        // check if we need to add the current value to the options
        if (!empty($data['value']) && $this->override) {
            $found = false;
            foreach ($data['options'] as $option) {
                if ($option['id'] == $data['value']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $data['options'][] = array('id' => $data['value'], 'name' => $data['value']);
            }
        }
        if (empty($name)) {
            $data['name'] = 'dd_' . $this->id;
        } else {
            $data['name'] = $name;
        }
        if (empty($id)) {
            $data['id'] = $data['name'];
        } else {
            $data['id']= $id;
        }

        $data['tabindex'] =!empty($tabindex) ? $tabindex : 0;
        $data['invalid']  =!empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) : '';

        return xarTplProperty('addressbook', 'companylist', 'showinput', $data);
        //return $out;
    }

    function showOutput($args = array())
    {
        extract($args);
        if (isset($value)) {
            $this->value = $value;
        }
        $data=array();
        $data['value'] = $this->value;
        // get the option corresponding to this value
        $result = $this->value;
        // only apply xarVarPrepForDisplay on strings, not arrays et al.
        if (!empty($result) && is_string($result)) {
            $result = xarVarPrepForDisplay($result);
        }
        $data['option'] = array('id' => $this->value,
                                'name' => $result);

        return xarTplProperty('addressbook', 'companylist', 'showoutput', $data);
    }

    /**
     * Get the base information for this property.
     *
     * @return array base information for this property
     **/
    function getBasePropertyInfo()
    {
        $args = array();
        $baseInfo = array(
                          'id'         => 734,
                          'name'       => 'companylist',
                          'label'      => 'Company List',
                          'format'     => '734',
                          'validation' => '',
                          'source'     => '',
                          'dependancies' => '',
                          'requiresmodule' => 'addressbook',
                          'aliases'        => '',
                          'args'           => serialize($args)
                          // ...
                         );
        return $baseInfo;
    }
}

?>