
{component_define_params params=[ 'id', 'img', 'alt', 'attributes', 'classes', 'mods' ]}

<div class="m-1 {$classes} border border-light" data-media-item {cattr list=$attributes} {if $img}style="background-image: url({$img});"{/if}>
    <button type="button" class="close media-close text-danger mr-1 d-none" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    {component "bs-progressbar" 
        height=10 
        classes="w-100 mx-2 progress-media d-none" 
    }
</div>