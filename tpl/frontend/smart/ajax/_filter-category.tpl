<li class="<{if $item.selected OR $item.selected_children}> checked<{/if}><{if $item.selected_children}> opened<{/if}>" data-subcategories="<{count($item.subcategories)}>">
<{if !empty($item.subcategories)}>
    <a class="filter-shift" href="<{$item.url}>"><{$item[$title]}></a>
    <div class="sublevel <{if $item.selected_children}>shift<{/if}>">
        <div class="sub-heading">
            <a href="#" class="return">вернуться к списку</a>
            <a href="<{$item.url}>" class="filter-element direct <{if !$item.cnt}>disabled<{/if}>"><{$item[$title]}></a>
        </div>
        <div class="flex">
            <ul data-parent="<{$arItem.alias}>">
<{foreach name=z from=$arItem.subcategories key=zKey item=zItem}>
                <{include file="ajax/_filter.tpl" fid=$fid aid=$zKey value='alias' title='title' item=$zItem type=$type}>
<{if $smarty.foreach.z.iteration%10==0 and !$smarty.foreach.z.last}>
            </ul>
            <ul>
<{/if}>
<{/foreach}>
            </ul>
        </div>
    </div>
<{else}>
    <a class="filter-element <{if !$item.cnt}>disabled<{/if}>" href="<{$item.url}>"><{$item[$title]}></a>
<{/if}>
</li>