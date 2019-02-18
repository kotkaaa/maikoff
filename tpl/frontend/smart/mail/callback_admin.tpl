<!DOCTYPE html>
<html>
    <head>
        <title>Информационное сообщение</title>
    </head>
    <body style="font-family: 'Open Sans', Verdana, Tahoma, sans-serif;">
        <h2 style="font-weight: normal; color: #343434">Запрос обратного звонка</h2>
        <p><strong>Дата, время:</strong> <{$smarty.now|date_format:"%d.%m.%Y %H:%M"}></p>
<{if isset($arData.firstname) and !empty($arData.firstname)}>
        <p><strong>Имя:</strong> <{$arData.firstname}></p>
<{/if}>
        <p><strong>Телефон:</strong> <{$arData.phone}></p>
        <br/>
        <br/>
        <hr/>
        <p>Сообщение отправлено со страницы<br/>
            <strong><{$arData.source_name}></strong> <a href="<{$arData.source_url}>"><{$arData.source_url}></a></p>
        <p><a href="https://<{$arData.sitename}>/admin.php?module=orders&task=editItem&itemID=<{$arData.orderID}>">Перейти к редактированию заявки</a></p>
    </body>
</html>