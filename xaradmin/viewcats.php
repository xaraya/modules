<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * View the categories in the system
 *
 * @param pagerstart
 * @param catsperpage
 * @param useJSdisplay
 */
function categories_admin_viewcats()
{
    // Get parameters
    if(!xarVarFetch('pagerstart',   'isset', $pagerstart,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('catsperpage',  'isset', $catsperpage,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('useJSdisplay', 'isset', $useJSdisplay,  NULL, XARVAR_DONT_SET)) {return;}
    // Security check
    if(!xarSecurityCheck('ReadCategories')) return;

    $data = Array ();
    $data['reloadlabel'] = xarML('Reload');

    // Add pager
    if (empty($pagerstart)) {
        $data['pagerstart'] = 1;
    } else {
        $data['pagerstart'] = intval($pagerstart);
    }

    if (empty($catsperpage)) {
        $data['catsperpage'] = xarModGetVar('categories','catsperpage');
    } else {
        $data['catsperpage'] = intval($catsperpage);
    }

    $data['pagertotal'] = xarModAPIFunc('categories', 'user', 'countcats', array());

    $categories = xarModAPIFunc('categories',
                                'user',
                                'getcat',
                                array('start' => $data['pagerstart'],
                                      'count' => $data['catsperpage'],
                                      'cid' => false,
                                      'getchildren' => true));

    if (empty($categories)) {
        return xarTplModule('categories','admin','viewcats-nocats',$data);
    }


    if (!isset($useJSdisplay)) {
        $useJSdisplay = $data['useJSdisplay'] = xarModGetVar('categories','useJSdisplay');
    } else {
        $data['useJSdisplay'] = $useJSdisplay;
    }


    if (!$useJSdisplay) {
        xarModLoad('categories','renderer');

        foreach ($categories as $category) {
            $category['xar_pid'] = $category['parent'];
            $category['xar_cid'] = $category['cid'];
            //What is used in the renderer? Do we need all this extra information in the array?
            //$category['depth'] = $category['indentation'];

/*
    // Note : extending category information with other fields is possible via DD,
    // so getcatinfo() should be able to retrieve that for you in the future
            // there are no 'category' 'display' hooks in use at the moment, and if they
            // were, they should probably be used when individual categories are displayed
            $category['hooks'] = xarModCallHooks('category',
                                                 'display',
                                                 $category['cid'],
                                                 array('returnurl' => xarModURL('categories',
                                                                                'admin',
                                                                                'viewcats',
                                                                                array())));
            if (isset($category['hooks']) && is_array($category['hooks'])) {
                $category['hooks'] = join('',$category['hooks']);
            }
*/
            $cats[] = $category;
        }
        $categories = $cats;

        categories_renderer_array_markdepths_bypid($categories);
        $categories = categories_renderer_array_maptree($categories);

        $data['categories'] = $categories;
        return xarTplModule('categories','admin','viewcats-render',$data);

    } else {
        include ('modules/categories/xarincludes/HTML_TreeMenu-1.1.5/treemenu.php');

        // Create the presentation class
        $menu  = &new HTML_TreeMenu();
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
