<{assign var=arParentItems value=array()}>
<{section name=i loop=$arItems}>
<{if $arItems[i].opened and !empty($arItems[i].subcategories)}>
<{$opened = 1}>
<{$arParent = $arItems[i]}>
<{$arParentItems = $arItems}>
<{$arItems = $arItems[i].subcategories}>
<{break}>
<{/if}>
<{/section}>
<div class="stack">
    <div class="head">
<{if $opened}>
        <a href="<{include file="core/href.tpl" arCategory=$arrModules.prints}>"><{$arrModules.prints.title}></a>
        <span class="crumb"></span>
        <a href="<{include file="core/href.tpl" arCategory=$arParent}>"><{$arParent.title}></a>
<{else}>
        <a href="<{include file="core/href.tpl" arCategory=$arrModules.prints}>">Категория</a>
<{/if}>
    </div>
    <div class="cols">
<{if $opened and !empty($arParentItems)}>
        <ul class="overlay">
<{section name=i loop=$arParentItems max=10}>
            <li>
                <a href="<{include file="core/href.tpl" arCategory=$arParentItems[i]}>"><{$arParentItems[i].title}></a>
            </li>
<{/section}>
        </ul>
<{/if}>
        <ul>
<{section name=i loop=$arItems}>
            <li class="<{if $arItems[i].opened}>opened<{/if}>">
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}>
                <a href="<{include file="core/href.tpl" arCategory=$arItems[i]}>">
<{/if}>
                    <{$arItems[i].title}>
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}>
                </a>
<{/if}>
            </li>
<{if $smarty.section.i.iteration%10==0 and !$smarty.section.i.last}>
        </ul>
        <ul>
<{/if}>
<{/section}>
        </ul>
    </div>
</div>