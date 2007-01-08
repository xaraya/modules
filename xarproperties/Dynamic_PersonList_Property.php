<?php
/**
 * Dynamic Person List Property
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author SIGMAPersonnel module development team
 */

include_once "modules/base/xarproperties/Dynamic_Select_Property.php";

/**
 * handle the personlist property
 * This is a list type of property, it allows you to select a person from this module
 *
 * @author MichelV <michelv@xarayahosting.nl>
 *
 */
class Dynamic_PersonList_Property extends Dynamic_Select_Property
{
    /**
     * Set the property
     *
     * @return
     **/
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
    /**
     * Validate a value
     *
     * @return bool
     **/
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

    /**
     * Show the input form
     *
     * @return array with template
     **/
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

        $select_options = array('sortby'=>'lastname');

        $persons = array();
        // TODO: Add get for personlist
        $persons[] = array('id' =>0, 'name' =>'Please select' );
        $items = xarModAPIFunc('sigmapersonnel', 'user', 'getall', $select_options);

        foreach ($items as $item) {
            if (xarSecurityCheck('ReadSigmapersonnel', 0, 'PersonnelItem', "All:All:All")) {

            /* Clean up the item text before display */
            $item['name'] = xarVarPrepForDisplay($item['firstname'].' '.$item['lastname']);
            $item['id'] = $item['personid'];
            /* Add this item to the list of items to be displayed */
            $persons[] = $item;
            }
        }

        $data['persons'] = $persons;
        $data['invalid']  = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) : '';
        $data['tabindex'] =! empty($tabindex) ? $tabindex : 0;

        $template="";
        return xarTplProperty('sigmapersonnel', 'personlist', 'showinput', $data);

        //return $out;
    }
    /**
     * Show the actual output
     *
     * @return array
     **/
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
         if (isset($personid)) {
             $data['personid']=$personid;
         }
         $template="";
         return xarTplProperty('sigmapersonnel', 'personlist', 'showoutput', $data);

    }

    /**
     * Get the base information for this property.
     *
     * @return array with base information for this property
     **/
    function getBasePropertyInfo()
    {
        $args = array();
        $baseInfo = array(
                          'id'             => 418,
                          'name'           => 'Personnellisting',
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
}
?>