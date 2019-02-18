<!DOCTYPE html>
<html style='color:#212121; font-family:"Open Sans", sans-serif; font-size:15px; margin:0; min-width:76.15385em; padding:0; scroll-behavior:smooth; width:100%' width="100%">
    <head>
        <title>Заказ №<{$arData.order_id}></title>
        <meta name="viewport" content="width=794, initial-scale=1.0, minimum-scale=1.0"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <body style='color:#212121; font-family:"Open Sans", sans-serif; font-size:1em; line-height: 1.4; margin:0; min-width:76.15385em; padding:0; scroll-behavior:smooth; width:100%; position:relative;' width="100%">
        <div class="page" style="margin:0 auto; padding:0; text-align:left; width:210mm" align="left" width="210mm">
            <div class="print-page" style="padding:1.5em 3.5em 3em 3.5em;">
                <div class="print-head clearfix" style="padding-bottom:1.3cm;">
                    <div class="print-logo" style="float:left; width:4.5cm" width="4.5cm">
                        <a href="<{$smarty.const.WLCMS_HTTP_HOST}>" style="color:#09719C; text-decoration:none;">
                            <img src="<{$smarty.const.WLCMS_HTTP_HOST}>/images/site/smart/logo.jpg" alt="Maikoff.com.ua"/>
                        </a>
                    </div>
                    <div class="print-shedule" style="margin-left:6cm; padding:0.15cm 0 0 0;">
                        <div class="print-shedule-phone" style="font-size:1.28em; letter-spacing:-0.025em; padding-bottom:0.1cm; text-align: right;">
<{foreach name=i from=$HTMLHelper->getContactPhones($objSettingsInfo->sitePhone) item=phone}>
                            <nobr><a href="tel:<{$phone.tel}>" style="color:inherit; text-decoration:none;"><{$phone.num}></a></nobr><{if !$smarty.foreach.i.last}>, <{/if}>
<{/foreach}>
                        </div>
                        <div style="font-size:0.85em; letter-spacing:0.015em; text-align: right;">Киев, ул. Константиновская 68 (Подол)</div>
                    </div>
                </div>
                <div class="print-order">
                    <h2 class="print-order-title" style="margin-bottom:0.5em; margin-top:0; font-size:2.4em; font-weight:400">Заказ № <{$arData.order_id}></h2>
                    <p style="font-size: 1em; margin-bottom: 2em;">
<{if !empty($arData.firstname)}>
                        Здравствуйте, <{$arData.name}>!<br/>
<{/if}>
<{if $arPayment.is_card}>
                        При оплате заказа на карту сроки изготовления товара от 2 до 5 рабочих дней.
<{else}>
                        Для подтверждения заказа наш менеджер свяжется с вами<br/>
                        в ближайшее время.
<{/if}>
                    </p>
                    <div class="print-cart">
                        <table style="border-collapse:collapse; border-spacing:0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td colspan="2" style="font-size:1em; padding: 0.3em 0.2em; border-bottom:1px solid #b9b9b9; color: #444444;" align="left" width="45%">Название товара</td>
                                <td style="font-size:1em; padding: 0.3em 0.2em; border-bottom:1px solid #b9b9b9; color: #444444;" align="center" width="20%">цена</td>
                                <td style="font-size:1em; padding: 0.3em 0.2em; border-bottom:1px solid #b9b9b9; color: #444444;" align="center" width="15%">кол-во</td>
                                <td style="font-size:1em; padding: 0.3em 0.2em; border-bottom:1px solid #b9b9b9; color: #444444;" align="right" width="20%">сумма</td>
                            </tr>
<{foreach name=i from=$Basket->getItems() key=arKey item=arItem}>
                            <tr>
                                <td width="17%" align="left" valign="top" style="padding-top:0.85em; padding-bottom:0.85em; border-bottom:1px solid #b9b9b9;">
                                    <a href="<{$smarty.const.WLCMS_HTTP_HOST}><{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}><{if $arItem.module=="prints"}>#<{$arItem.color_hex}><{/if}>" target="_blank">
                                        <img src="<{$smarty.const.WLCMS_HTTP_HOST}><{$arItem.small_image}>" alt="<{$arItem.title}>" style="max-width:2.65cm; border:none">
                                    </a>
                                </td>
                                <td align="left" valign="top" style="font-size:1.15em; line-height:1.538em; padding-bottom:0.5em; padding-top:0.85em; border-bottom:1px solid #b9b9b9;">
                                    <a href="<{$smarty.const.WLCMS_HTTP_HOST}><{include file="core/href_item.tpl" arCategory=$arItem.arCategory arItem=$arItem}><{if $arItem.module=="prints"}>#<{$arItem.color_hex}><{/if}>" target="_blank" style="color:#09719C; text-decoration:none"> <{$arItem.title}> </a>
<{if !empty($arItem.color_title)}>
                                    <br/><span style="font-size: 0.85em; color: #666;"><{$arItem.color_title}> цвет</span>
<{/if}>
<{if !empty($arItem.size_title)}>
                                    <br/><span style="font-size: 0.85em; color: #666;">Размер <{$arItem.size_title}></span>
<{/if}>
                                    <br/><span style="font-size: 0.85em; color: #666;">Артикул <{$arItem.pcode}></span>
                                </td>
                                <td align="center" valign="middle" style="font-size:1em;padding-bottom:0.85em; border-bottom:1px solid #b9b9b9;"> <{$arItem.price|number_format:0:".":" "}> грн </td>
                                <td align="center" valign="middle" style="font-size:1em;padding-bottom:0.85em; border-bottom:1px solid #b9b9b9;"> <{$arItem.quantity}> шт </td>
                                <td align="right" valign="middle" style="font-size:1em;padding-bottom:0.85em; border-bottom:1px solid #b9b9b9;"><span style="font-size: 1.25em; font-weight: bold;"><{$arItem.amount|number_format:0:".":" "}></span> грн</td>
                            </tr>
<{/foreach}>
                            <tr>
                                <td colspan="5" style="padding:0; border-top:1px solid #b9b9b9; font-size:1em; height: 1px;" height="1"></td>
                            </tr>
<{if $Basket->getShippingPrice()>0}>
                            <tr>
                                <td colspan="3" align="left" valign="middle" style="padding-top:0.85em; font-size: 1em;"> Общая сумма </td>
                                <td colspan="2" align="right" valign="middle" style="padding-top:0.85em;"><span style="font-size: 1.5em; font-weight: bold;"><{$Basket->getTotalPrice()|number_format:0:".":" "}></span> грн</td>
                            </tr>
                            <tr>
                                <td colspan="3" align="left" valign="middle" style="padding-top:0.85em; font-size: 1em;"> Стоимость доставки </td>
                                <td colspan="2" align="right" valign="middle" style="padding-top:0.85em;"><span style="font-size: 1.5em; font-weight: bold;"><{$Basket->getShippingPrice()|number_format:0:".":" "}></span> грн</td>
                            </tr>
<{elseif $Basket->getShippingID()==2}>
                            <tr>
                                <td colspan="5" align="left" valign="middle" style="padding-top:0.85em;font-size: .85em;">
                                    Стоимость доставки согласно тарифов компании-перевозчика
                                </td>
                            </tr>
<{/if}>
                            <tr>
                                <td colspan="3" align="left" valign="middle" style="padding-top:0.85em; font-size: 1.25em;"> Итого к оплате </td>
                                <td colspan="2" align="right" valign="middle" style="padding-top:0.85em;"><span style="font-size: 1.5em; font-weight: bold;"><{$Basket->getTotalPrice(1)|number_format:0:".":" "}></span> грн</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <br/>
                <br/>
                <br/>
                <div class="print-info">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" align="left" valign="top" width="30%">
                                Дата и время
                            </td>
                            <td width="1.25em"></td>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" valign="top">
                                <{$smarty.now|date_format:"%d.%m.%Y %H:%M"}>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" align="left" valign="top" width="30%">
                                Имя
                            </td>
                            <td width="1.25em"></td>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" valign="top">
                                <{$arData.name}>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" align="left" valign="top" width="30%">
                                Способ доставки
                            </td>
                            <td width="1.25em"></td>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" valign="top" >
                                <{$arData.shipping}>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" align="left" valign="top" width="30%">
                                Телефон
                            </td>
                            <td width="1.25em"></td>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" valign="top">
                                <{$arData.phone}>
                            </td>
                        </tr>
<{if !empty($arData.email)}>
                        <tr>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" align="left" valign="top" width="30%">
                                Эл. почта
                            </td>
                            <td width="1.25em"></td>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" valign="top">
                                <{$arData.email}>
                            </td>
                        </tr>
<{/if}>
<{if !empty($arData.comment)}>
                        <tr>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" align="left" valign="top" width="30%">
                                Комментарий к заказу
                            </td>
                            <td width="1.25em"></td>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" valign="top">
                                <{$arData.comment|unScreenData}>
                            </td>
                        </tr>
<{/if}>
<{if $arPayment.is_card}>
                        <tr>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" align="left" valign="top" width="30%">
                                Данные по оплате
                            </td>
                            <td width="1.25em"></td>
                            <td style="padding-bottom:0.1cm;padding-top:0.1cm; font-size:1em;" valign="top">
                                <{$arPayment.card_info}>
                            </td>
                        </tr>
<{/if}>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>