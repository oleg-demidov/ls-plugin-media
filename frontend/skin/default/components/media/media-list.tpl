
{component_define_params params=[ 'aMedias', 'attributes', 'classes', 'mods' ]}

{foreach $aMedias as $oMedia}
    {component "media:media.item" 
        attributes = [
            'data-id' => $oMedia->getId()
        ]
        img = $oMedia->getObject()->getWebPath('100x100crop')}
{/foreach}


