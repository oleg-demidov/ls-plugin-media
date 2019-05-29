
{component_define_params params=[ 'id', 'img', 'alt', 'attributes', 'classes', 'mods' ]}

<div class="p-2 {$classes}" data-media-item {cattr list=$attributes} {if $img}style="background-image: url({$img});"{/if}>
    {component "bs-progressbar" height=30 classes="d-none"}
</div>