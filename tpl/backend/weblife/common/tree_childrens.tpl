<{* REQUIRE VARS: dependID=int $arrChildrens=array() $islist=[true|false]*}>

<{if isset($islist) && $islist}>
<ul>
<{section name=i loop=$arrChildrens}>
    <li class="<{if $dependID==$arrChildrens[i].id OR (empty($dependID) && $arrPageData.cid==$arrChildrens[i].id)}> active<{/if}>">
        <a href="<{$arrPageData.admin_url|cat:"&cid="|cat:$arrChildrens[i].id}>">
            &nbsp;<img src="/images/admin/treeview/folder-closed.png" /> 
            <{$arrChildrens[i].title}> 
            [<{if isset($arrChildrens[i].itemsCnt)}><{$arrChildrens[i].itemsCnt}><{else}><{count($arrChildrens[i].items)}><{/if}>] 
            <{if $arrChildrens[i].active==0}>(<{$smarty.const.HEAD_INACTIVE}>)<{/if}>
        </a>
<{if !empty($arrChildrens[i].items)}>
        <ul>
<{section name=j loop=$arrChildrens[i].items}>
            <li class="<{if $dependID==$arrChildrens[i].id OR (empty($dependID) && $arrPageData.cid==$arrChildrens[i].id)}> active<{/if}>">
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$arrChildrens[i].items[j].id}>">
                    <{$arrChildrens[i].items[j].title}> 
                </a>
            </li>
<{/section}>
        </ul>
<{/if}>
   
<{if !empty($arrChildrens[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/tree_childrens.tpl' dependID=$dependID arrChildrens=$arrChildrens[i].childrens islist=$islist}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
     </li>
<{/section}>
</ul>

<{else}>
    <{section name=i loop=$arrChildrens}>
         <option value="<{$arrChildrens[i].id}>"<{if $dependID==$arrChildrens[i].id OR (empty($dependID) && $arrPageData.cid==$arrChildrens[i].id)}>  selected<{/if}>><{$arrChildrens[i].margin}><{$arrChildrens[i].title}> &nbsp; [<{$smarty.const.HEAD_ITEMS}>: <{if isset($arrChildrens[i].itemsCnt)}><{$arrChildrens[i].itemsCnt}><{else}><{count($arrChildrens[i].items)}><{/if}>] &nbsp; <{if $arrChildrens[i].active==0}>( <{$smarty.const.HEAD_INACTIVE}> ) &nbsp; <{/if}></option>
<{if !empty($arrChildrens[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/tree_childrens.tpl' dependID=$dependID arrChildrens=$arrChildrens[i].childrens}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>    
    <{/section}>
<{/if}>