<?php
/**
 * Pass individual menu items to the admin menu
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Pass individual menu items to the admin  menu
 * This function delivers the in-page admin menu items too
 *
 * @author the Example module development team
 * @return array containing the menulinks for the main and the in-page admin menus.
 */
function crispbb_adminapi_getmenulinks($args)
{
    // seems to me we call request info in the template to determine the active link,
    // why don't we just do that here instead, let's take it a stage further,
    // by allowing optional args, we can be specific what we want
    extract($args);

    // get request info
    $request = xarRequestGetInfo();

    // see if calling func specified the current module
    if (!empty($current_module)) {
        $modname = $current_module;
    }
    // we didn't get a modname in the args, get from request
    if (empty($modname)) {
        $modname = $request[0];
    }

    // see if calling func specified current module type
    if (!empty($current_type)) {
        $modtype = $current_type;
    }
    // no modtype from args, get from request
    if (empty($modtype)) {
        $modtype = $request[1];
    }

    // see if calling func specified current module function
    if (!empty($current_func)) {
        $modfunc = $current_func;
    }
    // no modtype from args, get from request
    if (empty($modfunc)) {
        $modfunc = $request[2];
    }

    // optional active sublink, can only be passed in args, used internally by module
    if (empty($current_sublink)) {
        $current_sublink = '';
    }

    // optional forum id, used by some sub links
    if (empty($fid)) {
        $fid = NULL;
    }

    if (empty($catid)) {
        $catid = NULL;
    }

    // minimum privilege
    if (!xarSecurityCheck('AddCrispBB', 0, 'Forum', 'All:All')) return array();

    $userLevel = xarModAPIFunc('crispbb', 'user', 'getseclevel',
        array('catid' => $catid, 'fid' => $fid));
    $secLevels = empty($secLevels) ? xarModAPIFunc('crispbb', 'user', 'getsettings', array('setting' => 'fprivileges')) : $secLevels;
    // must have at least add forum privs to be here
    if ($userLevel < 400) { // No privs
        return array();
    }

    // menu is only active if crispbb is current module and current type is admin
    $menuactive = $modname == 'crispbb' && $modtype == 'admin' ? true : false;


    $menulinks = array();

    $activefuncs = array('view', 'new', 'forumconfig', 'modify', 'delete', 'posters','topics', 'deletetopic');
    // key menulinks by function name, then we can access them individually
    $menulinks['view'] = array('url' => xarModURL('crispbb','admin','view'),
        'title' => xarML('View/manage forum listings.'),
        'label' => xarML('Manage Forums'),
        'active' => $menuactive && in_array($modfunc, $activefuncs) ? true : false
    );
    if ($userLevel == 800) {
        $activefuncs = array('modifyhooks', 'unlinkhooks');
        $menulinks['modifyhooks'] = array('url' => xarModURL('crispbb','admin','modifyhooks'),
            'title' => xarML('View/manage module hooks configuration.'),
            'label' => xarML('Manage Hooks'),
            'active' => $menuactive && in_array($modfunc, $activefuncs) ? true : false
        );
        $activefuncs = array('modifyconfig');
        $menulinks['modifyconfig'] = array('url' => xarModURL('crispbb','admin','modifyconfig'),
            'title' => xarML('View/manage module configuration.'),
            'label' => xarML('Modify Config'),
            'active' => $menuactive && in_array($modfunc, $activefuncs) ? true : false
        );
        $activefuncs = array('overview');
        $menulinks['overview'] = array('url' => xarModURL('crispbb','admin','overview'),
            'title' => xarML('View information about this module.'),
            'label' => xarML('Overview'),
            'active' => $menuactive && in_array($modfunc, $activefuncs) ? true : false
        );
    }

    if (empty($args)) return $menulinks;

    $sublinks = array();
    if ($menuactive) {
        switch ($modfunc) {
            case 'view':
            case 'modify':
            case 'delete':
            case 'new':
            case 'forumconfig':
            case 'categories':
            case 'privileges':
            case 'posters':
            case 'topics':
            case 'deletetopic':
            $activelinks = array('view', 'modify', 'delete');
            if (!empty($secLevels[$userLevel]['addforum'])) {
            $sublinks['view'] = array(
                'url' => xarModURL('crispbb', 'admin', 'view'),
                'title' => xarML('Overview of forum listings'),
                'label' => xarML('Forums'),
                'active' => in_array($modfunc, $activelinks) ? true : false
            );
            }
            $activelinks = array('modify', 'delete');
            if (in_array($modfunc, $activelinks)) {
                $tablinks = array();
                if (!empty($fid)) $forum = xarModAPIFunc('crispbb', 'user', 'getforum', array('fid' => $fid));
                if (!empty($secLevels[$userLevel]['addforum'])) {
                $tablinks['view'] = array(
                    'url' => xarModURL('crispbb', 'admin', 'modify', array('fid' => $fid)),
                    'title' => xarML('Forum Overview'),
                    'label' => xarML('Overview'),
                    'active' => $modfunc == 'modify' && empty($current_sublink) ? true : false
                );
                }
                if (!empty($secLevels[$userLevel]['editforum'])) {
                $tablinks['modify'] = array(
                    'url' => xarModURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'edit')),
                    'title' => xarML('Modify forum configuration'),
                    'label' => xarML('Edit'),
                    'active' => $current_sublink == 'edit' ? true : false
                );
                if ($forum['ftype'] != 1) {
                    $tablinks['forumhooks'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'forumhooks')),
                        'title' => xarML('Set forum hooks configuration'),
                        'label' => xarML('Forum Hooks'),
                        'active' => $current_sublink == 'forumhooks' ? true : false
                    );
                    $tablinks['topichooks'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'topichooks')),
                        'title' => xarML('Set forum topic hooks configuration'),
                        'label' => xarML('Topic Hooks'),
                        'active' => $current_sublink == 'topichooks' ? true : false
                    );
                    $tablinks['posthooks'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'posthooks')),
                        'title' => xarML('Set forum post hooks configuration'),
                        'label' => xarML('Post Hooks'),
                        'active' => $current_sublink == 'posthooks' ? true : false
                    );
                    $tablinks['privileges'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'privileges')),
                        'title' => xarML('Modify privileges for this forum'),
                        'label' => xarML('Privileges'),
                        'active' => $current_sublink == 'privileges' ? true : false
                    );
                    }
                }
                if (!empty($secLevels[$userLevel]['deleteforum'])) {
                $tablinks['delete'] = array(
                    'url' => xarModURL('crispbb', 'admin', 'delete', array('fid' => $fid)),
                    'title' => xarML('Delete this forum'),
                    'label' => xarML('Delete'),
                    'active' => $modfunc == 'delete' ? true : false
                );
                }
            }
            if (!empty($secLevels[$userLevel]['addforum'])) {
            $activelinks = array('new');
            $sublinks['new'] = array(
                'url' => xarModURL('crispbb', 'admin', 'new'),
                'title' => xarML('Add a new forum to the system'),
                'label' => xarML('Add Forum'),
                'active' => in_array($modfunc, $activelinks) ? true : false
            );
            }
            if ($userLevel == 800) {
                $activelinks = array('forumconfig');
                $sublinks['forumconfig'] = array(
                    'url' => xarModURL('crispbb', 'admin', 'forumconfig'),
                    'title' => xarML('Set default configuration for new forums'),
                    'label' => xarML('Set Defaults'),
                    'active' => in_array($modfunc, $activelinks) ? true : false
                );
                if (in_array($modfunc, $activelinks)) {
                    $tablinks = array();
                    $tablinks['forumconfig'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'forumconfig'),
                        'title' => xarML('Set default forum configuration'),
                        'label' => xarML('Forum Config'),
                        'active' => empty($current_sublink) ? true : false
                    );
                    $tablinks['forumhooks'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'forumconfig', array('sublink' => 'forumhooks')),
                        'title' => xarML('Set default forum hooks configuration'),
                        'label' => xarML('Forum Hooks'),
                        'active' => $current_sublink == 'forumhooks' ? true : false
                    );
                    $tablinks['topichooks'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'forumconfig', array('sublink' => 'topichooks')),
                        'title' => xarML('Set default topic hooks configuration'),
                        'label' => xarML('Topic Hooks'),
                        'active' => $current_sublink == 'topichooks' ? true : false
                    );
                    $tablinks['posthooks'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'forumconfig', array('sublink' => 'posthooks')),
                        'title' => xarML('Set default post hooks configuration'),
                        'label' => xarML('Post Hooks'),
                        'active' => $current_sublink == 'posthooks' ? true : false
                    );
                    $tablinks['setdefaults'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'forumconfig', array('sublink' => 'privileges')),
                        'title' => xarML('Set default privileges configuration'),
                        'label' => xarML('Privileges'),
                        'active' => $current_sublink == 'privileges' ? true : false
                    );
                }
                $activelinks = array('posters');
                $sublinks['posters'] = array(
                    'url' => xarModURL('crispbb', 'admin', 'posters'),
                    'title' => xarML('Overview of forum posters'),
                    'label' => xarML('Posters'),
                    'active' => in_array($modfunc, $activelinks) ? true : false
                );
            }
            /*
            $activelinks = array('topics', 'deletetopic');
            $sublinks['topics'] = array(
                'url' => xarModURL('crispbb', 'admin', 'topics'),
                'title' => xarML('Manage forum topics'),
                'label' => xarML('Topics'),
                'active' => in_array($modfunc, $activelinks) ? true : false
            );
            */
            /*
            $activelinks = array('categories');
            $sublinks['categories'] = array(
                'url' => xarModURL('crispbb', 'admin', 'categories'),
                'title' => xarML('Category configuration for forums'),
                'label' => xarML('Categories'),
                'active' => in_array($modfunc, $activelinks) ? true : false
            );
            */
            $activefunc = 'view';
            break;

            case 'modifyconfig':
                if ($userLevel == 800) {
                    $activelinks = array('modifyconfig');
                    $sublinks['modifyconfig'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'modifyconfig'),
                        'title' => xarML('Global Module Settings'),
                        'label' => xarML('Module'),
                        'active' => in_array($modfunc, $activelinks) ? true : false
                    );
                    $activefunc = 'modifyconfig';
               }
            break;
            case 'modifyhooks':
            case 'unlinkhooks':
                if ($userLevel == 800) {
                    $activelinks = array('modifyhooks', 'unlinkhooks');
                    $sublinks['modifyhooks'] = array(
                        'url' => xarModURL('crispbb', 'admin', 'modifyhooks'),
                        'title' => xarML('crispBB Hooks Configuration'),
                        'label' => xarML('crispBB Hooks'),
                        'active' => in_array($modfunc, $activelinks) ? true : false
                    );
                    $activefunc = 'modifyhooks';
                }
            break;

            case 'overview':
            default:

            break;
        }
    }
    if (!empty($activefunc) && !empty($sublinks)) {
        if (!empty($tablinks) && !empty($sublinks[$modfunc])) {
            $sublinks[$modfunc]['sublinks'] = $tablinks;
        } elseif (!empty($tablinks)) {
            $sublinks[$activefunc]['sublinks'] = $tablinks;
        }
        $menulinks[$activefunc]['sublinks'] = $sublinks;
    }
    /* Finally we return the values back to caller for display.
     */
    return $menulinks;
}
?>
