<{* REQUIRE VARS: $arrBreadCrumb=array()*}><{*include file='core/breadcrumb.tpl' arrBreadCrumb=$arrPageData.arrBreadCrumb*}>
<{if count($arrBreadCrumb)>0}>
<{if !$IS_AJAX}>
<div class="breadcrumbs">
<{/if}>
    <a href="/"></a>&nbsp;
<{foreach name=i from=$arrBreadCrumb key=sKey item=sItem}>
<{if !$smarty.foreach.i.last}>
    <a href="<{$sKey}>"><{$sItem}></a>&nbsp;
<{else}><span><{$sItem}></span><{/if}>
<{/foreach}>
<{if !$IS_AJAX}>
</div>
<{/if}>
<{/if}>