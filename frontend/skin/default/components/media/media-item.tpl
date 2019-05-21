
{component_define_params params=[  'oMedia' , 'attributes', 'classes', 'mods' ]}

<div class="{$component} p-2" data-id="{$oMedia->getId()}">
    <div>
        <img src="{$oMedia->getObject()->getWebPath('100x100crop')}" alt="{$oMedia->getName()}">
    </div>
</div>