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

    $smilies = array("(1,':D','modules/smilies/xarimages/happy.gif','Happy')",
                    "(2,':-D','modules/smilies/xarimages/happy.gif','Happy')",
                    "(3,':grin:','modules/smilies/xarimages/happy.gif','Happy')",
                    "(4,':\)','modules/smilies/xarimages/smile.gif','Smile')",
                    "(5,':-\)','modules/smilies/xarimages/smile.gif','Smile')",
                    "(6,':smile:','modules/smilies/xarimages/smile.gif','Smile')",
                    "(7,':\(','modules/smilies/xarimages/frown.gif','Frown')",
                    "(8,':-\(','modules/smilies/xarimages/frown.gif','Frown')",
                    "(9,':frown:','modules/smilies/xarimages/frown.gif','Frown')",
                    "(10,':sad:','modules/smilies/xarimages/frown.gif','Frown')",
                    "(11,':o','modules/smilies/xarimages/surprised.gif','Surprised')",
                    "(12,':0','modules/smilies/xarimages/surprised.gif','Surprised')",
                    "(13,':-o','modules/smilies/xarimages/surprised.gif','Surprised')",
                    "(14,':-0','modules/smilies/xarimages/surprised.gif','Surprised')",
                    "(15,':eek:','modules/smilies/xarimages/surprised.gif','Surprised')",
                    "(16,':\?','modules/smilies/xarimages/confused.gif','Confused')",
                    "(17,':-\?','modules/smilies/xarimages/confused.gif','Confused')",
                    "(18,'8\)','modules/smilies/xarimages/cool.gif','Cool')",
                    "(19,'8-\)','modules/smilies/xarimages/cool.gif','Cool')",
                    "(20,':cool:','modules/smilies/xarimages/cool.gif','Cool')",
                    "(21,':lol:','modules/smilies/xarimages/lol.gif','LOL!')",
                    "(22,':x','modules/smilies/xarimages/mad.gif','Mad')",
                    "(23,':-x','modules/smilies/xarimages/mad.gif','Mad')",
                    "(24,':mad:','modules/smilies/xarimages/mad.gif','Mad')",
                    "(25,':p','modules/smilies/xarimages/razz.gif','Razz')",
                    "(26,':-p','modules/smilies/xarimages/razz.gif','Razz')",
                    "(27,':razz:','modules/smilies/xarimages/razz.gif','Razz')",
                    "(28,':oops:','modules/smilies/xarimages/redface.gif','Embarassed')",
                    "(29,':cry:','modules/smilies/xarimages/cry.gif','Sad')",
                    "(30,':>','modules/smilies/xarimages/evil.gif','Evil')",
                    "(31,':->','modules/smilies/xarimages/evil.gif','Evil')",
                    "(32,':evil:','modules/smilies/xarimages/evil.gif','Evil')",
                    "(33,':roll:','modules/smilies/xarimages/rolleyes.gif','WhateeeEver!')",
                    "(34,';\)','modules/smilies/xarimages/wink.gif','Wink')",
                    "(35,';-\)','modules/smilies/xarimages/wink.gif','Wink')",
                    "(36,':wink:','modules/smilies/xarimages/wink.gif','Wink')");


    foreach ($smilies as $smilie) {
        $query = "INSERT INTO $smiliestable VALUES $smilie";
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