<{if !empty($item)}>
<div class="content-box clearfix">
    <{include file="core/breadcrumb.tpl" arrBreadCrumb=$arrPageData.arrBreadCrumb}>
    <div class="flex">
        <div class="right-box">
            <{include file="core/content-top.tpl"}>
            <div class="product-grid" id="products">
                <{include file='ajax/products.tpl' items=$items}>
            </div>
            <{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=1 showPrevNext=0 showAll=0}>
        </div>
    </div>
</div>
<{include file="core/contact-us.tpl"}>
<{/if}>