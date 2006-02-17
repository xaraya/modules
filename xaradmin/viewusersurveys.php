<?php
/**
 * Surveys overview
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
 * Overview of all current user surveys.
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     MichelV <michelv@xarayahosting.nl>
 * @param string $order  the string used                      [OPTIONAL A REQURIED]
 * @param int    $changestatus
 * @param newstatus, transfer, status, system_status,show_summary
 *
 * @return array usersurveys
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 *
 */

function surveys_admin_viewusersurveys() {

    if (!xarSecurityCheck('ModerateAssessment', 1, 'Assessment', 'All:All:All:All')) {
        return;
    }

    $order_columns = array(
        'usid', 'username', 'fullname', 'status', 'system_status', 'start_date', 'submit_date', 'closed_date', 'last_updated'
    );

    xarVarFetch('order',        'str', $order,          '', XARVAR_NOT_REQUIRED);
    xarVarFetch('changestatus', 'int', $changestatus,   0,  XARVAR_NOT_REQUIRED);
    xarVarFetch('newstatus',    'str', $newstatus,      '', XARVAR_NOT_REQUIRED);
    xarVarFetch('transfer',     'int', $transfer,       '', XARVAR_NOT_REQUIRED);

    xarVarFetch('status',       'str', $status,         NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('system_status','str', $system_status,  NULL, XARVAR_NOT_REQUIRED);

    xarVarFetch('show_summary', 'int:0:1', $show_summary, 0, XARVAR_NOT_REQUIRED);

    // Template data.
    $data = array();

    // TODO: save the default in a user variable.
    xarVarFetch('initletter', 'pre:alpha:upper:passthru:str:1:1', $initletter, '', XARVAR_NOT_REQUIRED);

    $username = NULL;
    if (!empty($initletter)) {
        $username = $initletter . '%';
    }

    $letters = array();
    for($i = ord('A'); $i <= ord('Z'); $i++) {
        $letters[] = chr($i);
    }
    $letters[] = xarML('All');
    $data['letters'] = $letters;
    $data['initletter'] = $initletter;

    if (!empty($transfer)) {
        // URL to return to when we are done.
        $returnurl = xarServerGetCurrentURL(array('transfer' => '')) . '#usid_' . $transfer;

        ob_start();
        xarModAPIfunc('surveys', 'admin', 'transfersurvey', array('usid' => $transfer, 'debug' => true));
        $log = ob_get_contents();
        ob_end_clean();

        $data['log'] = $log;
        $data['returnurl'] = $returnurl;

        return $data;
    }

    $statuses = xarModAPIfunc('surveys', 'user', 'lookupstatuses', array('type' => 'SURVEY'));
    $system_statuses = xarModAPIfunc('surveys', 'user', 'lookupstatuses', array('type' => 'SURVEY', 'return' => 'system_status'));

    $data['statuses'] = $statuses;
    $data['status'] = $status;

    $data['system_statuses'] = $system_statuses;
    $data['system_status'] = $system_status;

    if (!empty($changestatus)) {
        // Request to change the status of a survey.
        $data['changestatus'] = $changestatus;

        if (!empty($newstatus)) {
            // A new status is selected - set it.
            xarModAPIfunc(
                'surveys', 'admin', 'update',
                array('usid' => $changestatus, 'status' => $newstatus)
            );
            xarResponseRedirect(xarServerGetCurrentURL(array('newstatus' => '', 'changestatus' => '')) . '#usid_' . $changestatus);
            return true;
        }
    }

    if (!empty($order)) {
        $order = explode(',', str_replace(' ', '+', $order));

        $order_items = array();
        foreach ($order as $key => $ord) {
            if (strlen($ord) < 2) {continue;}
            $dir = substr($ord, 0, 1);
            $col = substr($ord, 1);
            if (in_array($col, $order_columns) && ($dir == '+' || $dir == '-')) {
                $order_items[$col] = $dir;
            }
            //echo " dir=$dir col=$col ";
        } //var_dump($order_items);

        // Put the order string back together.
        $order = array();
        foreach($order_items as $col => $dir) {
            $order[] = $dir . $col;
        }
        $order = implode(',', $order);
    }

    $toggle_order = array();
    $current_order = array();

    foreach($order_columns as $order_column) {
        if (strpos($order, '+'.$order_column) !== FALSE) {
            $toggle_order[$order_column] = str_replace('+'.$order_column, '-'.$order_column, $order);
            $current_order[$order_column] = '+';
        } elseif (strpos($order, '-'.$order_column) !== FALSE) {
            $toggle_order[$order_column] = str_replace('-'.$order_column, '', $order);
            $current_order[$order_column] = '-';
        } else {
            $toggle_order[$order_column] = $order . ',+'.$order_column;
            $current_order[$order_column] = '=';
        }
        $toggle_order[$order_column] = array('order' => str_replace(',,', ',', trim($toggle_order[$order_column], ',')));
    }

    // Get the main data.
    $surveys = xarModAPIfunc(
        'surveys', 'user', 'getusersurveys',
        array(
            'status' => $status,
            'system_status' => $system_status,
            'username' => $username
        )
    );

    if (!empty($surveys)) {
        // Add in extra lookups
        foreach($surveys as $key => $survey) {
            if ($survey['uid']) {
                $surveys[$key]['username'] = xarUserGetVar('uname', $survey['uid']);
                $surveys[$key]['fullname'] = xarUserGetVar('name',  $survey['uid']);
            }

            if ($show_summary) {
                $surveys[$key]['summary'] = xarModAPIfunc(
                    'surveys', 'user', 'usersurveyidentity',
                    array(
                        // A newline override can be passed in.
                        // Not ideal, but does the job for now.
                        'newline' => (isset($newline) ? $newline : '; '),
                        'usid' => $survey['usid'],
                        'template' => $survey['summary_template']
                    )
                );
            }
        }

        // Sort the surveys
        if (!empty($order)) {
            // Sort the array.
            // We are sorting the array so that DD fields can be included too.
            // Create a temporary function so that we can inject the column order string.
            $sortfunc = create_function(
                '$a,$b',
                'return _surveys_admin_viewusersurveys_uasort($a,$b,"' . $order . '");'
            );

            uasort($surveys, $sortfunc);
        }
    } else {
        $surveys = array();
    }

    $data['show_summary'] = $show_summary;
    $data['surveys'] = $surveys;
    $data['toggle_order'] = $toggle_order;
    $data['current_order'] = $current_order;

    return $data;
}

// This function will sort an array by any columns, named in a CSV list, with +/-
// indicating whether sorting should be ascending or descending.
function _surveys_admin_viewusersurveys_uasort($a, $b, $c) {
    // Sorting is case-insensitive.
    // Loop for each field to compare.
    foreach(explode(',', $c) as $field) {
        // Determine direction of sorting.
        $dir = 1;
        // Direction can be specified by prefixing the field name with a '+' or '-'.
        if ($field{0} == '+') {$field = substr($field, 1);}
        if ($field{0} == '-') {$field = substr($field, 1); $dir = -1;}
        // Support nat-sorting by prefixing the field name with a '*'.
        if ($field{0} == '*') {$field = substr($field, 1); $nat = true;}
        // Break if the field does not exist.
        if (!isset($a[$field]) || !isset($b[$field])) {break;}
        // If identical, move on to the next field.
        if (($casecmp = strcasecmp($a[$field], $b[$field])) == 0) {continue;}
        // Not identical - one must be greater than the other.
        if (empty($nat)) {
            return $casecmp * ($dir);
        } else {
            return strnatcasecmp($a[$field], $b[$field]) * ($dir);
        }
    }
    // Items are equally positioned using the sorting rules.
    return 0;
}

?>