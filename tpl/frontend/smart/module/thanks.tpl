<div class="ordering-result">
    <div class="result-wrapper">
        <div class="thanks">
            <div class="icon"></div>
            <div class="thanks-info">
                <h3><{$arrPageData.headTitle}></h3>
                <p>Номер заказа #<{$item.id}></p>
            </div>
        </div>
        <div class="inline">
            <div class="your-orders">
                <p class="information">
                    <{if $item.arPayment.is_card}>
                        Вам на почту отправлено письмо с инструкциями по оплате.
                    <{else}>
                        В ближайшее время ваш заказ будет обработан нашими менеджерами.
                        Мы свяжемся с Вами по указанному при оформлении номеру телефона.
                    <{/if}>
                </p>
                <div class="order-edit">
                    <p>
                        Мой заказ
                    </p>
                    <p>
                        <span><{$item.total_price|number_format:0:".":" "}> </span>
                    </p>
                </div>
<{foreach name=i from=$purchases key=arKey item=arItem}>
                <div class="order-item">
<{if !empty($arItem.product_url)}>
                    <a href="<{$arItem.product_url}>">
<{/if}>
                    <img src="<{$arItem.product_image}>" alt="<{$arItem.title}>"/>
<{if !empty($arItem.product_url)}>
                    </a>
<{/if}>
                    <div class="order-info">
                        <h4>
<{if !empty($arItem.product_url)}>
                            <a href="<{$arItem.product_url}>">
<{/if}>
                            <{$arItem.title}></h4>
<{if !empty($arItem.product_url)}>
                            </a>
<{/if}>
                        <ul>
<{if !empty($arItem.color_title)}>
                            <li>
                                <span><{$arItem.color_title}> цвет</span>
                            </li>
<{/if}>
<{if !empty($arItem.size_title)}>
                            <li>
                                <span class="will-hide">Размер <{$arItem.size_title}></span>
                            </li>
<{/if}>
                            <li>
                                <span>Артикул <{$arItem.pcode}></span>
                            </li>
                        </ul>
                        <div class="order-price">
                            <span class="price"><{$arItem.price|number_format:0:".":" "}> </span>
                        </div>
                    </div>
                </div>
<{/foreach}>
            </div>
            <div class="work-graphic">
                <p>Мы всегда на связи<br/>Пн - Пт   10:00 - 19:00</p>
                <ul>
<{foreach name=i from=$HTMLHelper->getContactPhones($objSettingsInfo->sitePhone) item=phone}>
                    <li>
                        <a href="tel:<{$phone.tel}>"><{$phone.num}></a>
                    </li>
<{/foreach}>
                </ul>
                <p>
                    Пишите нам на e-mail:
                    <a href="mailto:<{$objSettingsInfo->siteEmail}>"><{$objSettingsInfo->siteEmail}></a>
                </p>
            </div>
        </div>
    </div>
</div>
<{include file='core/custom-selections.tpl' selection=$CustomSelections->getSelection(Selections::SELECTIONS_THANKS_PAGE) template="product-slider"}>
<{include file='core/contact-us.tpl'}>