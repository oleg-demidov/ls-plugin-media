
{component_define_params params=[  'oUser' , 'attributes', 'classes', 'mods' ]}


<div {cattr list=$attributes} data-library>

   {* {if $oUser->isAdministrator() or $oUser->isAllow('media_admin')}
        admin
    {/if}*}
    
    {component "media:uploader" }
    
    <div class="d-flex flex-wrap py-2" data-library-medias></div>
    
    
    {component "bs-modal"
        bmods   = "lg"
        attributes = [
            'data-media-modal' => true
        ]
        header  = ""
        content = "media"
    }

</div>