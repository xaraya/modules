<?
/**
 * modify an item
 * @param 'hid' the id of the headline to be modified
 */
function headlines_admin_modify($args)
{
    // Get parameters from whatever input we need
    list($hid,
         $obid)= xarVarCleanFromInput('hid',
                                      'obid');

    if (!empty($obid)) {
        $hid = $obid;
    }

    // Security Check
	if(!xarSecurityCheck('EditHeadlines')) return;
    $data = array();


    $data = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($data == false) return;

    $data['authid'] = xarSecGenAuthKey();

    $data['module'] = 'headlines';
    $data['itemtype'] = NULL; // forum
    $data['itemid'] = $hid;
    $hooks = xarModCallHooks('item','modify',$hid,$data);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
    
}
?>