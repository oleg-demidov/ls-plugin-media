
{component_define_params params=[ 'oMedia' ]}


<form class="d-flex flex-column" action="">
    <div><img class="mr-2" src="{$oMedia->getObject()->getWebPath('100x100crop')}" alt="">{$oMedia->getName()}</div>
    <div class='mt-2'>{component "bs-form.text"  name="alt" label="Описание (alt)" value=$oMedia->getName()}</div>
    <div>
        {$items = []}
        {foreach $oMedia->getObject()->getWebPathAll() as $key => $path}
            {$items[] = [
                text => $key , value => $path
            ]}
        {/foreach}
        {component "bs-form.select" name="path" label="Размер" items=$items}
    </div>
    <input type="hidden" name="href" value="{$oMedia->getObject()->getWebPathOriginal()}">
        {component "bs-button" 
            text        = "Вставить" 
            attributes  = ['data-btn-insert' => true] 
            bmods       = "success"}     
</form>