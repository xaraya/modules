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
include_once "modules/addressbook/xarglobal.php";

/*
 * @author mikespub <mikespub@xaraya.com>
*/
class Dynamic_ContactList_Property extends Dynamic_Select_Property
{
    var $options;
    var $override = false; // allow values other than those in the options

    function Dynamic_ContactList_Property($args)
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
        $this->invalid = xarML('contact');
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

        if (!isset($company)) {
            $data['company'] = "";
        } else {
            $data['company'] = $company;
        }
        if (!isset($options) || count($options) == 0) {
            $data['options'] = xarModAPIFunc('addressbook', 'user', 'getcompanies', array('company' => $data['company']));
            array_shift($data['options']);
            $instructions = array('id'=>'0','name'=>xarML('Select a company...'));
            array_unshift($data['options'], $instructions);
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

        $data['contactselect'] = xarModFunc('addressbook', 'user', 'select', array('company' => $data['value'], 'fieldname' => $data['name'], 'fieldid' => $data['id']));

        return xarTplProperty('addressbook', 'contactlist', 'showinput', $data);
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
        $item = xarModAPIFunc('addressbook', 'user', 'getDetailValues', array('id' => $this->value));
        $displayName = '';
        $displayName .= xarVarPrepHTMLDisplay($item['company'])."<br>";

        if ((!empty($item['fname']) && !empty($item['lname'])) ||
            (!empty($item['fname']) || !empty($item['lname']))) {
            if (xarModGetVar('addressbook', 'name_order')==_AB_NO_FIRST_LAST) {
                if (!empty($prefixes) && $item['prefix'] > 0) {
                    $displayName .= $prefixes[$item['prefix']-1]['name'].' ';
                }
                $displayName .= xarVarPrepHTMLDisplay($item['fname']).' '.xarVarPrepHTMLDisplay($item['lname']);
            } else {
                if (!empty($item['lname'])) {
                    $displayName .= xarVarPrepHTMLDisplay($item['lname']).', ';
                }
                if (!empty($prefixes) && $item['prefix'] > 0) {
                    $displayName .= $prefixes[$item['prefix']-1]['name'].' ';
                }
                $displayName .= xarVarPrepHTMLDisplay($item['fname']);
            }
        }

        $result = $displayName;
        // only apply xarVarPrepForDisplay on strings, not arrays et al.
        if (!empty($result) && is_string($result)) {
            $result = xarVarPrepHTMLDisplay($result);
        }
        $data['option'] = array('id' => $this->value,
                                'name' => $result);
                                
        return xarTplProperty('addressbook', 'contactlist', 'showoutput', $data);
    }

    /**
     * Get the base information for this property.
     *
     * @returns array
     * @return base information for this property
     **/
    function getBasePropertyInfo()
    {
        $args = array();
        $baseInfo = array(
                          'id'         => 735,
                          'name'       => 'contactlist',
                          'label'      => 'Contact List',
                          'format'     => '735',
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