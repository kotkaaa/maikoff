<div class="product-price">
    <div class="btn-wrap">
        <div class="price" data-price="<{$item.price}>">
            <{$item.price|number_format:0:'.':' '}>
        </div>
        <{include file="core/product-buy-button.tpl"}>
        <{*<div class="buy-terms">
            с нанесением от 5 шт - 70 грн/шт<br/>
            с нанесением от 25 шт - 50 грн/шт
        </div>*}>
    </div>
</div>