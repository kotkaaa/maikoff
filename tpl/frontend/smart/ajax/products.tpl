<{section name=i loop=$items}>
<{* SHOW LAST PRODUCT IN LIST *}>
<{if $smarty.section.i.last and $arrPageData.page < $arrPageData.total_pages}>
<{if $arCategory.module == 'prints' || $arCategory.module == 'search'}>
    <{include file='core/product-print-next.tpl' item=$items[i]}>
<{else}>
    <{include file='core/product-print.tpl' item=$items[i]}>
<{/if}>
<{* SHOW PRODUCT LIST *}>
<{else}>
<{if $arCategory.module == 'prints' || $arCategory.module == 'search'}>
    <{include file='core/product-print.tpl' item=$items[i]}>
<{else}>
    <{include file='core/product.tpl' item=$items[i]}>
<{/if}>
<{/if}>
<{/section}>