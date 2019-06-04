
{component_define_params params=[  'oUser' , 'attributes', 'classes', 'mods' ]}


<div {cattr list=$attributes} data-library>

   {* {if $oUser->isAdministrator() or $oUser->isAllow('media_admin')}
        admin
    {/if}*}
    
    {component "media:uploader" }
    
    <form class="d-flex justify-content-start mt-2" data-sort-form> 
        <div class="align-self-center"> 
            {component 'bs-button.toggle' 
                attributes = ['data-toggle-view' => true] 
                name="view" 
                items=[
                    [icon => "th-large", value => 'tile', bmods => "outline-secondary", checked=> true], 
                    [icon => "th-list", value => 'column', bmods => "outline-secondary"]] 
            }
        </div>
        <div class="align-self-center pr-1 pl-2">     
            {$aLang.plugin.media.library.sort.text} 
        </div>
        <div class="align-self-center"> 
            {component "bs-form.select" 
                name = "order"
                classesGroup = "mb-0"
                classes = "sort-field"
                items = [
                    [
                        text => $aLang.plugin.media.library.sort.date_desc,
                        value => 'date_create-desc'
                    ],
                    [
                        text => $aLang.plugin.media.library.sort.date_asc,
                        value => 'date_create-asc'
                    ]
                ]

            }
        </div>
        
        {if $oUser->isAdministrator() or $oUser->isAllow('media_admin')}
            <div class="align-self-center pr-1 pl-2"> 
                {$aLang.plugin.media.library.author}:
            </div>
            <div class="align-self-center"> 
                {component "bs-form.text" 
                    classes = "sort-field"
                    placeholder = "administrator"
                    name = "author"
                    classesGroup = "mb-0"
                }
            </div>  
        {/if}
        
    </form>
    
    
    <div class="d-flex flex-wrap py-2 media-tile" data-library-medias></div>
    
    {component "bs-button" 
        badge   = ' '
        icon    = 'redo-alt'
        attributes  = ['data-load-btn' => true] 
        bmods       = "primary" 
        text        = "Подгрузить"}
    
    {component "bs-modal"
        bmods   = "lg"
        attributes = [
            'data-media-modal' => true
        ]
        header  = ""
        content = "media"
    }

</div>