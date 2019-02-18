<{if !isset($arParent)}><{assign var=arParent value=array()}><{/if}>
<{if !isset($marginLevel)}><{assign var=marginLevel value=0}><{/if}>
<{if !isset($opened)}><{assign var=opened value=0}><{/if}>
<{* level 1 *}>
<{if !$marginLevel}>
<ul>
<{section name=i loop=$arItems}>
    <li class="<{if $arItems[i].module=="prints" or !empty($arItems[i].subcategories)}>level-more<{/if}><{if $arItems[i].show_on_top}> duplicate<{/if}><{if $arItems[i].module=='constructor'}> constructor<{/if}>">
        <a href="<{if $arItems[i].separator or $arItems[i].id==$arCategory.id}>#<{else}><{include file="core/href.tpl" arCategory=$arItems[i]}><{/if}>">
            <{$arItems[i].title}>
        </a>
<{if $arItems[i].module=="prints"}>
        <{include file="menu/left.tpl" arItems=$catalogMenu arParent=$arItems[i] marginLevel=2 opened=0}>
<{elseif $arItems[i].module!="prints" and !empty($arItems[i].subcategories)}>
        <{include file="menu/left.tpl" arItems=$arItems[i].subcategories arParent=$arItems[i] marginLevel=2 opened=0}>
<{/if}>
    </li>
<{/section}>
</ul>
<{* level 2 *}>
<{elseif $marginLevel==2}>
<div class="sublevel">
    <div class="submenu">
        <a href="#" class="return">Назад</a>
        <ul>
            <li class="head">
                <a href="<{include file="core/href.tpl" arCategory=$arParent}>"><{$arParent.title}></a>
            </li>
            <li class="separator"></li>
<{section name=i loop=$arItems}>
            <li class="root">
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}>
                <a href="<{include file="core/href.tpl" arCategory=$arItems[i]}>">
<{/if}>
                    <{$arItems[i].title}>
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}>
                </a>
<{/if}>
            </li>
<{if !empty($arItems[i].subcategories) and $arItems[i].module!="prints"}>
            <{include file="menu/left.tpl" arItems=$arItems[i].subcategories arParent=$arItems[i] marginLevel=3 opened=0}>
<{if !$smarty.section.i.last}>
            <li class="separator"></li>
<{/if}>
<{/if}>
<{/section}>
        </ul>
    </div>
</div>
<{* level 3 *}>
<{elseif $marginLevel==3}>
<{section name=i loop=$arItems}>
<li class="<{if !empty($arItems[i].image_icon)}>brand<{/if}>">
<{if !empty($arItems[i].image_icon)}>
    <a href="<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}><{include file="core/href.tpl" arCategory=$arItems[i]}><{else}>#<{/if}>" class="img">
        <img src="<{$smarty.const.MAIN_CATEGORIES_URL_DIR}><{$arItems[i].image_icon}>" alt=""/>
    </a>
<{/if}>
    <a href="<{if $arItems[i].separator or $arItems[i].id==$arCategory.id}>#<{else}><{include file="core/href.tpl" arCategory=$arItems[i]}><{/if}>">
        <{$arItems[i].title}>
    </a>
</li>
<{/section}>
<{/if}>