<{if !isset($arParent)}><{assign var=arParent value=array()}><{/if}>
<{if !isset($marginLevel)}><{assign var=marginLevel value=0}><{/if}>
<{if !isset($opened)}><{assign var=opened value=0}><{/if}>
<{* LEVEL 1*}>
<{if !$marginLevel}>
<nav class="desktop-menu">
    <ul class="nomargin pull-right">
<{section name=i loop=$arItems}>
        <li class="<{if $arItems[i].module=="prints" or !empty($arItems[i].subcategories)}>level-more<{/if}><{if $arItems[i].opened}> opened<{/if}><{if $arItems[i].id==$arCategory.id}> current<{/if}><{if !$arItems[i].show_on_top}> hidden<{/if}><{if $arItems[i].module == 'constructor'}> constructor<{/if}>">
<{if $arItems[i].separator or $arItems[i].id==$arCategory.id}><span><{else}><a href="<{include file="core/href.tpl" arCategory=$arItems[i]}>"><{/if}><{$arItems[i].title}><{if $arItems[i].separator  or $arItems[i].id==$arCategory.id}></span><{else}></a><{/if}>
<{if $arItems[i].module=="prints" or !empty($arItems[i].subcategories)}>
            <{include file="menu/top.tpl" arItems=$arItems[i].subcategories arParent=$arItems[i] marginLevel=2 opened=0}>
<{/if}>
        </li>
<{/section}>
    </ul>
</nav>
<{* LEVEL 2*}>
<{elseif $marginLevel==2}>
<div class="dropdown">
    <div class="container stacks">
<{if $arParent.module=="prints"}>
        <{include file="menu/catalog.tpl" arItems=$catalogMenu arParent=$arParent marginLevel=0 opened=0}>
<{/if}>
<{section name=i loop=$arItems}>
        <div class="stack">
            <div class="head">
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}>
                <a href="<{include file="core/href.tpl" arCategory=$arItems[i]}>">
<{/if}>
                <{$arItems[i].title}>
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}>
                </a>
<{/if}>
            </div>
            <div class="cols">
<{if !empty($arItems[i].subcategories)}>
                <{include file="menu/top.tpl" arItems=$arItems[i].subcategories arParent=$arItems[i] marginLevel=3 opened=0}>
<{/if}>
<{if !empty($arParent.image_menu)}>
                <ul>
                    <li>
                        <img class="menu-hover-image" src="<{$smarty.const.MAIN_CATEGORIES_URL_DIR}><{$arParent.image_menu}>" data-original="<{$smarty.const.MAIN_CATEGORIES_URL_DIR}><{$arParent.image_menu}>" alt=""/>
                    </li>
                </ul>
<{/if}>
            </div>
        </div>
<{/section}>
    </div>
</div>
<{* LEVEL 3*}>
<{elseif $marginLevel==3}>
<ul>
<{section name=i loop=$arItems}>
    <li class="<{if $arItems[i].opened}>opened<{/if}> <{if !empty($arItems[i].image_icon)}>brand<{/if}>">
<{if !empty($arItems[i].image_icon)}>
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}>
        <a href="<{include file="core/href.tpl" arCategory=$arItems[i]}>" class="img">
<{/if}>
        <img src="<{$smarty.const.MAIN_CATEGORIES_URL_DIR}><{$arItems[i].image_icon}>" alt="<{$arItems[i].title}>"/>
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}>
        </a>
<{/if}>
<{/if}>
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}>
        <a href="<{include file="core/href.tpl" arCategory=$arItems[i]}>" <{if !empty($arItems[i].image_menu)}>data-hover-image="<{$smarty.const.MAIN_CATEGORIES_URL_DIR}><{$arItems[i].image_menu}>"<{/if}>>
<{/if}>
        <{$arItems[i].title}>
<{if !$arItems[i].separator and $arItems[i].id!=$arCategory.id}>
        </a>
<{/if}>
    </li>
<{/section}>
</ul>
<{/if}>