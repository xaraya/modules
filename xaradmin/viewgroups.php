<?php
/**
 * View Survey groups
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
/**
 * View the groups
 */
function surveys_admin_viewgroups()
{
    // Get parameters
    if(!xarVarFetch('useJSdisplay', 'isset', $useJSdisplay,  NULL, XARVAR_DONT_SET)) {return;}

    // Security check
    if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
        // No privilege for editing survey structures.
        return false;
    }

    $data = Array ();
    $data['reloadlabel'] = xarML('Reload');

    $data['pagertotal'] = xarModAPIFunc('categories', 'user', 'countcats', array());

    $groups = xarModAPIFunc(
        'surveys', 'user', 'getgroups',
        array('gid' => 0, 'group_key' => 'index')
    );

    if (empty($groups)) {
        return 'NO GROUPS DEFINED';
        //return xarTplModule('categories', 'admin', 'viewcats-nocats', $data);
    }

    if (!isset($useJSdisplay)) {
        $useJSdisplay = $data['useJSdisplay'] = xarModGetVar('categories', 'useJSdisplay');
    } else {
        $data['useJSdisplay'] = $useJSdisplay;
    }


    // Unset the top group - it is a virtual group.
    //unset($groups['items'][0]);

    if (!$useJSdisplay) {
        xarModLoad('categories', 'renderer');

        foreach ($groups['items'] as $key => $item) {
            $groups['items'][$key]['xar_pid'] = $item['parent'];
            $groups['items'][$key]['xar_cid'] = $item['xar_gid'];
            if (isset($groups['children'][$item['xar_gid']])) {
                $groups['items'][$key]['children'] = count($groups['children'][$item['xar_gid']]);
            } else {
                $groups['items'][$key]['children'] = 0;
            }
            if (isset($item['group_name'])) {
                $groups['items'][$key]['name'] = $item['group_name'];
            } else {
                $groups['items'][$key]['name'] = '';
            }
            if (isset($item['group_desc'])) {
                $groups['items'][$key]['description'] = $item['group_desc'];
            } else {
                $groups['items'][$key]['description'] = '';
            }
        }

        categories_renderer_array_markdepths_bypid($groups['items']);
        $groups['items'] = categories_renderer_array_maptree($groups['items']);

        $data['groups'] = $groups['items'];
        return xarTplModule('surveys', 'admin', 'viewgroups-render',$data);

    } else {
        include ('modules/categories/xarincludes/HTML_TreeMenu-1.1.5/treemenu.php');

        // Create the presentation class
        $menu  = &new HTML_TreeMenu();
        $stack = array(&$menu);

        for($key=0; $key < count($categories); $key++) {
            // First element doesnt need to be compared...
            if ($key != 0) {
                // Check for $i >= 1 so it wont check the $menu
                for ($i=count($stack)-1;
                     (($i >= 1) &&
                      ($stack[$i]->indentation >= $categories[$key]['indentation']));
                      $i--)
                {
                    // Takes the last node out of the stack
                    array_pop($stack);
                }
            }

            $text = $categories[$key]['name'] . ' -- '. $categories[$key]['description'];
            $text = addslashes($text);
            $url = xarModURL('categories', 'admin', 'deletecat', array('cid' => $categories[$key]['cid']));
            $text .= "&nbsp;<a href=\"$url\"><img src=\"modules/categories/xarimages/delete.gif\" alt=\"Delete\"/></a>";
            $url = xarModURL('categories', 'admin', 'modifycat', array('cid' => $categories[$key]['cid'], 'creating' => 'false'));
            $text .= "&nbsp;<a href=\"$url\"><img src=\"modules/categories/xarimages/edit.gif\" alt=\"Edit\"/></a>&nbsp;";

            $node = &new HTML_TreeNode(array('text' => $text));
            $node->indentation = $categories[$key]['indentation'];
            $stack[count($stack)-1]->addItem($node);
            $stack[] = &$node;
        }

        for ($i=count($stack)-1;$i >= 1;$i--) {
            // Takes the last node out of the stack
            array_pop($stack);
        }


        $treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => 'modules/categories/xarincludes/HTML_TreeMenu-1.1.5/imagesAlt', 'defaultClass' => 'treeMenuDefault'));
        $data['cats'] = $treeMenu->toHTML();
        return xarTplModule('categories','admin','viewcats-jscript',$data);
    }
}

?>