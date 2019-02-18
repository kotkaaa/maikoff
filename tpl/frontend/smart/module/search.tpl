<div class="content-box clearfix">
    <div class="flex">
        <div class="right-box">
            <div class="search-result-sign">
                <h1>Результаты поиска</h1>
                <p class="search_result"><{$arrPageData.search_result}></p>
            </div>
<{if !empty($items)}>
            <div class="selected-filter">
                <{include file='ajax/control-sort.tpl'}>
                <{include file='ajax/selected_filters.tpl'}>
            </div>
            <{include file='ajax/toolbar.tpl'}>
            <div class="product-grid" id="products">
                <{include file='ajax/products.tpl' items=$items}>
            </div>
            <{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=1 showPrevNext=0 showAll=0}>
<{/if}>
        </div>
        <div class="left-box">
<{if !empty($items)}>
            <{include file='ajax/filter.tpl'}>
<{/if}>
        </div>
    </div>
</div>
<{include file="core/contact-us.tpl"}>