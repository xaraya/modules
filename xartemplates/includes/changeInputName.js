function xar_security_changeInputName(name, form, value)
{
    if( name == 'default_item_levels' )
    {
        form.default_item_levels_xoverview.name = "default_item_levels[" + value + "][overview]";
        form.default_item_levels_xread.name = "default_item_levels[" + value + "][read]";
        form.default_item_levels_xcomment.name = "default_item_levels[" + value + "][comment]";
        form.default_item_levels_xwrite.name = "default_item_levels[" + value + "][write]";
        form.default_item_levels_xmanage.name = "default_item_levels[" + value + "][manage]";
        form.default_item_levels_xadmin.name = "default_item_levels[" + value + "][admin]";
    }
    else if( name == 'default_module_levels' )
    {
        form.default_module_levels_xoverview.name = "default_module_levels[" + value + "][overview]";
        form.default_module_levels_xread.name = "default_module_levels[" + value + "][read]";
        form.default_module_levels_xcomment.name = "default_module_levels[" + value + "][comment]";
        form.default_module_levels_xwrite.name = "default_module_levels[" + value + "][write]";
        form.default_module_levels_xmanage.name = "default_module_levels[" + value + "][manage]";
        form.default_module_levels_xadmin.name = "default_module_levels[" + value + "][admin]";
    }
    else
    {
        form.levels_xoverview.name = "levels[" + value + "][overview]";
        form.levels_xread.name = "levels[" + value + "][read]";
        form.levels_xcomment.name = "levels[" + value + "][comment]";
        form.levels_xwrite.name = "levels[" + value + "][write]";
        form.levels_xmanage.name = "levels[" + value + "][manage]";
        form.levels_xadmin.name = "levels[" + value + "][admin]";
    }
    return true;
}