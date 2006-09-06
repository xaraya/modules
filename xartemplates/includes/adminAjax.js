function xar_security_ModuleChanged(){
    $('param_itemtype').value = 0;
    var url = 'index.php?module=security&type=ajax&func=server';
    var pars = 'action=getitemtypes&param_modid='+$F('param_modid');
    var myAjax = new Ajax.Updater('itemtype', url, { method: 'post', parameters: pars});
    xar_security_ItemtypeChanged();
}
function xar_security_ItemtypeChanged(){
    var url = 'index.php?module=security&type=ajax&func=server';
    var pars = 'action=getitemids&param_modid='+$F('param_modid') + '&param_itemtype=' + $F('param_itemtype');
    var myAjax = new Ajax.Updater('itemid', url, { method: 'post', parameters: pars});
    $('param_itemid').value = 0;
    xar_security_ItemidChanged();
}

function xar_security_ItemidChanged(){
    xar_security_LoadSecurity();
}