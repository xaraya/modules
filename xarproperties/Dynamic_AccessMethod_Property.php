<?php
/**
 * Dynamic State List Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Base module
 */
/*
 * @author John Cox
*/
include_once "modules/base/xarproperties/Dynamic_Select_Property.php";

/**
 * handle the userlist property
 *
 * @package dynamicdata
 *
 */
class Dynamic_AccessMethod_Property extends Dynamic_Select_Property
{

    function Dynamic_AccessMethod_Property($args)
    {
        $this->Dynamic_Select_Property($args);
    }

    function validateValue($value = null)
    {
        if (!isset($value)) {
            $value = $this->value;
        }
        if (empty($value)) {
            $value = array();
        } elseif (!is_array($value)) {
            $tmp = @unserialize($value);
            if ($tmp === false) {
                $value = array($value);
            } else {
                $value = $tmp;
            }
        }
        $validlist = array();
        $options = $this->getOptions();
        foreach ($options as $option) {
            array_push($validlist,$option['id']);
        }
        foreach ($value as $val) {
            if (!in_array($val,$validlist)) {
                $this->invalid = xarML('Related Access Method');
                $this->value = null;
                return false;
            }
        }
        $this->value = serialize($value);
        return true;
    }

//    function showInput($name = '', $value = null, $options = array(), $id = '', $tabindex = '')
    function showInput($args = array())
    {
        extract($args);
        $data = array();

        if (!isset($value)) {
            $value = $this->value;
        }
        
        $accessmethodlist = xarModAPIFunc('accessmethods','user','getall');
       
        $options = array();
        $options[] = array('id' =>'', 'name' =>'Please select' );
        foreach($accessmethodlist as $accessdetails) {
            $options[] = array('id' =>$accessdetails['siteid'], 'name' =>$accessdetails['accesstype'].': '.$accessdetails['site_name']);
        }
        
        if (empty($value)) {
            $value = array();
        } elseif (!is_array($value)) {
            $tmp = @unserialize($value);
            if ($tmp === false) {
                $value = array($value);
            } else {
                $value = $tmp;
            }
        }
        if (empty($name)) {
            $name = 'dd_' . $this->id;
        }
        if (empty($id)) {
            $id = $name;
        }
        $data['value']  = $value;
        $data['name']   = $name;
        $data['id']     = $id;
        $data['options']= $options;

        $data['tabindex'] =!empty($tabindex) ? $tabindex : 0;
        $data['invalid']  =!empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) : '';


        $template="";
        return xarTplProperty('accessmethods', 'accessmethod', 'showinput', $data);

        //return $out;
    }

    function showOutput($args = array())
    {
        extract($args);
        $data = array();

        if (!isset($value)) {
            $value = $this->value;
        }
        if (empty($value)) {
            $value = array();
        } elseif (!is_array($value)) {
            $tmp = @unserialize($value);
            if ($tmp === false) {
                $value = array($value);
            } else {
                $value = $tmp;
            }
        }
        if (!isset($options)) {
            $accessmethodlist = xarModAPIFunc('accessmethods','user','getall');
           
            $options = array();
            $options[] = array('id' =>'', 'name' =>'Please select' );
            foreach($accessmethodlist as $accessdetails) {
                $options[] = array('id' =>$accessdetails['siteid'], 'name' =>$accessdetails['site_name'],
                                    'url' => $accessdetails['url'],
                                    'accesstype' => $accessdetails['accesstype']);
            }
        }
        $data['value']= $value;
        $data['options']= $options;
        $template="";
        return xarTplProperty('accessmethods', 'accessmethod', 'showoutput', $data);

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
                              'id'         => 732,
                              'name'       => 'accessmethod',
                              'label'      => 'Access Method Dropdown',
                              'format'     => '732',
                              'validation' => '',
                            'source'     => '',
                            'dependancies' => '',
                            'requiresmodule' => 'accessmethods',
                            'aliases'        => '',
                            'args'           => serialize($args)
                            // ...
                           );
        return $baseInfo;
     }



}

?>
