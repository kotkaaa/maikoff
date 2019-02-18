<{if !$IS_AJAX}>
<div class="toolbar" id="mToolbar">
<{/if}>
<{if $arCategory.module=="prints"}>
    <a href="#" class="catalog-print-trigger" data-toggle="category">Каталог принтов</a>
<{/if}>
<{if !empty($arrPageData.filters)}>
    <a href="#" data-toggle="filters">Подбор товара <em class="selected-count"><{if !empty($arrPageData.selectedFilters)}>+<{$arrPageData.selectedFilters|count}><{/if}></em></a>
<{/if}>
<{foreach name=i from=$arrPageData.arSorting key=sortID item=sorting}>
<{if $sorting.active}>
    <a href="#" data-toggle="sorting"><{$sorting.title}></a>
<{/if}>
<{/foreach}>
<{if !$IS_AJAX}>
</div>
<{/if}>