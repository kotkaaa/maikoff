<div class="basket-dropdown hidden">
    <div class="basket-body">
        <div class="basket-inner container">
            <div class="heading">
                <strong>Корзина товаров</strong>
                <span>Количество</span>
                <span>За один товар</span>
                <span>Общая стоимость</span>
            </div>
            <div class="list" id="basketLayout">
                <{include file="ajax/basket.tpl"}>
            </div>
        </div>
    </div>
    <div class="basket-footer">
        <div class="basket-inner container">
            <div class="basket-quick-ckeckout">
                <div class="form">
                    <form action="<{include file="core/href.tpl" arCategory=$arrModules.checkout params="quick=1"}>" method="POST">
                        <input type="tel" name="phone">
                        <button class="btn btn-l btn-success" type="submit" disabled></button>
                    </form>
                </div>
                <a href="javascript:Basket.close();" class="return">Продолжить покупки</a>
            </div>
            <{*<input type="file" class="hidden" name="files[]" id="basketFileUpload" accept=".png,.jpg,.jpeg,.gif,.ai,.eps,.cdr,.pdf,.psd" multiple="">*}>
            
            <{*<button class="btn btn-primary btn-xxl btn-attach sm" <{if count($Basket->getFiles())>=3}>disabled<{/if}>>
                <span class="pin"></span>
                Вложить лого
            </button>
            <div class="attachments">
<{foreach from=$Basket->getFiles() key=fileID item=file}>
                <span data-file-id="<{$fileID}>"><{$file.title}> <a href="#">&times;</a></span>
<{/foreach}>
            </div>*}>
            <div class="total">
                Общая сумма
                <strong id="basketTotal">
                    <{$Basket->getTotalPrice()|number_format:0:'.':' '}>
                </strong><br/>
                <a href="<{include file="core/href.tpl" arCategory=$arrModules.checkout}>" class="btn btn-warning btn-xxl lg">Оформить заказ</a>
            </div>
        </div>
    </div>
</div>