<?php

class Keywords extends xarObject
{
    public const CONFIG_MODVAR = 'keywords_config';
    protected static $instance;
    protected static $configs = [];

    public function __construct()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }

    public static function getConfig($module, $itemtype=0, $args=[])
    {
        sys::import('modules.keywords.class.config');
        $hash = "$module:$itemtype";
        if (isset(self::$configs[$hash])) {
            if (empty($args)) {
                return self::$configs[$hash];
            }
            $config = self::$configs[$hash];
        }
        if (empty($config) && !empty($itemtype)) { // try for module itemtype specific settings
            $config = @unserialize(xarModVars::get($module, self::CONFIG_MODVAR.'_'.$itemtype));
        }
        if (empty($config)) { // fall back on module specific defaults
            $config =  @unserialize(xarModVars::get($module, self::CONFIG_MODVAR));
        }
        if (empty($config)) { // fall back on keywords defaults
            $config =  @unserialize(xarModVars::get('keywords', self::CONFIG_MODVAR));
        }
        if (empty($config)) {
            // first run ever or keywords defaults modvar deleted manually, re create using object defaults
            $config = new KeywordsConfig($module, $itemtype);
        } elseif (is_array($config)) {
            // config stored as array, create object using array values
            $config = new KeywordsConfig($module, $itemtype, $config);
        } elseif ($config->module != $module || $config->itemtype != $itemtype) {
            // config stored as object, sync module and itemtype
            $args['module'] = $module;
            $args['itemtype'] = $itemtype;
        }
        if (!empty($args)) {
            $config->setArgs($args);
            $config->refresh($config);
        }
        return self::$configs[$hash] = $config;
    }

    public static function setConfig($module, $itemtype=0, $args=[])
    {
        $config = self::getConfig($module, $itemtype);
        if (!empty($args)) {
            $config->setArgs($args);
            $config->refresh($config);
        }
        if ($config->config_state == 'itemtype' && !empty($itemtype)) {
            $modvar = self::CONFIG_MODVAR.'_'.$itemtype;
        } else {
            $modvar = self::CONFIG_MODVAR;
        }
        xarModVars::set($module, $modvar, serialize($config->getPublicProperties()));
        return true;
    }
}
