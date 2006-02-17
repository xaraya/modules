<?php
/**
 * Question type 'multichoicegroups'
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/**
 * Class Question type 'multichoicegroups'
 *
 * Data definition:
 *  listname = list name ('lists' module)
 *  desc = question text (default language)
 *  desc_{lang} = question text (alt languages)
 * Options from the list:
 *  x = option value
 *  x = option text (default language)
 *  x_{lang} = option text (alt languages)
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return int  type and name returned                       [OPTIONAL A REQURIED]
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Deprecated [release version here]             [AS REQUIRED]
 */

function surveys_questionsapi_multichoicegroups($args) {
    if (!empty($args)) {
        return new surveys_questionsapi_multichoicegroups($args);
    } else {
        return 'surveys_questionsapi_multichoicegroups';
    }
}

// Load the default class for extending.
xarModAPIfunc('surveys', 'questions', 'default');

// The multichoice object
class surveys_questionsapi_multichoicegroups extends surveys_questionsapi_default
{
    // List data.
    var $listdata = NULL;

    // Various properties.
    var $listname = NULL;
    var $commentbox = false;
    var $optiongroups = array();
    var $optiongroupsflat = array();
    var $defaultgroup = -1;
    var $groups = NULL;

    // Overriding flags.
    var $object_name = 'multichoicegroups';
    var $response_capable = true;

    // Constructor.
    function surveys_questionsapi_multichoicegroups(&$args) {
        // Default initialisation first
        $this->surveys_questionsapi_default($args);

        // If no response has been provided so far, then set the defaults.
        if ($this->response_capable && empty($this->response)) {
            // Set default value.
            $this->response = array(
                'value1' => $this->dbquestion['default_value'],
                'value2' => NULL,
                'value3' => '',
                'status' => 'NORESPONSE',
                'rtid' => $this->dbquestion['rtid'],
                'rid' => NULL,
                'qid' => $this->dbquestion['qid'],
                'qtid' => $this->dbquestion['qtid']
            );
        }

        // Move the dd properties of the question to the object properties.
        // This is done in case the DD properties are not set up correctly.
        if (isset($this->dbquestion['dd']['listname'])) {
            $this->listname = $this->dbquestion['dd']['listname'];
        }
        if (!empty($this->dbquestion['dd']['commentbox'])) {
            $this->commentbox = true;
        }

        if (isset($this->dbquestion['dd']['optiongroups'])) {
            $optiongroups = $this->dbquestion['dd']['optiongroups'];

            // Split the optiongroups into groups, separated by ';'
            $groups = explode(';', $optiongroups);

            // Now each group can contain one or more values, separated by ','.
            $defaultgroup = -1;
            foreach($groups as $key => $group) {
                if ($group == '*') {
                    // This is the default group.
                    $defaultgroup = $key;
                    $groups[$key] = array();
                } else {
                    $groups[$key] = explode(',', $group);
                    $this->optiongroupsflat = array_merge($this->optiongroupsflat, $groups[$key]);
                }
            }
            // Now we have an array of groups to hang the options from.
            $this->optiongroups = $groups;
            $this->defaultgroup = $defaultgroup;
        } else {
            // Dump everything into one group.
            $this->defaultgroup = 0;
            $this->optiongroups = array(array());
        } //var_dump($this->optiongroups);
    }

    // Render the question.
    // Render target are:
    // - input (for an input/data entry form)
    // - output (for a report)
    // Templates are:
    // - {target}-{typename}[-{custom}]
    // e.g. input-multichoicelist.xt, output-checkbox-special.xt
    function render($args) {
        // Handle the parameters.
        surveys_questionsapi_default::render($args);

        // Get the list items.
        $this->_get_list_data();

        // Use the question description as the question text, falling
        // back to the list description if the question is null.
        if (!empty($this->dbquestion['question_desc'])) {
            $question_text = $this->dbquestion['question_desc'];
        } else {
            $question_text = $this->listdata['list_desc'];
        }

        // If readonly, then make sure we only display for 'output'.
        if ($this->readonly && $this->target != 'output') {$this->target = 'output';}

        if ($this->target == 'input') {
            // Form input rendering.
            //var_dump($this->dbquestion);

            // Transfer the items to the template list.
            $listitems = array();
            $serial = 0;
            foreach ($this->listdata['items'] as $itemkey => $item) {
                $listitems[] = array(
                    'value' => $item['item_code'],
                    'label' => $item['item_desc'],
                    'name' => $item['item_short_name'],
                    'id' => $this->form_prefix_id . ($serial+=1)
                );
            }

            // Set the current value.
            $current_value = $this->response['value1'];

            // Multi-select, so there could be more than one response.
            $current_value = explode($this->multi_select_separator, $current_value);
            //var_dump($this->optiongroups);
            // Create the groups array by referencing the options according to
            // the specified groupings.
            $groups = array();
            // Set up blank option groups, so they appear in the correct order.
            // TODO: clear out blank groups at the end.
            foreach($this->optiongroups as $gkey => $gvalue) {
                $groups[$gkey] = array();
            }
            $current_group = -1;
            foreach($listitems as $qkey => $listitem) {
                //echo " $qkey=".$listitem['value'];
                // Determine which group a question is in.
                // Loop through all groups.
                $setg = -1;
                foreach($this->optiongroups as $gkey => $optiongroup) {
                    //echo ' TEST ' . $listitem['value'] . ' IN '; var_dump($optiongroup); echo '<br/>';
                    if (in_array($listitem['value'], $optiongroup)) {
                        //echo " YES value '".$listitem['value']."' in group $gkey <br/>";
                        if (!isset($groups[$gkey])) {$groups[$gkey] = array();}
                        $groups[$gkey][] = $qkey;
                        $setg = $gkey;
                        break;
                    }
                }
                if ($setg == -1 && $this->defaultgroup >= 0) {
                    if (!isset($groups[$this->defaultgroup])) {$groups[$this->defaultgroup] = array();}
                    $groups[$this->defaultgroup][] = $qkey;
                    $setg = $this->defaultgroup;
                }

                // If the current value is in this group, then flag it
                // for the template.
                if ($setg >= 0 && $current_group == -1) {
                    if (in_array($listitem['value'], $current_value)) {
                        $current_group = $setg;
                    }
                }
                //echo " current group = $current_group ";
            }

            // TODO: consider passing the object into the template.
            $template_data = $this->default_render_params();
            $template_data['groups'] = $groups;
            $template_data['question_text'] = &$question_text;
            $template_data['listitems'] = &$listitems;
            $template_data['current_value'] = $current_value;
            $template_data['current_group'] = $current_group;
            $template_data['commentbox'] = $this->commentbox;
            $template_data['comment_name'] = $this->form_prefix_name . 'comment';
            $template_data['comment_id'] = $this->form_prefix_id . 'comment';
            $template_data['current_comment'] = $this->response['value3'];
            $template_data['submit_hidden'] = $this->submit_hidden;

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }

        if ($this->target == 'output') {
            // Format for a report.

            // Multi-select, so the value is a list of items.
            $current_value = explode($this->multi_select_separator, $this->response['value1']);

            // List of selected items.
            $listitems = array();

            // TODO: for efficiency, could we just fetch the list items we need to display?
            foreach ($current_value as $item) {
                if (isset($this->listdata['items'][$item])) {
                    $listitems[] = array(
                        'value' => $this->listdata['items'][$item]['item_code'],
                        'label' => $this->listdata['items'][$item]['item_desc'],
                        'name' => $this->listdata['items'][$item]['item_short_name']
                    );
                }
            }

            $template_data = $this->default_render_params();
            $template_data['question_text'] = &$question_text;
            $template_data['listitems'] = $listitems;
            $template_data['commentbox'] = $this->commentbox;
            $template_data['comment'] = $this->response['value3'];

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }

        // Some other unsupported target.
        return '';
    }

    // Validate the choice(s).
    function validate() {
        // If not response-capable, then there is nothing to validate.
        //if (!$this->response_capable) {return true;}

        // If no response, then fail.
        if (empty($this->response)) {return false;}

        // The value is empty (not set or NULL)
        if (!isset($this->response['value1']) || $this->response['value1'] == '') {
            // If mandatory, then fail.
            if ($this->dbquestion['mandatory']) {
                $this->error = $this->errors['mandatory'];
                $this->response['status'] = 'INVALID';
            } else {
                $this->response['status'] = 'COMPLETE';
            }
        } else {
            // Not null, so check the values against the database lists.
            // Get value into an array.
            $values = explode($this->multi_select_separator, $this->response['value1']);

            // Validate each value.
            $this->_get_list_data();
            $this->response['status'] = 'COMPLETE';
            foreach ($values as $value) {
                if (!isset($this->listdata['items'][$value])) {
                    // The value is not in the list.
                    $this->error = $this->errors['notinlist'];
                    $this->response['status'] = 'INVALID';
                    break;
                }
            }
        }

        // Special custom check: if the answer is 'unable', and a comment box is
        // displayed, then the comment box cannot be left blank.
        if ($this->response['status'] == 'COMPLETE' && $this->response['value1'] == 'unable' && !empty($this->commentbox) && empty($this->response['value3'])) {
            $this->response['status'] = 'INVALID';
            $this->error = xarML('if you are unable to answer this question, please add comments to explain why');
        }

        $this->valid = ($this->response['status'] == 'COMPLETE' ? true : false);
        return $this->valid;
    }

    // Read the submitted response from the page.
    function submit() {
        // Fields are:
        // - {prefix}_comment{qid} for the comment
        // - {prefix}{qid} or {prefix}{qid}[] for the option or options

        // Fetch the submited group ID, then filter for any submitted value
        // in that group only.

        // If not response-capable, then there is nothing to do.
        //if (!$this->response_capable) {return true;}

        // If comments are requested, then get the question details.
        if (!empty($this->commentbox)) {
            $comment_name = $this->form_prefix_name . 'comment';
            if ($result = xarVarFetch($comment_name, 'pre:left:1000:trim:passthru:str', $comment, '', XARVAR_NOT_REQUIRED)) {
                $this->response['value3'] = $comment;
            }
        }

        // Multiselect, so fetch an array.
        $name = $this->form_prefix_name;
        $result = xarvarFetch($name, 'list:str', $pre_value, array(), XARVAR_NOT_REQUIRED);

        // Get the group number.
        $group_name = $this->form_prefix_name . '_group';
        $result = xarvarFetch($group_name, 'int', $groupid, 0, XARVAR_NOT_REQUIRED);

        // Now build the value array.
        $value = array();
        //echo " pre-value="; var_dump($pre_value);
        //echo "<br/> groupid=$groupid <br>groups="; var_dump($this->optiongroups);
        //echo " <br>flat="; var_dump($this->optiongroupsflat);
        if ($groupid == $this->defaultgroup) {
            // If the default ('*') group is selected, then only allow through
            // the values that do not appear in any other group.
            foreach($pre_value as $pre) {
                if (!in_array($pre, $this->optiongroupsflat)) {
                    // Not in the flat list of option groups: so add it to the list.
                    $value[] = $pre;
                }
            }
        } else {
            // If the non-default group is selected, then only allow through
            // the values for that group.
            if (isset($this->optiongroups[$groupid])) {
                foreach($this->optiongroups[$groupid] as $group_value) {
                    //echo " <br>checking '$group_value' is in pre-value ";
                    if (in_array($group_value, $pre_value)) {
                        // If the submitted value appears in the possible group values
                        // then add it to the result list.
                        $value[] = $group_value;
                    }
                }
            }
        }

        $value = implode($this->multi_select_separator, $value);
        //echo " <br>value=$value";
        $this->response['value1'] = $value;
    }


    // Initialise the question type. Includes:
    // - Question and response types if they do not exist
    // - DD object and properties
    function init_question_type() {
        // Get and/or create the question type (and optional response type).
        $type = $this->_get_create_question_types();
        if (empty($type)) {return;}

        // Prepare the object data for the question type.
        $objectdef = array(
            'name' => 'question_multichoicegroups_' . $type['qtid'],
            'label' => 'Question Multichoice Groups',
            'itemtype' => $type['qtid'],
            'type' => 'Q'
        );

        // Fetch and/or create the DD object.
        $object = $this->_get_create_dd_object($objectdef);

        // Now we have the DD object for the question type.
        // Time to create some properties.

        // Define the properties.
        $propertydefs = array(
            'listname' => array('label'=>'List name', 'type'=>'listslist', 'default'=>'', 'validation'=>""),
            'optiongroups' => array('label'=>'Option groups', 'type'=>'textbox', 'default'=>'', 'validation'=>"*;va1,val2;val3;etc"),
            'commentbox' => array('label'=>'Comment box', 'type'=>'checkbox', 'default'=>'0', 'validation'=>'')
        );

        // Create the dd properties, where they do not yet exist.
        $this->_create_dd_properties($object, $propertydefs);

        // There are no DD properties for the response type.

        return true;
    }

    function _get_list_data() {
        // Fetch the list items.
        // TODO: error if lists module not present?
        // TODO: list($list) = ... (but suppress errors?)

        if (isset($this->listdata)) {return true;}

        // Get the list item data - both list details and items.
        // Index the lists by 'index' (we only want one - index 0)
        // Index the items by 'code' - makes validation easier.
        $list = xarModAPIfunc(
            'lists', 'user', 'getlistitems',
            array(
                'list_name'=>$this->listname,
                'listkey'=>'index',
                'itemkey'=>'code',
                'lang_suffix' => $this->lang_suffix
            )
        );

        if (empty($list)) {
            // TODO: the list is empty - what to do?
            return;
        }

        // Set the property.
        // The list we need will be the first (and only) on the list.
        $this->listdata =& $list[0];

        return true;
    }
}

?>