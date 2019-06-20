ls.hook.add('ls_template_init_end', function(){ 
    $('[data-library]').mediaLibrary();
    $('#modalInsert').mediaModalInsert();
    $('[data-media-field]').mediaField();
    $('[data-cropper]').mediaCropper();
});

