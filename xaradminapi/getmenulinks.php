<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Standard function to pass menu links to whoever
 * used by base admin menu block, crispBB admin GUI functions
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
sys::import('modules.crispbb.class.cache.links');
function crispbb_adminapi_getmenulinks($args)
{
    // seems to me we call request info in the template to determine the active link,
    // why don't we just do that here instead, let's take it a stage further,
    // by allowing optional args, we can be specific what we want
    extract($args);

    // get request info
    $request = xarRequest::getInfo();

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

    $userLevel = xarMod::apiFunc('crispbb', 'user', 'getseclevel',
        array('catid' => $catid, 'fid' => $fid));
    $secLevels = empty($secLevels) ? xarMod::apiFunc('crispbb', 'user', 'getsettings', array('setting' => 'fprivileges')) : $secLevels;
    // must have at least add forum privs to be here
    if ($userLevel < 400) { // No privs
        return array();
    }

    // menu is only active if crispbb is current module and current type is admin
    $menuactive = $modname == 'crispbb' && $modtype == 'admin' ? true : false;

    $menulinks = array();

    $activefuncs = array('view', 'new', 'forumconfig', 'modify', 'delete', 'posters','topics', 'deletetopic', 'trashcan', 'categories', 'newcat', 'modifycat', 'deletecat');
    // key menulinks by function name, then we can access them individually
    $link = LinkCache::getCachedURL('crispbb','admin','view');
    $menulinks['view'] = array('url' => $link,
        'title' => xarML('View/manage forum listings.'),
        'label' => xarML('Manage Forums'),
        'active' => $menuactive && in_array($modfunc, $activefuncs) ? true : false
    );
    if ($userLevel == 800) {
        $activefuncs = array('modifyhooks', 'unlinkhooks');
        $link = LinkCache::getCachedURL('crispbb','admin','modifyhooks');
        $menulinks['modifyhooks'] = array('url' => $link,
            'title' => xarML('View/manage module hooks configuration.'),
            'label' => xarML('Manage Hooks'),
            'active' => $menuactive && in_array($modfunc, $activefuncs) ? true : false
        );
        $activefuncs = array('modifyconfig');
            $link = LinkCache::getCachedURL('crispbb','admin','modifyconfig');
        $menulinks['modifyconfig'] = array('url' => $link,
            'title' => xarML('View/manage module configuration.'),
            'label' => xarML('Modify Config'),
            'active' => $menuactive && in_array($modfunc, $activefuncs) ? true : false
        );
        $activefuncs = array('overview');
            $link = LinkCache::getCachedURL('crispbb','admin','overview');
        $menulinks['overview'] = array('url' => $link,
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
            case 'newcat':
            case 'modifycat':
            case 'deletecat':
            case 'privileges':
            case 'posters':
            case 'topics':
            case 'deletetopic':
            case 'trashcan':
            $activelinks = array('view', 'modify', 'delete');
            if (!empty($secLevels[$userLevel]['addforum'])) {
                $link = LinkCache::getCachedURL('crispbb','admin','view');
            $sublinks['view'] = array(
                'url' => $link,
                'title' => xarML('Overview of forum listings'),
                'label' => xarML('Forums'),
                'active' => in_array($modfunc, $activelinks) ? true : false
            );
            }
            $activelinks = array('modify', 'delete');
            if (in_array($modfunc, $activelinks)) {
                $tablinks = array();
                if (!empty($fid)) $forum = xarMod::apiFunc('crispbb', 'user', 'getforum', array('fid' => $fid));
                if (!empty($secLevels[$userLevel]['addforum'])) {
                    $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $fid));
                $tablinks['view'] = array(
                    'url' => $link,
                    'title' => xarML('Forum Overview'),
                    'label' => xarML('Overview'),
                    'active' => $modfunc == 'modify' && empty($current_sublink) ? true : false
                );
                }
                if (!empty($secLevels[$userLevel]['editforum'])) {
                    $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'edit'));
                $tablinks['modify'] = array(
                    'url' => $link,
                    'title' => xarML('Modify forum configuration'),
                    'label' => xarML('Edit'),
                    'active' => $current_sublink == 'edit' ? true : false
                );
                if ($forum['ftype'] != 1) {
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'forumhooks'));
                    $tablinks['forumhooks'] = array(
                        'url' => $link,
                        'title' => xarML('Set forum hooks configuration'),
                        'label' => xarML('Forum Hooks'),
                        'active' => $current_sublink == 'forumhooks' ? true : false
                    );
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'topichooks'));
                    $tablinks['topichooks'] = array(
                        'url' => $link,
                        'title' => xarML('Set forum topic hooks configuration'),
                        'label' => xarML('Topic Hooks'),
                        'active' => $current_sublink == 'topichooks' ? true : false
                    );
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'posthooks'));
                    $tablinks['posthooks'] = array(
                        'url' => $link,
                        'title' => xarML('Set forum post hooks configuration'),
                        'label' => xarML('Post Hooks'),
                        'active' => $current_sublink == 'posthooks' ? true : false
                    );
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'privileges'));
                    $tablinks['privileges'] = array(
                        'url' => $link,
                        'title' => xarML('Modify privileges for this forum'),
                        'label' => xarML('Privileges'),
                        'active' => $current_sublink == 'privileges' ? true : false
                    );
                    }
                }
                if (!empty($secLevels[$userLevel]['deleteforum'])) {
                    $link = LinkCache::getCachedURL('crispbb', 'admin', 'delete', array('fid' => $fid));
                $tablinks['delete'] = array(
                    'url' => $link,
                    'title' => xarML('Delete this forum'),
                    'label' => xarML('Delete'),
                    'active' => $modfunc == 'delete' ? true : false
                );
                }
            }
            if (!empty($secLevels[$userLevel]['addforum'])) {
            $activelinks = array('new');
                $link = LinkCache::getCachedURL('crispbb', 'admin', 'new');
            $sublinks['new'] = array(
                'url' => $link,
                'title' => xarML('Add a new forum to the system'),
                'label' => xarML('Add Forum'),
                'active' => in_array($modfunc, $activelinks) ? true : false
            );
            }
            if ($userLevel == 800) {
                $activelinks = array('forumconfig');
                    $link = LinkCache::getCachedURL('crispbb', 'admin', 'forumconfig');
                $sublinks['forumconfig'] = array(
                    'url' => $link,
                    'title' => xarML('Set default configuration for new forums'),
                    'label' => xarML('Set Defaults'),
                    'active' => in_array($modfunc, $activelinks) ? true : false
                );
                if (in_array($modfunc, $activelinks)) {
                    $tablinks = array();
                    $tablinks['forumconfig'] = array(
                        'url' => $link,
                        'title' => xarML('Set default forum configuration'),
                        'label' => xarML('Forum Config'),
                        'active' => empty($current_sublink) ? true : false
                    );
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'forumconfig', array('sublink' => 'forumhooks'));
                    $tablinks['forumhooks'] = array(
                        'url' => $link,
                        'title' => xarML('Set default forum hooks configuration'),
                        'label' => xarML('Forum Hooks'),
                        'active' => $current_sublink == 'forumhooks' ? true : false
                    );
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'forumconfig', array('sublink' => 'topichooks'));
                    $tablinks['topichooks'] = array(
                        'url' => $link,
                        'title' => xarML('Set default topic hooks configuration'),
                        'label' => xarML('Topic Hooks'),
                        'active' => $current_sublink == 'topichooks' ? true : false
                    );
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'forumconfig', array('sublink' => 'posthooks'));
                    $tablinks['posthooks'] = array(
                        'url' => $link,
                        'title' => xarML('Set default post hooks configuration'),
                        'label' => xarML('Post Hooks'),
                        'active' => $current_sublink == 'posthooks' ? true : false
                    );
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'forumconfig', array('sublink' => 'privileges'));
                    $tablinks['setdefaults'] = array(
                        'url' => $link,
                        'title' => xarML('Set default privileges configuration'),
                        'label' => xarML('Privileges'),
                        'active' => $current_sublink == 'privileges' ? true : false
                    );
                }
                $activelinks = array('posters');
                    $link = LinkCache::getCachedURL('crispbb', 'admin', 'posters');
                $sublinks['posters'] = array(
                    'url' => $link,
                    'title' => xarML('Overview of forum posters'),
                    'label' => xarML('Posters'),
                    'active' => in_array($modfunc, $activelinks) ? true : false
                );
                $activelinks = array('categories', 'newcat', 'modifycat', 'deletecat');
                    $link = LinkCache::getCachedURL('crispbb', 'admin', 'categories');
                $sublinks[in_array($modfunc, $activelinks)?$modfunc:'categories'] = array(
                    'url' => $link,
                    'title' => xarML('Category configuration for forums'),
                    'label' => xarML('Categories'),
                    'active' => in_array($modfunc, $activelinks) ? true : false
                );
                if (in_array($modfunc, $activelinks)) {
                    $tablinks = array();
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'categories');
                    $tablinks['categories'] = array(
                        'url' => $link,
                        'title' => xarML('View forum categories'),
                        'label' => xarML('View'),
                        'active' =>  $modfunc == 'deletecat' || ($modfunc == 'categories' && empty($current_sublink)) ? true : false
                    );
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'newcat');
                    $tablinks['newcat'] = array(
                        'url' => $link,
                        'title' => xarML('Add new forum category'),
                        'label' => xarML('New Category'),
                        'active' => $modfunc == 'newcat' ? true : false
                    );
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'categories', array('sublink' => 'mastercat'));
                    $tablinks['mastercat'] = array(
                        'url' => $link,
                        'label' => xarML('Base Category'),
                        'title' => xarML('Set Base Category for crispBB Forums'),
                        'active' => $current_sublink == 'mastercat' ? true : false
                    );
                }
                /*
                $activelinks = array('trashcan');
                $sublinks['trashcan'] = array(
                    'url' => xarModURL('crispbb', 'admin', 'trashcan'),
                    'title' => xarML('Overview of deleted items'),
                    'label' => xarML('Trashcan'),
                    'active' => in_array($modfunc, $activelinks) ? true : false
                );
                */
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

            $activefunc = 'view';
           break;

            case 'modifyconfig':
                if ($userLevel == 800) {
                    $activelinks = array('modifyconfig');
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'modifyconfig');
                    $sublinks['modifyconfig'] = array(
                        'url' => $link,
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
                        $link = LinkCache::getCachedURL('crispbb', 'admin', 'modifyhooks');
                    $sublinks['modifyhooks'] = array(
                        'url' => $link,
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
