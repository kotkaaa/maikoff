<{if !$IS_DEV}>
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-43308345-1']);        
    <{* печать лого *}>
    <{if $arCategory.module=="landing_pechat_logo" || $arrPageData.rootID==$arrModules.landing_pechat_logo.id}>
        _gaq.push(['_setPageGroup', 1, 'Печать лого']);
        _gaq.push(['_setPageGroup', 2, '<{HTMLHelper::clearString($arCategory.title)}>']);
        _gaq.push(['_setPageGroup', 3, 'Печать лого / <{HTMLHelper::clearString($arCategory.title)}>']); 
    <{* товары под печать *}>
    <{elseif $arCategory.id==187 || $arrPageData.rootID==187}>
        _gaq.push(['_setPageGroup', 1, 'Печать лого']);
        _gaq.push(['_setPageGroup', 2, '<{HTMLHelper::clearString($arCategory.title)}>']);
        _gaq.push(['_setPageGroup', 3, 'Печать лого / <{HTMLHelper::clearString($arCategory.title)}>']);
    <{* Футболки с принтами - категория *}>
    <{elseif $arCategory.module=="prints" AND empty($item) AND !empty($items)}>
        _gaq.push(['_setPageGroup', 1, 'Футболки с принтами']);
        _gaq.push(['_setPageGroup', 2, '<{HTMLHelper::clearString($arCategory.title)}>']);
        _gaq.push(['_setPageGroup', 3, 'Футболки с принтами / <{HTMLHelper::clearString($arCategory.title)}>']);
    <{* Футболки с принтами - товар *}>
    <{elseif $arCategory.module=="prints" AND !empty($item)}>
        _gaq.push(['_setPageGroup', 1, 'Карточка принта']);
        _gaq.push(['_setPageGroup', 2, '<{HTMLHelper::clearString($item.title)}>']);
        _gaq.push(['_setPageGroup', 3, 'Карточка принта / <{HTMLHelper::clearString($item.title)}>']);
    <{* печать на товарах *}>
    <{elseif $arCategory.module=="landing_pechat_na_tovarah" || $arrPageData.rootID==$arrModules.landing_pechat_na_tovarah.id}>
        _gaq.push(['_setPageGroup', 1, 'Печать на товарах']);
        _gaq.push(['_setPageGroup', 2, '<{HTMLHelper::clearString($arCategory.title)}>']);
        _gaq.push(['_setPageGroup', 3, 'Печать на товарах / <{HTMLHelper::clearString($arCategory.title)}>']);
    <{elseif $arCategory.module=="home"}>
        _gaq.push(['_setPageGroup', 1, 'Главная страница']);
    <{elseif $arCategory.module=="feedback"}>
        _gaq.push(['_setPageGroup', 1, 'Контакты']);
    <{elseif $arCategory.module=="brands"}>
        _gaq.push(['_setPageGroup', 1, 'Бренды']);
    <{elseif $arCategory.module=="checkout"}>
        _gaq.push(['_setPageGroup', 1, 'Оформление заказа']);
    <{elseif $arCategory.module=="search"}>
        _gaq.push(['_setPageGroup', 1, 'Результаты поиска']);
    <{elseif $arCategory.module=="news"}>
        _gaq.push(['_setPageGroup', 1, 'Статьи']);
    <{else}>
        _gaq.push(['_setPageGroup', 1, 'Статические страницы']);
    <{/if}>
    _gaq.push(['_trackPageview']);
    <{$trackingEcommerceJS}>  
    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
<{/if}>