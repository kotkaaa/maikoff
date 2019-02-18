<div class="footer-col">
    <ul class="list-links">
        <li class="footer-logo"></li>
        <li class="footer-social">
            <a href="mailto:<{$objSettingsInfo->siteEmail}>"></a>
            <a href="//facebook.com/Maikoff/" target="_blank"></a>
            <a href="#"></a>
        </li>
        <li>Пн - Пт: 10:00-19:00</li>
<{foreach name=i from=$HTMLHelper->getContactPhones($objSettingsInfo->sitePhone) item=phone}>
        <li>
            <a href="tel:<{$phone.tel}>"><{$phone.num}></a>
        </li>
<{/foreach}>
        <li><a href="mailto:<{$objSettingsInfo->siteEmail}>"><{$objSettingsInfo->siteEmail}></a></li>
    </ul>
</div>