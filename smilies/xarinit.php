<?php
/**
 * File: $Id$
 *
 * Xaraya Smilies
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/

//Load Table Maintainance API
xarDBLoadTableMaintenanceAPI();
/**
 * initialise the smilies module
 */
function smilies_init()
{

    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $smiliestable = $xartable['smilies'];

    $fields = array(
    'xar_sid'          => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
    'xar_code'         => array('type'=>'varchar','size'=>100,'null'=>false,'default'=>''),
    'xar_icon'         => array('type'=>'varchar','size'=>100,'null'=>false,'default'=>''),
    'xar_emotion'      => array('type'=>'varchar','size'=>200,'null'=>false,'default'=>'')
    );

    $query = xarDBCreateTable($smiliestable,$fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_xar_smilies_1',
                   'fields'    => array('xar_code'),
                   'unique'    => TRUE);
    $query = xarDBCreateIndex($smiliestable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $smilies = array("':D','modules/smilies/xarimages/happy.gif','Happy'",
                    "':-D','modules/smilies/xarimages/happy.gif','Happy'",
                    "':grin:','modules/smilies/xarimages/happy.gif','Happy'",
                    "':\)','modules/smilies/xarimages/smile.gif','Smile'",
                    "':-\)','modules/smilies/xarimages/smile.gif','Smile'",
                    "':smile:','modules/smilies/xarimages/smile.gif','Smile'",
                    "':\(','modules/smilies/xarimages/frown.gif','Frown'",
                    "':-\(','modules/smilies/xarimages/frown.gif','Frown'",
                    "':frown:','modules/smilies/xarimages/frown.gif','Frown'",
                    "':sad:','modules/smilies/xarimages/frown.gif','Frown'",
                    "':o','modules/smilies/xarimages/surprised.gif','Surprised'",
                    "':0','modules/smilies/xarimages/surprised.gif','Surprised'",
                    "':-o','modules/smilies/xarimages/surprised.gif','Surprised'",
                    "':-0','modules/smilies/xarimages/surprised.gif','Surprised'",
                    "':eek:','modules/smilies/xarimages/surprised.gif','Surprised'",
                    "':\?','modules/smilies/xarimages/confused.gif','Confused'",
                    "':-\?','modules/smilies/xarimages/confused.gif','Confused'",
                    "'8\)','modules/smilies/xarimages/cool.gif','Cool'",
                    "'8-\)','modules/smilies/xarimages/cool.gif','Cool'",
                    "':cool:','modules/smilies/xarimages/cool.gif','Cool'",
                    "':lol:','modules/smilies/xarimages/lol.gif','LOL!'",
                    "':x','modules/smilies/xarimages/mad.gif','Mad'",
                    "':-x','modules/smilies/xarimages/mad.gif','Mad'",
                    "':mad:','modules/smilies/xarimages/mad.gif','Mad'",
                    "':p','modules/smilies/xarimages/razz.gif','Razz'",
                    "':-p','modules/smilies/xarimages/razz.gif','Razz'",
                    "':razz:','modules/smilies/xarimages/razz.gif','Razz'",
                    "':oops:','modules/smilies/xarimages/redface.gif','Embarassed'",
                    "':cry:','modules/smilies/xarimages/cry.gif','Sad'",
                    "':>','modules/smilies/xarimages/evil.gif','Evil'",
                    "':->','modules/smilies/xarimages/evil.gif','Evil'",
                    "':evil:','modules/smilies/xarimages/evil.gif','Evil'",
                    "':roll:','modules/smilies/xarimages/rolleyes.gif','WhateeeEver!'",
                    "';\)','modules/smilies/xarimages/wink.gif','Wink'",
                    "';-\)','modules/smilies/xarimages/wink.gif','Wink'",
                    "':wink:','modules/smilies/xarimages/wink.gif','Wink'");


    foreach ($smilies as $smilie) {
        // Get next ID in table
        $nextId = $dbconn->GenId($smiliestable);
        $query = "INSERT INTO $smiliestable VALUES ($nextId,$smilie)";
        $result =& $dbconn->Execute($query);
        if (!$result) return;

    }

    // Set up module variables
    xarModSetVar('smilies', 'itemsperpage', 20);

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'smilies',
                             'blockType'=> 'smiley'))) return;

    // Set up module hooks
    if (!xarModRegisterHook('item',
                            'transform',
                            'API',
                            'smilies',
                            'user',
                            'transform')) {
        return false;
    }

    // Register Masks
    xarRegisterMask('OverviewSmilies','All','smilies','All','All','ACCESS_READ');
    xarRegisterMask('ReadSmilies','All','smilies','All','All','ACCESS_READ');
    xarRegisterMask('EditSmilies','All','smilies','All','All','ACCESS_EDIT');
    xarRegisterMask('AddSmilies','All','smilies','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteSmilies','All','smilies','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminSmilies','All','smilies','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}


function smilies_upgrade($oldversion){
    switch($oldversion){
       case '1.0':
    }
    return true;
}

/**
 * delete the smiley module
 */
function smilies_delete()
{
    // Remove module hooks
    if (!xarModUnregisterHook('item',
                              'transform',
                              'API',
                              'smilies',
                              'user',
                              'transform')) {
        return false;
    }

    // Drop the table
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $smiliestable = $xartable['smilies'];
    $query = xarDBDropTable($smiliestable);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Remove module variables
    xarModDelVar('smilies', 'itemsperpage');

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'smilies',
                             'blockType'=> 'smiley'))) return;

    // Remove Masks and Instances
    xarRemoveMasks('smilies');
    xarRemoveInstances('smilies');

    // Deletion successful
    return true;
}

?>
