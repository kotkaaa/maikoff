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
<form method="POST" action="<{$arrPageData.current_url}>">
    <table class="list" width="100%" cellspacing="0" cellpadding="0" id="seriesTable">
        <tr>
            <td colspan="2" style="padding-left: 10px;">
                <strong><{$smarty.const.HEAD_META_DATA}></strong><br/><br/>
                <div class="inline"><{$smarty.const.HEAD_SEO_TITLE}></div>
                <input type="text" name="seo_title" id="seo_title" size="94" value="<{$item.seo_title}>"/><br/><br/>
                <div class="inline"><{$smarty.const.HEAD_DESCRIPTION}> </div>
                <input type="text" name="meta_descr" id="meta_descr" size="94" value="<{$item.meta_descr}>"/><br/><br/>
                <div class="inline"><{$smarty.const.HEAD_KEYWORDS}></div>
                <input type="text" name="meta_key" id="meta_key" size="94" value="<{$item.meta_key}>"/><br/><br/>
            </td>
            <td class="buttons_row" width="145" align="center" valign="top">
                <input class="buttons" name="submit" type="submit" value="<{$smarty.const.BUTTON_SAVE}>"/>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-left: 10px;">
                <strong><{$smarty.const.HEAD_SEO_TEXT}></strong> <a href="javascript:toggleEditor('seoText');"><{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}></a><br/><br/>
                <textarea name="seo_text" id="seoText" style="width:100%;height:500px;"><{$item.seo_text}></textarea>
            </td>
            <td class="buttons_row">&nbsp;</td>
        </tr>
    </table>
</form>