<{* REQUIRE VARS: $arCategory=array() [$params=mixed array or string] *}><{if !isset($params)}><{assign var='params' value=''}><{/if}><{$UrlWL->buildCategoryUrl($arCategory, $params)}>