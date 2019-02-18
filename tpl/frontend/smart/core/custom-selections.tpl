<{* product slider *}>
<{if !empty($selection.items)}>
<{if $template=="product-slider"}>
<div class="product-slider-2">
    <h2 class="h2-product-slider-2"><{$selection.title}></h2>
<{if !empty($selection.descr)}>
    <span class="after-sign"><{$selection.descr}></span>
<{/if}>
    <div class="product-swiper-element">
        <div class="arrows">
            <div class="swiper-button-prev btn-pv swiper-button-prev-0"></div>
            <div class="swiper-button-next btn-nx swiper-button-next-0"></div>
        </div>
        <div class="swiper-container">
            <div class="swiper-scrollbar"></div>
            <div class="swiper-wrapper">
<{foreach from=$selection.items item=arItem}>
                <div class="swiper-slide">
                    <div class="product-item">
<{if !empty($arItem.url)}>
                            <a href="<{$arItem.url}>">
<{/if}>
                                <img src="<{$arItem.image}>" alt="<{$arItem.title}>"/>
<{if !empty($arItem.url)}>
                            </a>
<{/if}>
<{if !empty($arItem.title)}>
                        <p class="product-name-big">
<{if !empty($arItem.url)}>
                            <a href="<{$arItem.url}>">
<{/if}>
                                <{$arItem.title}>
<{if !empty($arItem.url)}>
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
<{* brand slider *}>
<{elseif $template=="brand-slider"}>
<div class="brand-slider">
    <h2><{$selection.title}></h2>
<{if !empty($selection.descr)}>
    <span class="after-sign"><{$selection.descr}></span>
<{/if}>
    <div class="brand-swiper-element">
        <div class="arrows">
            <div class="swiper-button-prev btn-pv swiper-button-prev-0 swiper-button-disabled"></div>
            <div class="swiper-button-next btn-nx swiper-button-next-0"></div>
        </div>
        <div class="swiper-container brand-wrapper">
            <div class="swiper-wrapper">
<{foreach from=$selection.items item=arItem}>
                <div class="swiper-slide">
                    <div class="product-item">
<{if !empty($arItem.url)}>
                        <a href="<{$arItem.url}>">
<{/if}>
                            <img src="<{$arItem.image}>" alt="<{$arItem.title}>"/>
<{if !empty($arItem.url)}>
                        </a>
<{/if}>
<{if !empty($arItem.title)}>
                        <p>
<{if !empty($arItem.url)}>
                            <a href="<{$arItem.url}>">
<{/if}>
                                <{$arItem.title}>
<{if !empty($arItem.url)}>
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