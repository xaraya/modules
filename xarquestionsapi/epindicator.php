<?php
/**
 * Question type 'epindicator'
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team 
 */
/*
 * Question type 'epindicator'
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 * Data definition:
 *  listname = list name ('lists' module)
 *  desc = question text (default language)
 *  desc_{lang} = question text (alt languages)
 * Options from the list:
 *  x = option value
 *  x = option text (default language)
 *  x_{lang} = option text (alt languages) *
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

function surveys_questionsapi_epindicator($args) {
    if (!empty($args)) {
        return new surveys_questionsapi_epindicator($args);
    } else {
        return 'surveys_questionsapi_epindicator';
    }
}

// Load the default class for extending.
xarModAPIfunc('surveys', 'questions', 'default');

// The main object.
class surveys_questionsapi_epindicator extends surveys_questionsapi_default
{
    // Overriding flags.
    var $object_name = 'epindicator';
    var $response_capable = true;
    var $valid = true;

    // Various properties.
    var $nace_code = NULL;
    var $ep_code = NULL;
    var $listname = NULL;
    var $attributes = array();

    // Constructor.
    function surveys_questionsapi_epindicator(&$args) {
        // Default initialisation first
        $this->surveys_questionsapi_default($args);

        // Get the sub-sector. We need to know which question in the
        // current survey holds the sub-sector.
        if (!empty($this->dbquestion)) {
            if (empty($this->dbquestion['dd']['epsection'])) {
                // TODO: error message
                echo "Missing DD property or value 'epsection'";
                return;
            }
            $this->ep_code = $this->dbquestion['dd']['epsection'];

            $nace_code = $this->_get_nace_code();
            $this->listname = $this->ep_code . '-' . $this->nace_code;

            // Get the attribute details.
            // The details are stored in a list so they are translatable.
            $attributes = xarModAPIfunc(
                'lists', 'user', 'getlistitems',
                array(
                    'list_name' => 'ep_attributes',
                    'itemkey' => 'code',
                    'items_only' => true,
                    'lang_suffix' => $this->lang_suffix
                )
            );
            $this->attributes =& $attributes;

            // Database stuff.
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

            // If this is the first EP question in a group (the mandatory flag will be set)...

            if ($this->dbquestion['mandatory'] && !empty($this->nace_code)) {
                // ...then check if this is a mandatory attribute.
                $query = 'SELECT subsector_attributes.validation'
                    . ' FROM remas_ep_subsector_attributes AS subsector_attributes'

                    . ' INNER JOIN remas_ep_subsectors AS subsectors'
                    . ' ON subsectors.id = subsector_attributes.subsector_id'

                    . ' INNER JOIN remas_ep_attributes AS attributes'
                    . ' ON attributes.id = subsector_attributes.attribute_id'

                    . ' WHERE attributes.code = ? AND subsectors.nace = ?';

                // Execute the query.
                $result = $dbconn->execute($query, array($this->ep_code, $this->nace_code));
                if (!$result) {return;}
                if ($result->EOF) {
                    $validation = 'OPTIONAL';
                } else {
                    list($validation) = $result->fields;
                }
                // Reset the 'mandatory' status if the sub-sector attribute does not indicate
                // this attribute is mandatory.
                if ($validation == 'OPTIONAL') {
                    $this->dbquestion['mandatory'] = false;
                }
            } else {
                $validation = 'OPTIONAL';
            }

            // If no response has been provided so far, then set the defaults.
            if ($this->response_capable && empty($this->response)) {
                // Set default value.
                $this->response = array(
                    'value1' => NULL, //$this->dbquestion['default_value'],
                    'value2' => NULL,
                    'value3' => '',
                    'status' => 'NORESPONSE',
                    'rtid' => $this->dbquestion['rtid'],
                    'rid' => NULL,
                    'qid' => $this->dbquestion['qid'],
                    'qtid' => $this->dbquestion['qtid']
                );
            }
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

        // Use the question description as the boiler-late text.
        // If there is a DD language-suffix version, then use that instead.
        if (isset($this->dbquestion['dd']['question_desc' . $this->lang_suffix])) {
            $text = $this->dbquestion['dd']['question_desc' . $this->lang_suffix];
        }
        if (empty($text)) {
            $text = $this->dbquestion['question_desc'];
        }

        // If readonly, then make sure we only display for 'output'.
        if ($this->readonly && $this->target != 'output') {$this->target = 'output';}

        // Database stuff.
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();

        if ($this->target == 'input') {
            // We need a list of indicators, based on two items of information:
            // 1. The sub-sector (obtained from another question)
            // 2. The EP number (set for the question)

            // Each EP question has a 'disable' option that can be used if
            // the user no longer wants to include an indicator. The 'disable'
            // value also suppresses the inclusion of later EP questions.
            // If an EP attribute is mandatory, then the 'disable' option is
            // not available on the *first* question in a group. A flag indicates
            // which question is the first (the 'mandatory' flag can indicate this).

            //var_dump($this->nace_code);
            //var_dump($this->ep_code);
            //var_dump($this->listname);

            // Get the list data
            $listitems = xarModAPIfunc(
                'lists', 'user', 'getlistitems',
                array(
                    'list_name' => $this->listname,
                    'itemkey' => 'code',
                    'items_only' => true,
                    'lang_suffix' => $this->lang_suffix
                )
            );

            // If the list is empty, then this question must be made optional.
            if (empty($listitems)) {
                $this->dbquestion['mandatory'] = false;
            }

            // If optional, then add a further 'optional' item to the list.
            if (!$this->dbquestion['mandatory']) {
                array_unshift($listitems, array('item_code' => '', 'item_desc' => xarML('-- No Selection --')));
            }
            //var_dump($listitems);

            $template_data = $this->default_render_params();
            $template_data['attribute'] = $this->attributes[$this->ep_code];
            $template_data['listitems'] = &$listitems;
            $template_data['indicator_name'] = $this->form_prefix_name . '_ind';
            $template_data['indicator_id'] = $this->form_prefix_id . '_ind';
            $template_data['value_name'] = $this->form_prefix_name . '_val';
            $template_data['value_id'] = $this->form_prefix_id . '_val';
            $template_data['comment_name'] = $this->form_prefix_name . '_comment';
            $template_data['comment_id'] = $this->form_prefix_id . '_comment';
            $template_data['submit_hidden'] = $this->submit_hidden;
            $template_data['current_value'] = $this->response['value1'];
            $template_data['current_indicator'] = $this->response['value2'];
            $template_data['current_comment'] = $this->response['value3'];

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }


        if ($this->target == 'output') {
            $listitems = xarModAPIfunc(
                'lists', 'user', 'getlistitems',
                array(
                    'list_name' => $this->listname,
                    'itemkey' => 'code',
                    'items_only' => true,
                    'lang_suffix' => $this->lang_suffix
                )
            );
            //var_dump($listitems); echo " i=".$this->response['value2'];
            $template_data = $this->default_render_params();
            $template_data['attribute'] = $this->attributes[$this->ep_code];
            $template_data['indicator'] = !empty($listitems[$this->response['value2']]) ? $listitems[$this->response['value2']] : NULL;
            $template_data['value'] = $this->response['value1'];
            $template_data['comment'] = $this->response['value3'];

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }

        // Some other unsupported target.
        return '';
    }

    // Validate the EP response.
    function validate() {
        // If no response, then fail.
        if (empty($this->response)) {return false;}

        // If mandatory, then a value must be given, but only if there
        // are values to select from.
        if ($this->dbquestion['mandatory']) {
            if (empty($this->response['value2']) || $this->response['value1'] == '') {
                // Check there are list items to check from.
                $listitems = xarModAPIfunc(
                    'lists', 'user', 'getlistitems',
                    array(
                        'list_name' => $this->listname,
                        'itemkey' => 'code',
                        'items_only' => true,
                        'lang_suffix' => $this->lang_suffix
                    )
                );
                if (!empty($listitems)) {
                    $this->error = $this->errors['mandatory'];
                    $this->valid = false;
                    $this->response['status'] = 'INVALID';
                    return false;
                }
            }
        }

        // If an indicator is chosen, then a value must be supplied.
        if (!empty($this->response['value2']) && $this->response['value1'] == '') {
            $this->error = xarML('if an indicator is selected, then a value must be given too');
            $this->valid = false;
            $this->response['status'] = 'INVALID';
            return false;
        }

        // TODO: should these values always be numeric? Do that as an additional check. - Yes

        if (!empty($this->response['value1']) && !is_numeric($this->response['value1'])) {
            $this->error = xarML('the indicator value must be numeric');
            $this->valid = false;
            $this->response['status'] = 'INVALID';
            return false;
        }

        // If no indicator is chosen, then blank out the other fields.
        if (empty($this->response['value2'])) {
            $this->response['value1'] = '';
            $this->response['value3'] = '';
        }

        // Apply data-driven validation rules.
        // The indicator must be valid for the sub-sector.
        if (!empty($this->response['value2'])) {
            $nace_code = $this->_get_nace_code();

            // Database stuff.
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

            $query = 'SELECT subsector_attributes.validation'
                . ' FROM remas_ep_subsector_attributes AS subsector_attributes'

                . ' INNER JOIN remas_ep_subsectors AS subsectors'
                . ' ON subsectors.id = subsector_attributes.subsector_id'

                . ' INNER JOIN remas_ep_attributes AS attributes'
                . ' ON attributes.id = subsector_attributes.attribute_id'

                . ' INNER JOIN remas_ep_subsector_indicators AS subsector_indicators'
                . ' ON subsector_indicators.subsector_id = subsectors.id'
                . ' AND subsector_indicators.indicator_id = indicators.id'

                . ' INNER JOIN remas_ep_indicators AS indicators'
                . ' ON indicators.attribute_id = attributes.id'

                . ' WHERE attributes.code = ? AND subsectors.nace = ? AND subsector_indicators.indicator_id = ?';

            // Execute the query.
            $result = $dbconn->execute($query, array($this->ep_code, $this->nace_code, $this->response['value2']));
            if (!$result) {return;}
            if ($result->EOF) {
                // No rows, so the indicator ID is invalid.
                $this->error = xarML(
                    'indicator ID "#(1)" is invalid for sub-sector "#(2)" and EP section "#(3)"',
                    $this->response['value2'],
                    $this->nace_code,
                    $this->ep_code
                );
                $this->valid = false;
                $this->response['status'] = 'INVALID';
                return false;
            }
        }
        
        return $this->valid;
    }


    // Read the submitted response from the page.
    function submit() {
        // Get the indicator.
        $result = xarvarFetch($this->form_prefix_name . '_ind', 'str', $indicator, NULL, XARVAR_NOT_REQUIRED);
        $this->response['value2'] = $indicator;

        // Get the value.
        if (!empty($indicator)) {
            $result = xarvarFetch($this->form_prefix_name . '_val', 'str', $value, NULL, XARVAR_NOT_REQUIRED);
            $this->response['value1'] = $value;
        } else {
            $this->response['value1'] = NULL;
        }

        // Get the comment.
        $result = xarvarFetch($this->form_prefix_name . '_comment', 'pre:left:1000:trim:passthru:str', $comment, NULL, XARVAR_NOT_REQUIRED);
        $this->response['value3'] = $comment;

        return true;
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
            'name' => 'question_epindicator_' . $type['qtid'],
            'label' => 'Question EP Indicator',
            'itemtype' => $type['qtid'],
            'type' => 'Q'
        );

        // Fetch and/or create the DD object.
        $object = $this->_get_create_dd_object($objectdef);

        // Now we have the DD object for the question type.
        // Time to create some properties.

        // Define the properties.
        $propertydefs = array(
            'questionid' => array('label'=>'Sub-sector question', 'type'=>'questionslist', 'default'=>'', 'validation'=>""),
            'epsection' => array('label'=>'EP Section', 'type'=>'dropdown', 'default'=>'EP3', 'validation'=>'EP3,EP3;EP4,EP4;EP5,EP5;EP6,EP6;EP7,EP7;EP8,EP8;')
        );

        // Create the dd properties, where they do not yet exist.
        $this->_create_dd_properties($object, $propertydefs);

        // There are no DD properties for the response type.

        return true;
    }

    // Get the NACE code for this question.
    function _get_nace_code() {
        if (!empty($this->nace_code)) {
            return $this->nace_code;
        }

        if (empty($this->dbquestion['dd']['questionid'])) {
            // TODO: error message
            echo "Missing DD property or value 'questionid'";
            return;
        }

        // Get the question details holding the NACE code
        $sector_question = xarModAPIfunc(
            'surveys', 'user', 'getquestions',
            array('qid' => $this->dbquestion['dd']['questionid'], 'dd_flag' => false)
        );
        $sector_question = reset($sector_question);

        // Get the question response - it should contain NACE code.
        $response = xarModAPIfunc(
            'surveys', 'user', 'getquestionresponse',
            array(
                'name' => $sector_question['name'],
                'dd_flag' => false,
                'usid' => $this->dbquestion['usid']
            )
        );

        if (empty($response)) {
            // TODO: error message
            echo "NACE code not set in question";
            $this->nace_code = '';
        } else {
            // Set the NACE code property.
            // This is the user response to the 'NACE Code' question.
            $this->nace_code = $response['value1'];
        }

        return $this->nace_code;
    }
}

?>
