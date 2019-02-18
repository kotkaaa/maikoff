<{if !$IS_AJAX}>
<div class="selected-items" id="selected_filters">
<{/if}>
<{if !empty($arrPageData.selectedFilters)}>
<{foreach name=i from=$arrPageData.filters.items key=filterID item=filter}>
<{if $filter.type=='price' and $filter.children.selected.min and $filter.children.selected.max}>
    <a href="<{$filter.children.selected.url}>" class="filter-element">от <{$filter.children.selected.min}> до <{$filter.children.selected.max}> грн <span>&times;</span></a>
<{else}>
<{foreach name=j from=$filter.children key=arKey item=arItem}>
<{if $arItem.selected}>
    <a href="<{$arItem.url}>" class="filter-element"><{$arItem.title}> <span>&times;</span></a>
<{elseif $filter.tid==UrlFilters::TYPE_CATEGORY and $arItem.selected_children}>
<{foreach name=z from=$arItem.subcategories key=zKey item=zItem}>
<{if $zItem.selected}>
    <a href="<{$zItem.url}>" class="filter-element"><{$zItem.title}> <span>&times;</span></a>
<{/if}>
<{/foreach}>
<{/if}>
<{/foreach}>
<{/if}>
<{/foreach}>
    <a href="<{$UrlWL->copy()->resetPage()->resetFilters()->buildUrl()}>" class="filter-element">Очистить все фильтры <span>&times;</span></a>
<{/if}>
<{if !$IS_AJAX}>
</div>
<{/if}>