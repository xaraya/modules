<?php
/**
 * Workflow Module Configuration for Symfony Workflow events
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */

class xarWorkflowConfig extends xarObject
{
    public static $config = [];

    public static function init(array $args = [])
    {
        static::loadConfig();
        //static::setAutoload();
    }

    public static function loadConfig()
    {
        if (!empty(static::$config)) {
            return static::$config;
        }
        static::$config = [];
        //$configFile = sys::varpath() . '/cache/processes/config.json';
        $configFile = dirname(__DIR__).'/xardata/config.workflows.php';
        if (file_exists($configFile)) {
            //$contents = file_get_contents($configFile);
            //static::$config = json_decode($contents, true);
            static::$config = include($configFile);
        }
        return static::$config;
    }

    public static function checkAutoload()
    {
        // @checkme we need to require composer autoload here
        $root = sys::root();
        // flat install supporting symlinks
        if (empty($root)) {
            $vendor = realpath(dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/../vendor');
        } else {
            $vendor = realpath($root . 'vendor');
        }
        if (!file_exists($vendor . '/autoload.php')) {
            $message = <<<EOT
This test needs composer autoload to run the workflows
$ composer require --dev symfony/workflow
...
$ head html/code/modules/workflow/xaruser/test_run.php
&lt;?php
sys::import('modules.workflow.class.config');
xarWorkflowConfig::setAutoload();
...
EOT;
            throw new Exception($message);
        }
        return $vendor .'/autoload.php';
    }

    public static function setAutoload()
    {
        $autoloadFile = static::checkAutoload();
        require_once $autoloadFile;
    }

    public static function hasWorkflowConfig(string $workflowName)
    {
        static::loadConfig();
        if (!empty(static::$config) && !empty(static::$config[$workflowName])) {
            return true;
        }
        return false;
    }

    public static function getWorkflowConfig(string $workflowName)
    {
        if (!static::hasWorkflowConfig($workflowName)) {
            throw new Exception('Unknown workflow ' . $workflowName);
        }
        return static::$config[$workflowName];
    }

    public static function getInitialMarking(string $workflowName)
    {
        $config = static::getWorkflowConfig($workflowName);
        return is_array($config['initial_marking']) ? $config['initial_marking'][0] : $config['initial_marking'];
    }
}
