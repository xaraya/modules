<?php
/**
 * Dynamic Person List Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SIGMA Personnel module
 */
/**
 * @author MichelV
 */
include_once "modules/base/xarproperties/Dynamic_Select_Property.php";

/**
 * handle the personlist property
 * This is a list type of property, it allows you to select a person from this module
 *
 * @package dynamicdata
 *
 */
class Dynamic_PersonList_Property extends Dynamic_Select_Property
{

    function Dynamic_PersonList_Property($args)
    {
        $this->Dynamic_Select_Property($args);
        // Initialise the select option list.
        $this->options = array();

        // Handle user options if supplied.
        if (!empty($this->validation)) {
            $this->parseValidation($this->validation);
        }
    }

    function validateValue($value = null)
    {
        if (!isset($value)) {
            $value = $this->value;
        }
        if (!empty($value)) {
            if (is_string($value)) { // Place simple check here
                $this->value = $value;
            } else {
                $this->invalid = xarML('Person Listing');
                $this->value = null;
                return false;
            }
        } else {
            $this->value = '';
        }
        return true;
    }

    /*
    // TODO: validate the selected user against the specified group(s).
    function validateValue($value = null)
    {
        if (!isset($value)) {
            $value = $this->value;
        }
        if (!empty($value)) {
            // check if this is a valid user id
            $uname = xarUserGetVar('uname', $value);
            if (isset($uname)) {
                $this->value = $value;
                return true;
            } else {
                xarErrorHandled();
            }
        } elseif (empty($value)) {
            $this->value = $value;
            return true;
        }
        $this->invalid = xarML('selection');
        $this->value = null;
        return false;
    }
    */

//    function showInput($name = '', $value = null, $options = array(), $id = '', $tabindex = '')
    function showInput($args = array())
    {
        extract($args);
        $data = array();
        if (!isset($value)) {
            $value = $this->value;
        }
        if (empty($name)) {
            $name = 'dd_' . $this->id;
        }
        if (empty($id)) {
            $id = $name;
        }
        $data['value'] = $value;
        $data['name']  = $name;
        $data['id']    = $id;

        $persons = array();
        // TODO: Add get for personlist
        $persons[] = array('id' =>0, 'name' =>'Please select' );
        $persons = xarModAPIFunc('roles', 'user', 'getall', $select_options);

        $data['persons'] = $persons;
        $data['invalid']  = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) : '';
        $data['tabindex'] =! empty($tabindex) ? $tabindex : 0;


        $template="";
        return xarTplProperty('sigmapersonnel', 'personlist', 'showinput', $data);

        //return $out;
    }

    function showOutput($args = array())
    {
         extract($args);
         $data = array();

        if (isset($value)) {
             $data['value']=xarVarPrepHTMLDisplay($value);
         } else {
             $data['value']=xarVarPrepHTMLDisplay($this->value);
         }
         if (isset($name)) {
           $data['name']=$name;
         }
         if (isset($id)) {
             $data['id']=$id;
         }
         $template="";
         return xarTplProperty('sigmapersonnel', 'personlist', 'showoutput', $data);

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
                            'id'             => 418,
                            'name'           => 'SIGMApersonnellisting',
                            'label'          => 'Person Dropdown',
                            'format'         => '418',
                            'validation'     => '',
                            'source'         => '',
                            'dependancies'   => '',
                            'requiresmodule' => 'sigmapersonnel',
                            'aliases'        => '',
                            'args'           => serialize($args)
                            // ...
                           );
        return $baseInfo;
     }
     // Do we need the below?
    /**
     * Show the current validation rule in a specific form for this property type
     *
     * @param $args['name'] name of the field (default is 'dd_NN' with NN the property id)
     * @param $args['validation'] validation rule (default is the current validation)
     * @param $args['id'] id of the field
     * @param $args['tabindex'] tab index of the field
     * @returns string
     * @return string containing the HTML (or other) text to output in the BL template
     */
    function showValidation($args = array())
    {
        extract($args);

        $data = array();
        $data['name']       = !empty($name) ? $name : 'dd_'.$this->id;
        $data['id']         = !empty($id)   ? $id   : 'dd_'.$this->id;
        $data['tabindex']   = !empty($tabindex) ? $tabindex : 0;
        $data['invalid']    = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';
        $data['size']       = !empty($size) ? $size : 50;

        if (isset($validation)) {
            $this->validation = $validation;
        // CHECKME: reset grouplist et al. first if we call this from elsewhere ?
            $this->parseValidation($validation);
        }

    // TODO: adapt if the template uses a multi-select for groups
        $data['grouplist'] = join(',', $this->grouplist);
        $data['userstate'] = $this->userstate;
    // TODO: adapt if the template uses a multi-select for fields
        $data['showlist']  = join(',', $this->showlist);
        $data['orderlist'] = join(',', $this->orderlist);
        $data['showglue']  = xarVarPrepForDisplay($this->showglue);
        $data['other']     = '';

        // allow template override by child classes
        if (!isset($template)) {
            $template = '';
        }
        return xarTplProperty('roles', 'userlist', 'validation', $data);
    }

    /**
     * Update the current validation rule in a specific way for this property type
     *
     * @param $args['name'] name of the field (default is 'dd_NN' with NN the property id)
     * @param $args['validation'] validation rule (default is the current validation)
     * @param $args['id'] id of the field
     * @returns bool
     * @return bool true if the validation rule could be processed, false otherwise
     */
    function updateValidation($args = array())
    {
        extract($args);

        // in case we need to process additional input fields based on the name
        if (empty($name)) {
            $name = 'dd_'.$this->id;
        }
        // do something with the validation and save it in $this->validation
        if (isset($validation)) {
            if (!is_array($validation)) {
                $this->validation = $validation;

            } elseif (!empty($validation['other'])) {
                $this->validation = $validation['other'];

            } else {
                $options = array();
                if (!empty($validation['grouplist'])) {
                // TODO: adapt if the template uses a multi-select for groups
                    $options[] = 'group:' . $validation['grouplist'];
                }
                if (!empty($validation['userstate']) && is_numeric($validation['userstate'])) {
                    $options[] = 'state:' . $validation['userstate'];
                }
                if (!empty($validation['showlist'])) {
                // TODO: adapt if the template uses a multi-select for fields
                    $templist = explode(',', $validation['showlist']);
                    // Remove invalid elements (fields that are not valid).
                    $showfilter = create_function(
                        '$a', 'return preg_match(\'/^[-]?(name|uname|email|uid|state|date_reg)$/\', $a);'
                    );
                    $templist = array_filter($templist, $showfilter);
                    if (count($templist) > 0) {
                        $options[] = 'show:' . join(',', $templist);
                    }
                }
                if (!empty($validation['orderlist'])) {
                // TODO: adapt if the template uses a multi-select for fields
                    $options[] = 'order:' . $validation['orderlist'];
                }
                if (!empty($validation['showglue'])) {
                    $validation['showglue'] = str_replace(';', '\;', $validation['showglue']);
                    $options[] = 'showglue:' . $validation['showglue'];
                }
                $this->validation = join(';', $options);
            }
        }

        // tell the calling function that everything is OK
        return true;
    }
}
?>