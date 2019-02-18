<{* LEVEL 1 *}>
<{if !$marginLevel}>
<{section name=i loop=$arItems}>
<div class="footer-col menu">
    <div class="heading-col">
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id and $arItems[i].redirectid!=$arCategory.id}>
        <a href="<{include file="core/href.tpl" arCategory=$arItems[i]}>">
<{/if}>
        <span><{$arItems[i].title}></span>
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id and $arItems[i].redirectid!=$arCategory.id}>
        </a>
<{/if}>
    </div>
<{if !empty($arItems[i].subcategories)}>
    <{include file="menu/bottom.tpl" arItems=$arItems[i].subcategories marginLevel=$marginLevel+2}>
<{/if}>
</div>
<{/section}>
<{* LEVEL 2 *}>
<{else}>
<div class="flex">
    <ul class="list-links">
<{section name=i loop=$arItems}>
        <li>
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id and $arItems[i].redirectid!=$arCategory.id}>
            <a href="<{include file="core/href.tpl" arCategory=$arItems[i]}>">
<{/if}>
            <{$arItems[i].title}>
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id and $arItems[i].redirectid!=$arCategory.id}>
            </a>
<{/if}>
        </li>
<{if $smarty.section.i.iteration%8==0 and !$smarty.section.i.last}>
    </ul>
    <ul class="list-links">
<{/if}>
<{/section}>
    </ul>
</div>
<{/if}>