<{if $orders}>
    <table class="list" width="100%" style="border: 1px solid #b6b6b6;">
        <tr>   
            <td id="headb" align="center" width="32">№ заказа</td>
            <td id="headb" align="center" width="80">Статус</td>
            <td id="headb" align="center" width="50">Создан</td>
            <td id="headb" align="center" width="50">Выполнен</td>
            <{if $arrPageData.module != 'customers'}>
                <td id="headb" align="center" width="50">Пользователь</td>
            <{/if}>
            <td id="headb">Товары</td>
            <td id="headb" align="center" width="50">Стоимость доставки, грн</td>
            <td id="headb" align="center" width="50">Стоимость товаров, грн</td>
            <td id="headb" align="center" width="50">Итого</td>
        </tr>
        <{section name=i loop=$orders}>
        <tr<{if $orders[i].color_hex}> style="background:#<{$orders[i].color_hex}>"<{/if}>>
            <td align="center" width="32"><a href="/admin.php?module=orders&task=editItem&itemID=<{$orders[i].id}>"><{$orders[i].id}></a></td>
            <td align="center" width="80"><{$orders[i].status_title}></td>
            <td align="center" width="50"><{HTMLHelper::formatDate($orders[i].created)}></td>
            <td align="center" width="50"><{if $orders[i].closed}><{HTMLHelper::formatDate($orders[i].closed)}><{else}> -- <{/if}></td>
            <{if $arrPageData.module != 'customers'}>
                <td align="center"><a href="/admin.php?module=customers&task=editItem&itemID=<{$orders[i].user_id}>" target="_blank"><{$orders[i].name}></a></td> 
            <{/if}>
            <td>
                <{section name=j loop=$orders[i].products}>
                    <div>
                        <{$orders[i].products[j].title}>,
                        цвет - <b><{$orders[i].products[j].color_title}></b>,
                        размер - <b><{$orders[i].products[j].size_title}></b>,
                        кол-во - <b><{$orders[i].products[j].qty}> шт</b>,
                        цена за шт - <b><{HTMLHelper::formatPrice($orders[i].products[j].price)}> грн</b>
                    </div>
                <{/section}>
            </td>
            <td align="center" width="50"><{HTMLHelper::formatPrice($orders[i].shipping_price)}></td>
            <td align="center" width="50"><{HTMLHelper::formatPrice($orders[i].total_price)}></td>
            <td align="center" width="50"><{HTMLHelper::formatPrice($orders[i].shipping_price + $orders[i].total_price)}></td>
        </tr>
        <{/section}>
    </table>
<{/if}>