
{component "bs-modal"
    bmods = "lg"
    id = 'mediaLibraryModal'
    content = {component 'media:library' oUser = $oUser}
    header = $aLang.plugin.media.library.modal.header
}

{component "media:media.modal-insert"}