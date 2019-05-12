
{component_define_params params=[  'oUser' , 'attributes', 'classes', 'mods' ]}

{if $oUser->isAdministrator() or $oUser->isAllow('media_admin')}
    admin
{/if}

{component "media:uploader" 
    url = {router page="media/upload"}
    attributes = [
        "data-uploader" => true
    ]}
