 
{extends "component@bs-form.field"}

{component_define_params params=[ 'multiple', 'oEntity']}

{block name="field_options"}
    {$attributesGroup['data-media-field'] = true}
    {$attributesGroup['data-name'] = "{$name|default:'media'}[]"}
    {$attributesGroup['data-multiple'] = {$multiple|default:"true"}}
    
    {$validateRules['type'] = "number"}
    {$validateRules['min'] = $oEntity->media->getParam('validate_min')}
    {$validateRules['max'] = $oEntity->media->getParam('validate_max')}
    {$validate['msgError'] = {lang 
        name=$oEntity->media->getParam('validate_msg') 
        min=$validateRules['min'] 
        max=$validateRules['max']}}
{/block}





{block name="field_input"}

    {$aMedias = $oEntity->media->getMedia()}

    <input class="d-none form-control" data-media-count-field name="{Config::Get('plugin.media.field_name')}_count" 
        {cattr list=$validateRules} value="{$aMedias|@sizeof}">
    
    {capture name="content"}
        
        {foreach $aMedias as $oMedia}
            {component "media:media.item" oMedia=$oMedia}
        {/foreach}

        
    {/capture}
    
    {capture name="footer"}
      
        {component "bs-button.group" 
            items =[ 
                [
                    attributes  => ["data-media-count" => true],
                    bmods       => "outline-primary", 
                    text        => {$aMedias|sizeof}
                ],
                [
                    attributes  => ["data-add-btn" => true],
                    bmods       => "outline-primary", 
                    icon        => "plus", 
                    text        => $aLang.common.add
                ]
        ]}
            
        {component "bs-button" 
            attributes  = ["data-remove-btn" => true] 
            bmods       = "outline-danger"
            classes     = "d-none"
            icon        = "minus" 
            text        = $aLang.common.remove}
        
    {/capture}
    {component "bs-card" content=[
        [   
            type => 'body',
            attributes => ['data-field-body' => true],
            classes => "media-short d-flex flex-wrap p-3",
            content => $smarty.capture.content
        ],
        [
            type => 'footer',
            content => $smarty.capture.footer,
            classes => 'p-3'
        ]
    ]}
    
        
{/block}