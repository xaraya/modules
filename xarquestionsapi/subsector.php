<?php
/**
 * Class Question type 'subsector'
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Question type 'subsector'
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

function surveys_questionsapi_subsector($args) {
    if (!empty($args)) {
        return new surveys_questionsapi_subsector($args);
    } else {
        return 'surveys_questionsapi_subsector';
    }
}

// Load the default class for extending.
xarModAPIfunc('surveys', 'questions', 'default');

// The multichoice object
class surveys_questionsapi_subsector extends surveys_questionsapi_default
{
    // List data.
    var $sectors = NULL;
    var $subsectors = NULL;

    // Various properties.

    // Overriding flags.
    var $object_name = 'subsector';
    var $response_capable = true;

    // Constructor.
    function surveys_questionsapi_subsector(&$args) {
        // Default initialisation first
        $this->surveys_questionsapi_default($args);

        // If no response has been provided so far, then set the defaults.
        if ($this->response_capable && empty($this->response)) {
            // Set default value.
            $this->response = array(
                'value1' => '', //$this->dbquestion['default_value']
                'value2' => '',
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
        //if (isset($this->dbquestion['dd']['listname'])) {
        //    $this->listname = $this->dbquestion['dd']['listname'];
        //}
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

        // TODO: this can be done generically in the render initialisation.
        if (isset($this->dbquestion['dd']['question_desc' . $this->lang_suffix]) && $this->dbquestion['dd']['question_desc' . $this->lang_suffix] != '') {
            $question_text = $this->dbquestion['dd']['question_desc' . $this->lang_suffix];
        }

        // If readonly, then make sure we only display for 'output'.
        if ($this->readonly && $this->target != 'output') {$this->target = 'output';}

        if ($this->target == 'input') {
            // Form input rendering.
            //var_dump($this->dbquestion);

            // Get values for the sectors drop-down.
            $sectors = array();
            $serial = 0;
            foreach ($this->sectors['items'] as $itemkey => $item) {
                $sectors[] = array(
                    'value' => $item['item_code'],
                    'label' => $item['item_long_name'],
                    'id' => $this->form_prefix_id . '_sector' . ($serial+=1)
                );
            }
            array_unshift(
                $sectors,
                array(
                    'value' => '',
                    'label' => xarML('-- please select (if possible) --'),
                    'id' => $this->form_prefix_id . '_sector_none'
                )
            );
            //var_dump($this->sectors);

            // Get values for the sub-sectors drop-down.
            $subsectors = array();
            $serial = 0;
            foreach ($this->subsectors['items'] as $itemkey => $item) {
                $subsectors[] = array(
                    'value' => $item['item_code'],
                    'label' => $item['item_long_name'] . ' (' . $item['item_code'] . ')',
                    'id' => $this->form_prefix_id . '_sector' . ($serial+=1),
                    'sector' => $item['item_short_name']
                );
            }

            // Set the current value.
            $current_subsector = $this->response['value1'];
            $current_sector = $this->response['value2'];
            $current_custom = $this->response['value3'];

            // TODO: consider passing the object into the template.
            $template_data = $this->default_render_params();
            $template_data['question_text'] = &$question_text;
            $template_data['sectors'] = &$sectors;
            $template_data['current_sector'] = $current_sector;
            $template_data['name_sector'] = $this->form_prefix_name . '_sector';
            $template_data['id_sector'] = $this->form_prefix_id . '_sector';
            $template_data['subsectors'] = &$subsectors;
            $template_data['current_subsector'] = $current_subsector;

            $template_data['name_custom'] = $this->form_prefix_name . '_custom';
            $template_data['id_custom'] = $this->form_prefix_id . '_custom';
            $template_data['current_custom'] = $current_custom;

            $template_data['submit_hidden'] = $this->submit_hidden;

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }

        if ($this->target == 'output') {
            // Format for a report.
            $subsector = $this->response['value1'];
            $sectorid = $this->response['value2'];
            $othersector = $this->response['value3'];

            $template_data = $this->default_render_params();
            $template_data['question_text'] = &$question_text;
            $template_data['sector'] = (isset($this->sectors['items'][$sectorid]['item_long_name']) ? $this->sectors['items'][$sectorid]['item_long_name'] : '');
            $template_data['subsector'] = (isset($this->subsectors['items'][$subsector]['item_long_name']) ? $this->subsectors['items'][$subsector]['item_long_name'] : '');
            $template_data['nace'] = $subsector;
            $template_data['othersector'] = $othersector;

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }

        // Some other unsupported target.
        return '';
    }

    // Validate the choice(s).
    // value1: subsector
    // value2: sector
    // value3: other sector
    function validate() {
        // Get the sectors and sub-sector lists.
        $this->_get_list_data();

        // If no response, then fail.
        if (empty($this->response)) {return false;}

        // Start by assuming validation will succeed.
        $this->response['status'] = 'COMPLETE';

        // Get sector value and validate it.
        $sector = $this->response['value2'];
        if ($sector == '' || !array_key_exists($sector, $this->sectors['items'])) {
            $this->response['value2'] = '';
            // Reset the sub-sector if the sector has failed or is empty.
            $this->response['value1'] = '';
        }

        // Get sub-sector value and validate it.
        $subsector = $this->response['value1'];

        if (!isset($this->subsectors['items'][$subsector])) {
            // Reset the sub-sector if it is not in the list.
            $this->response['value1'] = '';
        } else {
            // If the sub-sector is in the list, then make sure it is valid
            // for the sector.
            if ($this->subsectors['items'][$subsector]['item_short_name'] != $sector) {
                // Not a valid sub-sector for this sector.
                $this->response['value1'] = '';
            }
        }

        // If a sub-sector has been selected, then null out the 'other' (custom) sector.
        if (!empty($this->response['value1']) || !empty($this->response['value2'])) {
            $this->response['value3'] = '';
        }

        // If just the sector is set, then return a message to inform that a sub-sector
        // needs to be selected. If there is just one possible value, then select it
        // without returning a message.
        if ($this->response['value2'] != '' && $this->response['value1'] == '') {
            $optioncount = 0;
            foreach($this->subsectors['items'] as $subsector_item) {
                if ($subsector_item['item_short_name'] == $sector) {
                    $optioncount += 1;
                    $auto_select = $subsector_item['item_code'];
                }
            }

            if ($optioncount == 1) {
                $this->response['value1'] = $auto_select;
            } else {
                $this->error = xarML('please select a sub-sector for this sector');
                $this->response['status'] = 'INVALID';
            }
        }

        // The sub-sector value is empty (not set or NULL) AND no custom sector was supplied
        if ($this->response['status'] != 'INVALID' && (!isset($this->response['value1']) || $this->response['value1'] == '') && (!isset($this->response['value3']) || $this->response['value3'] == '')) {
            // If mandatory, then fail.
            if ($this->dbquestion['mandatory']) {
                $this->error = $this->errors['mandatory'];
                $this->response['status'] = 'INVALID';
            }
        }

        $this->valid = ($this->response['status'] == 'INVALID' ? false : true);
        return $this->valid;
    }

    // Read the submitted response from the page.
    function submit() {
        $name_sector = $this->form_prefix_name . '_sector';
        $name_subsector = $this->form_prefix_name;
        $name_custom = $this->form_prefix_name . '_custom';

        $result = xarvarFetch($name_sector, 'int', $sector, '', XARVAR_NOT_REQUIRED);
        if ($result) {
            $this->response['value2'] = $sector;
        }

        $result = xarvarFetch($name_subsector, 'str:0:10', $subsector, '', XARVAR_NOT_REQUIRED);
        if ($result) {
            $this->response['value1'] = $subsector;
        }

        $result = xarvarFetch($name_custom, 'pre:trim:left:200:passthru:str:0:200', $custom, '', XARVAR_NOT_REQUIRED);
        if ($result) {
            $this->response['value3'] = $custom;
        }
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
            'name' => 'question_subsector_' . $type['qtid'],
            'label' => 'Question Sub-sector',
            'itemtype' => $type['qtid'],
            'type' => 'Q'
        );

        // Fetch and/or create the DD object.
        //$object = $this->_get_create_dd_object($objectdef);

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
        //$this->_create_dd_properties($object, $propertydefs);

        // There are no DD properties for the response type.

        return true;
    }

    function _get_list_data() {
        // Fetch the list items.
        // TODO: error if lists module not present?
        // TODO: list($list) = ... (but suppress errors?)

        if (isset($this->sectors)) {return true;}

        // Get the list item data - both list details and items.
        // Index the lists by 'index' (we only want one - index 0)
        // Index the items by 'code' - makes validation easier.

        // Sectors
        $list = xarModAPIfunc(
            'lists', 'user', 'getlistitems',
            array(
                'list_name'=>'sectors',
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
        $this->sectors =& $list[0];

        // Sub-sectors
        $list = xarModAPIfunc(
            'lists', 'user', 'getlistitems',
            array(
                'list_name'=>'subsectors',
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
        $this->subsectors =& $list[0];

        return true;
    }
}

?>
