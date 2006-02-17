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
function surveys_user_main()
{
    xarResponseRedirect(xarModURL('surveys','user','overview'));
    return true;

    $sid = 2;
    xarModAPIfunc('surveys', 'user', 'startsurvey', array('sid'=>$sid, 'uid'=>xarUserGetVar('uid')));
    xarModAPIfunc('surveys', 'user', 'switchtosurvey', array('sid'=>$sid, 'uid'=>xarUserGetVar('uid'))); // ,'gid'=>60
    return 'done (created user survey number '.$sid.' for user '.xarUserGetVar('uid').')'
        . '<br/>Start survey <a href="http://remas1.acadweb.co.uk/index.php?module=surveys&amp;func=showgroup">here</a>';

    $survey_id = 1;
    $user_id = 1;
    $group_id = 38;

    // Apply response rules. We will pretend we have just [successfuly] processed a page of submitted responses.
    xarModAPIfunc('surveys', 'admin', 'applyresponserules', array('uid'=>$user_id,'sid'=>$survey_id));

    // Get the survey details.
    $survey = xarModAPIfunc(
        'surveys', 'user', 'getsurvey',
        array('sid' => $survey_id)
    );

    $map = xarModAPIfunc(
        'surveys', 'user', 'getsurveymap',
        array('sid' => $survey_id, 'uid' => $user_id)
    );

    //echo "<pre>";

    //$test1 = xarModAPIfunc('lists','user','getlists',array('listkey'=>'name','column'=>'list_name','dd_flag'=>false));
    //var_dump($test1);

    // Display the survey map.
    echo '<h2>' . $survey['name'] . '</h2>';
    foreach ($map['items'] as $item) {
        if (isset($item['group_name'])) {
            echo "<div>" .$item['gid'].' '. str_repeat('&nbsp;&nbsp;&nbsp;', $item['level']) . $item['group_name'] .' '. $item['status'] .' '. $item['count'] .' '. $item['count_desc']  . "</div>";
        } else {
            echo "<div>VIRTUAL ROOT</div>";
        }
    }

    // Get all questions for the current group.
    //$questions = xarModAPIfunc('surveys', 'user', 'getusergroupquestions', array('gid'=>$group_id,'uid'=>$user_id,'sid'=>$survey_id));
    //var_dump($questions);

    // Get the current submit group.
    $objects =& xarModAPIfunc(
        'surveys', 'user', 'getsubmitgroup',
        array('gid'=>$group_id, 'uid'=>$user_id, 'sid'=>$survey_id)
    );

    echo '<form method="post" action="' .xarModURL('surveys','user','main'). '">';

    foreach ($objects as $object) {
        //$object->readonly = true;

        // Render an input form section.
        echo '<hr/>' . $input = $object->render('input');

        // Render an output section (for a report).
        echo '<hr/>' . $output = $object->render('output');
    }

    echo '<input name="submit" type="submit" value="Submit" />';
    echo '<label for="imageField">Next</label> <input name="submit" type="image" id="imageField" src="themes/Xaraya_Classic/images/blue.gif" width="16" height="32" border="0" />';
    echo '</form>';


    echo "<pre>";
    //var_dump($question_class);
    echo "</pre>";

    //echo "</pre>";

/*
    $result = xarModAPIfunc(
        'surveys', 'admin', 'creategroup',
        array('insertpoint'=>38,'offset'=>'firstchild', 'name'=>'again')
    );
*/
/*
    $result = xarModAPIfunc(
        'surveys', 'tree', 'insertprep',
        array('tablename'=>'remas_surveys_groups', 'idname'=>'xar_gid','insertpoint'=>34,'offset'=>'before')
    );

    var_dump($result);

    $dbconn =& xarDBGetConn();
    $query = 'insert into remas_surveys_groups (xar_survey_id,xar_parent,xar_left,xar_right,xar_name)'
        . " values(1,$result[parent],$result[left],$result[right],'name')";
    echo "<br/>";
    var_dump($query);
    $result = $dbconn->execute($query);
*/

    return "DONE";
}

?>