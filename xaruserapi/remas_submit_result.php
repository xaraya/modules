<?php
/**
 * Extract the result from a remas form POST.
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
 * Extract the result from a remas form POST.
 *
 * IN:-
 *  data: return data to process
 * OUT:-
 *  status: the overall status
 *  message: the short message
 *  detail: long message lines
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
 * @deprecated Soon 2005 Specific to one case
 */

function surveys_userapi_remas_submit_result($args) {
    extract($args);

    // Split the returned page into lines.
    $lines = preg_split('/[\r\n]+|<br[\/\s]*>/i', $data);

    // Initialise the return values.
    $status = '';
    $message = '';
    $detail = array();

    // State machine: work through the lines, one at a time.
    $state = 'HEADER';
    foreach ($lines as $lineno => $line) {
        $line = trim($line);
        if ($line == '') {
            // Skip over blank lines.
            continue;
        }
        //echo " $state=$line ";
        switch ($state) {
            case 'HEADER':
                // Processing the header.
                // We are only interested in the first line that indicates the result.
                if (preg_match('/^\*RESULT/i', $line)) {
                    $line = preg_replace('/^\*RESULT[\s]*/i', '', $line);
                    $line = preg_split('/\s+/', $line, 2);
                    $status = strtoupper($line[0]);
                    $message = (isset($line[1]) ? $line[1] : '');
                    $state = 'DETAIL';
                }
                break;
            case 'DETAIL':
                // Read zero or more detail lines, until we come to the '*END' string.
                if (preg_match('/^\*END/i', $line)) {
                    $state = 'TRAILER';
                } else {
                    $detail[] = $line;
                }
            case 'TRAILER':
                // We don't want any of the trailer lines.
                // Just ignore them.
                break;
            default:
                //
                break;
        };
    }

    //echo "status=$status<br/>message=$message<br/>detail=<br/>" . implode('<br/>', $detail);
    //echo "result=" . $result;
    //echo "<pre>"; var_dump($lines); echo "</pre>";

    return array(
        'status' => $status,
        'message' => $message,
        'detail' => $detail
    );
}

?>