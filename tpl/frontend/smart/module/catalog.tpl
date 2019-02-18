<{* DISPLAY ITEM FIRST IF NOT EMPTY *}>
<{if !empty($item)}>
<div class="product-container" data-item-id="<{$item.id}>">
    <div class="container clearfix">
        <{include file='core/breadcrumb.tpl' arrBreadCrumb=$arrPageData.arrBreadCrumb}>
        <h1 class="heading-title"><{$arrPageData.headTitle}></h1>
<{if !empty($item.pcode)}>
        <p class="product-sku">артикул <{$item.pcode}></p>
<{/if}>
        <div class="product-card product-card-standart" data-item-id="<{$item.id}>" data-idkey="<{$item.idKey}>">
            <div class="left-col">
                <{include file='core/product-gallery.tpl' images=$item.images showTitles=0 showThumbs=1}>
<{if !empty($item.size_grid)}>
                <div class="size-table-wrap">
                    <{include file="core/size-table.tpl"}>
                </div>
<{/if}>
            </div>
            <div class="right-col">
                <{include file="core/product-details.tpl"}>
            </div>
        </div>
    </div>
</div>
<{include file='core/last-watched.tpl'}>
<h2 class="feature-sign">Заказать печать просто</h2>
<{include file='core/features-2.tpl'}>
<{include file='core/contact-us.tpl'}>
<{* DISPLAY ITEMS LIST IF NOT EMPTY *}>
<{else}>
<div class="content-box clearfix">
    <div class="flex">
        <div class="right-box">
            <{include file="core/breadcrumb.tpl" arrBreadCrumb=$arrPageData.arrBreadCrumb}>
            <{include file="core/content-top.tpl"}>
            <div class="product-grid" id="products">
                <{include file='ajax/products.tpl' items=$items}>
            </div>
            <{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=1 showPrevNext=0 showAll=0}>
            <{include file="core/content-bottom.tpl"}>
        </div>
        <div class="left-box">
            <{include file='ajax/filter.tpl'}>
        </div>
    </div>
</div>
<{/if}>
<{include file='core/seo-text.tpl'}>