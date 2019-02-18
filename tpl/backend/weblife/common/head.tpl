<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<{$arrLangs.$lang.charset}>" />
    <title><{$arrPageData.headTitle}></title>
    <meta name="viewport" content="width=1048">
    <meta name="MobileOptimized" content="1048" />
    <meta name="HandheldFriendly" content="true" />
<{if $arrPageData.module == 'print'}>
    <script src="/js/jquery/jquery-<{$smarty.const.WLCMS_JQUERY_VERSION}>.min.js" type="text/javascript"></script>
<{else}>
    <link href="<{$arrPageData.css_dir}>admin.css?v=1" rel="stylesheet" type="text/css" />
    <link href="/js/jquery/themes/smoothness/jquery.ui.all.css" rel="stylesheet" type="text/css" />
    <link href="<{$arrPageData.images_dir}>weblife.ico" rel="shortcut icon" />
    <link href="/js/libs/highslide/highslide.css" type="text/css" rel="stylesheet" />
    <script src="/js/jquery/jquery-<{$smarty.const.WLCMS_JQUERY_VERSION}>.min.js" type="text/javascript"></script>
    <script src="/js/jquery/jquery.easing.1.3.js" type="text/javascript"></script>
    <script src="/js/jquery/ui/jquery-ui.custom.js" type="text/javascript"></script>
    <script type="text/javascript" src="/js/jquery/jquery.migrate.1.2.1.js"></script>
    <script type="text/javascript" src="/js/jquery/treeview/jquery.treeview.js"></script>
    <script src="/js/libs/highslide/highslide-full.packed.js" type="text/javascript"></script>
    <script src="/js/libs/highslide/langs/<{$lang}>.js" type="text/javascript"></script>
    <script src="/js/libs/highslide/highslide.config.admin.js" type="text/javascript"></script>
<{if !empty($arrPageData.headCss)}>
    <{$arrPageData.headCss|implode:"\n        "}>
<{/if}>
<{if !empty($arrPageData.headScripts)}>
    <{$arrPageData.headScripts|implode:"\n        "}>
<{/if}>
    <script src="/js/admin/select_drag.js" type="text/javascript"></script>
    <script src="/js/libs/tiny_mce/tiny_mce.js" type="text/javascript"></script>
    <script src="/js/libs/tiny_mce/tiny_mce_config.js" type="text/javascript"></script>
    <script src="/js/admin/admin_functions.js" type="text/javascript"></script>
    <script src="/js/admin/admin_extra.js" type="text/javascript"></script>
<{/if}>
</head>