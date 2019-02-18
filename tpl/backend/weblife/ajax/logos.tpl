<{section name="i" loop=$item.arLogos}>
    <div class="logo_block">
        <{if $item.arLogos[i].isdefault}>
            <div class="default">по умолчанию</div>
        <{else}>
            <a class="delete" href="/admin.php?module=print_assortments&printID=<{$item.id}>&task=deleteItem&itemID=<{$item.arLogos[i].id}>&ajax=1"><img src="<{$arrPageData.system_images}>delete.png"/></a>
        <{/if}>
        <a class="logo" href="/admin.php?module=print_assortments&printID=<{$item.id}>&task=editItem&itemID=<{$item.arLogos[i].id}>&ajax=1" 
           onclick="return hs.htmlExpand(this, {headingText:'Редактирование ассортимента <{$item.title}>', objectType:'iframe', preserveContent: false, width:800, marginTop: 0});">
            <img src="<{$smarty.const.UPLOAD_DIR}><{$smarty.const.DS}><{$arrPageData.module}><{$smarty.const.DS}><{$item.arLogos[i].filename}>"/>
        </a>
    </div>
<{/section}>