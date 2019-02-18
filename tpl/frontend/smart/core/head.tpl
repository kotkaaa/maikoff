<head>
    <title><{$HTMLHelper->prepareHeadTitle($arCategory)}></title>
    <meta http-equiv="Content-Type" content="text/html; charset=<{$arrLangs.$lang.charset}>"/>
    <meta name="keywords" content="<{$arCategory.meta_key}>" />
    <meta name="description" content="<{$arCategory.meta_descr}>" />
<{if $arCategory.meta_robots}>
    <meta name="robots" content="<{$arCategory.meta_robots}>" />
<{/if}>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta property="og:locale" content="ru_UA"/>
    <meta property="og:title" content="<{$HTMLHelper->prepareHeadTitle($arCategory)}>"/>
    <meta property="og:description" content="<{$arCategory.meta_descr}>" />
    <meta property="og:url" content="<{$smarty.const.WLCMS_HTTP_HOST|cat:$UrlWL->getUrl()}>"/>
<{if ($arCategory.module=="catalog" or $arCategory.module=="prints") and !empty($item)}>
    <meta property="og:type" content="product"/>
    <meta property="og:image" content="<{$smarty.const.WLCMS_HTTP_HOST|cat:$item.big_image}>"/>
    <meta property="og:image:type" content="image/jpeg"/>
    <meta property="og:image:width" content="540"/> 
    <meta property="og:image:height" content="620"/>
<{/if}>
<{if !empty($arrPageData.canonical)}>
    <link rel="canonical" href="<{$arrPageData.canonical}>"/>
<{/if}>
<{if !empty($arrPageData.link_prev)}>
    <link rel="prev" href="<{$arrPageData.link_prev}>"/>
<{/if}>
<{if !empty($arrPageData.link_next)}>
    <link rel="next" href="<{$arrPageData.link_next}>"/>
<{/if}>
<{foreach from=$arrPageData.headScripts item=script}>
<script src="<{$script}>" defer></script>
<{/foreach}>
    <script type="text/javascript">
        window.gClid = '<{$arrPageData.isGoogleAdwords}>';
    </script>
    <{include file='core/header-extra.tpl'}>
    <style type="text/css" media="screen">
        html:not(.js) body:after {
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgb(255,255,255);
            content: "";
        }
    </style>
</head>