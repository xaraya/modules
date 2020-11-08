<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * initialise the scheduler module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
sys::import('xaraya.tableddl');

function scheduler_init()
{
    # --------------------------------------------------------
#
    # Define the table structures
#

    // Get database information
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $prefix = xarDB::getPrefix();

    try {
        $dbconn->begin();

        // *_scheduler_jobs
        $query = xarTableDDL::createTable(
            $xartable['scheduler_jobs'],
            array('id' =>          array('type'        => 'integer',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'increment'   => true,
                                                              'primary_key' => true),
                                        'module' =>      array('type'       => 'varchar',
                                                               'size'       => 64,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'type' =>        array('type'       => 'varchar',
                                                               'size'       => 64,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'function' =>    array('type'       => 'varchar',
                                                               'size'       => 64,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'parameters' =>  array('type'       => 'varchar',
                                                               'size'       => 255,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'startdate' =>   array('type'        => 'integer',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'enddate' =>     array('type'        => 'integer',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'job_trigger' => array('type'        => 'integer',
                                                              'size'        => 'tiny',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'sourcetype' =>  array('type'        => 'integer',
                                                              'size'        => 'tiny',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'lastrun' =>     array('type'        => 'integer',
                                                              'unsigned'    => true,
                                                              'null'        => false,
                                                              'default'     => '0'),
                                        'job_interval' =>array('type'    => 'varchar',
                                                              'size'        => 4,
                                                              'null'        => false,
                                                              'default'     => ''),
                                        'result' =>      array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'source' =>      array('type'       => 'varchar',
                                                               'size'       => 128,
                                                               'null'       => false,
                                                               'default'    => ''),
                                        'crontab' =>      array('type'       => 'text',
                                                               'size'       => 'medium',
                                                               'null'       => false))
        );
        $dbconn->Execute($query);

        $dbconn->commit();
    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }

    # --------------------------------------------------------
#
    # Set up modvars
#
    xarModVars::set('scheduler', 'trigger', 'disabled');
    xarModVars::set('scheduler', 'lastrun', 0);
    xarModVars::set('scheduler', 'items_per_page', 20);
    xarModVars::set('scheduler', 'interval', 5*60);
    # --------------------------------------------------------
#
    # Register masks
#
    xarMasks::register('ManageScheduler', 'All', 'scheduler', 'All', 'All', 'ACCESS_DELETE');
    xarMasks::register('AdminScheduler', 'All', 'scheduler', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
#
    # Register privileges
#
    xarPrivileges::register('ManageScheduler', 'All', 'scheduler', 'All', 'All', 'ACCESS_DELETE');
    xarPrivileges::register('AdminScheduler', 'All', 'scheduler', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
#
    # Create DD objects
#
    // First pull in this module's properties as we use at least one in the objects below
    PropertyRegistration::importPropertyTypes(false, array('modules/scheduler/xarproperties'));

    $module = 'scheduler';
    $objects = array(
                   'scheduler_jobs',
                     );

    if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', array('module' => $module, 'objects' => $objects))) {
        return;
    }
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
            if (!xarMod::apiFunc(
                'blocks',
                'admin',
                'register_block_type',
                array('modName' => 'scheduler',
                                     'blockType' => 'trigger')
            )) {
                return;
            }
            // fall through to the next upgrade

            // no break
        case '1.1.0':
            // fall through to the next upgrade

        case '1.2.0':

            $triggers = xarMod::apiFunc('scheduler', 'user', 'triggers');
            $checktypes = xarMod::apiFunc('scheduler', 'user', 'sources');

            // fetch modvars
            $checktype = xarModVars::get('scheduler', 'checktype');
            $checkvalue = xarModVars::get('scheduler', 'checkvalue');
            $jobs = xarModVars::get('scheduler', 'jobs');
            $lastrun = xarModVars::get('scheduler', 'lastrun');
            $maxjobid = xarModVars::get('scheduler', 'maxjobid');
            $running = xarModVars::get('scheduler', 'running');
            $trigger = xarModVars::get('scheduler', 'trigger');

            switch ($trigger) {
                case 'external':
                    $trigger = 1;
                    break;
                case 'block':
                    $trigger = 2;
                    break;
                case 'event':
                    $trigger = 3;
                    break;
                default:
                case 'disabled':
                    $trigger = 0;
                    break;
            }

            switch ($checktype) {
                case 'ip':
                    $trigger = 2;
                    break;
                case 'proxy':
                    $trigger = 3;
                    break;
                case 'host':
                    $trigger = 4;
                    break;
                default:
                case 'local':
                    $trigger = 1;
                    break;
            }

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
                if (isset($job['config'])) {
                    $bindvars[] = $job['config'];
                } else {
                    $bindvars[] = '';
                }
                $result = $dbconn->Execute($query, $bindvars);

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
// no break
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
    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', array('module' => 'scheduler'));
}
