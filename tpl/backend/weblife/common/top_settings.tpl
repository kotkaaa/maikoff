<div class="top_settings">
    <{if $UserAccess->getAccessToModule('users')}>
        <a href="/admin.php?module=users"><{$smarty.const.ADMINISTRATORS}></a>
    <{/if}>
    <{if $UserAccess->getAccessToModule('customers')}>
        <a href="/admin.php?module=customers"><{$smarty.const.USERS}></a>
    <{/if}>
    <{if $UserAccess->getAccessToModule('settings')}>
        <a href="/admin.php?module=settings"><{$smarty.const.TOPLINK_SETTINGS}></a>
    <{/if}>
    <{if $UserAccess->getAccessToModule('cms_settings')}>
        <a href="/admin.php?module=cms_settings">CMS settings</a>
    <{/if}>
</div>