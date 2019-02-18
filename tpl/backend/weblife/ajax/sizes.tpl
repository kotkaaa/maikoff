<div id="messages" class="info <{if empty($arrPageData.messages)}>hidden_block<{/if}>">
<{if !empty($arrPageData.messages)}>
    <{$arrPageData.messages|@implode:'<br/>'}>
<{/if}>
</div>
<div id="messages" class="error <{if empty($arrPageData.errors)}>hidden_block<{/if}>">
<{if !empty($arrPageData.errors)}>
    <{$arrPageData.errors|@implode:'<br/>'}>
<{/if}>
</div>
<br/>
<form method="POST" action="<{$arrPageData.admin_url}>&task=addItem">
    <table class="sheet" cellspacing="0" cellpadding="0" width="100%" border="0" style="border: none;">
        <td width="190" style="padding-left: 10px;">
            <input type="text" name="title" class="field" size="25" value="<{if isset($item.title)}><{$item.title}><{/if}>"/>
        </td>
        <td>
            <input class="buttons" name="submit" type="submit" value="Добавить размер">
        </td>
    </table>
</form>
<br/>
<form method="POST" action="<{$arrPageData.admin_url}>&task=reorderItems">
    <table class="list colored" width="100%" cellspacing="0" cellpadding="0" id="sizesTable">
        <thead>
            <tr>
                <td id="headb">Название</td>
                <td id="headb" align="center" width="40">Сорт.</td>
                <td id="headb" align="center" width="30">Удал.</td>
            </tr>
        </thead>
        <tbody style="padding-top: 5px; padding-bottom: 5px;">
<{section name=i loop=$items}>
            <tr style="background-color: #FFFFFF;">
                <td style="padding-top: 5px; padding-bottom: 5px; padding-left: 10px;">
                    <input type="hidden" name="arItems[<{$items[i].id}>][id]" value="<{$items[i].id}>"/>
                    <input type="text" class="field" name="arItems[<{$items[i].id}>][title]" value="<{$items[i].title}>" size="25"/>
                </td>
                <td align="center" style="padding-top: 5px; padding-bottom: 5px;">
                    <img src="/images/sort.png" title="Нажмите и перетащите элемент на новое место в списке" style="cursor: pointer;"/>
                </td>
                <td align="center" style="padding-top: 5px; padding-bottom: 5px;">
<{if $items[i].edit}>
                    <a href="<{$arrPageData.admin_url}>&task=deleteItem&itemID=<{$items[i].id}>">
                        <img src="/images/admin/error.png" title="Нажмите и перетащите элемент на новое место в списке" width="20" height="20"/>
                    </a>
<{else}>
                    --
<{/if}>
                </td>
            </tr>
<{/section}>
        </tbody>
        <tfoot>
            <tr>
                <td align="right" colspan="3" style="padding-right: 10px;">
                    <input class="buttons" name="submit" type="submit" value="Сохранить"/>
                </td>
            </tr>
        </tfoot>
    </table>
</form>