<?php

/**
 * Create sample Autolink data (from an array - not quite an import)
 */

function autolinks_util_samples()
{
    // Security Check
    if (!xarSecurityCheck('AdminAutolinks')) {return;}

    // Template data.
    $data = array();

    // Create sample data if required.
    if (!xarVarFetch('createsamples', 'str', $createsamples, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!empty($createsamples)) {
        if (!xarSecConfirmAuthKey()) {return;}
        $result = xarModAPIfunc('autolinks', 'admin', 'samples', array('action' => 'create'));
        $data['status'] = $result;
    }

    $data['authid'] = xarSecGenAuthKey();

    // Get the samples.
    $sample_data = xarModAPIfunc(
        'autolinks', 'admin', 'samples', array('action' => 'get')
    );

    if (!is_array($sample_data)) {$sample_data = array();}

    $data['sample_data'] = $sample_data['autolink-types'];

    return $data;
}

?>