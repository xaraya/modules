<?php
/**
 * Modify module's configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 */

/**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author Example module development team
 * @return array
 */
function todolist_admin_modifyconfig()
{ 
    /* Initialise the $data

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(xarML('Modify Todolist module configuration'));
    $output->FormStart(pnModURL('todolist', 'admin', 'updateconfig'));
    $output->FormHidden('authid', pnSecGenAuthKey());

    $output->TableStart();

    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Access restricted')));
    $row[] = $output->FormText('ACCESS_RESTRICTED', pnModGetVar('todolist', 'ACCESS_RESTRICTED'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('BACKGROUND_COLOR (Default = #99CCFF)')));
    $row[] = $output->FormText('BACKGROUND_COLOR', pnModGetVar('todolist', 'BACKGROUND_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('DONE_COLOR (Default = #CCFFFF)')));
    $row[] = $output->FormText('DONE_COLOR', pnModGetVar('todolist', 'DONE_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('HIGH_COLOR (Default = #ffff00)')));
    $row[] = $output->FormText('HIGH_COLOR', pnModGetVar('todolist', 'HIGH_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('LOW_COLOR (Default = #66ccff)')));
    $row[] = $output->FormText('LOW_COLOR', pnModGetVar('todolist', 'LOW_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('MED_COLOR (Default = #FFcc66)')));
    $row[] = $output->FormText('MED_COLOR', pnModGetVar('todolist', 'MED_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('MOST_IMPORTANT_COLOR (Default = #FFFF99)')));
    $row[] = $output->FormText('MOST_IMPORTANT_COLOR', pnModGetVar('todolist', 'MOST_IMPORTANT_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('VERY_IMPORTANT_COLOR (Default = #FF3366)')));
    $row[] = $output->FormText('VERY_IMPORTANT_COLOR', pnModGetVar('todolist', 'VERY_IMPORTANT_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Dateformat: 1 = YYYY-MM-DD / 2 = DD.MM.JJJJJ / 3 = MM/DD/YYYY (Default - 2)')));
    $row[] = $output->FormText('DATEFORMAT', pnModGetVar('todolist', 'DATEFORMAT'), 1, 1);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Maximum number of done-entries shown on the main page.')));
    $row[] = $output->FormText('MAX_DONE', pnModGetVar('todolist', 'MAX_DONE'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Days in the past that should be higligted with VERY_IMPORTANT_COLOR and MOST_IMPORTANT_COLOR foreground-color (Disable = 0)')));
    $row[] = $output->FormText('MOST_IMPORTANT_DAYS', pnModGetVar('todolist', 'MOST_IMPORTANT_DAYS'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Refresh-time for the main page (Default = 600)')));
    $row[] = $output->FormText('REFRESH_MAIN', pnModGetVar('todolist', 'REFRESH_MAIN'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Should mails be send via local mailserver?')));
    $row[] = $output->FormText('SEND_MAILS', pnModGetVar('todolist', 'SEND_MAILS'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('If there is a note attached to the todo the number of notes attached is shown in the details column. To have another notification you can also show an asterisk in one of the left columns. Possible options are: 0 = disable extra asterisk, 1 = show it in #-column, 2 = show it in priority-column, 3 = show it in percentage completed-column, 4 = show it in text-column)')));
    $row[] = $output->FormText('SHOW_EXTRA_ASTERISK', pnModGetVar('todolist', 'SHOW_EXTRA_ASTERISK'), 1, 1);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Show the line-Numbers? [true/false] (Default = true)')));
    $row[] = $output->FormText('SHOW_LINE_NUMBERS', pnModGetVar('todolist', 'SHOW_LINE_NUMBERS'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Show percentage-completed in the tables? [true/false] (Default = true)')));
    $row[] = $output->FormText('SHOW_PERCENTAGE_IN_TABLE', pnModGetVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Show priority as text in the tables ? [true/false] (Default = true)')));
    $row[] = $output->FormText('SHOW_PRIORITY_IN_TABLE', pnModGetVar('todolist', 'SHOW_PRIORITY_IN_TABLE'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML("Custom title. For example the Company's-Name")));
    $row[] = $output->FormText('TODO_HEADING', pnModGetVar('todolist', 'TODO_HEADING'), 30, 30);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Days in the future that should be higligted with VERY_IMPORTANT_COLOR (Disable = 0)')));
    $row[] = $output->FormText('VERY_IMPORTANT_DAYS', pnModGetVar('todolist', 'VERY_IMPORTANT_DAYS'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Items per page')));
    $row[] = $output->FormText('ITEMS_PER_PAGE', pnModGetVar('todolist', 'ITEMS_PER_PAGE'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);


    $output->TableEnd();

    // End form
    $output->Linebreak(2);
    $output->FormSubmit(xarML('Update'));
    $output->FormEnd();
     */
    $data = array();

    /* common menu configuration */
    $data = xarModAPIFunc('todolist', 'admin', 'menu');
    
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AdminTodolist')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();

    /* Specify some values for display */
    $data['boldchecked'] = xarModGetVar('example', 'bold') ? true : false;
    $data['itemsvalue'] = xarModGetVar('example', 'itemsperpage');
    /* Note : if you don't plan on providing encode/decode functions for
     * short URLs (see xaruserapi.php), you should remove this from your
     * admin-modifyconfig.xard template !
     */
    $data['shorturlschecked'] = xarModGetVar('example', 'SupportShortURLs') ? true : false;

    /* If you plan to use alias names for you module then you should use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('example', 'useModuleAlias');
    $data['aliasname ']= xarModGetVar('example','aliasname');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'example',
                       array('module' => 'example'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for example module'));
    } else {
        $data['hooks'] = $hooks;
    
         /* You can use the output from individual hooks in your template too, e.g. with
         * $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }

    /* Return the template variables defined in this function */
    return $data;
}
?>