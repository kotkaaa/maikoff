<!-- Галлерея начало -->
<{if !empty($item) OR !empty($items) OR !empty($arrCategories)}>
<{* DISPLAY ITEM FIRST IF NOT EMPTY *}>

<{if !empty($item)}>
<{* DISPLAY ITEMS OF MAIN GALLERY CATEGORY IF NOT EMPTY *}>

<{elseif $arrModules.gallery.id==$arrPageData.catid}>
    <h1><img alt="<{$arCategory.title}>" title="<{$arCategory.title}>" src="<{$arCategory.image}>" /></h1>

<{section name=i loop=$arrCategories}>
    <a href="<{include file='core/href.tpl' arCategory=$arrCategories[i]}>" title="<{$arrCategories[i].title}>">
        <img border="0" src="<{$arrCategories[i].image}>" alt="<{$arrCategories[i].title}>" />
    </a>
    <br/>
    <a href="<{include file='core/href.tpl' arCategory=$arrCategories[i]}>" title="<{$arrCategories[i].title}>">
        <{$arrCategories[i].title}>
    </a>
    (<{$arrCategories[i].images}>)
<{/section}>
<{* DISPLAY ITEMS LIST IF NOT EMPTY *}>

<{elseif !empty($items)}>
    <h1><{$arCategory.title}></h1>
    <div id="gallery-wrapper" class="highslide-gallery">
        <ul class="gallery-items">
<{section name=i loop=$items}>
            <li class="image-box">
                <a class="highslide" title="<{$items[i].title}>" href="<{$items[i].image}>" onclick="return hs.expand(this, galleryOptions)">
                    <img border="0" src="<{$items[i].small_image}>" alt="" />
                </a>
            </li>
<{/section}>
        </ul>
    </div>
    <br clear="all" />
    <a href="<{$arrPageData.backurl}>"><{$smarty.const.URL_GALLERY_BACK}></a>

<{if $arrPageData.total_pages>1}>
<!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=1 showAll=0}>
<!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>

<{/if}>

<{* DISPLAY CATEGORY INFO *}>
<{else}>
<{include file='core/static.tpl'}>
<{/if}>
<!--Галлерея конец-->

