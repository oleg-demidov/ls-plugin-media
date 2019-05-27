
{component_define_params params=[  'oUser' , 'attributes', 'classes', 'mods' ]}

{$attributes = [
    'data-library' => true
]}

<div {cattr list=$attributes}>

    {if $oUser->isAdministrator() or $oUser->isAllow('media_admin')}
        admin
    {/if}
    
    <div class="d-flex flex-wrap p-2" data-library-medias>
        {component "media:uploader" classes="media-item"}
    </div>
   

    
</div>