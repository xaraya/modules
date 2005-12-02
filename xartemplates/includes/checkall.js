/**
 * check or uncheck all checkboxes in a form
 * Example :
 * <a href="javascript:xar_base_checkall(document.forms['thisform'],true)">Check All</a>
 * <a href="javascript:xar_base_checkall(document.forms['thisform'],false)">Uncheck All</a>
 *
 * modified by Curtis Farnham for toggling with single form element
 */
var toggle = true;

function xar_ebulletin_checkall(formobject) {
    for (i = 0; i < formobject.length; i++) {
        if (formobject.elements[i].type == 'checkbox') {
            formobject.elements[i].checked = toggle;
        }
    }
    toggle = !toggle;
}

