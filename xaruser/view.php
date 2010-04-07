<?php
/**
 * View a list of items
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * view a list of items
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 *
 * @param startnum
 * @param catid
 * @return array
 */
function dyn_example_user_view()
{

	// Check here to see that the current user has the privilege to view items in the module
    if (!xarSecurityCheck('ViewDynExample')) return;

	// Get this value from the URL query string
	if(!xarVarFetch('tab', 'isset', $tab, NULL, XARVAR_DONT_SET)) {return;}

	// Get this value from the URL query string
	// TODO: why XARVAR_DONT_SET ?
    if(!xarVarFetch('startnum', 'isset', $data['startnum'], NULL, XARVAR_DONT_SET)) {return;}

    // Get user setting for 'items_per_page'
    $data['items_per_page'] = xarModUserVars::get('dyn_example','items_per_page');

/* APPROACH #1 #3 and #4 */

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

    // Get the object we'll be working with
    $mylist = DataObjectMaster::getObjectList(array('name' => 'dyn_example'));

    // Alternative security check e.g. if your module doesn't have its own security masks for items
    // Check if the current user has 'view' access to this object
    //if (!$mylist->checkAccess('view'))
    //    return xarResponse::Forbidden(xarML('View #(1) is forbidden', $mylist->label));

    // We have some filters for the items
    $filters = array('numitems'  => $data['items_per_page'],
                     'startnum'  => $data['startnum'],
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE
                    );

    // Count the items first if you want a full pager - otherwise you'll get simple previous/next links
    /* APPROACH #3 and #4 Important you call this if you want to use the base-pager template tag */
    $total = $mylist->countItems($filters);

    // Get the items
    $items = $mylist->getItems($filters);

    // Pass along the whole object list to the template.  Only needed in Approach #1
    $data['mylist'] = & $mylist;

/* end APPROACH #1 and #3 */

/* start APPROACH #2 : retrieve the items directly in the template */

    // Note: we don't retrieve any items here ourselves - we'll let the
    //       <xar:data-view ... /> tag do that in the template itself

/* end APPROACH #2 : retrieve the items directly in the template */

/* start APPROACH #3 */

    $data['properties'] =& $mylist->getProperties();
    $data['values'] =& $mylist->items;

/* end APPROACH #3  */

/* start APPROACH #4 : getting only the raw item values via API */
    $data['labels'] = array();
    $data['items'] = array();
    foreach ($items as $itemid => $fields) {
        $data['items'][$itemid] = array();
        foreach ($fields as $name => $value) {
            $data['items'][$itemid][$name] = xarVarPrepForDisplay($value);
            // do some other processing here...
        }
        // define in some labels
        if (count($data['labels']) == 0) {
            foreach (array_keys($fields) as $name) {
                $data['labels'][$name] = xarML(ucfirst($name));
            }
        }
    }
    // pager
/* APPROACH #3 and #4: use the base-pager tag to build your pager */
    /**
     * Total number of items for the pager ($total), this must be set (see APPROACH #1)
     * In many cases, this is the only parameter the pager tag will need
     * you can safely omit any parameters in which you are using the pager defaults
    **/
    $data['total'] = $total;
    /**
     * itemsperpage for the pager, in this case we just use $data['items_per_page']
     * since this module uses the items_per_page moduservar, we can omit this
     * the pager tag will automatically use the items_per_page moduservar setting
     * for the current module
    **/
    // $data['itemsperpage'] = $data['items_per_page'];
    /**
     * startnum for the pager, in this case we just use $data['startnum']
     * since this module uses the 'startnum' param, we can omit this
     * the pager tag will automatically look for a startnum parameter (eg in the url)
    **/
    // $data['startnum'] = $data['startnum'];
    /**
     * urltemplate, the pager accepts a template to use for page links
     * this means you can specify eg, to use page= instead of startnum= (not recommended, btw)
     * in your urls, or pass in extra params or remove others.
     * normally this isn't necessary, and the current url with a different startnum for
     * each page will suffice. When that's the case, we can omit this,
     * the pager tag will automatically use the current url as it's template
    **/
    $data['urltemplate'] = xarServer::getCurrentURL(array('startnum' => '%%'));
    /**
     * urlitemmatch, in the example above we're using %% to indicate the value
     * the pager should change when building links to pages, if you use something
     * else, you'll need to let the pager tag know, in this case we're using the default
     * the pager tag uses '%%' as the default value for urlitemmatch
    **/
    // $data['urlitemmatch'] = '%%';
    /**
     * blockitems, by default the pager displays pages in 'blocks' of 10
     * use this if you want a different number of pages per block
     * note, you could just set this in the template
    **/
    // $data['blockitems'] = 10;
    /**
     * tplmodule, use this if you want to use a pager template eg from dyn_example
     * name your templates pager-[templatename].xt and place them
     * in dyn_example/xartemplates/, then add tplmodule="$tplmodule" to the tag
     * the pager tag uses 'base' as the default value for tplmodule
     * note, you could just set this in the template
    **/
    // $data['tplmodule'] = 'dyn_example'; // dyn_example/xartemplates/
    /**
     * template, the base module comes with several layouts by way of templates
     * pager-[default|multipage|multipagenext|multipageprev|openended].xt
     * the pager tag uses 'default' as the default value for template
     * note, you could just set this in the template
    **/
    // $date['template'] = 'mypager'; // $data['tplmodule']/xartemplates/pager-mypager.xt
    /**
     * see dyn_example/xartemplates/user-view.xt APPROACH #3 and #4
     * for examples of using these values in the base-pager tag
    **/
/* end APPROACH #4 : getting only the raw item values via API */

	if (isset($tab)) {
		$data['tab'] = $tab;
	} else {
		$data['tab'] = 1;
	}

    // We are changing the name of the page to raise
    // better search engine compatibility.
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Dynamic Examples')));

    // Return the template variables defined in this function
    return $data;
}

?>
