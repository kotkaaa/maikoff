<div class="product-form vertical">
    <div class="select-color">
        <label class="h-label">Цвета (<{$item.colors|@count}>)</label>
        <div class="row">
<{foreach from=$item.colors item=color}>
<{if $color.checked}>
            <input type="radio" name="color" id="color_<{$color.id}>" class="hidden" checked/>
            <label class="check-label" for="color_<{$color.id}>" title="<{$color.title}>">
                <div class="pic" style="background-color: #<{$color.hex}>;"></div>
                <span><{$HTMLHelper->shortenColorTitle($color.title, 8)}></span>
            </label>
<{else}>
            <a class="check-label" href="<{include file="core/href_item.tpl" arCategory=$color.arCategory arItem=$color params=""}>" title="<{$color.title}>">
                <div class="pic" style="background-color: #<{$color.hex}>;"></div>
                <span><{$HTMLHelper->shortenColorTitle($color.title, 8)}></span>
            </a>
<{/if}>
<{/foreach}>
        </div>
    </div>
    <div class="select-size">
        <label class="h-label">Выберите размеры</label>
        <div class="size-grid">
<{foreach from=$item.sizes item=size}>
            <{assign var=qty value=$Basket->qty($size.idKey)}>
            <div class="size-row">
                <div class="size-label"><{$size.title}></div>
                <div class="spin-edit xl js-ready" data-min-val="0">
                    <button class="spin-btn spin-down <{if $qty==0}>spin-del<{/if}>"></button>
                    <input type="text" value="<{$qty}>" data-idkey="<{$size.idKey}>" readonly>
                    <button class="spin-btn spin-up"></button>
                </div>
            </div>
<{/foreach}>
        </div>
    </div>
    <div class="get-calc">
        <label class="h-label">Просчет печати лого/надписи</label>
        Загрузите свое лого и отправьте
        нам для просчета печати на этой футболке<br/>
        <button class="btn btn-primary btn-xl btn-attach" onclick="Modal.open('<{include file="core/href.tpl" arCategory=$arrModules.request}>');">
            <span class="pin"></span>
            Рассчитать печать
        </button>
    </div>
</div>