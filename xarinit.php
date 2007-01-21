<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Recommend Module
 *
 */

/**
 * initialise the Recommend Module
 * @author John Cox
 * @author jojodee
 */
function recommend_init()
{

    $title = 'Interesting Site :: %%sitename%%';
    /* Set ModVar */
    $email = 'Hello %%toname%%, your friend %%name%% considered our site interesting and wanted to send it to you.

Site Name: %%sitename%% :: %%siteslogan%%
Site URL: %%siteurl%%';

    //$date = date('Y-m-d G:i:s');
    $date = time();
    xarModSetVar('recommend', 'numbersent', 0);
    xarModSetVar('recommend', 'date', $date);
    xarModSetVar('recommend', 'username', 'Admin');
    xarModSetVar('recommend', 'title', $title);
    xarModSetVar('recommend', 'template', $email);

    // Register Masks
    xarRegisterMask('OverviewRecommend','All','recommend','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('EditRecommend','All','recommend','All','All','ACCESS_EDIT');

    xarTplRegisterTag(
        'recommend', 'recommend-sendtofriend', array(),
        'recommend_userapi_rendersendtofriend'
    );

    /* This init function brings our module to version 1.0, run the upgrades for the rest of the initialisation */
    return example_upgrade('1.0.1');
}

/**
 * upgrade the send to friend module from an old version
 */
function recommend_upgrade($oldversion)
{
    switch ($oldversion) {
        case '0.01':
            // Remove Masks and Instances
            xarRemoveMasks('recommend');
            xarRemoveInstances('recommend');
            
            /* Set custom sendtofriend tag */
            xarTplRegisterTag(
               'recommend', 'recommend-sendtofriend', array(),
               'recommend_userapi_rendersendtofriend'
            );
            /* Set ModVar */
            $email = 'Hello %%toname%%, your friend %%name%% considered our site interesting and wanted to send it to you.

        Site Name: %%sitename%% :: %%siteslogan%%
        Site URL: %%siteurl%%';
            $title = 'Interesting Site :: %%sitename%%';
            $date = date('Y-m-d G:i:s');
            xarModSetVar('recommend', 'title', $title);
            xarModSetVar('recommend', 'numbersent', 1);
            xarModSetVar('recommend', 'lastsentemail', 'niceguyeddie@xaraya.com');
            xarModSetVar('recommend', 'lastsentname', 'John Cox');
            xarModSetVar('recommend', 'date', $date);
            xarModSetVar('recommend', 'username', 'Admin');
            xarModSetVar('recommend', 'template', $email);

            /* Register Masks */
            xarRegisterMask('OverviewRecommend','All','recommend','All','All','ACCESS_OVERVIEW');
            xarRegisterMask('EditRecommend','All','recommend','All','All','ACCESS_EDIT');
            
            case '1.0.0':
            
            $olddate = xarModGetVar('recommend', 'date');
            $newdate = strtotime($olddate);
            xarModSetVar('recommend', 'date', $newdate);
            
            case '1.0.1':
            xarRegisterMask('ReadRecommend','All','recommend','All','All','ACCESS_READ');
      
            case '1.0.2': //current version
            break;
    }

    /* Update successful */
    return true;
}

/**
 * delete the send to friend module
 */
function recommend_delete()
{
    /* Remove Masks and Instances */
    xarRemoveMasks('recommend');
    xarRemoveInstances('recommend');
    xarTplUnregisterTag('recommend-sendtofriend');

    return true;
}

?>