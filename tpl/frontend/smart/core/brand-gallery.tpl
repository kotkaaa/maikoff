<{if !empty($arItems)}>
<{if $itemID}>
<div class="brand-slider second">
    <h2 class="nomargin-bottom">Будь стильным</h2>
    <span>Мы предлагаем широкий ассортимент принтов под различные события в вашей жизни, от корпоративных мероприятий до клубных вечеринок.</span>
    <div class="brand-swiper-element">
        <div class="arrows">
            <div class="swiper-button-prev btn-pv swiper-button-prev-0"></div>
            <div class="swiper-button-next btn-nx swiper-button-next-0"></div>
        </div>
        <div class="swiper-container brand-wrapper">
            <div class="swiper-wrapper">
<{foreach from=$arItems item=arItem}>
                <div class="swiper-slide big swiper-slide-active">
                    <div class="product-item">
<{if !empty($arItem.href)}>
                        <a href="<{$arItem.image}>">
<{/if}>
                            <img src="<{$arItem.image}>" alt="<{$arItem.title}>">
<{if !empty($arItem.href)}>
                        </a>
<{/if}>
                    </div>
                </div>
<{/foreach}>
            </div>
        </div>
    </div>
</div>
<{else}>
<div class="brand-slider third">
    <div class="brand-swiper-element">
        <div class="arrows">
            <div class="swiper-button-prev btn-pv swiper-button-prev-0"></div>
            <div class="swiper-button-next btn-nx swiper-button-next-0"></div>
        </div>
        <div class="swiper-container brand-wrapper">
            <div class="swiper-wrapper">
<{foreach from=$arItems item=arItem}>
                <div class="swiper-slide">
                    <div class="product-item">
<{if !empty($arItem.href)}>
                        <a href="<{$arItem.href}>">
<{/if}>
                        <img src="<{$arItem.image}>" alt="<{$arItem.title}>">
<{if !empty($arItem.href)}>
                        </a>
<{/if}>
<{if !empty($arItem.title)}>
                        <p>
<{if !empty($arItem.href)}>
                            <a href="<{$arItem.image}>">
<{/if}>
                            <{$arItem.title}>
<{if !empty($arItem.href)}>
                            </a>
<{/if}>
                        </p>
<{/if}>
                    </div>
                </div>
<{/foreach}>
            </div>
        </div>
    </div>
</div>
<{/if}>
<{/if}>