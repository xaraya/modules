<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub 
 */
/**
 * initialise the scheduler module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function scheduler_init()
{
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    // Create database table

    // Get database information
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $prefix = xarDB::getPrefix();

    try {
        $dbconn->begin();

        // *_scheduler_jobs
        $query = xarDBCreateTable($xartable['scheduler_jobs'],
                                  array('id' =>         array('type'        => 'integer',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'increment'   => true,
                                                              'primary_key' => true),
                                        'job_trigger' => array('type'        => 'integer',
                                                              'size'        => 'tiny',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'checktype' =>  array('type'        => 'integer',
                                                              'size'        => 'tiny',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'lastrun' =>    array('type'        => 'integer',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'job_interval' =>   array('type'    => 'varchar',
                                                              'size'        => 4,
                                                              'null'        => false,
                                                              'default'     => ''),
                                        'module' =>      array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'functype' =>    array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'func' =>        array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'result' =>      array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'checkvalue' =>  array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'config' =>      array('type'       => 'text',
                                                               'size'       => 'medium',
                                                               'null'       => false)));
        $dbconn->Execute($query);

        $dbconn->commit();
    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }

    xarRegisterMask('AdminScheduler', 'All', 'scheduler', 'All', 'All', 'ACCESS_ADMIN');

    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                       array('modName' => 'scheduler',
                             'blockType' => 'trigger'))) return;

    // Initialisation successful
    return true;
}

/**
 * upgrade the scheduler module from an old version
 * This function can be called multiple times
 */
function scheduler_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                               array('modName' => 'scheduler',
                                     'blockType' => 'trigger'))) return;
            // fall through to the next upgrade

        case '1.1.0':
            // fall through to the next upgrade

        case '1.2.0':
            //Load Table Maintenance API
            sys::import('xaraya.tableddl');

            // Create database table

            // Get database information
            $dbconn = xarDB::getConn();
            $xartable =& xarDB::getTables();
            $prefix = xarDB::getPrefix();

            try {
                $dbconn->begin();

                // *_scheduler_jobs
		        $query = xarDBCreateTable($xartable['scheduler_jobs'],
                                  array('id' =>         array('type'        => 'integer',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'increment'   => true,
                                                              'primary_key' => true),
                                        'job_trigger' => array('type'        => 'integer',
                                                              'size'        => 'tiny',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'checktype' =>  array('type'        => 'integer',
                                                              'size'        => 'tiny',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'lastrun' =>    array('type'        => 'integer',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'job_interval' =>   array('type'    => 'varchar',
                                                              'size'        => 4,
                                                              'null'        => false,
                                                              'default'     => ''),
                                        'module' =>      array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'functype' =>    array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'func' =>        array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'result' =>      array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'checkvalue' =>  array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'config' =>      array('type'       => 'text',
                                                               'size'       => 'medium',
                                                               'null'       => false)));
                $dbconn->Execute($query);

                $dbconn->commit();
            } catch (Exception $e) {
                $dbconn->rollback();
                throw $e;
            }

            $triggers = xarModAPIFunc('scheduler','user','triggers');
            $checktypes = xarModAPIFunc('scheduler','user','sources');

            // fetch modvars
            $checktype = xarModVars::get('scheduler', 'checktype');
            $checkvalue = xarModVars::get('scheduler', 'checkvalue');
            $jobs = xarModVars::get('scheduler', 'jobs');
            $lastrun = xarModVars::get('scheduler', 'lastrun');
            $maxjobid = xarModVars::get('scheduler', 'maxjobid');
            $running = xarModVars::get('scheduler', 'running');
            $trigger = xarModVars::get('scheduler', 'trigger');

            // convert old strings to new ints
            $flip_triggers = array_flip($triggers);
            $flip_checktypes = array_flip($checktypes);


            $trigger = $flip_triggers[$trigger];
            $checktype = $flip_checktypes[$checktype];

            // import modvar data into table
            $jobs = unserialize($jobs);

            $table = $xartable['scheduler_jobs'];

            foreach ($jobs as $id => $job) {
                // use trigger and lastrun values for all existing jobs


                $query = "INSERT INTO $table
                            VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                $bindvars = array($id, 
                                  $trigger, 
                                  $checktype, 
                                  $job['lastrun'], 
                                  $job['interval'], 
                                  $job['module'], 
                                  $job['type'], 
                                  $job['func'], 
                                  $job['result'], 
                                  $checkvalue);
                if(isset($job['config'])) {
                    $bindvars[] = $job['config'];
                } else {
                    $bindvars[] = '';
                }
                $result = $dbconn->Execute($query,$bindvars);

                // create running modvar for each job
                xarModVars::set('scheduler', 'running.' . $id, 0);
            }

            // delete modvars
/*            xarModVars::delete('scheduler', 'checktype');
            xarModVars::delete('scheduler', 'checkvalue');
            xarModVars::delete('scheduler', 'jobs');
            xarModVars::delete('scheduler', 'lastrun');
            xarModVars::delete('scheduler', 'maxjobid');
            xarModVars::delete('scheduler', 'running');
            xarModVars::delete('scheduler', 'trigger');
*/
        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the scheduler module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function scheduler_delete()
{
    xarModVars::delete('scheduler', 'trigger');
    xarModVars::delete('scheduler', 'lastrun');
    xarModVars::delete('scheduler', 'jobs');

    xarRemoveMasks('scheduler');

    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type',
                       array('modName' => 'scheduler',
                             'blockType' => 'trigger'))) return;

    // Deletion successful
    return true;

    // return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'scheduler'));

}

?>
