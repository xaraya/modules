<?php
/**
 * Question type 'linkedsurvey'
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
 * Question type 'linkedsurvey'
 *
 * Options from the list:
 *  x = option value
 *  x = option text (default language)
 *  x_{lang} = option text (alt languages)
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *  listname = list name ('lists' module)
 *  desc = question text (default language)
 *  desc_{lang} = question text (alt languages)
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

function surveys_questionsapi_linkedsurvey($args) {
    if (!empty($args)) {
        return new surveys_questionsapi_linkedsurvey($args);
    } else {
        return 'surveys_questionsapi_linkedsurvey';
    }
}

// Load the default class for extending.
xarModAPIfunc('surveys', 'questions', 'default');

// The multichoice object
class surveys_questionsapi_linkedsurvey extends surveys_questionsapi_default
{
    // Various properties.
    var $survey_id = NULL;
    var $questionname = NULL;

    // Overriding flags.
    var $object_name = 'linkedsurvey';
    var $response_capable = true;

    // Constructor.
    function surveys_questionsapi_linkedsurvey(&$args) {
        // Default initialisation first
        $this->surveys_questionsapi_default($args);

        // If no response has been provided so far, then set the defaults.
        if ($this->response_capable && empty($this->response)) {
            // Set default value.
            $this->response = array(
                'value1' => $this->dbquestion['default_value'],
                'value2' => '',
                'value3' => NULL,
                'status' => 'NORESPONSE',
                'rtid' => $this->dbquestion['rtid'],
                'rid' => NULL,
                'qid' => $this->dbquestion['qid'],
                'qtid' => $this->dbquestion['qtid']
            );
        }

        // Move the dd properties of the question to the object properties.
        if (isset($this->dbquestion['dd']['surveyid'])) {
            $this->survey_id = $this->dbquestion['dd']['surveyid'];
        }
        if (!empty($this->dbquestion['dd']['questionname'])) {
            $this->questionname = $this->dbquestion['dd']['questionname'];
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
            $question_text = $this->dbquestion['question_desc'];
        } else {
            $question_text = xarML('No question text');
        }

        // If readonly, then make sure we only display for 'output'.
        if ($this->readonly && $this->target != 'output') {$this->target = 'output';}

        if ($this->target == 'input') {
            // Form input rendering.

            // Get a list of user surveys for the current user.
            // TODO: if no complete surveys, then check if there is a survey in progress.
            $user_surveys = xarModAPIfunc(
                'surveys', 'user', 'getusersurveys',
                array('sid' => $this->survey_id, 'status' => 'COMPLETE', 'current_user' => true)
            );

            // Get the value of the selected question (to identify each survey).

            foreach($user_surveys as $key => $user_survey) {
                if (!empty($this->questionname)) {
                    $response = xarModAPIfunc(
                        'surveys', 'user', 'getquestionresponse',
                        array('usid' => $user_survey['usid'], 'name' => $this->questionname)
                    );
                    if (isset($response)) {
                        // TODO: perhaps pass the whole response in, so access
                        // to value2, value3 etc. is available.
                        $user_surveys[$key]['label'] = $response['value1'];
                    } else {
                        $user_surveys[$key]['label'] = $user_survey['name'] . ' ('.$user_survey['usid'].')';
                    }
                } else {
                    $user_surveys[$key]['label'] = $user_survey['name'] . ' ('.$user_survey['usid'].')';
                }
                $user_surveys[$key]['id'] = $this->form_prefix_id . '_' . $user_survey['usid'];
            }

            // TODO: consider passing the object into the template, then all
            // additional DD values etc will be there by default.
            $template_data = $this->default_render_params();
            $template_data['question_text'] = &$question_text;
            $template_data['user_surveys'] = $user_surveys;
            $template_data['current_value'] = (isset($this->response['value1']) ? $this->response['value1'] : '');
            $template_data['survey_id'] = $this->survey_id;
            $template_data['submit_hidden'] = $this->submit_hidden;

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }

        if ($this->target == 'output') {
            if (isset($this->response['value1'])) {
                $user_survey = xarModAPIfunc(
                    'surveys', 'user', 'getusersurvey',
                    array('usid' => $this->response['value1'])
                );
                if (!empty($this->questionname)) {
                    $response = xarModAPIfunc(
                        'surveys', 'user', 'getquestionresponse',
                        array('usid' => $user_survey['usid'], 'name' => $this->questionname)
                    );
                    $user_survey['label'] = (isset($response['value1']) ? $response['value1'] : '');
                }
            } else {
                $user_survey = array();
            }

            // Get the summary for the survey.
            $summary = xarModAPIfunc(
                'surveys', 'user', 'usersurveyidentity',
                array(
                    'newline' => '<br />',
                    'usid' => $user_survey['usid'],
                    'template' => $user_survey['summary_template']
                )
            );

            // Format for a report.
            $template_data = $this->default_render_params();
            $template_data['question_text'] = &$question_text;
            $template_data['usid'] = (isset($this->response['value1']) ? $this->response['value1'] : '');
            $template_data['user_survey'] = $user_survey;
            $template_data['survey_id'] = $this->survey_id;
            $template_data['summary'] = $summary;

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
        if (!isset($this->response['value1']) || $this->response['value1'] == '') {
            // If mandatory, then fail.
            if ($this->dbquestion['mandatory']) {
                $this->error = $this->errors['mandatory'];
                $this->response['status'] = 'INVALID';
            }
        }

        // Validate against the user surveys.
        $user_survey = xarModAPIfunc(
            'surveys', 'user', 'getusersurvey',
            array('usid' => $this->response['value1'])
        );

        if (empty($user_survey)) {
            $this->error = xarML('User survey ID is invalid');
            $this->response['status'] = 'INVALID';
        } else {
            // Get the identity question response from the survey.
            if (!empty($this->questionname)) {
                $response_text = xarModAPIfunc(
                    'surveys', 'user', 'getquestionresponse',
                    array('usid' => $user_survey['usid'], 'name' => $this->questionname)
                );
                $this->response['value2'] = (isset($response_text['value1']) ? $response_text['value1'] : '');
            } else {
                $this->response['value2'] = xarML('no identity');
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

        // The length is set to 200 as that is the max database column size.
        // Trim and truncate the input before we start applying custom validation to it.
        if ($result = xarVarFetch($name, 'id', $usid, '', XARVAR_NOT_REQUIRED)) {
            $this->response['value1'] = $usid;
            $status = true;
        }

        return $status;
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
            'name' => 'question_'.$this->object_name.'_' . $type['qtid'],
            'label' => 'Question Linked Survey',
            'itemtype' => $type['qtid'],
            'type' => 'Q'
        );

        // Fetch and/or create the DD object.
        // TODO: could the object and properties be merged into one?
        $object = $this->_get_create_dd_object($objectdef);

        // Now we have the DD object for the question type.
        // Time to create some properties.

        // Define the properties.
        // The question name identifies a question in the selected survey that provides some
        // identity for that survey - something the user has entered to distinguish
        // different instances of the survey.
        $propertydefs = array(
            'surveyid' => array('label'=>'Survey ID', 'type'=>'textbox', 'default'=>'', 'validation'=>'id'),
            'questionname' => array('label'=>'Question name', 'type'=>'textbox', 'default'=>'', 'validation'=>'')
        );

        // Create the dd properties, where they do not yet exist.
        $this->_create_dd_properties($object, $propertydefs);

        // There are no DD properties for the response type.

        return true;
    }
}

?>