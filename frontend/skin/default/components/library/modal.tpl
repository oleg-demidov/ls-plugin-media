{capture name="header"}
    {$aLang.plugin.media.library.modal.header}
    {component "bs-button" 
        text = "Загрузить по ссылке" 
        attributes = [ "data-btn-upload-url" => true ] 
        bmods = "outline-secondary sm"}
{/capture}


{component "bs-modal"
    bmods = "lg"
    id = 'mediaLibraryModal'
    content = {component 'media:library' oUser = $oUser}
    header = $smarty.capture.header
}

{component "media:media.modal-insert"}