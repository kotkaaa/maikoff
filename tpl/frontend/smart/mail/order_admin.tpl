<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" type="text/html; charset=utf-8" />
        <title>Новый заказ №<{$arData.order_id}></title>
    </head>
    <body style="font-family: 'Open Sans', Verdana, Tahoma, sans-serif; margin: 0; padding: 0;">
        <h2>Информация о заказе</h2>
        <table border="0" cellspacing="0" cellpadding="4">
            <tr valign="top">
                <td>
                    <strong>Дата создания:</strong>
                </td>
                <td>
                    <{$smarty.now|date_format:"%d.%m.%Y %h:%m"}>
                </td>
            </tr>
<{if isset($arData.name) and !empty($arData.name)}>
            <tr valign="top">
                <td>
                    <strong>Имя:</strong>
                </td>
                <td>
                    <{$arData.name}>
                </td>
            </tr>
<{/if}>
            <tr valign="top">
                <td>
                    <strong>Телефон:</strong>
                </td>
                <td>
                    <{$arData.phone}>
                </td>
            </tr>
<{if isset($arData.email) and !empty($arData.email)}>
            <tr valign="top">
                <td>
                    <strong>E-mail:</strong>
                </td>
                <td>
                    <{$arData.email}>
                </td>
            </tr>
<{/if}>
<{if isset($arData.shipping) and !empty($arData.shipping)}>
            <tr valign="top">
                <td>
                    <strong>Доставка:</strong>
                </td>
                <td>
                    <{$arData.shipping}>
                </td>
            </tr>
<{/if}>
<{if isset($arData.comment) and !empty($arData.comment)}>
            <tr valign="top">
                <td>
                    <strong>Комментарий к заказу:</strong>
                </td>
                <td>
                    <{$arData.comment|unScreenData}>
                </td>
            </tr>
<{/if}>
        </table>
        <br/>
        <h2 style="font-weight: normal;">Товары</h2>
        <table border="0" cellspacing="0" cellpadding="4" style="border-color: #343434;">
            <tr>
                <th colspan="2" align="left" style="font-size: 1em;">Наименование</th>
                <th align="center" style="font-size: 1em;" width='15%'>Цена</th>
                <th align="center" style="font-size: 1em;" width='15%'>Кол-во</th>
                <th align="center" style="font-size: 1em;" width='15%'>Сумма</th>
            </tr>
<{foreach name=i from=$Basket->getItems() key=arKey item=arItem}>
            <tr valign="top">
                <td valign="top" style="border-top: 1px solid #CCC;" width='15%'>
                    <a href="<{$smarty.const.WLCMS_HTTP_HOST}><{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}><{if $arItem.module=="prints"}>#<{$arItem.color_hex}><{/if}>" target="_blank">
                        <img src="<{$smarty.const.WLCMS_HTTP_HOST}><{$arItem.small_image}>"/>
                    </a>
                </td>
                <td valign="top" align="left" style="border-top: 1px solid #CCC;">
                    <span style="font-size: 1.5em">
                        <a href="<{$smarty.const.WLCMS_HTTP_HOST}><{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}><{if $arItem.module=="prints"}>#<{$arItem.color_hex}><{/if}>" target="_blank"><{$arItem.title}></a>
                    </span>
<{if !empty($arItem.color_title)}>
                    <br/><span style="font-size: 0.85em; color: #666;"><{$arItem.color_title}> цвет</span>
<{/if}>
<{if !empty($arItem.size_title)}>
                    <br/><span style="font-size: 0.85em; color: #666;">Размер <{$arItem.size_title}></span>
<{/if}>
                    <br/><span style="font-size: 0.85em; color: #666;">Артикул <{$arItem.pcode}></span>
                </td>
                <td align="center" valign='middle' style="border-top: 1px solid #CCC;"> <{$arItem.price}> грн </td>
                <td align="center" valign='middle' style="border-top: 1px solid #CCC;"> <{$arItem.quantity}> шт </td>
                <td align="center" valign='middle' style="border-top: 1px solid #CCC;"> <{$arItem.amount}> грн </td>
            </tr>
<{/foreach}>
            <tr valign="top">
                <td valign="top" style="border-top: 1px solid #CCC;" colspan="5"></td>
            </tr>
<{if $Basket->getShippingPrice()>0}>
            <tr>
                <td colspan="4" align="right" style="font-size: 1em;">
                    Общая сумма:
                </td>
                <td align="center" style="font-size: 1em;">
                    <strong style="font-size: 1.25em;"><{$Basket->getTotalPrice()|number_format:0:".":" "}></strong> грн
                </td>
            </tr>
            <tr>
                <td colspan="4" align="right" style="font-size: 1em;">
                    Стоимость доставки:
                </td>
                <td align="center" style="font-size: 1em;">
                    <strong style="font-size: 1.25em;"><{$Basket->getShippingPrice()|number_format:0:".":" "}></strong> грн
                </td>
            </tr>
<{elseif $Basket->getShippingID()==2}>
            <tr>
                <td colspan="5" align="right" style="font-size: .85em;">
                    Стоимость доставки согласно тарифов компании-перевозчика
                </td>
            </tr>
<{/if}>
            <tr>
                <td colspan="4" align="right" style="font-size: 1em;">
                    Итого:
                </td>
                <td align="center" style="font-size: 1.25em;">
                    <strong style="font-size: 1.5em;"><{$Basket->getTotalPrice(1)|number_format:0:".":" "}></strong> грн
                </td>
            </tr>
        </table>
    </body>
</html>