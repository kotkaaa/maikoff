Информационное сообщение сайта <{$arData.sitename}>
--------------------------------------------------------------------------------

C Feedback формы сайта <{$arData.server}> было отправлено сообщение со следующи содержанием:

IP: <{$arData.ip}>
Время: <{$arData.created}>
Имя: <{$arData.firstname}>
Телефон: <{$arData.phone}>
<{if isset($arData.email) and !empty($arData.email)}>
E-mail: <{$arData.email}>
<{/if}>
Текст письма:
<{$arData.message}>
Ссылка на заявку https://<{$arData.sitename}>/admin.php?module=orders&task=editItem&itemID=<{$arData.orderID}>

--------------------------------------------------------------------------------
Сообщение сгенерировано автоматически.