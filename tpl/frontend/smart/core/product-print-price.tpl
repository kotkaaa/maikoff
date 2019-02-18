<div class="product-price horizontal">
    <div class="btn-wrap">
        <div class="price" data-price="<{$item.price}>">
            <{$item.price|number_format:0:'.':' '}>
        </div>
        <{include file="core/product-print-buy-button.tpl"}>
    </div>
<{*    <div class="sign">Бесплатная доставка от 4-х единиц с печатью</div>*}>
</div>