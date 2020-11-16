<?php
/**
 * PreRequest Subject Observer
 *
 * @TODO: this really belongs in themes
**/
sys::import('xaraya.structures.events.observer');
class WurflPostDispatchObserver extends EventObserver implements ixarEventObserver
{
    public $module = 'wurfl';
    
    public function notify(ixarEventSubject $subject)
    {
        // post request default page template handling
        $set = false;
        if (xarTpl::getPageTemplateName() != 'default') {
            return $set;
        }
        
        $set = true;
        $request = xarController::getRequest();
        if (!xarUserIsLoggedIn() && $request->getType() == 'user') {
            // For the anonymous user, see if a module specific page exists
            if (!xarTplSetPageTemplateName('user-'.$request->getModule())) {
                xarTplSetPageTemplateName($request->getModule());
            }
            return $set;
        }

        if (xarUserIsLoggedIn()) {
            if (xarUserIsLoggedIn() && $request->getType() == 'user') {
                // Same thing for user side where user is logged in
                if (!xarTpl::setPageTemplateName('user-'.$request->getModule())) {
                    xarTpl::setPageTemplateName('user');
                }
            } elseif (xarUserIsLoggedIn() && $request->getType() == 'admin') {
                // Use the admin-$modName.xt page if available when $modType is admin
                // falling back on admin.xt if the former isn't available
                if (!xarTpl::setPageTemplateName('admin-'.$request->getModule())) {
                    xarTpl::setPageTemplateName('admin');
                }
            }
        }
    }
}
