<?php // $Id 

/**
 * generates the header of every page
 *
 * @param $page         int    the page for which this header is
 * @param $printlayout  int    is it page ready for printer?
 * @return string HTML content
 */
function page_top($page,$printlayout)
{
    global $order_by, $feedback, $QUERY_STRING, $date, $id;

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    $modinfo = pnModGetInfo(pnModGetIDFromName('todolist'));
    $todoimagespath = 'modules/'.pnVarPrepForOS($modinfo['directory']).'/xarimages/';

    $str = "";

    /*
    if ($page == THELIST || $page == ACTIONS) {
        $str .= '<meta http-equiv="refresh" content="'.pnModGetVar('todolist', 'REFRESH_MAIN').'; URL='.pnModURL('todolist', 'user', 'main', array());
        if (isset($order_by)) $str .='&amp;order_by='.$order_by;
        $str .= '"/>';
    }
    */
    // Javascripts
    if ($page == THELIST) {
        $jetzt = getdate(time());
        $str .= '
        <script type="text/javascript" language="JavaScript">
        <!--
        function showCalendar()
        {
        var calenderWin =  window.open("modules.php?op=modload&name=todolist&file=calendar&m='.$jetzt['mon'].'&dj='.$jetzt['year'].'", "","width=210,height=210");
        // calenderWin.moveTo(screen.width-210,0);
        calenderWin.focus();
        }
        // -->
        </script>
        ';
        // Javascript for project-select (main-page)
        $str .= '
        <script type="text/javascript" language="JavaScript">
        <!--
        function selectproject() {
              window.location.href="'.pnModURL('todolist', 'user', 'main', array()).'&';
        if (isset($order_by)) {
            $str .= 'order_by='.$order_by.'&';
        }
        $str .= 'selected_project=" + document.projectform.selected_project.options[window.document.projectform.selected_project.selectedIndex].value
        }
        // -->
        </script>';
        // Javascript for project-select (add form)
        $str .= '
        <script type="text/javascript" language="JavaScript">
        <!--
        function updateaddbox() {
              window.location.href="'.pnModURL('todolist', 'user', 'main', array()).'&';
        if (isset($order_by)) {
            $str .= 'order_by='.$order_by.'&';
        }
        $str .= 'add_project=" + document.addform.project.options[window.document.addform.project.selectedIndex].value
        }
        // -->
        </script>';
    }
    if ($page == DETPAGE){
        // Javascript for project-select (add form)
        $str .= '
        <script type="text/javascript" language="JavaScript">
        <!--
        function updatedetails() {
              window.location.href="'.pnModURL('todolist', 'user', 'main', array()).'&';
        $str .= 'route='.DETAILS.'&id='.$id.'&';
        $str .= 'detail_project=" + document.detailform.project.options[window.document.detailform.project.selectedIndex].value
        }';

        //sets the status to done if percentage_completed=100 is selected.
        $str .='
        function updatestatus() {
            if (document.detailform.percentage_completed.options[document.detailform.percentage_completed.selectedIndex].value == 100 ) {
                document.detailform.status.selectedIndex=document.detailform.status.options.length-1;
            }
        }';

        //sets percentage_completed=100 if status is set to done.
        $str .='
        function updatepercentage() {
            if (document.detailform.status.options[document.detailform.status.options.selectedIndex].value==10) {
                document.detailform.percentage_completed.selectedIndex = document.detailform.percentage_completed.options.length-1;
            }
        }';
        $str .='
        // -->
        </script>';
    }

    $str .= '<a name="top" /><table width="100%"><tr><td width="33%"><p align="left" />';

    if (!$printlayout) {
        switch ($page) {
        case THELIST: 
            if (pnSessionGetVar('todolist_show_icons')) {
                $str .= '<a href="'.pnModURL('todolist', 'user', 'main', array()).'">
                   <img border="0" src="'.$todoimagespath.'reload.png" alt="'.xarML('reload').'" /></a>';
            } else {
                $str .= '<a href="'.pnModURL('todolist', 'user', 'main', array()).'">'.xarML('reload').'</a>';
            }
            break;
        default:
            if (pnSessionGetVar('todolist_show_icons'))
                $str.= '<a href="'.pnModURL('todolist', 'user', 'main', array()).'">
                   <img border="0" src="'.$todoimagespath.'back.png" alt="'.xarML('back').'" /></a>';
            else
                $str .= '<a href="'.pnModURL('todolist', 'user', 'main', array()).'">'.xarML('back').'</a>';
            break;
        }

        if (pnSessionGetVar('todolist_show_icons')) {
            $str .= '<br/><a href="'.pnModURL('todolist', 'user', 'main', array()).'#todoAddForm" accesskey="n">
                 <img border="0" src="'.$todoimagespath.'new.png" alt="'.xarML('new').'" /></a>';
            $str .= ' <a href="'.pnModURL('todolist', 'user', 'main', array()).'#todoSearchForm" accesskey="s">
                 <img border="0" src="'.$todoimagespath.'find.png" alt="'.xarML('search').'" /></a>';
        } else {
            $str .= '<br/><a href="'.pnModURL('todolist', 'user', 'main', array()).'#todoAddForm" accesskey="n">'.xarML('new') .'</a> / ';
            $str .= '<a href="'.pnModURL('todolist', 'user', 'main', array()).'#todoSearchForm" accesskey="s">'.xarML('search').'</a>';
        }

        if (!$printlayout && ($page!=DETPAGE) && ($page!=PREFPAGE)) {
            if ($QUERY_STRING=="") {
                if (pnSessionGetVar('todolist_show_icons')) {
                    $str .= '<a target="_blank" href="'.pnModURL('todolist', 'user', 'main', array()).'&amp;printlayout=true">';
                    $str .= '<img border="0" src="'.$todoimagespath.'print.png" alt="'.xarML('printlayout').'" /></a>';
                } else {
                    $str .= '/ <a target="_blank" href="'.pnModURL('todolist', 'user', 'main', array()).'&amp;printlayout=true" accesskey="p">'.
                            xarML('printlayout')."</a>";
                }
            } else {
                if (pnSessionGetVar('todolist_show_icons'))
                    $str .= '<a target="_blank" href="'.pnModURL('todolist', 'user', 'main', array()).'&'.$QUERY_STRING.'&amp;printlayout=true" accesskey="p">
                        <img border="0" src="'.$todoimagespath.'print.png" alt="'.xarML('printlayout').'" /></a>';
                else {
                    $str .= '/ <a target="_blank" href="'.pnModURL('todolist', 'user', 'main', array()).'&'.$QUERY_STRING.'&amp;printlayout=true" accesskey="p">'.
                            xarML('printlayout')."</a>";
                }
            }
        }
        if ($page != PREFPAGE) {
            if (pnSessionGetVar('todolist_show_icons')) {
                $str .=' <a href="'.pnModURL('todolist', 'user', 'main', array()).'&amp;route='.PREFERENCES.'" accesskey="p">
                    <img border="0" src="'.$todoimagespath.'preferences.png" alt="'.xarML('preferences').'" /></a>';
            }
            else {
                $str .=' / <a href="'.pnModURL('todolist', 'user', 'main', array()).'&amp;route='.PREFERENCES.'" accesskey="p">'.xarML('preferences').'</a>';
            }
        }
    }
    $str .= '</td><td width="33%"><h1 align="center">';
    $str .= pnModGetVar('todolist', 'TODO_HEADING');
    if ($page == PREFPAGE) {
        $str .= "<br/>".xarML('preferences');
    }
    $str .= '</h1></td>';

    if ($page == THELIST) {
        // How many things in this project aren't finished yet?
        $todos_column = &$pntable['todolist_todos_column'];
        $project_members_column = &$pntable['todolist_project_members'];
        $responsible_persons_column = &$pntable['todolist_responsible_persons'];
        $query = "SELECT count(distinct($todos_column[todo_id])) AS anzahl_jobs
                FROM $pntable[todolist_todos], $pntable[todolist_project_members],
                $pntable[todolist_responsible_persons] WHERE $todos_column[status] <= 5";
                //WHERE todo_priority IN (1,2,3)";
                //AND project_id=$selected_project");

        if (pnSessionGetVar('todolist_selected_project') != "all") {
             $query .= " AND $todos_column[project_id]=".pnSessionGetVar('todolist_selected_project');
        } else {
             $query .= " AND $todos_column[project_id] in ".pnSessionGetVar('todolist_my_projects');
        }

        if (pnSessionGetVar('todolist_my_tasks') == 1) {
            $query .= ' AND $responsible_persons_column[user_id] = '. pnUserGetVar('uid') .'
                AND $todos_column[todo_id] = $responsible_persons_column[todo_id]';
        }

        $result = $dbconn->Execute($query);
        // $db->next_record();
        $anzahl_jobs = $result->fields[0];
    }

    $str .= '<td width="33%" align="right">'. convDate($date) . "<br/>";
    if (isset($anzahl_jobs)) {
        $str .= '<font size="-1">'.$anzahl_jobs . " " .xarML('things to do').' </font>';
    }

    if (($page != DETPAGE) && ($page != PREFPAGE)) {
         if (!$printlayout) {
             $str .= '
                 <form method="post" action="'.pnModURL('todolist', 'user', 'main', array()).'" name="mytasksform">
                 <input type="hidden" name="module" value="todolist" />
                 <input type="hidden" name="route" value="'.FRONTPAGE.'"/>';
             if (pnSessionGetVar('todolist_my_tasks') == 1) {
                 $str .= '<input type="hidden" name="my_tasks" value="0"/>
                      <input type="submit" value="'.xarML('all tasks').'" />';
             } else {
                 $str .= '<input type="hidden" name="my_tasks" value="1"/>
                      <input type="submit" value="'.xarML('my tasks').'" />';
             }
             $str .= '</form>';
             $str .= '
                 <form method="get" action="'.pnModURL('todolist', 'user', 'main', array()).'" name="projectform">
                 <input type="hidden" name="module" value="todolist" />
                 <input type="hidden" name="route" value="'.FRONTPAGE.'"/>';
             $str .= makeProjectDropdown("selected_project",pnSessionGetVar('todolist_selected_project'),
                     true, "selectproject()");
             $str .= '</form>';
         } else {
              if (pnSessionGetVar('todolist_my_tasks') == 1) {
                  $str .= '<br />'.xarML('my tasks');
              } else {
                  $str .= '<br />'.xarML('all tasks');
              }
         }
    }
    $str .= '</td></tr></table>';
    if ($feedback) {
        $str .= '<p>'.$feedback.'</p>';
    }
    return $str;
} // end page_top


/**
 * generates the footer of every page
 *
 * @param $page        int        the page for which this header is
 * @param $printlayout int        is it page ready for printer?
 * @return string HTML content
 */
function page_foot($page,$printlayout)
{
    $modinfo = pnModGetInfo(pnModGetIDFromName('todolist'));
    $todoimagespath = 'modules/'.pnVarPrepForOS($modinfo['directory']).'/xarimages/';

    $str = '<hr noshade="noshade" />
            <table width="100%"><tr><td width="30%" align="left" valign="top">';
    if (!$printlayout) {
        switch ($page) {
        case THELIST: 
            if (pnSessionGetVar('todolist_show_icons')) {
                $str .= '<a href="#top">';
                $str .= '<img border="0" src="'.$todoimagespath.'up.png" alt="'.xarML('top').'" /></a>';
            } else
                $str .= "  <a href=\"#top\">".xarML('top').'</a>';
            break;
        default:
            if (pnSessionGetVar('todolist_show_icons'))
                $str.= '<a href="'.pnModURL('todolist', 'user', 'main', array()).'">
                   <img border="0" src="'.$todoimagespath.'back.png" alt="'.xarML('back').'" /></a>';
            else
                $str .= '<a href="'.pnModURL('todolist', 'user', 'main', array()).'">'.xarML('back').'</a>';
            break;
        }
    }
    $str .= '</td><td width="40%"><p align="center" />Todolist v'.$modinfo['version'];
    $str .= '</a> &copy; 1999-2001 <a href="mailto:jhm@gmx.net">J&ouml;rg Menke</a>
            </td><td width="30%" align="right" valign="top">';

    if (pnUserLoggedIn() && !$printlayout){
        $str .= pnUserGetVar('uname') . ' (';
        if (pnSessionGetVar('todolist_my_tasks') == 1) {
            $str .= xarML('my tasks');
        } else {
            $str .= xarML('all tasks');
        }
        $str .=  ')<br />';
        $str .=  convDate(date("Y-m-d H:i"));
    }

    $str .= '</td></tr></table>';
    return $str;

} // end page_foot
?>