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
/**
 * udpate item from surveys_admin_modify
 */
function surveys_admin_updategroup()
{
    // Get parameters
    //var_dump($GLOBALS['_POST']); //return;

    //Checkbox work for submit buttons too
    if (!xarVarFetch('reassign', 'checkbox',  $reassign, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('repeat',   'int:1:100', $repeat,   1,     XARVAR_NOT_REQUIRED)) return;
    if ($reassign) {
        xarResponseRedirect(xarModUrl('surveys', 'admin', 'modifygroup', array('repeat' => $repeat)));
        return true;
    }
    if (!xarVarFetch('creating', 'bool', $creating)) return;
    if ($creating) {
        if (!xarVarFetch('gids', 'array', $gids)) return;
    } else {
        if (!xarVarFetch('gids', 'array', $gids)) return;
    }
    if (!xarVarFetch('name', 'list:str:0:255', $name)) return;
    if (!xarVarFetch('description', 'list:str:0:255', $description)) return;
    if (!xarVarFetch('moving', 'list:bool', $moving)) return;
    if (!xarVarFetch('groupexists', 'list:bool', $catexists)) return;
    if (!xarVarFetch('refgid', 'list:int:0', $refcid)) return;
    if (!xarVarFetch('position', 'list:enum:1:2:3:4', $position)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Reverses the order of gids with the 'last children' option:

    $old_gids = $gids;
    $gids = array();
    foreach ($old_gids as $key => $gid) {
        // Empty -> Creating Groups (ALL OF THEM should have empty gids!)
        if (empty($gid)) {
            $gid = $key;
            $creating = true;
        }

        if (intval($position[$gid]) == 3 ||
            intval($position[$gid]) == 2 ) {
            array_unshift ($gids, $gid);
        } else {
            array_push ($gids, $gid);
        }
    }
    foreach ($gids as $gid) {
        switch (intval($position[$gid])) {
            case 1: // above - same level
            default:
                $offset = 'before';
                break;
            case 2: // below - same level
                $offset = 'after';
                break;
            case 3: // below - child category
                $offset = 'lastchild';
                break;
            case 4: // above - child category
                $offset = 'firstchild';
                break;
        }

        // Pass to API
        if (!$creating) {
            if (!xarModAPIFunc(
                'surveys', 'admin', 'updategroup',
                array(
                    'gid'           => $gid,
                    'name'          => $name[$gid],
                    'desc'          => $description[$gid],
                    'moving'        => $moving[$gid],
                    'insertpoint'   => $refcid[$gid],
                    'offset'        => $offset
                )
            )) return;
        } else {
            // Pass to API
            if (!xarModAPIFunc(
                'surveys', 'admin', 'creategroup',
                array(
                    'name'          => $name[$gid],
                    'desc'          => $description[$gid],
                    'catexists'     => $catexists[$gid],
                    'insertpoint'   => $refcid[$gid],
                    'offset'        => $offset
                )
            )) return;
        }
    }
    if ($creating) {
        xarResponseRedirect(xarModUrl('surveys', 'admin', 'modifygroup', array()));
    } else {
        xarResponseRedirect(xarModUrl('surveys', 'admin', 'viewgroups', array()));
    }

    return true;
}
?>