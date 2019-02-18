<h2><{$arCategory.title}></h2>
<div class="ordering-wrapper modal-order-form">
    <div class="form">
        <form action="" method="POST" id="checkoutForm">
            <div class="feedback">
                <label for="name">* Ваше имя</label>
                <input type="text" name="name" id="name" class="name requiredfield <{if isset($arrPageData.errors.name)}>error<{/if}>" value="<{if isset($formData.name)}><{$formData.name}><{/if}>"/>
                <label for="phone">* Номер телефона для обратной связи</label>
                <input type="tel" name="phone" id="phone" class="phone requiredfield <{if isset($arrPageData.errors.phone)}>error<{/if}>" placeholder="+38" value="<{if isset($formData.phone)}><{$formData.phone}><{/if}>"/>
                <label for="email">  E-mail для обратной связи</label>
                <input type="email" name="email" id="email" class="email <{if isset($arrPageData.errors.email)}>error<{/if}>" value="<{if isset($formData.email)}><{$formData.email}><{/if}>"/>
                <{*<input type="file" class="hidden" name="files[]" id="checkoutFileUpload" accept=".png,.jpg,.jpeg,.gif,.ai,.eps,.cdr,.pdf,.psd" multiple="">
                <button class="btn btn-primary btn-l btn-attach sm" id="checkoutAttachBtn" <{if count($Basket->getFiles())>=3}>disabled<{/if}>>
                    <span class="pin"></span>
                    Вложить лого для печати
                </button>
                <div class="attachments">
                    <p id="checkoutAttachments">
<{foreach from=$Basket->getFiles() key=fileID item=file}>
                        <span data-file-id="<{$fileID}>"><{$file.name}> <a href="#">&times;</a></span>
<{/foreach}>
                    </p>
                </div>*}>
                <div class="f-comment">
                    <label class="toggle" onclick="$(this).toggleClass('toggle-on');"></label>
                    <textarea name="comment"><{if isset($formData.comment)}><{$formData.comment}><{/if}></textarea>
                </div>
                <label for="payment_id">  Способ оплаты</label>
                <select name="payment_id" class="choose-payment requiredfield" id="payment_id">
<{foreach name=i from=$Basket->getPaymentTypes() item=payment}>
                    <option value="<{$payment.id}>" <{if $payment.id==$Basket->getPaymentID()}>selected<{/if}>><{$payment.title}></option>
<{/foreach}>
                </select>
                <label for="shipping_id">  Способ доставки</label>
                <select name="shipping_id" class="choose-delivery requiredfield" id="shipping_id">
<{foreach name=i from=$Basket->getShippingTypes() item=type}>
                    <option value="<{$type.id}>" <{if $type.id==$Basket->getShippingID()}>selected<{/if}>><{$type.title}><{if $type.id==3}> (Подол)<{/if}></option>
<{/foreach}>
                </select>
                <div class="shipping-address hidden toggle-content" data-shipping-id="3">
                    <p>Адрес: г. Киев, ул. Константиновская 68<br/>
                    (подол), ст. метро Т. Шевченко</p>
                    <p>Время работы:<br/>
                        Пн. - Пт.: 10:00 - 19:00<br/>
                        Сб. - Вс.: Выходной</p>
                    <p>
                        <strong><a href="#">Схема проезда</a></strong>
                    </p>
                </div>
                <label for="city" class="toggle-content hidden" data-shipping-id="2">* Город</label>
                <input type="text" name="city" id="city" class="email toggle-content requiredfield hidden <{if isset($arrPageData.errors.city)}>error<{/if}>" value="<{if isset($formData.city)}><{$formData.city}><{/if}>" data-shipping-id="2"/>
                <label for="address" class="toggle-content hidden" data-shipping-id="2">* № Новой Почты или название улицы</label>
                <input type="text" name="address" id="address" class="email toggle-content requiredfield hidden <{if isset($arrPageData.errors.address)}>error<{/if}>" value="<{if isset($formData.address)}><{$formData.address}><{/if}>" data-shipping-id="2" disabled/>
                <label for="address" class="toggle-content " data-shipping-id="1">* Адрес доставки</label>
                <input type="text" name="address" id="address" class="email toggle-content requiredfield <{if isset($arrPageData.errors.address)}>error<{/if}>" value="<{if isset($formData.address)}><{$formData.address}><{/if}>" data-shipping-id="1" disabled/>
                <label for="recepient" class="toggle-content hidden" data-shipping-id="2">* Имя и фамилия получателя</label>
                <input type="text" name="recepient" id="recepient" class="email toggle-content requiredfield hidden <{if isset($arrPageData.errors.recepient)}>error<{/if}>" value="<{if isset($formData.recepient)}><{$formData.recepient}><{/if}>" data-shipping-id="2"/>
                <div class="your-orders" id="basketCheckout">
                    <{include file="ajax/basket-inline.tpl"}>
                </div>
                <div class="buttons-submit">
                    <button class="btn btn-warning btn-xl adaptive">Подтверждаю заказ</button><br>
                    <a href="/" class="continue-shopping adaptive">Продолжить покупки</a>
                </div>
            </div>
        </form>
    </div>
</div>