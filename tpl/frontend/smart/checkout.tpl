<!DOCTYPE html>
<html lang="ru">
    <{include file="core/head.tpl"}>
    <body>
        <div class="page-body">
            <header class="header-container">
                <div class="container-sm">
                    <div class="logo">
                        <a href="/">
                            <img src="/images/smart/icons/svg/logo-maikoff.svg" alt="">
                            <p class="logo-sign">печать на одежде</p>
                        </a>
                    </div>
                    <div class="icons">
                        <div class="callback" id="callback">
                            <button class="btn-phone" onclick=""></button>
                        </div>
                        <button class="btn-basket" onclick="" <{if $Basket->isEmptyBasket()}>disabled<{/if}>>
                            <span class="cnt"><{$Basket->getTotalAmount()}></span>
                        </button>
                    </div>
                    <{include file='core/callback.tpl'}>
                    <{include file='core/basket.tpl'}>
                </div>
            </header>
            <div class="ordering">
                <div class="container-sm">
<{if !empty($arCategory.module)}>
                    <{include file='module/'|cat:$arCategory.module|cat:'.tpl'}>
<{else}>
                    <{include file='core/static.tpl'}>
<{/if}>
                </div>
            </div>
            <footer>
                <div class="container-sm">
                    <p><{$objSettingsInfo->copyright}></p>
                </div>
            </footer>
        </div>
        <div id="scrollup">
            <img onclick="$('html, body').animate({scrollTop: 0}, 400);" src="/images/site/smart/b_up.png" alt="ToTop">
        </div>
        <{include file='core/footer-extra.tpl'}>
    </body>
</html>