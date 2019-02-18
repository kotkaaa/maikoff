<{* REQUIRE VARS: $arItems=array(), $marginLevel=int <{include file='menu/main.tpl' arItems=$mainMenu marginLevel=0}>  *}>

<{if !$marginLevel}>
<!-- ++++++++++++++ Start MAIN Menu ++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>

<ul class="<{if $marginLevel==0}>sf-menu<{else}>submenu<{/if}>">
<{section name=i loop=$arItems}>
        <li class="<{if $marginLevel==0}>m<{else}>s<{/if}>menu mlevel<{$arItems[i].level}><{if $smarty.section.i.first}> first<{/if}><{if $smarty.section.i.last}> last<{/if}><{if $arItems[i].opened}> current<{/if}>">
            <a href="<{include file='core/href.tpl' arCategory=$arItems[i]}>" title="<{$arItems[i].title}>" <{if !empty($arItems[i].subcategories)}>class="parent"<{/if}>>
                <{$arItems[i].title}>
            </a>  
<{if !empty($arItems[i].subcategories)}>
            <{include file='menu/main.tpl' arItems=$arItems[i].subcategories marginLevel=$marginLevel+2}>
<{/if}>
            <{include file='menu/catalog_sub.tpl' arItems=$HTMLHelper->getFilterMenuLevel($UrlWL, $arItems[i], $lang)}>
        </li>
        
<{/section}>
</ul>

<{if !$marginLevel}>
<!-- ++++++++++++++ End MAIN Menu ++++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>

<{* Menu Simple
<{section name=i loop=$arItems}>
                <a href="<{include file='core/href.tpl' arCategory=$arItems[i]}>" class="mmenu<{if $arItems[i].opened}> active<{/if}><{if $smarty.section.i.first}> first<{elseif $smarty.section.i.last}> last<{/if}>" title="<{$arItems[i].title}>">
                    <span><{$arItems[i].title}></span>
                </a>
<{if !$smarty.section.i.last}>
                <div class="menu-main-sep"></div>
<{/if}>
<{/section}>
*}>

