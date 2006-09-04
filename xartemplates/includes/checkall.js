function xar_gallery_checkall(formobject) {
    for (i = 0; i < formobject.length; i++) {
        if (formobject.elements[i].type == 'checkbox') {
            formobject.elements[i].checked = ! formobject.elements[i].checked;
        }
    }
}