<div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
<{if !empty($arrPageData.errors)}>
    <{$arrPageData.errors|@implode:'<br/>'}>
<{elseif !empty($arrPageData.messages)}>
    <{$arrPageData.messages|@implode:'<br/>'}>
<{/if}>
</div>
<{if !empty($item)}>
<form method="post" action="<{$arrPageData.admin_url|cat:"&task=editItem&itemID="|cat:$arrPageData.itemID}>">
    
    
    <table class="list" width="100%">
        <tbody>
            <tr>
                <td>
                    <div id="tabs">
                        <ul>
                            <li>
                                <a href="#tabs-strict">STRICT</a>
                            </li>
                            <li>
                                <a href="#tabs-variable">VARIABLE</a>
                            </li>
                        </ul>
                        <div id="tabs-strict">
                            <table>
                                <tr valign="top">
                                    <td width="150" align="left">
                                        <strong><{$smarty.const.HEAD_TITLE}> H1</strong>
                                    </td>
                                    <td>
                                        <input type="text" name="title" id="title" size="115" value="<{$item.title}>" />
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td width="150" align="left">
                                        <strong><{$smarty.const.HEAD_SEO_TITLE}></strong>
                                    </td>
                                    <td>
                                        <input type="text" name="seo_title" id="seo_title" size="115" value="<{$item.seo_title}>" />
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td width="150" align="left">
                                        <strong><{$smarty.const.HEAD_DESCRIPTION}></strong>
                                    </td>
                                    <td>
                                        <input type="text" name="meta_descr" id="meta_descr" size="115" value="<{$item.meta_descr}>" />
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td width="150" align="left">
                                        <strong><{$smarty.const.HEAD_KEYWORDS}></strong>
                                    </td>
                                    <td>
                                        <input type="text" name="meta_key" id="meta_key" size="115" value="<{$item.meta_key}>"/>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td width="150" align="left">
                                        <strong><{$smarty.const.HEAD_ROBOTS}></strong>
                                    </td>
                                    <td>
                                        <select name="meta_robots">
                                            <option value=""> &nbsp; <{$smarty.const.HEAD_NOT_SELECT}> &nbsp; </option>
<{section name=i loop=$arrPageData.robots}>
                                            <option value="<{$arrPageData.robots[i]}>"<{if $item.meta_robots==$arrPageData.robots[i]}> selected<{/if}>> &nbsp; <{$arrPageData.robots[i]}> &nbsp; </option>
<{/section}>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <strong><{$smarty.const.HEAD_SEO_TEXT}></strong> <a href="javascript:toggleEditor('seoText');"><{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}></a><br/>
                                        <textarea name="seo_text" id="seoText" style="width: 100%; height: 400px;"><{$item.seo_text}></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div id="tabs-variable">
                            <table>
                                <tr valign="top">
                                    <td width="150" align="left">
                                        <strong><{$smarty.const.HEAD_TITLE}> H1</strong>
                                    </td>
                                    <td>
                                        <input type="text" name="title_var" id="title" size="115" value="<{$item.title_var}>" />
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td width="150" align="left">
                                        <strong><{$smarty.const.HEAD_SEO_TITLE}></strong>
                                    </td>
                                    <td>
                                        <input type="text" name="seo_title_var" id="seo_title_var" size="115" value="<{$item.seo_title_var}>" />
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td width="150" align="left">
                                        <strong><{$smarty.const.HEAD_DESCRIPTION}></strong>
                                    </td>
                                    <td>
                                        <input type="text" name="meta_descr_var" id="meta_descr_var" size="115" value="<{$item.meta_descr_var}>" />
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td width="150" align="left">
                                        <strong><{$smarty.const.HEAD_KEYWORDS}></strong>
                                    </td>
                                    <td>
                                        <input type="text" name="meta_key_var" id="meta_key_var" size="115" value="<{$item.meta_key_var}>"/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td align="center">
                    <input class="buttons inline-block" name="submit" type="submit" value="Сохранить">&emsp;
                    <input class="buttons inline-block" name="reset" type="reset" value="Отмена" onclick="return window.parent.hs.close();">
                </td>
            </tr>
        </tfoot>
        
    </table>
</form>
<{/if}>
