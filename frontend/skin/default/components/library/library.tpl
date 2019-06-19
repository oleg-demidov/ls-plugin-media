
{component_define_params params=[  'oUser' , 'attributes', 'classes', 'mods' ]}


<div {cattr list=$attributes} data-library>

   {* {if $oUser->isAdministrator() or $oUser->isAllow('media_admin')}
        admin
    {/if}*}
    
    {component "media:uploader" }
    
    <form class="d-flex justify-content-start mt-2 flex-wrap" data-sort-form> 
        <div class="align-self-center m-1"> 
            {component 'bs-button.toggle' 
                attributes = ['data-toggle-view' => true] 
                name="view" 
                items=[
                    [icon => "th-large", value => 'tile', bmods => "outline-secondary", checked=> true], 
                    [icon => "th-list", value => 'column', bmods => "outline-secondary"]] 
            }
        </div>
        
        <div class="align-self-center m-1"> 
            {component "bs-form.select"
                prepend = {component "bs-icon" icon="sort" display="s"}
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
            
        <div class="align-self-center m-1"> 
            {component "bs-form.select"
                prepend = {component "bs-icon" icon="file" display="r"}
                name = "type"
                classesGroup = "mb-0"
                classes = "sort-field"
                items = [
                    [
                        text => $aLang.plugin.media.library.types.all,
                        value => 'all'
                    ],
                    [
                        text => $aLang.plugin.media.library.types.images,
                        value => 'image'
                    ],
                    [
                        text => $aLang.plugin.media.library.types.video,
                        value => 'video'
                    ],
                    [
                        text => $aLang.plugin.media.library.types.docs,
                        value => 'doc'
                    ]
                ]

            }
            
        </div>
        
        {if $oUser->isAdministrator() or $oUser->isAllow('media_admin')}
            
            <div class="align-self-center m-1"> 
                {component "bs-form.text" 
                    prepend = {component "bs-icon" icon="user" display="r"}
                    classes = "sort-field"
                    placeholder = $aLang.plugin.media.library.author
                    name = "author"
                    classesGroup = "mb-0"
                }
            </div>  
        {/if}
        
    </form>
    
    
    <div class="d-flex flex-wrap pb-2 media-tile" data-library-medias></div>
    
    {component "bs-button" 
        badge   = ' '
        icon    = 'redo-alt'
        attributes  = ['data-load-btn' => true] 
        bmods       = "primary" 
        classes     = "m-1"
        text        = $aLang.plugin.media.library.button_load.text}
        
    {component "bs-button" 
        attributes  = ['data-clear-btn' => true] 
        bmods       = "outline-secondary" 
        classes     = "m-1"
        text        = $aLang.plugin.media.library.button_clear.text}
    
    {component "bs-button" 
        attributes  = ['data-insert-btn' => true] 
        bmods       = "success" 
        classes     = "m-1"
        text        = $aLang.plugin.media.library.button_insert.text}
        
    

</div>