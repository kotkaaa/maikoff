<{if !empty($arItems)}>
<{* ROOT LEVEL *}>
<{if !$marginLevel}>
<div class="submenu">
<{section name=i loop=$arItems}>
    <div class="section">
        <a href="<{if $arItems[i].separator or $arItems[i].id==$arCategory.id}>#<{else}><{include file="core/href.tpl" arCategory=$arItems[i]}><{/if}>" class="section-toggle<{if !empty($arItems[i].subcategories)}> toggle-ready<{/if}><{if $arItems[i].opened}> opened<{/if}>"><{$arItems[i].title}></a>
<{if !empty($arItems[i].subcategories)}>
        <div class="section-wrap">
            <{include file="menu/sub.tpl" arItems=$arItems[i].subcategories arParent=$arItems[i] marginLevel=1}>
        </div>
<{/if}>
    </div>
<{/section}>
</div>
<{* 2-ND MENU LEVEL *}>
<{elseif $marginLevel==1}>
<{* DETECT OPENED CATEGORY FIRSTLY *}>
<{assign var=opened value=0}>
<{section name=i loop=$arItems}>
<{if $arItems[i].opened and !empty($arItems[i].subcategories)}>
<{$opened = $arItems[i].id}>
<{break}>
<{/if}>
<{/section}>
<ul class="list<{if $opened}> shift<{/if}>">
<{section name=i loop=$arItems}>
    <li class="<{if !empty($arItems[i].subcategories)}>level-more<{/if}><{if $arItems[i].opened}> opened<{/if}>">
        <a href="<{if $arItems[i].separator or $arItems[i].id==$arCategory.id}>#<{else}><{include file="core/href.tpl" arCategory=$arItems[i]}><{/if}>"><{$arItems[i].title}></a>
<{if !empty($arItems[i].subcategories)}>
        <{include file="menu/sub.tpl" arItems=$arItems[i].subcategories arParent=$arItems[i] marginLevel=2}>
<{/if}>
    </li>
<{/section}>
</ul>
<{* 3-RD MENU LEVEL *}>
<{elseif $marginLevel==2}>
<div class="sublevel">
    <div class="return">
        <span class="back"></span>
        <a href="<{if $arParent.separator or $arItems[i].id==$arCategory.id}>#<{else}><{include file="core/href.tpl" arCategory=$arParent}><{/if}>"><{$arParent.title}></a>
    </div>
    <ul>
<{section name=i loop=$arItems}>
        <li class="<{if $arItems[i].opened}>opened<{/if}>">
            <a href="<{if $arItems[i].separator or $arItems[i].id!=$arCategory.id}>#<{else}><{include file="core/href.tpl" arCategory=$arItems[i]}><{/if}>"><{$arItems[i].title}></a>
        </li>
<{/section}>
    </ul>
</div>
<{/if}>
<{/if}>