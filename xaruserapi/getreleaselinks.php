<?php


function release_userapi_getreleaselinks($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($rnid)) {
        $rnid = null;
    }
    if (!isset($count)) {
        $count = true;
    }
    if (!$count) {
        $all = 1;
    }

   $releaseinfo = array();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

     $releaseinfo = xarModAPIFunc(
        'release', 'user', 'getallnotes',
        array('approved' => 1)
    );

    $totreleases = count($releaseinfo);

    $releaselinks = array();
    foreach ($releaseinfo as $release) {
         $item['release']['regname']=$release['regname'];
         $item['release']['link'] = xarModURL('release','admin','modifynote',array('rnid'=>$release['rnid']));
        $releaselinks[] = $item['release'];
    }
    $releaselinks['counted']=$totreleases;
    return $releaselinks;
}

?>
