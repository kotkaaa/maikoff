<{if $arrLangs|@count>1}>
<{*<span><{$smarty.const.SITE_LANGUAGE}>:</span>*}>
<{foreach from=$arrLangs key=lnKey item=arLnItem name=i}>
        <a href="<{$arLangsUrls.$lnKey}>" title="<{$arLnItem.title}>"<{if $lnKey==$lang}> class="active"<{/if}>><span><{$arLnItem.title}></span></a>
<{if !$smarty.foreach.i.last}>
        <span class="sep"></span>
<{/if}>
<{/foreach}>
<{/if}>

