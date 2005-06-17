<?php
/**
 * Dynamic Send To Friend Property
 *
 * @package dynamicdata
 * @subpackage properties
 */

/**
 * Class to handle Send To Friend property
 *
 * @package dynamicdata
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
        if (!isset($value)) {
            $value = $this->value;
        }
        if (empty($name)) {
            $name = 'dd_' . $this->id;
        }
        if (empty($id)) {
            $id = $name;
        }
        
        $data['name']     = $name;
        $data['id']       = $id;
        $data['value']    = isset($value) ? xarVarPrepForDisplay($value) : xarVarPrepForDisplay($this->value);
        $data['tabindex'] = !empty($tabindex) ? ' tabindex="'.$tabindex.'"': '';
        $data['invalid']  = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';

        $template="";
        return xarTplProperty('recommend', 'sendtofriend', 'showinput', $data);

        /*
        return '<input type="checkbox"'.
               ' name="' . $name . '"' .
               ' value="1"' .
               ' id="'. $id . '"' .
               (!empty($tabindex) ? ' tabindex="'.$tabindex.'"' : '') .
               (!empty($value) ? ' checked="checked"' : '') .
               ' />' .
               (!empty($this->invalid) ? ' <span class="xar-error">'.xarML('Invalid #(1)', $this->invalid) .'</span>' : '');
       */
    }

    function showOutput($args = array())
    {   //tidy up this, add a few checks
        extract($args);
        if(!xarVarFetch('aid',  'isset', $aid,   NULL, XARVAR_DONT_SET)) {return;}

        if (!isset($value)) {
            $value = $this->value;
        }

        if (!empty($value) && isset($aid)){
        $data['aid']=    $aid;
        $data['value']=  $value;
           /* move to template
            $alttext = xarML('Send this article to a friend');
            $sendimg = xarTplGetImage('sendtofriend.gif', 'recommend');
               $out= '<a href="'.xarModURL('recommend','user','sendtofriend',array('aid'=>$aid)).'"><img src="'.$sendimg.'" style="border:0;" alt="'.$alttext.'" /></a>';
            return $out;
          */
        $template="";
        return xarTplProperty('recommend', 'sendtofriend', 'showoutput', $data );

       } else {
       //value is not set - we don't want to show the link
           return '';
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
