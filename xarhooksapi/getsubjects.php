<?php
function keywords_hooksapi_getsubjects(array $args=array())
{
    extract($args);

    if (!isset($module) || empty($module)) {
        $module = null;
    }

    $subjects = xarHooks::getObserverSubjects('keywords', $module);
    if (!empty($subjects)) {
        foreach ($subjects as $hookedto => $hooks) {
            $modinfo = xarMod::getInfo(xarMod::getRegID($hookedto));
            try {
                $itemtypes = xarMod::apiFunc($hookedto, 'user', 'getitemtypes');
            } catch (Exception $e) {
                $itemtypes = array();
            }
            $modinfo['itemtypes'] = array();
            foreach ($itemtypes as $typeid => $typeinfo) {
                if (!isset($hooks[0]) && !isset($hooks[$typeid])) {
                    continue;
                } // not hooked
                $modinfo['itemtypes'][$typeid] = $typeinfo;
            }
            $subjects[$hookedto] += $modinfo;
        }
        ksort($subjects);
    }
    return $subjects;
}
