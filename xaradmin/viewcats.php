<?php

/**
 * create item from xarModFunc('categories','admin','viewcat')
 */
function categories_admin_viewcats()
{
    // Get parameters
    if(!xarVarFetch('useJSdisplay', 'isset', $useJSdisplay, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('activetab',    'isset', $activetab,    0, XARVAR_NOT_REQUIRED)) {return;}

    // Security check
    if(!xarSecurityCheck('ReadCategories')) return;

    if (empty($activetab)) {
        $categories = array();
    } else {
        $categories = xarModAPIFunc('categories',
                                    'user',
                                    'getcat',
                                    array(
                                          'cid' => $activetab,
                                          ));
    }

    if (!isset($useJSdisplay)) {
        $useJSdisplay = $data['useJSdisplay'] = xarModVars::get('categories','useJSdisplay');
    } else {
        $data['useJSdisplay'] = $useJSdisplay;
    }

    if (!$useJSdisplay) {
        $data['options'] = $categories;
        return xarTplModule('categories','admin','viewcats-render',$data);

    } else {

// TODO: this option was disabled for 1.0. Perhaps reactivate it?

//        if (!xarModLoad('categories','/xarincludes/HTML_TreeMenu-1.1.5/treemenu.php')) die('problems loading tree');
        include ('modules/categories/xarincludes/HTML_TreeMenu-1.1.5/treemenu.php');

        // Create the presentation class
        $menu  = new HTML_TreeMenu();
        $stack = array(&$menu);

        for($key=0;$key<count($categories);$key++) {
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
//            $node = new HTML_TreeNode(array('text' => $categories[$key]['name'], 'icon' => $icon, 'expandedIcon' => $expandedIcon));

            $text = $categories[$key]['name'] . ' -- '. $categories[$key]['description'];
            $text = addslashes($text);
//            $text .= '<td>'.$categories[$key]['description'].'</td>';
            $url = xarModURL('categories', 'admin', 'deletecat', array('cid' => $categories[$key]['cid']));
            $text .= "&#160;<a href=\"$url\"><img src=\"modules/categories/xarimages/delete.gif\" alt=\"Delete\"/></a>";
            $url = xarModURL('categories', 'admin', 'modifycat', array('cid' => $categories[$key]['cid'], 'creating' => 'false'));
            $text .= "&#160;<a href=\"$url\"><img src=\"modules/categories/xarimages/edit.gif\" alt=\"Edit\"/></a>&#160;";

            $node = new HTML_TreeNode(array('text' => $text));
            $node->indentation = $categories[$key]['indentation'];
            $stack[count($stack)-1]->addItem($node);
            $stack[] = &$node;
        }

        for ($i=count($stack)-1;$i >= 1;$i--) {
            // Takes the last node out of the stack
            array_pop($stack);
        }

        $treeMenu = new HTML_TreeMenu_DHTML($menu, array('images' => 'modules/categories/xarincludes/HTML_TreeMenu-1.1.5/imagesAlt', 'defaultClass' => 'treeMenuDefault'));
        $data['cats'] = $treeMenu->toHTML();
        return xarTplModule('categories','admin','viewcats-jscript',$data);
    }
}

?>
