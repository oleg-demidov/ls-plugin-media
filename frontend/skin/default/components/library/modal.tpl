
{component "bs-modal"
    bmods = "lg"
    id = 'mediaLibraryModal'
    content = {component 'media:library' oUser = $oUser}
    header = $aLang.plugin.media.library.modal.header
}

{component "bs-modal"
    bmods = "lg"
    id = 'mediaInsert'
    content = ' '
    header = $aLang.plugin.media.library.modal_insert.header
}