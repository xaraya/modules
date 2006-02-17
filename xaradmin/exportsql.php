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
 * Export survey tables as SQL (for importing into Access, say).
 *
 * Page is rendered as a text document type.
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

function surveys_admin_exportsql() {
    $EOL = "\r\n";

    // Only for survey admins.
    if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
        // No privilege for editing survey structures.
        return false;
    }

    //echo "<html><body>\n";
    //echo "hello, world\n";
    //echo "even more\n";
    //echo "username=" . xarUserGetVar('name');

    // user_surveys table
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $metaobj = xarDBNewDataDict($dbconn, 'METADATA');

    xarModLoad('lists');
    xarModLoad('dynamicdata');

    $table_list = array(
        $xartable['roles'],

        $xartable['lists_types'],
        $xartable['lists_items'],

        $xartable['surveys_groups'],
        $xartable['surveys_group_rules'],
        $xartable['surveys_questions'],
        $xartable['surveys_question_groups'],
        $xartable['surveys_status'],
        $xartable['surveys_surveys'],
        $xartable['surveys_types'],
        $xartable['surveys_user_surveys'],
        $xartable['surveys_user_responses'],
        $xartable['surveys_user_groups'],

        $xartable['dynamic_objects'],
        $xartable['dynamic_properties'],
        $xartable['dynamic_properties_def'],
        $xartable['dynamic_data'],

//        'remas_ema_options',
//        'remas_ema_questions',

//        'remas_ep_subsectors',
//        'remas_ep_attributes',
//        'remas_ep_graphing_sectors',
//        'remas_ep_indicators',
//        'remas_ep_sectors',
//        'remas_ep_subsector_attributes',
//        'remas_ep_subsector_indicators'
    );

    $table_list_flat = implode(':', $table_list);

    xarVarFetch('tablename', 'enum:' . $table_list_flat, $tablename, NULL, XARVAR_NOT_REQUIRED);

    if (!isset($tablename)) {
        // No [valid] table set, so display a menu.
        return array(
            'table_list' => $table_list
        );
    }

    header("Content-type: text/plain; charset=utf-8");

    //var_dump($metaobj);
    //echo $xartable['surveys_user_surveys'];
    $metadata = $metaobj->getColumns($tablename);
    //var_dump($metadata);

    // Create SQL
    $types = array();
    $columns = array();
    foreach($metadata AS $data) {
        $columns[] = '`' . $data->name . '`';
        $columns_remote[] = '[' . preg_replace('/^xar_/i', '', $data->name) . ']';
        if ($data->type == 'int' && strpos($data->name, 'date') > 0 || $data->name == 'xar_date_reg') {
            $types[] = 'date';
        } else {
            $types[] = $data->type;
        }
    }
    $query = 'SELECT ' . implode(',', $columns) . ' FROM ' . $tablename;
    $querycount = 'SELECT COUNT(*) FROM ' . $tablename;
    //echo " $query ";

    // Execute count query.
    $result = $dbconn->Execute($querycount);
    if (!$result->EOF) {
        list($count) = $result->fields;
    }

    // Execute the main query.
    $result = $dbconn->SelectLimit($query, -1, 0);

    $tablenameremote = preg_replace('/^'.xarDBGetSiteTablePrefix().'/i', 'export', $tablename);

    echo 'TABLE ' . $tablenameremote . $EOL;
    echo 'COUNT ' . $count . $EOL;
    echo 'COLUMNS ' . $tablenameremote . $EOL;
    echo 'DELETE FROM ' . $tablenameremote . $EOL;
    echo 'INSERT INTO ' . $tablenameremote . ' (' . implode(',', $columns_remote) . ')' . $EOL;

    while (!$result->EOF) {
        $row = $result->fields;

        $values = array();

        foreach($row as $key => $col) {
            switch ($types[$key]) {
                case 'int' :
                case 'tinyint' :
                    if (!isset($col)) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = $col;
                    }
                    break;
                case 'varchar':
                case 'mediumtext':
                case 'longtext':
                case 'text':
                case 'char':
                    if (!isset($col)) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . str_replace($EOL, "'&CHR(".ord($EOL[0]).")&CHR(".ord($EOL[1]).")&'", str_replace(array("'"), array("''"), $col)) . "'";
                    }
                    break;
                case 'date':
                    if (!isset($col) || $col == 0) {
                        $values[] = 'NULL';
                    } else {
                        // DateValue('June 30, 2004')
                        $values[] = "DateValue('" . date('F j, Y', $col) . "')";
                    }
                    break;
                default:
                    die ('Unknown "' . $types[$key] . '"');
            }
        }

        echo 'VALUES (' . implode(',', $values) . ')' . $EOL;

        $result->MoveNext();
    }

    //var_dump($row);
    //echo $query;
    //var_dump($types);
    //echo "</body></html>\n";
    exit;
}

?>