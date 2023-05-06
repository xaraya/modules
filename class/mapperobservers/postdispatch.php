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
        if (xarTpl::getPageTemplateName() != 'default') return $set;
        
        $set = true;
        $request = xarController::getRequest();
        if (!xarUser::isLoggedIn() && $request->getType() == 'user') {
            // For the anonymous user, see if a module specific page exists
            if (!xarTpl::setPageTemplateName('user-'.$request->getModule())) {
                xarTpl::setPageTemplateName($request->getModule());
            }
            return $set;
        }

        if (xarUser::isLoggedIn()) {
            if (xarUser::isLoggedIn() && $request->getType() == 'user') {
                // Same thing for user side where user is logged in
                if (!xarTpl::setPageTemplateName('user-'.$request->getModule())) {
                    xarTpl::setPageTemplateName('user');
                }
            } elseif (xarUser::isLoggedIn() && $request->getType() == 'admin') {
                 // Use the admin-$modName.xt page if available when $modType is admin
                // falling back on admin.xt if the former isn't available
                if (!xarTpl::setPageTemplateName('admin-'.$request->getModule())) {
                    xarTpl::setPageTemplateName('admin');
                }
            }
        }
    }
}
?>