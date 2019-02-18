<{if !empty($item) && $itemModule=='orders'}>        
    <div id="printAll" class="wrapper" style="max-height:550px;overflow-y:scroll;">
        <div id="printCheck" style="padding: 20px">
            <div>                    
                <div style="float:left;font:15px sans-serif;">Maikoff.com.ua</div>
                <div style="float:right;font:15px sans-serif;"><{HTMLHelper::formatDate($smarty.now|date_format:"d.m.Y H:i:s")}></div>
                <div style="clear:both;"></div>
            </div>
            <br/>
            <table width="100%" border='1' cellspacing="2" cellpadding="2" style="border-collapse:collapse;font:11px sans-serif;">
                <caption style="margin-bottom:13px"><span style="font:italic 17px sans-serif;">ТОВАРНИЙ ЧЕК №<{$item.id}></span></caption>
                <tbody>
                    <tr>
                        <td align="center" width='30'>№</td>
                        <td align="center" width='50'></td>
                        <td>Назва</td>
                        <td align="center" width='30'>К-ть<br/>шт</td>
                        <td align="center" width='30'>Ціна,<br/>грн</td>
                        <td align="center" width='30'>Сума,<br/>грн</td>
                        <td align="center" width='30'>Знижка,<br/>грн</td>
                        <td align="center" width='30'>Всього,<br/>грн</td>
                    </tr>
                    <{if !empty($item.arProducts)}>
                        <{section name=i loop=$item.arProducts}>  
                            <tr>
                                <td align="center"><{$smarty.section.i.iteration}></td>
                                <td align="center"><img src="<{$item.arProducts[i].product_image}>" width="50"/></td>
                                <td align="left" style="word-break:break-all;padding:5px;">
                                    <b><{$item.arProducts[i].title|unscreenData}></b><br/>
                                    Тип товару: <{$item.arProducts[i].substrate_title}><br/>
                                    Колір: <{$item.arProducts[i].color_title}><br/>
                                    Розмір: <{$item.arProducts[i].size_title}><br/>
                                    <{if $item.arProducts[i].brand_title}>Бренд: <{$item.arProducts[i].brand_title}><br/><{/if}>
                                    <{if $item.arProducts[i].series_title}>Серія: <{$item.arProducts[i].series_title}><br/><{/if}>
                                </td>
                                <td align="center"><{$item.arProducts[i].qty}></td>
                                <td align="center"><{HTMLHelper::formatPrice($item.arProducts[i].price)}></td>
                                <td align="center"><{HTMLHelper::formatPrice($item.arProducts[i].price*$item.arProducts[i].qty)}></td>
                                <td align="center"><{if $item.arProducts[i].discount_value > 0}><{HTMLHelper::formatPrice($item.arProducts[i].discount_value)}><{else}> -- <{/if}></td>
                                <td align="center"><{HTMLHelper::formatPrice($item.arProducts[i].total_price)}></td>
                            </tr>
                        <{/section}>
                    <{/if}>
                    <tr>
                        <td colspan="7" align="right">Вартість товарів, грн:</td>
                        <td align="center"><{HTMLHelper::formatPrice($item.total_price)}></td>
                    </tr>
                    <tr>
                        <td colspan="7" align="right">Вартість доставки, грн:</td>
                        <td align="center"><{HTMLHelper::formatPrice($item.shipping_price)}></td>
                    </tr>
                    <tr>
                        <td colspan="7" align="right">Всього, грн:</td>
                        <td align="center"><b><{HTMLHelper::formatPrice($item.shipping_price+$item.total_price)}></b></td>
                    </tr>
                    <{if $item.prepay}>
                    <tr>
                        <td colspan="7" align="right">Предоплата, грн:</td>
                        <td align="center"><b><{HTMLHelper::formatPrice($item.prepay)}></b></td>
                    </tr>
                    <tr>
                        <td colspan="7" align="right">Остаток к оплате, грн:</td>
                        <td align="center"><b><{HTMLHelper::formatPrice($item.shipping_price+$item.total_price-$item.prepay)}></b></td>
                    </tr>
                    <{/if}>
                </tbody>
            </table>

            <div style="font-family:sans-serif;font-size:14px">
                <table width="100%" border='1' cellspacing="2" cellpadding="2" style="border-collapse:collapse;font:11px sans-serif;">                    
                    <tr><td width="95">Имя получателя:</td><td><{if $item.name}><{$item.name}><{else if $item.user_id}><{$item.user_title}><{/if}><{if $item.recepient}> (получатель НП <{$item.recepient}>)<{/if}></td></tr>
                    <tr><td>Адрес:</td><td><{if $item.city}>г. <{$item.city}><br/><{/if}><{$item.address}></td></tr>
                    <tr><td>Телефон:</td><td><{$item.phone}></td></tr>
                    <{if $item.comment}><tr><td>Коммент клиента:</td><td><{$item.comment}></td></tr><{/if}>
                    <tr><td>К оплате, грн</td><td><{HTMLHelper::formatPrice($item.shipping_price+$item.total_price-$item.prepay)}></td></tr>
                </table>
                <div style="text-align:center;padding:20px;border:1px solid"><b>Заказ №<{$item.id}></b></div><br/><br/>
            </div>
        </div>
    </div>
    <div class="nav" style="width:100%;text-align:center;background:#eee;padding:10px 0px;position:fixed;bottom:0px;">           
        <button onclick="return printInContainer('printCheck');" style="background:green;color:#fff;padding:5px 10px;cursor:pointer;">Печатать чек</button>
    </div>

<{/if}>

<script type="text/javascript">
    function printInContainer(id) {
        var htmlString = document.getElementById(id).innerHTML;
        var newIframe = document.createElement('iframe');
        newIframe.width = '1px';
        newIframe.height = '1px';
        newIframe.src = 'about:blank';

        // for IE wait for the IFrame to load so we can access contentWindow.document.body
        newIframe.onload = function() {
            var script_tag = newIframe.contentWindow.document.createElement("script");
            script_tag.type = "text/javascript";
            var script = newIframe.contentWindow.document.createTextNode('function Print(){ window.focus(); window.print(); }');
            script_tag.appendChild(script);

            newIframe.contentWindow.document.body.innerHTML = htmlString;
            newIframe.contentWindow.document.body.appendChild(script_tag);

            // for chrome, a timeout for loading large amounts of content
            setTimeout(function() {
                newIframe.contentWindow.Print();
                newIframe.contentWindow.document.body.removeChild(script_tag);
                newIframe.parentElement.removeChild(newIframe);
            }, 200);
        };
        document.body.appendChild(newIframe);
    }
</script>