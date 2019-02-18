<{if !empty($item)}>
<div class="content-box clearfix">
    <div class="flex">
        <div class="right-box">
            <{include file="core/breadcrumb.tpl" arrBreadCrumb=$arrPageData.arrBreadCrumb}>
            <{include file="core/content-top.tpl"}>
            <div class="product-grid" id="products">
                <{include file='ajax/products.tpl' items=$items}>
            </div>
            <{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=1 showPrevNext=0 showAll=0}>
        </div>
        <div class="left-box">
            <{include file='ajax/filter.tpl'}>
        </div>
    </div>
</div>
<{if !empty($item.gallery)}>
<{include file="core/brand-gallery.tpl" arItems=$item.gallery itemID=$item.id}>
<{/if}>
<{elseif empty($item) and !empty($items)}>
<div class="top-banner">
    <div class="container">
        <h2>Бренды и производители</h2>
        <{$arCategory.text}>
        <div class="banner-brands">
            <ul>
<{foreach from=$items item=brand}>
                <li>
                    &nbsp;&nbsp;<a href="#brand_<{$brand.id}>"><{$brand.title}></a>&nbsp;&nbsp;
                </li>
<{/foreach}>
            </ul>
        </div>
    </div>
</div>
<{foreach from=$items item=brand}>
<div class="brand-block">
    <div id="brand_<{$brand.id}>" style="position: absolute; top: -55px;"></div>
    <div>
        <a href="<{include file="core/href_item.tpl" arCategory=$arCategory arItem=$brand params=""}>">
            <img src="<{$brand.image}>" alt="<{$brand.title}>">
        </a>
    </div>
    <div>
        <h2>
            <a href="<{include file="core/href_item.tpl" arCategory=$arCategory arItem=$brand params=""}>"><{$brand.title}></a>
        </h2>
        <{$brand.descr}>
    </div>
</div>
<{if !empty($brand.gallery)}>
<{include file="core/brand-gallery.tpl" arItems=$brand.gallery itemID=0}>
<{/if}>
<{/foreach}>
<{include file="core/contact-us.tpl"}>
<{/if}>