function xar_security_LoadSecurity()
{
    var url = 'index.php?module=security&type=admin&func=ajax_server';
    var pars = 'action=loadsecurity&param_modid='+$F('param_modid')+'&param_itemtype='+$F('param_itemtype') +
        '&param_itemid='+$F('param_itemid');
    var myAjax = new Ajax.Request( url, { method: 'post', parameters: pars, onComplete: xar_security_showResponse });
}

function xar_security_GenerateLevelsTable()
{
    //$('securitymessage').innerHTML = JSON.stringify(security);
    var sl = ' <table width="100%"><tr><th class="xar-security-levels-table-role">User</th>'
        + '<th class="xar-security-levels-table-col"><xar:mlstring>Overview</xar:mlstring></th>'
        + '<th class="xar-security-levels-table-col"><xar:mlstring>Read</xar:mlstring></th>'
        + '<th class="xar-security-levels-table-col"><xar:mlstring>Comment</xar:mlstring></th>'
        + '<th class="xar-security-levels-table-col"><xar:mlstring>Write</xar:mlstring></th>'
        + '<th class="xar-security-levels-table-col"><xar:mlstring>Manage</xar:mlstring></th>'
        + '<th class="xar-security-levels-table-col"><xar:mlstring>Admin</xar:mlstring></th>'
        +'</tr>';

    var value = null;
    for(key in security.levels)
    {
        if( key >= 0 )
        {
            sl += '<tr><td>'+security.user_names[key]+'</td>';
            for( keytype in security.levels[key])
            {
                value = security.levels[key][keytype];
                if( value == 1 || value == 0 )
                {
                    if( value == 1 )
                    {
                        sl += '<td><input type="checkbox" value="1" checked="checked" onclick="return xar_security_Toggle(\'' + key + '\',\'' + keytype + '\');" />' + '</td>';
                    }
                    else
                    {
                        sl += '<td><input type="checkbox" value="1" onclick="return xar_security_Toggle(\'' + key + '\',\'' + keytype + '\');" />' + '</td>';

                    }
                }
            }

            sl += '</tr>';
        }
    }

    sl += '<tr><td><select id="param_group" class="xar-form-textlong" onchange="return xar_security_AddGroup();">';
    for( key in groups )
    {
        sl += '<option value="'+key+'">' + groups[key] + '</option>';
    }
    sl += '</select></td></tr>';

    sl += '</table>';

    sl += '<input type="button" name="submit" value="Save" onclick="return xar_security_SaveSecurity();" />';

    $('securitylevels').innerHTML = sl;
}

function xar_security_SaveSecurity()
{
    var url = 'index.php?module=security&type=admin&func=ajax_server';
    var pars = 'action=savesecurity&param_security='+ JSON.stringify(security);
    var myAjax = new Ajax.Request( url, { method: 'post', parameters: pars, onComplete: xar_security_Saved });
}

function xar_security_Saved(originalRequest)
{
    $('securitymessage').innerHTML = originalRequest.responseText;
}

function xar_security_showResponse(originalRequest)
{
    // Remoce old message
    $('securitymessage').innerHTML = '';
    security = null;
    security = JSON.parse(originalRequest.responseText );
    xar_security_GenerateLevelsTable();
}

function xar_security_AddGroup()
{
    var new_level = new Object();
    new_level.overview = 0;
    new_level.read     = 0;
    new_level.comment  = 0;
    new_level.write    = 0;
    new_level.manage   = 0;
    new_level.admin    = 0;
    security.levels[$F('param_group')] = new_level;

    var options = $A($('param_group').getElementsByTagName('option'));
	var opt = options.find( function(name){
		return (name.value == $F('param_group'));
	});
    security.user_names[$F('param_group')] = opt.innerHTML;

    xar_security_GenerateLevelsTable();
}

function xar_security_Toggle( uid, leveltype )
{
    security.levels[uid][leveltype] = !security.levels[uid][leveltype];
    return true;
}