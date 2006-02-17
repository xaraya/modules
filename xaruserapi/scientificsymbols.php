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

function surveys_userapi_scientificsymbols($args) {
    $result = $args;

    // Note: subscript-N is entity number 8320+N (e.g. &#8322; for subscript 2 - but not working on IE5.5!)
    if (is_string($args)) {
        $result = str_replace(
            array(
                'm3', 'Nm3',
                'O2', 'CO2', 'SO2',
                'NOX', 'AOX',
                'oC'
            ),
            array(
                'm&sup3;', 'Nm&sup3;',
                'O2', 'CO2', 'SO2',
                'NOx', 'AOx',
                '&deg;C'
            ),
            $args
        );
    }

    return $result;
}

?>
