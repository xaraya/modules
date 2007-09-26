<?php
/**
 * AddressBook user getAddressList
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */
include_once "modules/base/xarproperties/Dynamic_Select_Property.php";
include_once "modules/addressbook/xarglobal.php";

/**
 * @author ckraeft <ckraeft@xaraya.com>
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

        $data['size'] = !empty($size) ? $size : 1;

        $data['multiple'] = !empty($multiple) ? $multiple : "";

        if(!xarModAPILoad('addressbook', 'user')) return;

        // get the option corresponding to this value
        if(strstr($data['value'], ",")) {
            // no default selected, remove all options from list instead
            $data['displayName'] = "";
//            $data['valueexplodelist'] = $data['value'];
//            $data['valueexplodelist'] = substr($data['value'], 0, 1) == "," ? substr($data['value'], 1) : $data['value'];
        } elseif($data['value'] > 0) {
            $item = xarModAPIFunc('addressbook', 'user', 'getDetailValues', array('id' => $data['value']));

            $displayName = '';
            if(!empty($item['company'])) {
                $displayName .= xarVarPrepForDisplay($item['company'])."<br>";
            }
            if ((!empty($item['fname']) && !empty($item['lname'])) ||
                (!empty($item['fname']) || !empty($item['lname']))) {
                if (xarModGetVar('addressbook', 'name_order')==_AB_NO_FIRST_LAST) {
                    if (!empty($prefixes) && $item['prefix'] > 0) {
                        $displayName .= $prefixes[$item['prefix']-1]['name'].' ';
                    }
                    $displayName .= xarVarPrepForDisplay($item['fname']).' '.xarVarPrepForDisplay($item['lname']);
                } else {
                    if (!empty($item['lname'])) {
                        $displayName .= xarVarPrepForDisplay($item['lname']).', ';
                    }
                    if (!empty($prefixes) && $item['prefix'] > 0) {
                        $displayName .= $prefixes[$item['prefix']-1]['name'].' ';
                    }
                    $displayName .= xarVarPrepForDisplay($item['fname']);
                }
            }
            $data['displayName'] = $displayName;
        } else {
            $data['displayName'] = "";
        }
        if (!isset($item['company'])) {
            $data['company'] = "";
        } else {
            $data['company'] = $item['company'];
        }
        if (!isset($options) || count($options) == 0) {
            $optionlist = xarModAPIFunc('addressbook', 'user', 'getcompanies', array('company' => $data['company']));
            array_shift($optionlist);
            $data['options'] = $optionlist;
//            $instructions = array('id'=>'0','name'=>xarML('Select a company...'));
//            array_unshift($data['options'], $instructions);
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
        if(!empty($data['company'])) {
            $data['contactselect'] = xarModFunc('addressbook',
                                                'user',
                                                'select',
                                                array('company' => $data['company'],
                                                    'fieldname' => $data['name'],
                                                    'fieldid' => $data['id'],
                                                    'value' => $data['value'],
                                                    'size' => $data['size']));
        } else {
            $data['contactselect'] = "";
        }

        return xarTplProperty('addressbook', 'contactlist', 'showinput', $data);
    }
    /**
     * Generate the data for the template
     * @param array args
     * @return array with fname, lname, title, name (=displayname), company and all address info
     * @todo MichelV: do we want to return more?
     */
    function showOutput($args = array())
    {
        extract($args);
        if (isset($value)) {
            $this->value = $value;
        }
        $data=array();
        $data['value'] = $this->value;
        // get the option corresponding to this value
        $item = array();
        if (!empty($this->value)) {
            $item = xarModAPIFunc('addressbook', 'user', 'getDetailValues', array('id' => $this->value));
        }
        if (empty($item)) {
            $item['address_1']='';
            $item['address_2']='';
            $item['zip']='';
            $item['city']='';
            $item['title']='';
            $item['lname']='';
            $item['fname']='';
        }
        $displayCompany = '';
        if(!empty($item['company'])) {
            $displayCompany = xarVarPrepForDisplay($item['company']);
        }
        $displayName ='';
        if ((!empty($item['fname']) && !empty($item['lname'])) || (!empty($item['fname']) || !empty($item['lname']))) {
            if (xarModGetVar('addressbook', 'name_order')==_AB_NO_FIRST_LAST) {
                if (!empty($prefixes) && $item['prefix'] > 0) {
                    $displayName .= $prefixes[$item['prefix']-1]['name'].' ';
                }
                $displayName .= xarVarPrepForDisplay($item['fname']).' '.xarVarPrepForDisplay($item['lname']);
            } else {
                if (!empty($item['lname'])) {
                    $displayName .= xarVarPrepForDisplay($item['lname']).', ';
                }
                if (!empty($prefixes) && $item['prefix'] > 0) {
                    $displayName .= $prefixes[$item['prefix']-1]['name'].' ';
                }
                $displayName .= xarVarPrepForDisplay($item['fname']);
            }
        }

        $result = $displayName;
        // only apply xarVarPrepForDisplay on strings, not arrays et al.
        if (!empty($result) && is_string($result)) {
            $result = xarVarPrepForDisplay($result);
        }

        $data['option'] = array('id' => $this->value,
                                'name' => $result,
                                'company' => $displayCompany,
                                'title' => xarVarPrepForDisplay($item['title']),
                                'lname' => xarVarPrepForDisplay($item['lname']),
                                'fname' => xarVarPrepForDisplay($item['fname']),
                                'address_1' => xarVarPrepForDisplay($item['address_1']),
                                'address_2' => xarVarPrepForDisplay($item['address_2']),
                                'zip' => xarVarPrepForDisplay($item['zip']),
                                'city' => xarVarPrepForDisplay($item['city'])
                                );

        return xarTplProperty('addressbook', 'contactlist', 'showoutput', $data);
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