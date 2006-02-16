<?php
/**
 * Dynamic userlist property
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Dynamic courselist property
 * based on work of:
 * @author DD: mikespub <mikespub@xaraya.com>
 * @author MichelV <michelv@xaraya.com>
 * @todo MichelV: <1> field selection
 */

/* Include the base class */
include_once "modules/base/xarproperties/Dynamic_Select_Property.php";

class Dynamic_CourseList_Property extends Dynamic_Select_Property
{
    var $catlist = array();
    var $showlist = array();
    var $orderlist = array();
    var $typelist = array();
    var $levellist = array();
    var $showglue = '; ';

    /*
    * Options available to course selection
    * ===================================
    * $pitemrules = "type:$rule_type;level:$rule_level;category:$rule_cat;source:$rule_source";
    * Options take the form:
    *   option-type:option-value;
    * We ignore source, only take 'internal' requests ;)
    * option-types:
    *   category:catid[,catid] - select only courses who are members of the given category(ies)
    *   level:value - select only courses of the given level (int)
    *   type:value - select only courses of the given value for type (varchar)
    *   show:field[,field] - show the specified field(s) in the select item
    *   showglue:string - string to join multiple fields together
    *   order:field[,field] - order the selection by the specified field
    * where
    *   field - name|uname|email|uid TO BE DETERMINED
    */

    function Dynamic_CourseList_Property($args)
    {
        // Don't initialise the parent class as it handles the
        // validation in an inappropriate way for user lists.
        $this->Dynamic_Select_Property($args);

        // Initialise the select option list.
        $this->options = array();

        // Handle user options if supplied.
        if (!empty($this->validation)) {
            $this->parseValidation($this->validation);
        }
    }

    // TODO: validate the selected user against the specified group(s).
    function validateValue($value = null)
    {
        if (!isset($value)) {
            $value = $this->value;
        }
        if (!empty($value)) {
            // check if this is a valid user id
            $course = xarModApiFunc('courses','user','get',array('courseid'=>$value));
            if (isset($course)) {
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

    function showInput($args = array())
    {
        $select_options = array();

        extract($args);
        $courses=array();
        $data= array();
        //$users=array(0;

        if (!isset($value)) {
            $value = $this->value;
        }
        if (!isset($options) || count($options) == 0) {
            $options = $this->options;
        }
        if (count($options) == 0) {
           /* if ($this->levels <> -1) {
                $select_options['level'] = $this->levels;
            }*/
            if (!empty($this->orderlist)) {
                $select_options['order'] = implode(',', $this->orderlist);
            }
            if (!empty($this->catlist)) {
                $select_options['category'] = implode(',', $this->catlist);
            }
            if (!empty($this->typelist)) {
                $select_options['type'] = implode(',', $this->typelist);
            }
            $courses = xarModAPIFunc('courses', 'user', 'getall', $select_options);
            $options[] = array('id' => 0, 'name' => xarML('Please choose a course'));
            // Loop for each course retrieved and populate the options array.
            // TODO: have options show usefull info
            if (empty($this->showlist)) {
                // Simple case (default) -
                foreach ($courses as $course) {
                    $options[] = array('id' => $course['courseid'], 'name' => $course['name']);
                }
            } else {
                // Complex case: allow specific fields to be selected.
                foreach ($courses as $course) {
                    $namevalue = array();
                    foreach ($this->showlist as $showfield) {
                        $namevalue[] = $course[$showfield];
                    }
                    $options[] = array('id' => $course['courseid'], 'name' => implode($this->showglue, $namevalue));
                }
            }
        }

        if (empty($name)) {
            $data['name'] = 'dd_' . $this->id;
        } else {
            $data['name'] = $name;
        }

        if (empty($id)) {
            // TODO: strip out characters that are not allowed in a name.
            $data['id'] = xarVarPrepForDisplay($data['name']);
        } else {
            $data['id']= $id;
        }
       //$data['select_options']=$select_options;
        $data['value']=$value;
        $data['options']=$options;
        $data['courses']=$courses;
        $data['tabindex']=!empty($tabindex) ? $tabindex : 0;
        $data['invalid']=!empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) : '';

        return xarTplProperty('courses', 'courselist', 'showinput', $data);
    }

    // TODO: format the output according to the 'showlist'.
    // TODO: provide an option to allow admin to decide whether to wrap the user
    // in a link or not.
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
         if (isset($courseid)) {
             $data['courseid']=$courseid;
         }
         $template="";
         return xarTplProperty('courses', 'courselist', 'showoutput', $data);

    }

    function parseValidation($validation = '')
    {
        foreach(preg_split('/(?<!\\\);/', $validation) as $option) {
            // Semi-colons can be escaped with a '\' prefix.
            $option = str_replace('\;', ';', $option);
            // An option comes in two parts: option-type:option-value
            if (strchr($option, ':')) {
                list($option_type, $option_value) = explode(':', $option, 2);
                if ($option_type == 'level' && is_numeric($option_value)) {
                    $this->level = $option_value;
                }
                if ($option_type == 'type') {
                    $this->type = $option_value;
                }
                if ($option_type == 'showglue') {
                    $this->showglue = $option_value;
                }
                if ($option_type == 'catid') {
                    $this->catlist = array_merge($this->catlist, explode(',', $option_value));
                }
                if ($option_type == 'show') {
                    $this->showlist = array_merge($this->showlist, explode(',', $option_value));
                    // Remove invalid elements (fields that are not valid).
                    $showfilter = create_function(
                    // TODO: improve this listing
                        '$a', 'return preg_match(\'/^[-]?(name|credits|mincredit|desc)$/\', $a);'
                    );
                    $this->showlist = array_filter($this->showlist, $showfilter);
                }
                if ($option_type == 'order') {
                    $this->orderlist = array_merge($this->orderlist, explode(',', $option_value));
                }
            }
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
        $baseInfo = array(
                          'id'              => 179,
                          'name'            => 'courselist',
                          'label'           => 'Dropdown list of Courses',
                          'format'          => '179',
                          'validation'      => '',
                          'source'          => '',
                          'dependancies'    => '',
                          'requiresmodule'  => 'courses',
                          'aliases'         => '',
                          'args'            => ''
                          // ...
                         );
        return $baseInfo;
    }

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
        $data['catlist'] = join(',', $this->catlist);
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
        return xarTplProperty('courses', 'courselist', 'validation', $data);
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
                    $options[] = 'category:' . $validation['catlist'];
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
