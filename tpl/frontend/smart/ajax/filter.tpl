<div class="filters" id="filters_form">
<{foreach name=i from=$arrPageData.filters.items key=filterID item=filter}>
<{if $filter.totalCnt}>
<{if $filter.tid==UrlFilters::TYPE_PRICE}>
    <{continue}>
<{elseif $filter.tid==UrlFilters::TYPE_CATEGORY and $arCategory.module!="search"}>
    <div class="catalog">
        <a href="#" class="trigger">Каталог <{if $arCategory.module=='prints'}>принтов<{elseif $arCategory.module=='catalog' OR $arCategory.module=='brands'}>одежды<{else}>товаров<{/if}></a>
        <div class="flyout">
            <div class="root flex <{if $filter.has_selected_children}>shift<{/if}>" data-selected-children="<{$filter.has_selected_children}>">
                <ul class="root-level">
<{foreach name=j from=$filter.children key=arKey item=arItem}>
                    <{include file="ajax/_filter-category.tpl" fid=$filterID aid=$arKey value='id' title='title' item=$arItem type=$filter.tid}>
<{if $smarty.foreach.j.iteration%12==0 and !$smarty.foreach.j.last}>
                </ul>
                <ul class="root-level">
<{/if}>
<{/foreach}>
                </ul>
            </div>
            <a href="#" class="close"></a>
        </div>
    </div>
<{else}>
    <div class="section">
        <a href="#" class="section-toggle">
            <h2><{$filter.title}></h2>
        </a>
        <div class="section-wrap">
<{if $filter.tid==UrlFilters::TYPE_COLOR}>
            <div class="gamma">
<{foreach name=j from=$filter.children key=arKey item=arItem}>
                <{include file="ajax/_filter.tpl" fid=$filterID aid=$arKey value='id' title='short_title' item=$arItem type=$filter.tid}>
<{/foreach}>
            </div>
<{else}>
            <ul class="list">
<{foreach name=j from=$filter.children key=arKey item=arItem}>
<{if $filter.tid==UrlFilters::TYPE_BRAND}>
                <{include file="ajax/_filter.tpl" fid=$filterID aid=$arKey value='id' title='title' item=$arItem type=$filter.tid}>
<{else}>
                <{include file="ajax/_filter.tpl" fid=$filterID aid=$arKey value='alias' title='title' item=$arItem type=$filter.tid}>
<{/if}>
<{/foreach}>
            </ul>
<{/if}>
        </div>
    </div>
<{/if}>
<{/if}>
<{/foreach}>
</div>