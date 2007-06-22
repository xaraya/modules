<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 */

/**
 * Class to handle Send To Friend property
 * @author jojodee
 * @package recommend 
 */
include_once "modules/dynamicdata/class/properties.php";
class Dynamic_SendToFriend_Property extends Dynamic_Property
{
   function validateValue($value = null)
    {
      if (!empty($value)) {
            $this->value = 1;
        } else {
            $this->value = 0;
        }
        return true;
    }

    function showInput($args = array())
    {
        extract($args);
      
        $data=array();

        if (!isset($value)) {
            $value = $this->value;
        }
        if (empty($name)) {
            $name = 'dd_' . $this->id;
        }
        if (empty($id)) {
            $id = $name;
        }
        $data['value']=$value;
        $data['name']=$name;
        $data['id']=$id;
        $data['checked']=!empty($value) ? true : false;
        $data['tabindex']=!empty($tabindex) ? $tabindex : 0;
        $data['invalid'] = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid): '';

        $template="";
        return xarTplProperty('recommend', 'sendtofriend', 'showinput', $data);

    }

    function showOutput($args = array())
    {   /* tidy up this, add a few checks */
        extract($args);
       
        if(!xarVarFetch('aid',  'isset', $aid,   NULL, XARVAR_DONT_SET)) {return;}

        $data=array();
        if (!isset($value)) {
            $value = $this->value;
        }
        $data['value']=$value;
        if (isset($aid)) {
           $data['aid']=    $aid;
          return xarTplProperty('recommend', 'sendtofriend', 'showoutput', $data );
       }else{
           return false;
       }
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
                              'id'         => 106,
                              'name'       => 'sendtofriend',
                              'label'      => 'Send To A Friend',
                              'format'     => '106',
                              'validation' => '',
                            'source'     => '',
                            'dependancies' => '',
                            'requiresmodule' => 'recommend',
                            'aliases'        => '',
                            'args'           => serialize($args)
                            // ...
                           );
        return $baseInfo;
     }

}

?>