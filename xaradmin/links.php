<?php
/**
 * Site Tools Check links package
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * @author mikespub
 * Check URLs and images in articles, roles, ...
 */
function sitetools_admin_links()
{
    if (!xarVar::fetch('find', 'str:1:', $find, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('check', 'str:1:', $check, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    /* Security check */
    if (!xarSecurity::check('AdminSiteTools')) {
        return;
    }

    $data = array();

    $data['checked'] = xarModVars::get('sitetools', 'links_checked');
    if (!xarVar::fetch('startnum', 'str:1:', $data['startnum'], '1', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('sort', 'str:1:', $data['sort'], '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('filter', 'str:1:', $filter, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('status', 'str:1:', $status, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!empty($filter)) {
        $data['where'] = 'status ne 200';
    } elseif (!empty($status) && is_numeric($status)) {
        $data['where'] = 'status eq ' . $status;
    } else {
        $data['where'] = '';
    }

    /* get the list of relevant link fields per module/itemtype */
    $data['modules'] = xarMod::apiFunc('sitetools', 'admin', 'getlinkfields');
    if (!isset($data['modules'])) {
        return;
    }

    if (empty($find)) {
        $todo = xarModVars::get('sitetools', 'links_todo');
        if (!empty($todo)) {
            $data['todo'] = unserialize($todo);
        }
        $count = xarModVars::get('sitetools', 'links_count');
        $data['total'] = 0;
        if (!empty($count)) {
            $data['count'] = unserialize($count);
            foreach ($data['count'] as $module => $numitems) {
                $data['total'] += $numitems;
            }
        }

        /* nothing more to do here... */
        if (empty($check)) {
            /* Generate a one-time authorisation code for this operation */
            $data['authid'] = xarSec::genAuthKey();

            /* Return the template variables defined in this function */
            return $data;
        }
    }

    /* Confirm authorisation code. */
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    if (!empty($check)) {
        /* let's run without interruptions for a while :) */
        @ignore_user_abort(true);
        @set_time_limit(30*60);

        /* For some reason, PHP thinks it's in the Apache root during shutdown functions,
         * so we save the current base dir here - otherwise xarMod::apiFunc() will fail
         */
        $GLOBALS['xarSitetools_BaseDir'] = realpath('.');

        /* register the shutdown function that will execute the jobs after this script finishes */
        register_shutdown_function('sitetools_admin_startcheck');

        /* try to force a reload (still doesn't work for Windows servers) */
        $url = xarController::URL('sitetools', 'admin', 'links');
        $url = preg_replace('/&amp;/', '&', $url);
        header("Refresh: 0; URL=$url");

        $data['checked'] = xarML('Link check started');

        /* Generate a one-time authorisation code for this operation *?
        $data['authid'] = xarSec::genAuthKey();

        /* Return the template variables defined in this function */
        return $data;
    }

    @set_time_limit(120);

    if (!xarVar::fetch('todo', 'isset', $todo, array(), xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('skiplocal', 'isset', $skiplocal, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('method', 'isset', $method, 'GET', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('follow', 'isset', $follow, 0, xarVar::NOT_REQUIRED)) {
        return;
    }

    // build up field list per module & itemtype
    $fields = array();
    foreach ($todo as $field => $val) {
        if (empty($val)) {
            continue;
        }
        if (!preg_match('/^(\w+)\.(\d+)\.(\w+)/', $field, $matches)) {
            continue;
        }
        if (!isset($fields[$matches[1]])) {
            $fields[$matches[1]] = array();
        }
        if (!isset($fields[$matches[1]][$matches[2]])) {
            $fields[$matches[1]][$matches[2]] = array();
        }
        $fields[$matches[1]][$matches[2]][] = $matches[3];
    }
    /* find the links in the different fields and save them to the database */
    $data['count'] = xarMod::apiFunc(
        'sitetools',
        'admin',
        'findlinks',
        array('fields' => $fields,
                                         'skiplocal' => $skiplocal)
    );
    if (!isset($data['count'])) {
        return;
    }

    $data['total'] = 0;
    foreach ($data['count'] as $module => $numitems) {
        $data['total'] += $numitems;
    }

    $data['todo'] = $todo;

    xarModVars::set('sitetools', 'links_todo', serialize($data['todo']));
    xarModVars::set('sitetools', 'links_count', serialize($data['count']));
    xarModVars::set('sitetools', 'links_skiplocal', $skiplocal);
    xarModVars::set('sitetools', 'links_method', $method);
    xarModVars::set('sitetools', 'links_follow', $follow);

    /* some clean-up of previous link checks */
    if (!empty($data['checked'])) {
        $data['checked'] = '';
        xarModVars::delete('sitetools', 'links_checked');
    }

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSec::genAuthKey();

    /*return */
    return $data;
}

/**
 * shutdown function to start checking the links
 */
function sitetools_admin_startcheck()
{
    /* For some reason, PHP thinks it's in the Apache root during shutdown functions,
     * so we move back to our own base dir first - otherwise xarMod::apiFunc() will fail
     */
    if (!empty($GLOBALS['xarSitetools_BaseDir'])) {
        chdir($GLOBALS['xarSitetools_BaseDir']);
    }

    $skiplocal = xarModVars::get('sitetools', 'links_skiplocal');
    $method = xarModVars::get('sitetools', 'links_method');
    $follow = xarModVars::get('sitetools', 'links_follow');
    xarMod::apiFunc(
        'sitetools',
        'admin',
        'checklinks',
        array('skiplocal' => $skiplocal,
                        'method' => $emthod,
                        'follow' => $follow)
    );
}
