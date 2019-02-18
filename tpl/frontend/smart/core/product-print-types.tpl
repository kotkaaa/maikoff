<{if !empty($item.print_types) and is_array($item.print_types)}>
<label class="switch-toggle">Возможные виды печати на этой футболке</label>
<div class="kind-print switch-content">
    <ul>
<{foreach name=i from=$item.print_types item=type}>
        <li>
            <a href="<{include file="core/href_item.tpl" arCategory=$arrModules.print_types arItem=$type params=""}>" target="_blank" class="icon" <{if !empty($type.icon)}>style="background-image: url(<{$type.icon}>);"<{/if}>></a>
            <a href="<{include file="core/href_item.tpl" arCategory=$arrModules.print_types arItem=$type params=""}>" target="_blank"><{$type.title}></a>
        </li>
<{/foreach}>
    </ul>
</div>
<{/if}>