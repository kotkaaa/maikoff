<li class="<{if !empty($item.subcategories)}>level-more <{if $item.selected_children}>opened<{/if}><{elseif $item.selected OR $item.selected_children}>checked<{elseif $item.cnt==0}>disabled<{/if}>">
<{if !empty($item.subcategories)}>
    <a href="<{$item.url}>"><{$item[$title]}></a>
    <div class="sublevel <{if $item.selected_children}>shifted<{/if}>">
        <div class="return">
            <span class="back"></span>
            <a href="#"><{$item[$title]}></a>
        </div>
        <ul data-parent="<{$item.alias}>">
<{foreach name=z from=$item.subcategories key=zKey item=zItem}>
            <!--{<{$zItem.parent_id}>}-->
            <{include file="ajax/_filter.tpl" fid=$fid aid=$zKey value='alias' title='title' item=$zItem type=$type}>
<{/foreach}>
        </ul>
    </div>
<{else}>
    <a class="filter-element <{if $item.cnt==0}>disabled<{/if}>" href="<{$item.url}>"><{$item[$title]}></a>
<{/if}>
</li>
<{*<li class="<{if !empty($catalogMenu[i].subcategories)}>level-more<{/if}><{if $catalogMenu[i].opened}> opened<{/if}>">
<{if !$catalogMenu[i].separator and $catalogMenu[i].id!=$arCategory.id}>
    <a href="<{include file="core/href.tpl" arCategory=$catalogMenu[i]}>">
<{/if}>
        <{$catalogMenu[i].title}>
<{if !$catalogMenu[i].separator and $catalogMenu[i].id!=$arCategory.id}>
    </a>
<{/if}>
<{if !empty($catalogMenu[i].subcategories)}>
    <div class="sublevel">
        <div class="return">
            <span class="back"></span>
            <a href="#">Все категории</a>
        </div>
        <ul>
            <li class="uppercase <{if $catalogMenu[i].id==$arCategory.id}>opened<{/if}>">
<{if !$catalogMenu[i].separator and $catalogMenu[i].id!=$arCategory.id}>
                <a href="<{include file="core/href.tpl" arCategory=$catalogMenu[i]}>">
<{/if}>
                    <{$catalogMenu[i].title}>
<{if !$catalogMenu[i].separator and $catalogMenu[i].id!=$arCategory.id}>
                </a>
<{/if}>
            </li>
<{section name=j loop=$catalogMenu[i].subcategories}>
            <li class="<{if $catalogMenu[i].subcategories[j].opened}> opened<{/if}>">
<{if !$catalogMenu[i].subcategories[j].separator and $catalogMenu[i].subcategories[j].id!=$arCategory.id}>
                <a href="<{include file="core/href.tpl" arCategory=$catalogMenu[i].subcategories[j]}>">
<{/if}>
                    <{$catalogMenu[i].subcategories[j].title}>
<{if !$catalogMenu[i].subcategories[j].separator and $catalogMenu[i].subcategories[j].id!=$arCategory.id}>
                </a>
<{/if}>
            </li>
<{/section}>
        </ul>
    </div>
<{/if}>
</li>*}>