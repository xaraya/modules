<?php

/**
 * Test that a given include template exists.
 * This fills in for a gap in the Xaraya core API, whereby include
 * templates do not get checked for suffixes.
 *
 * The code here is based on the function xarTpl_includeModuleTemplate()
 * so will need a certain amount of tracking if the logic in that core
 * function changes.
 *
 * If a suffix exists, then it will be used (there is no fallback here to the
 * non-suffixed template name.
 *
 * @param modName string Module name
 * @param templateName string Template base name
 * @param templateSuffix string Template suffix name
 * @return boolean True if the template with the optional suffix exists; false otherwise.
 *
 */

function mag_userapi_testtemplate($args)
{
    extract($args);

    if (empty($modname)) $modName = 'mag';

    // These two are mandatory.
    if (empty($templateName) || empty($modName)) return false;

    $templateName = xarVarPrepForOS($templateName);

    // Add on the suffix if we have one.
    if (!empty($templateSuffix)) $templateName .= '-' . xarVarPrepForOS($templateSuffix);

    if (file_exists(xarTplGetThemeDir() . "/modules/$modName/includes/$templateName.xt")) {
        return true;
    } elseif (file_exists("modules/$modName/xartemplates/includes/$templateName.xd")) {
        return true;
    }

    return false;
}

?>