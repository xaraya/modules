<?php
/**
 * Question type 'default'
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @link http://xaraya.com/index.php/release/45.html
 * @author Surveys module development team
 */
/**
 * Question type 'default'
 *
 * Long Description [OPTIONAL one or more lines]
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

function surveys_questionsapi_default($args) {
    return true;
}

// Default class for all question objects.
class surveys_questionsapi_default
{
    // Initial question and response details from the database,
    // i.e. current stored values.
    // The response may be NULL if no response has been provided yet
    // or if none is required.
    // These two properties will remain constant throughout the life
    // of the object.
    var $dbquestion = NULL;
    var $dbresponse = NULL;

    // Current user response.
    // - For a new question, this is populated with the default.
    // - For a submitted question, this gets populated with the submitted details.
    // - For an error response, this is populated with the submitted, but erroneous, details.
    // The response property is a general register for temporarily holding the response.
    var $response = NULL;

    // Various flags.
    // The readonly flag is set if the survey is locked.
    var $response_capable = false;
    var $language = NULL;
    // The lang_suffix is appended to DD field names to over-ride the non-prefixed field.
    var $lang_suffix = NULL;
    var $country = NULL;
    var $object_name = NULL;
    var $valid = NULL;
    var $updated = false;

    // Error message for when validation fails.
    var $error = NULL;

    // Rendering flags.
    var $template = NULL;
    var $readonly = false;
    var $target = 'input';
    var $survey_editor = false;

    // Various constants/settings.
    // The prefixes are used to prefix all form items - both names and IDs.
    // The suffix will be the question ID for the ID, and the question ID
    // plus optional array characters for the name.
    // e.g. <input id="surveys_123_multi" name="surveys_456_checkbox[]" ... />
    var $form_prefix_id = 'surveys_{qid}_{object}';
    var $form_prefix_name = 'surveys_{qid}_{object}';
    var $multi_select_separator = ',';
    var $submit_hidden = NULL;

    // Constructor.
    // question: the question details
    // response: the current response details (response can be an element of question)
    // TODO: what happens here if we don't have a question object or even a question ID?
    function surveys_questionsapi_default(&$args) {
        if (isset($args['question'])) {
            $this->dbquestion = $args['question'];
        }

        // Set the readonly flag if necessary.
        // A question may be set to display its result on subsequent
        // pages by setting it to readonly mode when linked to groups
        // other than the first group.
        if ($this->dbquestion['readonly'] == 'Y') {
            $this->readonly = true;
        }

        // A question in a group can be given an over-riding template.
        // This allows, for example, some questions to be displayed in
        // full, and others to be simplified (e.g. in an address block,
        // where you may want the labels formatted to the left rather
        // than above).
        if (!empty($this->dbquestion['template'])) {
            $this->template = $this->dbquestion['template'];
        }

        // The response may be an element of the question.
        if (isset($this->dbquestion['response'])) {
            // Point the dbresponse to the element in the question.
            $this->dbresponse =& $this->dbquestion['response'];
            // This only unlinks the response from the question - the
            // $dbresponse property is still a handle for the response,
            // so it is not deleted.
            unset($this->dbquestion['response']);
        } elseif (isset($args['response'])) {
            $this->dbresponse = $args['response'];
        }

        // Add the question ID and object name to the ID and name prefixes.
        list($this->form_prefix_id, $this->form_prefix_name) = str_replace(
            array('{qid}', '{object}'),
            array($this->dbquestion['qid'], $this->object_name),
            array($this->form_prefix_id, $this->form_prefix_name)
        );

        // Create the name of the hidden submit item (to identify the question).
        $this->submit_hidden = $this->form_prefix_name . '_submit';

        // Copy the db response to the current response, if it exists.
        // The extending class will provide a default for 'response' if
        // we do not create one here.
        if (isset($this->dbresponse)) {
            $this->response = $this->dbresponse;
        }

        // Determine the current language.
        // The language is used as a prefix for question details,
        // such as the description, falling back to non-prefixed
        // name if the prefixed version is not set.
        $this->lang_suffix = xarModAPIfunc('surveys', 'user', 'getlanguagesuffix');

        // Set some common error messages.
        $this->errors['mandatory'] = xarML('this item is mandatory; a value or selection must be provided');
        $this->errors['notinlist'] = xarML('one or more values provided is not in the list of valid values');
    }

    // Render the question and current response.
    // This stub just handles the default parameters and stores them in the properties.
    function render($args) {
        extract($args);

        // Various flags can be over-ridden when rendering.
        // The flags are set permanently for the object.
        // TODO: to avoid side-effects, should these be temporary?
        if (isset($target)) {$this->target = $target;}
        if (isset($readonly)) {$this->readonly = $readonly;}
        if (isset($template)) {$this->template = $template;}
    }

    // Standard render parameters that are passed to every question template
    // in every render mode (i.e. every render target).
    function default_render_params() {
        return array (
            'qid' => $this->dbquestion['qid'],
            'question_name' => $this->dbquestion['question_name'],
            'mandatory' => $this->dbquestion['mandatory'],
            'status' => $this->response['status'],
            'readonly' => $this->readonly,
            'error' => $this->error,
            // Form item name and ID (over-ridden in some question types where suffixes are needed)
            'name' => $this->form_prefix_name,
            'id' => $this->form_prefix_id,
            // Pass any DD details through to the template too
            'dd' => (isset($this->dbquestion['dd']) ? $this->dbquestion['dd'] : array()),
            'survey_editor' => $this->survey_editor,
        );
    }


    // Validate the choice(s).
    function validate() {
        return true;
    }

    // Read the submitted response from the page.
    function submit() {
        return true;
    }

    // Import values directly into the question responses (bypassing the submit form).
    // A question type may override this if basic validation is required.
    // TODO: handle importing DD values here for question types that need more than
    // the standard value1 to value3.
    function import($args) {
        for($i = 1; $i <= 3; $i++) {
            if (isset($args['value'.$i])) {
                $this->response['value'.$i] = $args['value'.$i];
            }
        }
        return true;
    }

    // Initialise the question type (used when installing the question type).
    function init_question_type() {
        return true;
    }

    // TODO: the question type init methods below should be shifted out to
    // separate script to reduce the burden on this object script.

    // Return the database question type array for this question type.
    // If types have not been set up, then set them up here.
    // The question and response type names can be passed in, or left to default.
    function _get_create_question_types($args = array()) {
        extract($args);

        // Get the question type.
        $type = xarModAPIfunc(
            'surveys', 'user', 'gettype',
            array('type' => 'Q', 'object_name' => $this->object_name)
        );
        if (empty($type)) {
            // The type does not exist: create the types now.
            // Create the response type first (if needed)
            if ($this->response_capable) {
                $rtid = xarModAPIfunc(
                    'surveys', 'admin', 'createtype',
                    array(
                        'type' => 'R',
                        'name' => (isset($response_type_name) ? $response_type_name : xarML('Response #(1)', $this->object_name))
                    )
                );
            } else {
                $qtid = NULL;
            }
            // Now create the question type.
            $qtid = xarModAPIfunc(
                'surveys', 'admin', 'createtype',
                array(
                    'type' => 'Q',
                    'name' => (isset($question_type_name) ? $question_type_name : xarML('Question #(1)', $this->object_name)),
                    'response_type_id' => $rtid,
                    'object_name' => $this->object_name
                )
            );
            // Now fetch the type back again.
            $type = xarModAPIfunc(
                'surveys', 'user', 'gettype',
                array('type' => 'Q', 'object_name' => $this->object_name)
            );
        }

        return $type;
    }

    // Fetch the DD object for a question type.
    // Create the object if it does not exist.
    function _get_create_dd_object($args) {
        // Args required are: name, label, itemtype, type
        extract($args);

        $moduleid = xarModGetIDFromName('surveys');

        // Check the object does not already exist first (keyed on module/itemtype).
        $object = xarModAPIFunc(
            'dynamicdata', 'user', 'getobject',
            array('moduleid' => $moduleid, 'itemtype' => $itemtype)
        );

        // For some reason, 'getobject' will return a blank object rather than
        // a NULL if the object with module/itemtype details was not found.
        // Check the objectid property to see if this is a real DD object or not.
        if (empty($object) || !isset($object->objectid)) {
            // Prepare the object data.
            $objectdef = array(
                'name' => $name,
                'label' => $label,
                'moduleid' => $moduleid,
                'itemtype' => $itemtype,
                'urlparam' => ($type == 'Q' ? 'qid' : 'rid')
            );

            // Create the DD object.
            // TODO: error handling, in case the object does not get created.
            $objectid = xarModAPIFunc('dynamicdata', 'admin', 'createobject', $objectdef);
            $object = xarModAPIFunc(
                'dynamicdata', 'user', 'getobject',
                array('objectid' => $objectid)
            );
        }

        return $object;
    }

    // Create the properties for a DD object.
    // Takes two parameters: object and properties
    function _create_dd_properties(& $object, $propertydefs) {
        // If no properties to create, then nothing to do.
        if (empty($propertydefs)) {return true;}

        // Remove any properties that already exist in the object.
        // We are not going to attempt to update existing properties.
        $order = 0;
        if (isset($object->properties) && is_array($object->properties)) {
            foreach($object->properties as $name => $property) {
                if (isset($propertydefs[$name])) {unset($propertydefs[$name]);}
                if ($property->order > $order) {$order = $property->order;}
            }
        }

        // If there are any properties left to create, then add them to the DD object.
        if (count($propertydefs) > 0) {
            // Get property type/ID lookup.
            $proptypes = xarModAPIFunc('dynamicdata', 'user', 'getproptypes');
            $name2id = array();
            foreach ($proptypes as $propid => $proptype) {
                $name2id[$proptype['name']] = $propid;
            }

            foreach($propertydefs as $name => $propertydef) {
                // Convert the type name to its ID.
                if (!is_numeric($propertydef['type'])) {
                    if (isset($name2id[$propertydef['type']])) {
                        $propertydef['type'] = $name2id[$propertydef['type']];
                    } else {
                        $propertydef['type'] = 1;
                    }
                }

                // Set other property values.
                $order += 1;
                $propertydef['order'] = $order;
                $propertydef['name'] = $name;
                $propertydef['objectid'] = $object->objectid;
                $propertydef['moduleid'] = $object->moduleid;
                $propertydef['itemtype'] = $object->itemtype;

                // Now create the property.
                $propertyid = xarModAPIFunc('dynamicdata', 'admin', 'createproperty', $propertydef);
            }
        }

        return true;
    }

    // Store the current response details.
    // The response is stored against the user-survey (usid) and question ID (qid).
    // If the response ID (rid) is available, then an update can be done.
    // TODO: allow store of DD fields in the response too.
    // TODO: set a flag if details have been stored, so we can tell later
    // that changes have been made in the database.
    function store() {
        // If not response-capable, then there is nothing to store.
        if (!$this->response_capable) {return true;}
        //echo "STORE"; var_dump($this->response);

        // If the response has not been validated, then validate it now.
        // We don't care about the result, so long as it has been set.
        if (!isset($this->valid)) {
            $this->validate();
        }

        // If we have a response ID, then use it to update. x
        // If a response exists, then update it, otherwise create one.
        if (empty($this->response['rid'])) {
            // Do a last check, in case a response has been written.
            // This is important when importing and when a question
            // appears in more than one group.

            $check_response = xarModAPIfunc(
                'surveys', 'user', 'getquestionresponse',
                array('usid' => $this->dbquestion['usid'], 'qid' => $this->dbquestion['qid'])
            );
            if (!empty($check_response)) {
                $this->response = $check_response;
            }
        }

        if (!empty($this->response['rid'])) {
            $result = xarModAPIfunc(
                'surveys', 'admin', 'update',
                array(
                    'rid' => $this->response['rid'],
                    'status' /*'_notna'*/ => ($this->valid ? 'COMPLETE' : 'INVALID'),
                    'value1' => $this->response['value1'],
                    'value2' => $this->response['value2'],
                    'value3' => $this->response['value3']
                )
            );
            // TODO: hooks for updating an item rid of type rtid.
        } else {
            // No response ID, so create a new response.
            $result = xarModAPIfunc(
                'surveys', 'admin', 'createresponse',
                array(
                    'user_survey_id' => $this->dbquestion['usid'],
                    'question_id' => $this->dbquestion['qid'],
                    'status' => ($this->valid ? 'COMPLETE' : 'INVALID'),
                    'value1' => $this->response['value1'],
                    'value2' => $this->response['value2'],
                    'value3' => $this->response['value3']
                )
            );
        }

        // Set the update flag to indicate an update was performed.
        $this->updated = true;

        return true;
    }
}

?>