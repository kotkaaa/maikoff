<{if !$IS_AJAX}>
<div class="filters-popup">
    <a href="javascript:MobileFilters.close();" class="close"></a>
    <div class="heading">Подбор товаров</div>
    <div class="filters-form" id="filters_form_mobile">
<{/if}>
<{if !empty($arrPageData.arSorting)}>
        <div class="section" data-section="sorting">
            <a href="#" class="section-toggle">Сортировка</a>
            <div class="section-wrap">
                <ul class="list">
<{foreach name=i from=$arrPageData.arSorting key=sortID item=sorting}>
                    <li class="<{if $sorting.active}>checked<{/if}>">
                        <a href="<{$sorting.url}>"><{$sorting.title}></a>
                    </li>
<{/foreach}>
                </ul>
            </div>
        </div>
<{/if}>
<{foreach name=i from=$arrPageData.filters.items key=filterID item=filter}>
<{if $filter.tid==UrlFilters::TYPE_PRICE}>
        <{continue}>
<{/if}>
<{if !$filter.totalCnt}><{continue}><{/if}>
        <div class="section" <{if $filter.tid==UrlFilters::TYPE_CATEGORY}>data-section="category"<{/if}>>
            <a href="#" class="section-toggle"><{$filter.title}></a>
            <div class="section-wrap">
<{if $filter.tid==UrlFilters::TYPE_COLOR}>
                <div class="gamma">
<{foreach name=j from=$filter.children key=arKey item=arItem}>
                    <{include file="ajax/_filter.tpl" fid=$filterID aid=$arKey value='id' title='short_title' item=$arItem type=$filter.tid}>
<{/foreach}>
                </div>
<{elseif $filter.tid==UrlFilters::TYPE_CATEGORY and $arCategory.module!="search"}>
                <ul class="list<{if $filter.has_selected_children}> shift<{/if}> menu-simple">
<{foreach name=j from=$filter.children key=arKey item=arItem}>
                    <{include file="ajax/_filter-category-mobile.tpl" fid=$filterID aid=$arKey value='id' title='title' item=$arItem type=$filter.tid}>
<{/foreach}>
                </ul>
<{else}>
                <ul class="list">
<{foreach name=j from=$filter.children key=arKey item=arItem}>
                    <{include file="ajax/_filter.tpl" fid=$filterID aid=$arKey value='id' title='title' item=$arItem type=$filter.tid}>
<{/foreach}>
                </ul>
<{/if}>
            </div>
        </div>
<{/foreach}>
<{if !$IS_AJAX}>
    </div>
    <div class="footer clearfix">
        <button class="pull-left btn btn-warning btn-xxl" onclick="MobileFilters.apply();">Показать товары</button>
        <button class="pull-right btn btn-primary btn-xxl" onclick="MobileFilters.cancel();">Отмена</button>
    </div>
</div>
<{/if}>