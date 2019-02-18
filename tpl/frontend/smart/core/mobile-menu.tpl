<div class="mobile-menu">
    <div class="catalog">
        <a href="#" class="close">Закрыть</a>
        <{include file="menu/left.tpl" arItems=$mainMenu marginLevel=0}>
        <div class="contact-info">
            <div class="phones">
<{foreach name=i from=$HTMLHelper->getContactPhones($objSettingsInfo->sitePhone) item=phone}>
                <a href="tel:<{$phone.tel}>"><{$phone.num}></a><{if !$smarty.foreach.i.last}><br/><{/if}>
<{/foreach}>
            </div>
            <a href="mailto:<{$objSettingsInfo->siteEmail}>" class="email"><{$objSettingsInfo->siteEmail}></a>
            <div class="socials">
                <a href="mailto:<{$objSettingsInfo->siteEmail}>" class="eml"></a>
                <a href="//facebook.com/Maikoff/" class="fb" target="_blank"></a>
                <a href="#" class="tw"></a>
            </div>
        </div>
    </div>
</div>