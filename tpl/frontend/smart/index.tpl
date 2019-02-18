<!DOCTYPE html>
<html lang="ru">
    <{include file="core/head.tpl"}>
    <body>
        <{include file='core/seo-content.tpl'}>
        <div class="page-body">
            <{include file='core/header.tpl'}>
<{if !empty($arCategory.module)}>
            <{include file='module/'|cat:$arCategory.module|cat:'.tpl'}>
<{else}>
            <{include file='core/static.tpl'}>
<{/if}>
            <{include file='core/footer.tpl'}>
        </div>
<{if ($arCategory.module=="catalog" or $arCategory.module=="search" or $arCategory.module=="prints" or ($arCategory.module=="brands" and !empty($item))) and !empty($items)}>
        <{include file='ajax/filter-mobile.tpl'}>
<{/if}>
        <{include file='core/mobile-menu.tpl'}>
        <{include file='core/modal.tpl'}>
        <{include file='core/footer-extra.tpl'}>
    </body>
</html>