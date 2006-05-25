function xar_security_changeInputName(form, value)
{
    form.xoverview.name = "overview[" + value + "]";
    form.xread.name = "read[" + value + "]";
    form.xcomment.name = "comment[" + value + "]";
    form.xwrite.name = "write[" + value + "]";
    form.xmanage.name = "manage[" + value + "]";
    form.xadmin.name = "admin[" + value + "]";
    return true;
}