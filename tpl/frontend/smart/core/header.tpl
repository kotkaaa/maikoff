<{*include file='core/header.tpl'*}>
<header class="header-container">
     <div class="container">
        <div class="icon-nav" onclick="">
            <a href="#">
                <span></span>
                <span></span>
                <span></span>
            </a>
            <div>Меню</div>
        </div>
        <div class="logo">
<{if $arCategory.module!="home"}>
            <a href="/">
<{/if}>
                <img src="/images/smart/icons/svg/logo-maikoff.svg" alt="">
                <p class="logo-sign">печать на одежде</p>
<{if $arCategory.module!="home"}>
            </a>
<{/if}>
        </div>
        <{include file='core/search-form.tpl'}>
        <{include file='menu/top.tpl' arItems=$mainMenu}>
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