<div class="static-page container clearfix">
    <{include file='core/breadcrumb.tpl' arrBreadCrumb=$arrPageData.arrBreadCrumb}>
<{if !empty($subMenu)}>
    <div class="left-box">
        <{include file="menu/sub.tpl" arItems=$subMenu marginLevel=0}>
    </div>
<{/if}>
    <div class="right-box">
        <h1><{$arCategory.title}></h1>
<{if !empty($arCategory.text)}>
        <{$arCategory.text}>
<{else}>
        <br /><br /><br />
        <center><{$smarty.const.NO_CONTENT}></center>
<{/if}>
    </div>
</div>
<{include file='core/last-watched.tpl'}>
<{include file='core/contact-us.tpl'}>