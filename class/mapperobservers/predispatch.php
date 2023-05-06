<?php
/**
 * PreRequest Subject Observer
 *
 * @TODO: this really belongs in themes
**/
sys::import('xaraya.structures.events.observer');
class WurflPreDispatchObserver extends EventObserver implements ixarEventObserver
{
    public $module = 'wurfl';
    
    public function notify(ixarEventSubject $subject)
    {
        // pre request default theme handling
        // Default Page Title
        // CHECKME: Does this need to be here?
        $SiteSlogan = xarModVars::get('themes', 'SiteSlogan');
        xarTpl::setPageTitle(xarVar::prepForDisplay($SiteSlogan));

        $request = xarController::getRequest();
        if (empty($theme) && xarUser::isLoggedIn() && $request->getType() == 'admin') {
            // Admin theme 
            $theme = xarModVars::get('themes', 'admin_theme');
            self::setTheme($theme);
        } elseif (empty($theme) && (bool) xarModVars::get('themes', 'enable_user_menu') == true) {
            // User Override (configured in themes admin modifyconfig)
            // Users are allowed to set theme in profile, get user setting...
            $theme = xarModUserVars::get('themes', 'default_theme');
            // get the list of permitted themes
            $user_themes = xarModVars::get('themes', 'user_themes');
            $user_themes = !empty($user_themes) ? explode(',',$user_themes) : array();
    
            // Set the theme if it is valid
            if (!empty($user_themes) && in_array($theme, $user_themes)) {
                self::setTheme($theme);
            }
        } else {
            self::setTheme($theme);
        }
    }

    public static function setTheme($theme='') 
    {
        $set = false;
        if (empty($theme)) return $set;
        $theme = xarVar::prepForOS($theme);
        if (xarTheme::isAvailable($theme)){
            $set = true;
            xarTpl::setThemeName($theme);
            xarVar::setCached('Themes.name','CurrentTheme', $theme);
        }
        return $set;
    }

}
?>