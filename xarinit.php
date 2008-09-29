<?php
/**
 * Xaraya Smilies
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
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

    $smilies = array(
        array(':D','happy.gif','Happy'),
        array(':-D','happy.gif','Happy'),
        array(':grin:','happy.gif','Happy'),
        array(':)','smile.gif','Smile'),
        array(':-)','smile.gif','Smile'),
        array(':smile:','smile.gif','Smile'),
        array(':(','frown.gif','Frown'),
        array(':-(','frown.gif','Frown'),
        array(':frown:','frown.gif','Frown'),
        array(':sad:','frown.gif','Frown'),
        array(':o','surprised.gif','Surprised'),
        array(':0','surprised.gif','Surprised'),
        array(':-o','surprised.gif','Surprised'),
        array(':-0','surprised.gif','Surprised'),
        array(':eek:','surprised.gif','Surprised'),
        array(':?','confused.gif','Confused'),
        array(':-?','confused.gif','Confused'),
        array('8)','cool.gif','Cool'),
        array('8-)','cool.gif','Cool'),
        array(':cool:','cool.gif','Cool'),
        array(':lol:','lol.gif','LOL!'),
        array(':x','mad.gif','Mad'),
        array(':-x','mad.gif','Mad'),
        array(':mad:','mad.gif','Mad'),
        array(':p','razz.gif','Razz'),
        array(':-p','razz.gif','Razz'),
        array(':razz:','razz.gif','Razz'),
        array(':oops:','redface.gif','Embarassed'),
        array(':emb:','embarrassed.gif','Embarassed'),
        array(':cry:','cry.gif','Sad'),
        array(':>','evil.gif','Evil'),
        array(':->','evil.gif','Evil'),
        array(':evil:','evil.gif','Evil'),
        array(':roll:','rolleyes.gif','WhateeeEver!'),
        array(';)','wink.gif','Wink'),
        array(';-)','wink.gif','Wink'),
        array(':wink:','wink.gif','Wink'),
        array(':laugh:','laughing.gif','Laughing'),
        array(':roflmao:','roflmao.gif','ROFLMAO'),
        array(':beer:','drinkbeer.gif','Drink Beer'),
    );

    foreach ($smilies as $smilie) {
        list($code, $icon,$emotion) = $smilie;
        $nextId = $dbconn->GenId($smiliestable);
        $query = "INSERT INTO $smiliestable
                (xar_sid, xar_code, xar_icon, xar_emotion)
                VALUES (?,?,?,?)";
        $bindvars = array($nextId, $code, $icon, $emotion);
        $result =& $dbconn->Execute($query,$bindvars);
        if (!$result) return;
     }
    // Set up module variables
    xarModSetVar('smilies', 'itemsperpage', 20);
    // Bug 3957: added in 1.0.2, allow specific html tags to be ignored in transforms
    xarModSetVar('smilies', 'skiptags', serialize(array('code', 'textarea')));
    // Bug 5271: added in 1.0.3, allow admins to specify folder to serve smilies from
    // folder must be present in modules/smilies/xarimages/ folder
    // or /themes/{themename}/modules/smilies/xarimages/ folder
    xarModSetVar('smilies', 'image_folder', '');
    // Bug 5116: added in 1.0.3, option to hide duplicate emotions in user main
    xarModSetVar('smilies', 'showduplicates', false);
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

    // Set up module hooks
    if (!xarModRegisterHook('module',
                            'modifyconfig',
                            'GUI',
                            'smilies',
                            'admin',
                            'modifyconfighook')) return;

    // Set up module hooks
    if (!xarModRegisterHook('module',
                            'updateconfig',
                            'API',
                            'smilies',
                            'admin',
                            'updateconfighook')) return;

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
        case '1.0.1':
            // Bug 3957: allow admins to specify html tags to ignore when transforming smiley code, useful for 
            // eg: not transforming smilies in <code> and <textarea> tags used by BBCode's [code] tag
            xarModSetVar('smilies', 'skiptags', serialize(array('code', 'textarea')));
        case '1.0.2':
            // Bug 5271: allow admins to specify folder to serve smilies from
            // folder must be present in modules/smilies/xarimages/ folder
            // or /themes/{themename}/modules/smilies/xarimages/ folder
            xarModSetVar('smilies', 'imagefolder', '');
            // Bug 5116: option to hide duplicate emotions in user main
            xarModSetVar('smilies', 'showduplicates', false);
        case '1.0.3':
            // Set up module hooks
            if (!xarModRegisterHook('module',
                                    'modifyconfig',
                                    'GUI',
                                    'smilies',
                                    'admin',
                                    'modifyconfighook')) return;

            // Set up module hooks
            if (!xarModRegisterHook('module',
                                    'updateconfig',
                                    'API',
                                    'smilies',
                                    'admin',
                                    'updateconfighook')) return;            
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

    // Remove module hooks
    if (!xarModUnregisterHook('module',
                              'modifyconfig',
                              'GUI',
                              'smilies',
                              'admin',
                              'modifyconfighook')) return;

    // Remove module hooks
    if (!xarModUnregisterHook('module',
                              'updateconfig',
                              'API',
                              'smilies',
                              'admin',
                              'updateconfighook')) return;

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
