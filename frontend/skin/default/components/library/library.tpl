
{component_define_params params=[  'oUser' , 'attributes', 'classes', 'mods' ]}

{$attributes = [
    'data-library' => true
]}

<div {cattr list=$attributes}>

    {if $oUser->isAdministrator() or $oUser->isAllow('media_admin')}
        admin
    {/if}

    {component "bs-tabs" bmods = "tabs" items = [
        [
            active => true,
            text => $aLang.plugin.media.library.upload,
            content => {component "media:uploader" 
                url = {router page="media/upload"}
                attributes = [
                    "data-uploader" => true
                ]}
        ],
        [
            text => $aLang.plugin.media.library.library,
            content => '<div class="d-flex flex-wrap p-2" data-library-files></div>'
        ]
    ]}
    

    
</div>