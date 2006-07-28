<?
function example_admin_new(){

    $data = array();
/*
    $data['mapwidth']   = xarModGetVar('gmaps', 'mapwidth');
    $data['mapheight']  = xarModGetVar('gmaps', 'mapheight');
    $data['zoomlevel']  = xarModGetVar('gmaps', 'zoomlevel');
    $data['latitude']   = xarModGetVar('gmaps', 'latitude');
    $data['longitude']  = xarModGetVar('gmaps', 'longitude');
    $data['gmapskey']   = xarModGetVar('gmaps', 'gmapskey');
*/
//Temporary, 'cause I'm not workin' with defaults here
    $data['mapwidth']   = 800;
    $data['mapheight']  = 500;
    $data['zoomlevel']  = 1;
    $data['latitude']   = 0;
    $data['longitude']  = 30;
    $data['gmapskey']   = 'ABQIAAAA2Hq15yqt9tT8KVZMaBUu_hQSuE5V-KYDuBj6FSVq9WK3KljTSxQ8m73D5C9_VEpBBC4WY7r23GXhQw';
    
    return $data;
}
?>