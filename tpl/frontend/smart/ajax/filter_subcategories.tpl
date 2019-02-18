<{if !$IS_AJAX}>
<div id="filters_subcategories">
<{/if}>
<{foreach name=i from=$arrPageData.filters.items key=filterID item=filter}>
<{if $filter.totalCnt and $filter.tid==UrlFilters::TYPE_CATEGORY and $arCategory.module!="search"}>
<{foreach name=j from=$filter.children key=arKey item=arItem}>
<{if !empty($arItem.subcategories) and ($arItem.selected or $arItem.selected_children)}>
    <div class="filter-buttons">
<{foreach name=z from=$arItem.subcategories key=zKey item=zItem}>
        <{include file="ajax/_filter-subcategory.tpl" fid=$filterID aid=$zKey value='id' title='title' item=$zItem type=$filter.tid}>
<{/foreach}>
    </div>
<{/if}>
<{/foreach}>
<{/if}>
<{/foreach}>
<{if !$IS_AJAX}>
</div>
<{/if}>