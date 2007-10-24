<?php
/**
 * Dynamic HTMLArea Property
 *
 * Utilizes JavaScript based WYSIWYG Editor, HTMLArea
 *
 * @package dynamicdata
 * @subpackage properties
 */

/**
 * handle textarea property
 *
 * @package dynamicdata
 */
class Dynamic_HTMLArea_Property extends Dynamic_Property
{
    var $rows = 8;
    var $cols = 50;
    var $wrap = 'soft';

    function validateValue($value = null)
    {
        if (!isset($value)) {
            $value = $this->value;
        }
    // TODO: allowable HTML ?
        $this->value = $value;
        return true;
    }

//    function showInput($name = '', $value = null, $rows = 8, $cols = 50, $wrap = 'soft', $id = '', $tabindex = '')
    function showInput($args = array())
    {
        extract($args);

        if (!isset($name) )
        {
            $name = 'dd_'.$this->id;
        }
        if (empty($id)) {
            $id = $name;
        }

        $js_stuff = '<script type="text/javascript" src="htmlarea/htmlarea.js"></script>'
                   .'<script type="text/javascript" src="htmlarea/htmlarea-lang-en.js"></script>'
                   .'<script type="text/javascript" src="htmlarea/dialog.js"></script>'
                   .'<style type="text/css"><!--'
                   .'@import url(htmlarea/htmlarea.css);'
                   .'// --></style>';

        $js_stuff .='<script type="text/javascript">'
                   .'var editor'.$name.' = null;'
                   .'function init'.$name.'Editor()'
                   .'{'
                   .'    editor'.$name.' = new HTMLArea("'.$name.'");'
                   .'    editor'.$name.'.generate();'
                   .'    document.getElementById("'.$name.'button").style.display="none"; '
                   .'}'
                   .'</script>'
                   .'<input type="button" onclick="init'.$name.'Editor()" id="'.$name.'button" value="Use GUI Edit"/>';


        return '<textarea' .
               ' name="' . (!empty($name) ? $name : 'dd_'.$this->id) . '"' .
               ' rows="'. (!empty($rows) ? $rows : $this->rows) . '"' .
               ' cols="'. (!empty($cols) ? $cols : $this->cols) . '"' .
               ' wrap="'. (!empty($wrap) ? $wrap : $this->wrap) . '"' .
               ' id="'. $id . '"' .
               (!empty($tabindex) ? ' tabindex="'.$tabindex.'"' : '') .
               '>' . (isset($value) ? xarVarPrepForDisplay($value) : xarVarPrepForDisplay($this->value)) . '</textarea>' .
               (!empty($this->invalid) ? ' <span class="xar-error">'.xarML('Invalid #(1)', $this->invalid) .'</span>' : '')
               .$js_stuff;
    }

    function showOutput($args = array())
    {
         extract($args);
        if (isset($value)) {
            return xarVarPrepHTMLDisplay($value);
        } else {
            return xarVarPrepHTMLDisplay($this->value);
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
        $args['rows'] = 8;
        $aliases[] = array(
                              'id'         => 202,
                              'name'       => 'htmlarea_medium',
                              'label'      => 'Medium GUI Editor',
                              'format'     => '4',
                              'validation' => '',
                            'source'     => '',
                            'dependancies' => 'htmlarea/htmlarea.js',
                            'requiresmodule' => '',
                            'args' => serialize( $args ),
                            // ...
                           );
     
        $args['rows'] = 20;
        $args['cols'] = 80;
        $aliases[] = array(
                              'id'         => 203,
                              'name'       => 'htmlarea_large',
                              'label'      => 'Large GUI Editor',
                              'format'     => '5',
                              'validation' => '',
                            'source'     => '',
                            'dependancies' => 'htmlarea/htmlarea.js',
                            'requiresmodule' => '',
                            'args' => serialize( $args ),
                            // ...
                           );
     
        $args['rows'] = 2;
         $baseInfo = array(
                              'id'         => 201,
                              'name'       => 'htmlarea_small',
                              'label'      => 'Small GUI Editor',
                              'format'     => '3',
                              'validation' => '',
                            'source'     => '',
                            'dependancies' => 'htmlarea/htmlarea.js',
                            'requiresmodule' => '',
                            'aliases' => $aliases,
                            'args' => serialize( $args ),
                            
                            // ...
                           );
        return $baseInfo;
     }
}


?>
