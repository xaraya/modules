<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 *
 * Xaraya Recommend
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Recommend Module
 * @author John Cox
*/

/**
 * initialise the send to friend module
 */
function recommend_init()
{

    $title = 'Interesting Site :: %%sitename%%';
    // Set ModVar
    $email = 'Hello %%toname%%, your friend %%name%% considered our site interesting and wanted to send it to you.

Site Name: %%sitename%% :: %%siteslogan%%
Site URL: %%siteurl%%';

    $date = date('Y-m-d G:i:s');
    xarModSetVar('recommend', 'numbersent', 1);
    xarModSetVar('recommend', 'lastsentemail', 'niceguyeddie@xaraya.com');
    xarModSetVar('recommend', 'lastsentname', 'John Cox');
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

    return true;
}

/**
 * upgrade the send to friend module from an old version
 */
function recommend_upgrade($oldversion)
{
        // Upgrade dependent on old version number
    switch ($oldversion) {
        case 0.01:
            // Remove Masks and Instances
            xarRemoveMasks('recommend');
            xarRemoveInstances('recommend');
            
            //Set custom sendtofriend tag
            xarTplRegisterTag(
               'recommend', 'recommend-sendtofriend', array(),
               'recommend_userapi_rendersendtofriend'
            );
            // Set ModVar
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

            // Register Masks
            xarRegisterMask('OverviewRecommend','All','recommend','All','All','ACCESS_OVERVIEW');
            xarRegisterMask('EditRecommend','All','recommend','All','All','ACCESS_EDIT');
            break;
    }

    // Update successful
    return true;
}

/**
 * delete the send to friend module
 */
function recommend_delete()
{
    // Remove Masks and Instances
    xarRemoveMasks('recommend');
    xarRemoveInstances('recommend');
    xarTplUnregisterTag('recommend-sendtofriend');

    return true;
}

?>
