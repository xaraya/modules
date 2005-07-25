function CheckAll() {
        
    for (var i = 0; i < document.local_import_form.elements.length; i++) {
        if (document.local_import_form.file_all.checked) {
            if (document.local_import_form.elements[i].type == 'checkbox') {
                document.local_import_form.elements[i].checked = 0;
            }
        } else {
            if (document.local_import_form.elements[i].type == 'checkbox') {
                document.local_import_form.elements[i].checked = 1;
            }
        }
    }

    document.local_import_form.file_all.checked = !(document.local_import_form.file_all.checked);
}
