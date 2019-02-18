<div class="product-image">
    <div class="product-gallery">
        <div class="screen">
            <div class="swiper-container">
                <div class="swiper-wrapper">
<{foreach name=i from=$images item=image}>
                    <div class="swiper-slide">
                        <img src="<{$image.big_image}>" data-zoom-image="<{$image.image}>" class="zoom-img" alt=""/>
                    </div>
<{/foreach}>
                </div>
            </div>
        </div>
<{if $showTitles}>
        <div class="switch">
<{foreach name=i from=$images item=image}>
<{if !empty($image.title)}>
            <a href="#" class="<{if $smarty.foreach.i.first}>selected<{/if}>" data-index="<{$smarty.foreach.i.index}>"><{$image.title}></a>
<{/if}>
<{/foreach}>
        </div>
<{/if}>
<{if $showThumbs and count($images)>1}>
        <div class="thumbs">
            <div class="swiper-container">
                <div class="swiper-wrapper">
<{foreach name=i from=$images item=image}>
                    <div class="swiper-slide thumb <{if $smarty.foreach.i.first}>selected<{/if}>" onclick="">
                        <img src="<{$image.small_image}>" alt=""/>
                    </div>
<{/foreach}>
                </div>
            </div>
        </div>
<{/if}>
    </div>
    <{include file="core/product-social.tpl"}>
</div>