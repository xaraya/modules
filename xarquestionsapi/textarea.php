<?php
/**
 * Question type 'textarea'
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
 * Question type 'textarea'
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

function surveys_questionsapi_textarea($args) {
    if (!empty($args)) {
        return new surveys_questionsapi_textarea($args);
    } else {
        return 'surveys_questionsapi_textarea';
    }
}

// Load the default class for extending.
xarModAPIfunc('surveys', 'questions', 'default');

// The multichoice object
class surveys_questionsapi_textarea extends surveys_questionsapi_default
{
    // Various properties.

    // Overriding flags.
    var $object_name = 'textarea';
    var $response_capable = true;

    // Constructor.
    function surveys_questionsapi_textarea(&$args) {
        // Default initialisation first
        $this->surveys_questionsapi_default($args);

        // If no response has been provided so far, then set the defaults.
        if ($this->response_capable && empty($this->response)) {
            // Set default value.
            $this->response = array(
                'value1' => NULL,
                'value2' => NULL,
                'value3' => $this->dbquestion['default_value'],
                'status' => 'NORESPONSE',
                'rtid' => $this->dbquestion['rtid'],
                'rid' => NULL,
                'qid' => $this->dbquestion['qid'],
                'qtid' => $this->dbquestion['qtid']
            );
        }
    }

    /* Render the question.
     * Render target are:
     * - input (for an input/data entry form)
     * - output (for a report)
     * Templates are:
     * - {target}-{typename}[-{custom}]
     * e.g. input-multichoicelist.xt, output-checkbox-special.xt
     */
    function render($args) {
        // Handle the parameters.
        surveys_questionsapi_default::render($args);

        // Use the question description as the question text.
        if (!empty($this->dbquestion['question_desc'])) {
            // Multi-lingual stuff: use the alt-language text if available.
            $question_text = (isset($this->dbquestion['dd']['question_desc'.$this->lang_suffix]) && !empty($this->dbquestion['dd']['question_desc'.$this->lang_suffix]) ? $this->dbquestion['dd']['question_desc'.$this->lang_suffix] : $this->dbquestion['question_desc']);
        } else {
            $question_text = xarML('No question text');
        }

        // If readonly, then make sure we only display for 'output'.
        if ($this->readonly && $this->target != 'output') {$this->target = 'output';}

        if ($this->target == 'input') {
            // Form input rendering.

            // Get the current value.
            $current_value = $this->response['value3'];

            // TODO: consider passing the object into the template, then all
            // additional DD values etc will be there by default.
            $template_data = $this->default_render_params();
            $template_data['question_text'] = &$question_text;
            $template_data['current_value'] = $current_value;
            $template_data['submit_hidden'] = $this->submit_hidden;

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }

        if ($this->target == 'output') {
            // Format for a report.
            $template_data = $this->default_render_params();
            $template_data['question_text'] = &$question_text;
            $template_data['value'] = (isset($this->response['value3']) ? $this->response['value3'] : '');

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }

        // Some other unsupported target.
        return '';
    }

    // Validate the choice(s).
    function validate() {
        // If no response, then fail.
        if (empty($this->response)) {return false;}

        // Start by assuming the item is valid.
        $this->response['status'] = 'COMPLETE';

        // The value is empty (not set or NULL)
        if (!isset($this->response['value3']) || $this->response['value3'] == '') {
            // If mandatory, then fail.
            if ($this->dbquestion['mandatory']) {
                $this->error = $this->errors['mandatory'];
                $this->response['status'] = 'INVALID';
            }
        }

        $this->valid = ($this->response['status'] == 'COMPLETE' ? true : false);
        return $this->valid;
    }

    // Read the submitted response from the page.
    function submit() {
        $status = false;

        // The name of the form item.
        $name = $this->form_prefix_name;

        // TODO: make the truncate length of the string user-defined.
        if ($result = xarVarFetch($name, 'pre:trim:left:1000:trim:passthru:str', $text, '', XARVAR_NOT_REQUIRED)) {
            $this->response['value3'] = $text;
            $status = true;
        }

        return $status;
    }


    /* Initialise the question type. Includes:
     * - Question and response types if they do not exist
     * - DD object and properties
     */
    function init_question_type() {
        // Get and/or create the question type (and optional response type).
        $type = $this->_get_create_question_types();
        if (empty($type)) {return;}

        // Prepare the object data for the question type.
        $objectdef = array(
            'name' => 'question_'.$this->object_name.'_' . $type['qtid'],
            'label' => 'Question Textarea',
            'itemtype' => $type['qtid'],
            'type' => 'Q'
        );

        // No DD objects to create.
        //return true;

        // Fetch and/or create the DD object.
        // TODO: could the object and properties be merged into one?
        $object = $this->_get_create_dd_object($objectdef);

        // Now we have the DD object for the question type.
        // Time to create some properties.

        // Define the properties.
        $propertydefs = array(
            //'listname' => array('label'=>'List name', 'type'=>'listslist', 'default'=>'', 'validation'=>""),
            //'multiselect' => array('label'=>'Multiselect', 'type'=>'checkbox', 'default'=>'0', 'validation'=>''),
            //'format' => array('label'=>'Format', 'type'=>'dropdown', 'default'=>'combo', 'validation'=>'combo,Combo or drop-down list;tickbox,Checkboxes or radio items'),
            //'commentbox' => array('label'=>'Comment box', 'type'=>'checkbox', 'default'=>'0', 'validation'=>'')
        );

        // Create the dd properties, where they do not yet exist.
        $this->_create_dd_properties($object, $propertydefs);

        // There are no DD properties for the response type.

        return true;
    }
}
?>