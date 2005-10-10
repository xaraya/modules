<?php
/**
 * Standard function to update module configuration parameters
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 */

/**
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author Example module development team
 */
function todolist_admin_updateconfig()
{
    /* Original

    $ACCESS_RESTRICTED = pnVarCleanFromInput('ACCESS_RESTRICTED');
    $BACKGROUND_COLOR = pnVarCleanFromInput('BACKGROUND_COLOR');
    $DATEFORMAT = pnVarCleanFromInput('DATEFORMAT');
    $DONE_COLOR = pnVarCleanFromInput('DONE_COLOR');
    $HIGH_COLOR = pnVarCleanFromInput('HIGH_COLOR');
    $LOW_COLOR = pnVarCleanFromInput('LOW_COLOR');
    $MAX_DONE = pnVarCleanFromInput('MAX_DONE');
    $MED_COLOR = pnVarCleanFromInput('MED_COLOR');
    $MOST_IMPORTANT_COLOR = pnVarCleanFromInput('MOST_IMPORTANT_COLOR');
    $MOST_IMPORTANT_DAYS = pnVarCleanFromInput('MOST_IMPORTANT_DAYS');
    $REFRESH_MAIN = pnVarCleanFromInput('REFRESH_MAIN');
    $SEND_MAILS = pnVarCleanFromInput('SEND_MAILS');
    $SHOW_EXTRA_ASTERISK = pnVarCleanFromInput('SHOW_EXTRA_ASTERISK');
    $SHOW_LINE_NUMBERS = pnVarCleanFromInput('SHOW_LINE_NUMBERS');
    $SHOW_PERCENTAGE_IN_TABLE = pnVarCleanFromInput('SHOW_PERCENTAGE_IN_TABLE');
    $SHOW_PRIORITY_IN_TABLE = pnVarCleanFromInput('SHOW_PRIORITY_IN_TABLE');
    $TODO_HEADING = pnVarCleanFromInput('TODO_HEADING');
    $VERY_IMPORTANT_COLOR = pnVarCleanFromInput('VERY_IMPORTANT_COLOR');
    $VERY_IMPORTANT_DAYS = pnVarCleanFromInput('VERY_IMPORTANT_DAYS');
    $ITEMS_PER_PAGE = pnVarCleanFromInput('ITEMS_PER_PAGE');


    if (!isset($ACCESS_RESTRICTED)) $ACCESS_RESTRICTED = "";
    if (!isset($BACKGROUND_COLOR)) $BACKGROUND_COLOR ="#99ccff";
    if (!isset($DATEFORMAT)) $DATEFORMAT = "1";
    if (!isset($DONE_COLOR)) $DONE_COLOR = "#ccffff";
    if (!isset($DONE_COLOR)) $DONE_COLOR = "#ffff00";
    if (!isset($LOW_COLOR)) $LOW_COLOR = "#66ccff";
    if (!isset($MAX_DONE)) $MAX_DONE = 10;
    if (!isset($MED_COLOR)) $MED_COLOR = "#ffcc66";
    if (!isset($MOST_IMPORTANT_COLOR)) $MOST_IMPORTANT_COLOR = "#ffff99";
    if (!isset($MOST_IMPORTANT_DAYS)) $MOST_IMPORTANT_DAYS = 3;
    if (!isset($REFRESH_MAIN)) $REFRESH_MAIN = 600;
    if (!isset($SEND_MAILS)) $SEND_MAILS = true;
    if (!isset($SHOW_EXTRA_ASTERISK)) $SHOW_EXTRA_ASTERISK = 1;
    if (!isset($SHOW_LINE_NUMBERS)) $SHOW_LINE_NUMBERS = true;
    if (!isset($SHOW_PERCENTAGE_IN_TABLE)) $SHOW_PERCENTAGE_IN_TABLE = true;
    if (!isset($SHOW_PRIORITY_IN_TABLE)) $SHOW_PRIORITY_IN_TABLE = true;
    if (!isset($TODO_HEADING)) $TODO_HEADING = "Todolist";
    if (!isset($VERY_IMPORTANT_COLOR)) $VERY_IMPORTANT_COLOR = "#ff3366";
    if (!isset($VERY_IMPORTANT_DAYS)) $VERY_IMPORTANT_DAYS = 3;
    if (!isset($ITEMS_PER_PAGE)) $ITEMS_PER_PAGE = 20;

    pnModSetVar('todolist', 'ACCESS_RESTRICTED', $ACCESS_RESTRICTED);
    pnModSetVar('todolist', 'BACKGROUND_COLOR', $BACKGROUND_COLOR);
    pnModSetVar('todolist', 'DATEFORMAT', $DATEFORMAT);
    pnModSetVar('todolist', 'DONE_COLOR', $DONE_COLOR);
    pnModSetVar('todolist', 'HIGH_COLOR', $HIGH_COLOR);
    pnModSetVar('todolist', 'LOW_COLOR', $LOW_COLOR);
    pnModSetVar('todolist', 'MAX_DONE', $MAX_DONE);
    pnModSetVar('todolist', 'MED_COLOR', $MED_COLOR);
    pnModSetVar('todolist', 'MOST_IMPORTANT_COLOR', $MOST_IMPORTANT_COLOR);
    pnModSetVar('todolist', 'MOST_IMPORTANT_DAYS', $MOST_IMPORTANT_DAYS);
    pnModSetVar('todolist', 'REFRESH_MAIN', $REFRESH_MAIN);
    pnModSetVar('todolist', 'SEND_MAILS', $SEND_MAILS);
    pnModSetVar('todolist', 'SHOW_EXTRA_ASTERISK', $SHOW_EXTRA_ASTERISK);
    pnModSetVar('todolist', 'SHOW_LINE_NUMBERS', $SHOW_LINE_NUMBERS);
    pnModSetVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE', $SHOW_PERCENTAGE_IN_TABLE);
    pnModSetVar('todolist', 'SHOW_PRIORITY_IN_TABLE', $SHOW_PRIORITY_IN_TABLE);
    pnModSetVar('todolist', 'TODO_HEADING', $TODO_HEADING);
    pnModSetVar('todolist', 'VERY_IMPORTANT_COLOR', $VERY_IMPORTANT_COLOR);
    pnModSetVar('todolist', 'VERY_IMPORTANT_DAYS', $VERY_IMPORTANT_DAYS);
    pnModSetVar('todolist', 'ITEMS_PER_PAGE', $ITEMS_PER_PAGE);

    pnRedirect(pnModURL('todolist', 'admin', 'main'));


     */
    if (!xarVarFetch('bold', 'checkbox', $bold, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias','checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;

    /* Confirm authorisation code.*/

    if (!xarSecConfirmAuthKey()) return;
    /* Update module variables.  Note that the default values are set in
     * xarVarFetch when recieving the incoming values, so no extra processing
     * is needed when setting the variables here.
     */
    xarModSetVar('example', 'bold', $bold);
    xarModSetVar('example', 'itemsperpage', $itemsperpage);
    xarModSetVar('example', 'SupportShortURLs', $shorturls);
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('example', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('example', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('example','aliasname');
    $newalias = trim($aliasname);
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('example','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='example')){
            xarModDelAlias($currentalias,'example');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'example');
    }
    /* now set the alias modvar */
    xarModSetVar('example', 'aliasname', $newalias);

    xarModCallHooks('module','updateconfig','example',
                   array('module' => 'example'));

    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('example', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
?>