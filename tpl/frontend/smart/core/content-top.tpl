<div class="content-top">
    <h1 class="page-heading"><{$arrPageData.headTitle}></h1>
<{if !empty($arCategory.image)}>
    <div class="banner"><img src="<{$smarty.const.MAIN_CATEGORIES_URL_DIR}><{$arCategory.image}>"></div>
<{/if}>
<{if $arCategory.module=="brands" and !empty($item) and !empty($item.series)}>
    <div class="filter-buttons">
<{foreach from=$item.series item=series}>
        <a href="<{include file="core/href_item.tpl" arCategory=$arrModules.brands arItem=$series}>"><{$series.title}></a>
<{/foreach}>
    </div>
<{elseif $arCategory.module=="prints"}>
    <{include file='ajax/filter_subcategories.tpl'}>
<{/if}>
    <div class="content-top-wrap">
        <{$arCategory.text}>
        <button class="btn btn-primary btn-l" onclick="Modal.open('<{include file="core/href.tpl" arCategory=$arrModules.request}>');">Сделать просчет с моим принтом</button>
    </div>
</div>
<div class="selected-filter">
    <{include file='ajax/control-sort.tpl'}>
    <{include file='ajax/selected_filters.tpl'}>
</div>
<{include file='ajax/toolbar.tpl'}>