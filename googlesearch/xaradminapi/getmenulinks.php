<?
function googlesearch_adminapi_getmenulinks()
{
    $menulinks[] = Array('url'   => xarModURL('googlesearch',
                                              'admin',
                                              'getcached'),
                          'title' => xarML('Retrieve Cached Google pages that match a query'),
                          'label' => xarML('Get Cached Pages'));
    $menulinks[] = Array('url'   => xarModURL('googlesearch',
                                              'admin',
                                              'managecached'),
                          'title' => xarML('Manage Cached Google pages'),
                          'label' => xarML('Manage Cached Pages'));
    $menulinks[] = Array('url'   => xarModURL('googlesearch',
                                              'admin',
                                              'collectcached'),
                          'title' => xarML('Collect Stored, Cached Google pages'),
                          'label' => xarML('Collect Cached Pages'));

    if(xarSecurityCheck('Admingooglesearch')) {
        $menulinks[] = Array('url'   => xarModURL('googlesearch',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Edit the googlesearch Configuration'),
                              'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>