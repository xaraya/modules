<?php
/**
 * Surveys table definitions function
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
 * Short Description [REQUIRED one line description]
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
/*
 * Question type 'multichoice'
 *
 * Data definition:
 *  listname = list name ('lists' module)
 *  desc = question text (default language)
 *  desc_{lang} = question text (alt languages)
 * Options from the list:
 *  x = option value
 *  x = option text (default language)
 *  x_{lang} = option text (alt languages)
 */

function surveys_questionsapi_boilerplate($args) {
    if (!empty($args)) {
        return new surveys_questionsapi_boilerplate($args);
    } else {
        return 'surveys_questionsapi_boilerplate';
    }
}

// Load the default class for extending.
xarModAPIfunc('surveys', 'questions', 'default');

// The multichoice object
class surveys_questionsapi_boilerplate extends surveys_questionsapi_default
{
    // Overriding flags.
    var $object_name = 'boilerplate';
    var $response_capable = false;
    var $valid = true;

    // Constructor.
    function surveys_questionsapi_multichoicelist(&$args) {
        // General flags for this question type.

        // Default initialisation first
        $this->surveys_questionsapi_default($args);
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
        if (isset($this->dbquestion['dd']['question_desc' . $this->lang_suffix]) && $this->dbquestion['dd']['question_desc' . $this->lang_suffix] != '') {
            $text = $this->dbquestion['dd']['question_desc' . $this->lang_suffix];
        }
        if (empty($text)) {
            $text = $this->dbquestion['question_desc'];
        }

        if ($this->target == 'output' || $this->target == 'input') {
            $template_data = $this->default_render_params();
            $template_data['question_text'] = &$text;

            return xarTplModule('surveys', $this->target, $this->object_name, $template_data, $this->template);
        }

        // Some other unsupported target.
        return '';
    }
}

?>