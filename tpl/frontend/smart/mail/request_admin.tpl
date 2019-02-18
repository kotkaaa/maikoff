<!DOCTYPE html>
<html>
    <head>
        <title>Информационное сообщение сайта <{$arData.sitename}></title>
    </head>
    <body style="font-family: 'Open Sans', Verdana, Tahoma, sans-serif;">
        <h2 style="font-weight: normal; color: #343434">Заявка на просчет</h2>
        <p><strong>Дата, время:</strong> <{$smarty.now|date_format:"%d.%m.%Y %H:%M"}></p>
        <p><strong>Имя:</strong> <{$arData.firstname}></p>
        <p><strong>Телефон:</strong> <{$arData.phone}></p>
<{if isset($arData.email) and !empty($arData.email)}>
        <p><strong>E-mail:</strong> <{$arData.email}></p>
<{/if}>
<{if isset($arData.comment) and !empty($arData.comment)}>
        <p><strong>Комментарий:</strong> <{$arData.comment}></p>
<{/if}>
<{if !empty($arData.items)}>
        <br/>
        <h3>Товары в заказе</h3>
        <table width="100%" cellspacing="0" cellpadding="10" border="1">
            <tr>
                <td align="left" bgcolor="#CCCCCC">Товар</td>
                <td align="left" bgcolor="#CCCCCC" width="170">Нанесение принта</td>
                <td align="center" bgcolor="#CCCCCC" width="70">Кол-во</td>
            </tr>
<{foreach name=i from=$arData.items item=item}>
            <tr>
                <td align="left">
                    <strong><{$item.type}></strong>
<{if !empty($item.color)}>
                    <br/>
                    Цвет: <{$item.color}>
<{/if}>
                </td>
                <td align="left">
<{if !empty($item.print)}>
<{foreach name=j from=$item.print item=print}>
                    <{$arrPageData.print_areas[$print]}>: <{$item[$print]}><{if !$smarty.foreach.j.last}><br/><{/if}>
<{/foreach}>
<{else}>
                    без нанесения
<{/if}>
                </td>
                <td align="center"><{$item.qty}> шт.</td>
            </tr>
<{/foreach}>
        </table>
<{/if}>
        <br/>
        <br/>
        <hr/>
<{if !empty($attachments)}>
        <h3>Вложения</h3>
<{section name=i loop=$attachments}>
        <div><a href="<{$attachments[i].url}>"><{$attachments[i].name}></a><{if !$attachments[i].inEmail}> превышен размер для вложения, смотрите файл в админзоне<{/if}></div>
<{/section}>
        <br/>
        <hr/>
<{/if}>
        <p>Сообщение отправлено со страницы<br/>
            <strong><{$arData.source_name}></strong> <a href="<{$arData.source_url}>"><{$arData.source_url}></a></p>
        <p><a href="<{$arData.sitename}>/admin.php?module=orders&task=editItem&itemID=<{$arData.orderID}>">Перейти к редактированию заявки</a></p>
    </body>
</html>