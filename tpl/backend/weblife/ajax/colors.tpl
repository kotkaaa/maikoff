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
        <tr>
            <td width="100" align="center">
                #<input type="text" name="hex" class="field jscolor {onFineChange:'updateColor(this, 0)'}" size="6" value="<{if isset($item.hex)}><{$item.hex}><{/if}>"/>
            </td>
            <td width="250">
                <input type="text" name="title" class="field" size="25" value="<{if isset($item.title)}><{$item.title}><{/if}>"/>
            </td>
            <td>
                <input class="buttons" name="submit" type="submit" value="Добавить цвет">
            </td>
        </tr>
    </table>
</form>
<br/>
<form method="POST" action="<{$arrPageData.admin_url}>&task=reorderItems">
    <table class="list colored" width="100%" cellspacing="0" cellpadding="0" id="colorsTable">
        <thead>
            <tr>
                <td id="headb" align="center" width="90">Код</td>
                <td id="headb" width="220">Название</td>
                <td id="headb" width="350">SEO путь</td>
                <td id="headb" align="center">Сорт.</td>
                <td id="headb" align="center">Удал.</td>
            </tr>
        </thead>
        <tbody style="padding-top: 5px; padding-bottom: 5px;">
<{section name=i loop=$items}>
            <tr class="colorsort" style="background-color: #FFFFFF;">
                <td align="center" style="padding-top: 5px; padding-bottom: 5px;">
                    <input type="hidden" name="arItems[<{$items[i].id}>][id]" value="<{$items[i].id}>"/>
                    #<input type="text" name="arItems[<{$items[i].id}>][hex]" class="field jscolor {onFineChange:'updateColor(this, <{$items[i].id}>)'}" size="6" value="<{$items[i].hex}>"/>
                </td>
                <td style="padding-top: 5px; padding-bottom: 5px;">
                    <input type="text" class="field" name="arItems[<{$items[i].id}>][title]" value="<{$items[i].title}>" size="25"/>
                    <img class="more" src="/images/admin/more.png" align="middle"/>
                    <div class="variations">
                        <label style="display: block; margin-bottom: 4px;">Single</label>
                        <input type="text" class="field" name="arItems[<{$items[i].id}>][title_single]" value="<{$items[i].title_single}>" style="width: 150px;">
                        <br>
                        <br>
                        <label style="display: block; margin-bottom: 4px;">Multi</label>
                        <input type="text" class="field" name="arItems[<{$items[i].id}>][title_multi]" value="<{$items[i].title_multi}>" style="width: 150px;">
                        <br>
                        <br>
                        <label style="display: block; margin-bottom: 4px;">Male</label>
                        <input type="text" class="field" name="arItems[<{$items[i].id}>][title_male]" value="<{$items[i].title_male}>" style="width: 150px;">
                        <br>
                        <br>
                        <label style="display: block; margin-bottom: 4px;">Female</label>
                        <input type="text" class="field" name="arItems[<{$items[i].id}>][title_female]" value="<{$items[i].title_female}>" style="width: 150px;">
                        <br>
                        <br>
                        <label style="display: block; margin-bottom: 4px;">Extra</label>
                        <input type="text" class="field" name="arItems[<{$items[i].id}>][title_extra]" value="<{$items[i].title_extra}>" style="width: 150px;">
                    </div>
                </td>
                <td style="padding-top: 5px; padding-bottom: 5px;">
                    <input type="text" class="field left" name="arItems[<{$items[i].id}>][seo_path]" value="<{$items[i].seo_path}>" size="25"/>
                    <input type="button" value="Генерировать" style="margin: 0 0 0 10px; min-width: 100px;" class="buttons left" onclick="if(this.form['arItems[<{$items[i].id}>][title]'].value.length==0){alert('Вы не ввели название цвета!'); this.form['arItems[<{$items[i].id}>][title]'].focus(); return false; } else{ generateSeoPath(this.form['arItems[<{$items[i].id}>][seo_path]'], this.form['arItems[<{$items[i].id}>][title]'].value, 'color');}">
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
                <td align="right" colspan="5">
                    <input class="buttons" name="submit" type="submit" value="Сохранить"/>
                </td>
            </tr>
        </tfoot>
    </table>
</form>