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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
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

    $smilies = array("':D','happy.gif','Happy'",
                    "':-D','happy.gif','Happy'",
                    "':grin:','happy.gif','Happy'",
                    "':\)','smile.gif','Smile'",
                    "':-\)','smile.gif','Smile'",
                    "':smile:','smile.gif','Smile'",
                    "':\(','frown.gif','Frown'",
                    "':-\(','frown.gif','Frown'",
                    "':frown:','frown.gif','Frown'",
                    "':sad:','frown.gif','Frown'",
                    "':o','surprised.gif','Surprised'",
                    "':0','surprised.gif','Surprised'",
                    "':-o','surprised.gif','Surprised'",
                    "':-0','surprised.gif','Surprised'",
                    "':eek:','surprised.gif','Surprised'",
                    "':\?','confused.gif','Confused'",
                    "':-\?','confused.gif','Confused'",
                    "'8\)','cool.gif','Cool'",
                    "'8-\)','cool.gif','Cool'",
                    "':cool:','cool.gif','Cool'",
                    "':lol:','lol.gif','LOL!'",
                    "':x','mad.gif','Mad'",
                    "':-x','mad.gif','Mad'",
                    "':mad:','mad.gif','Mad'",
                    "':p','razz.gif','Razz'",
                    "':-p','razz.gif','Razz'",
                    "':razz:','razz.gif','Razz'",
                    "':oops:','redface.gif','Embarassed'",
                    "':cry:','cry.gif','Sad'",
                    "':>','evil.gif','Evil'",
                    "':->','evil.gif','Evil'",
                    "':evil:','evil.gif','Evil'",
                    "':roll:','rolleyes.gif','WhateeeEver!'",
                    "';\)','wink.gif','Wink'",
                    "';-\)','wink.gif','Wink'",
                    "':wink:','wink.gif','Wink'");

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
                            'transform')) return;

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
function smilies_upgrade($oldversion)
{
    switch($oldversion){
       case '1.0':
       case '1.0.0':
            $smilies = xarModAPIFunc('smilies','user','getall');
            foreach ($smilies as $smile){
                // get rid of the 
                $smile['iconupdated'] = basename($smile['icon']);
                // update the field
                if(!xarModAPIFunc('smilies',
                                  'admin',
                                  'update',
                                   array('sid'      => $smile['sid'],
                                         'code'     => $smile['code'],
                                         'icon'     => $smile['iconupdated'],
                                         'emotion'  => $smile['emotion']))) return;
            }
            break;
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
                              'transform')) return;

    // Drop the table
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $smiliestable = $xartable['smilies'];
    $query = xarDBDropTable($smiliestable);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Remove module variables
    xarModDelAllVars('smilies');

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