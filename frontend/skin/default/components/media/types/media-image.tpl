
{component_define_params params=[ 'oMedia', 'alt', 'attributes', 'classes', 'mods', 'size', 'name']}

{$size = $size|default:"x100"}
{$img = $oMedia->getObject()->getWebPath($size)}
{$width = $oMedia->getObject()->getImage($img)->getWidth()}
{$height = $oMedia->getObject()->getImage($img)->getHeight()}

{$attributes['data-id'] = $oMedia->getId()}
{$attributes['data-web-path'] = $oMedia->getWebPath()}

<div class="m-1 media-item" data-media-item {cattr list=$attributes}>
    <div class="{$classes} d-flex justify-content-between position-relative">
        <div style="height:100px;">
            <img class="media-preview" height="{$height}" width="{$width}" src="{$img}" alt="{$oMedia->getName()}">
            <input type="hidden" data-input value="{$oMedia->getId()}" name="{$name|default:Config::Get('plugin.media.field_name')}[]">
        </div>
        {if $oMedia}
            
            <div class="media-info flex-fill pl-1">
                
                <div class="d-flex flex-column p-1 w-100 justify-content-between">
                    <div data-media-name class="text-truncate">{$oMedia->getName()}</div>

                    <div class="text-truncate">
                        <strong><em>{$oMedia->getAuthor()->getLogin()}</em></strong>
                        <small class="text-muted ml-3">{$oMedia->getDateCreateFormat()}</small>
                    </div>
                    <div class="d-flex justify-content-end">


                        <div class="align-self-end">
                            {*{component "bs-button" 
                                icon        = "edit"
                                attributes  = ['data-edit' => true]
                                bmods       = "outline-primary"}*}

                            {component "bs-button" 
                                icon        = "trash"
                                attributes  = ['data-remove' => true]
                                bmods       = "outline-danger sm"}

                            {component "bs-button" 
                                bmods   = "outline-success sm" 
                                text    = $aLang.plugin.media.library.button_select.text}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    
        <button type="button" class="close media-close text-danger mr-1 d-none" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {component "bs-progressbar" 
            height=10 
            classes="w-100 mx-2 progress-media d-none" 
        }
    </div>
</div>