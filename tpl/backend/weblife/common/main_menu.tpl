<{if !empty($arrPageData.main_menu)}>
<ul>
    <{section name=i loop=$arrPageData.main_menu}>
        <{if $UserAccess->getAccessToModule($arrPageData.main_menu[i].module)}>
        <li class="menu_item<{if $arrPageData.module==$arrPageData.main_menu[i].module || ($arrPageData.main_menu[i].module=='attribute_groups' && $arrPageData.module=='attributes') || ($arrPageData.main_menu[i].module=='models' && $arrPageData.module=='catalog')}> active<{/if}>">
            <a href="/admin.php?module=<{$arrPageData.main_menu[i].module}>"><{$arrPageData.main_menu[i].title}></a>
            <{if $arrPageData.main_menu[i].module=='orders' && $arrPageData.newOrders>0}>
                <div class="notice"><{$arrPageData.newOrders}></div>
            <{/if}>
        </li>
        <{/if}>
    <{/section}>
    <{if $UserAccess->getAccessToModule('colors')}>
        <li>
            <a href="/admin.php?module=colors&ajax=1" onclick="return hs.htmlExpand(this, {headingText:'Цвета', objectType:'iframe', preserveContent: false, width:800})" class=" ">Цвета</a>
        </li>
    <{/if}>
    <{if $UserAccess->getAccessToModule('sizes')}>
        <li>
            <a href="/admin.php?module=sizes&ajax=1" onclick="return hs.htmlExpand(this, {headingText:'Размеры', objectType:'iframe', preserveContent: false, width:350})" class=" ">Размеры</a>
        </li>
    <{/if}>
</ul>
<{/if}>
