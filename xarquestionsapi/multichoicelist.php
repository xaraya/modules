<?php
/**
 * Question type 'multichoice
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @link http://xaraya.com/index.php/release/45.html
 * @author Surveys module development team
 */
/**
 * Question type 'multichoice
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * Data definition:
 *  listname = list name ('lists' module)
 *  desc = question text (default language)
 *  desc_{lang} = question text (alt languages)
 * Options from the list:
 *  x = option value
 *  x = option text (default language)
 *  x_{lang} = option text (alt languages)
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
 */

function surveys_questionsapi_multichoicelist($args) {
    if (!empty($args)) {
        return new surveys_questionsapi_multichoicelist($args);
    } else {
        return 'surveys_questionsapi_multichoice';
    }
}

// Load the default class for extending.
xarModAPIfunc('surveys', 'questions', 'default');

// The multichoice object
class surveys_questionsapi_multichoicelist extends surveys_questionsapi_default
{
    // List data.
    var $listdata = NULL;

    // Various properties.
    var $listname = NULL;
    var $multiselect = false;
    var $format = NULL;
    var $commentbox = false;

    // Overriding flags.
    var $object_name = 'multichoicelist';
    var $response_capable = true;

    // Constructor.
    function surveys_questionsapi_multichoicelist(&$args) {
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
        if (!empty($this->dbquestion['dd']['multiselect'])) {
            $this->multiselect = true;
        }
        if (isset($this->dbquestion['dd']['listname'])) {
            $this->listname = $this->dbquestion['dd']['listname'];
        }
        if (isset($this->dbquestion['dd']['format'])) {
            $this->format = $this->dbquestion['dd']['format'];
        }
        if (!empty($this->dbquestion['dd']['commentbox'])) {
            $this->commentbox = true;
        }
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
            // Format can be one of: combo tickbox
            // Multi-select can be on or off
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
            if ($this->multiselect) {
                // For multi-select, the value is a list of items.
                $current_value = explode($this->multi_select_separator, $current_value);
            }

            // TODO: consider passing the object into the template.
            $template_data = $this->default_render_params();
            $template_data['format'] = $this->format;
            $template_data['multiselect'] = $this->multiselect;
            $template_data['question_text'] = &$question_text;
            $template_data['listitems'] = &$listitems;
            $template_data['current_value'] = $current_value;
            $template_data['commentbox'] = $this->commentbox;
            $template_data['comment_name'] = $this->form_prefix_name . 'comment';
            $template_data['comment_id'] = $this->form_prefix_id . 'comment';
            $template_data['current_comment'] = $this->response['value3'];
            $template_data['submit_hidden'] = $this->submit_hidden;

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }

        if ($this->target == 'output') {
            // Format for a report.

            if ($this->multiselect) {
                // For multi-select, the value is a list of items.
                $current_value = explode($this->multi_select_separator, $this->response['value1']);
            } else {
                $current_value = array($this->response['value1']);
            }

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
            $template_data['format'] = $this->format;
            $template_data['multiselect'] = $this->multiselect;
            $template_data['question_text'] = &$question_text;
            $template_data['question_name'] = $this->dbquestion['question_name'];
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

        // Start by assuming validation will pass.
        $this->valid = true;

        // The value is empty (not set or NULL)
        if (!isset($this->response['value1']) || $this->response['value1'] == '') {
            // If mandatory, then fail.
            if ($this->dbquestion['mandatory']) {
                $this->error = $this->errors['mandatory'];
                $this->valid = false;
                $this->response['status'] = 'INVALID';
                return false;
            } else {
                $this->valid = true;
                $this->response['status'] = 'COMPLETE';
                return true;
            }
        }

        // Not null, so check the values against the database lists.
        // Get value into an array.
        if ($this->multiselect) {
            $values = explode($this->multi_select_separator, $this->response['value1']);
        } else {
            $values = array($this->response['value1']);
        }

        // Validate each value.
        // TODO: for efficiency, could we just fetch the list items we need to display?
        if ($this->valid) {
            $this->_get_list_data();
            foreach ($values as $value) {
                if (!isset($this->listdata['items'][$value])) {
                    // The value is not in the list.
                    $this->error = $this->errors['notinlist'] . ' [' . xarVarPrepForDisplay($value) . ']';
                    $this->valid = false;
                }
            }
        }

        // Special custom check: if the answer is 'unable', and a comment box is
        // displayed, then the comment box cannot be left blank.
        if ($this->valid && $this->response['value1'] == 'unable' && !empty($this->commentbox) && empty($this->response['value3'])) {
            $this->valid = false;
            $this->error = xarML('if you are unable to answer this question, please add comments to explain why');
        }

        $this->response['status'] = ($this->valid ? 'COMPLETE' : 'INVALID');
        return $this->valid;
    }

    // Read the submitted response from the page.
    function submit() {
        // Fields are:
        // - {prefix}_comment{qid} for the comment
        // - {prefix}{qid} or {prefix}{qid}[] for the option or options

        // If not response-capable, then there is nothing to do.
        //if (!$this->response_capable) {return true;}

        // If comments are requested, then get the question details.
        if (!empty($this->commentbox)) {
            $comment_name = $this->form_prefix_name . 'comment';
            if ($result = xarVarFetch($comment_name, 'pre:left:1000:trim:passthru:str', $comment, '', XARVAR_NOT_REQUIRED)) {
                $this->response['value3'] = $comment;
            }
        }

        // If multiselect, then fetch an array, else fetch a single value.
        $name = $this->form_prefix_name;
        //echo " name=$name "; var_dump($GLOBALS['_POST']);
        if ($this->multiselect) {
            $result = xarvarFetch($name, 'list:str', $value, array(), XARVAR_NOT_REQUIRED);
            $value = implode($this->multi_select_separator, $value);
        } else {
            $result = xarvarFetch($name, 'str', $value, '', XARVAR_NOT_REQUIRED);
        }
        $this->response['value1'] = $value;
        //echo " SUBMIT "; var_dump($value);
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
            'name' => 'question_multichoice_' . $type['qtid'],
            'label' => 'Question Multichoice',
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
            'multiselect' => array('label'=>'Multiselect', 'type'=>'checkbox', 'default'=>'0', 'validation'=>''),
            'format' => array('label'=>'Format', 'type'=>'dropdown', 'default'=>'combo', 'validation'=>'combo,Combo or drop-down list;tickbox,Checkboxes or radio items'),
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