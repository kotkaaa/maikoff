<?php

/*
 * WebLife CMS
 * Created on 21.11.2018, 19:28:34
 * Developed by http://weblife.ua/
 */

/**
 * Description of MetaHelper
 *
 * @author user5
 */

abstract class MetaHelper {

    const EXP_COLOR       = "{color}";
    const EXP_COLORS      = "{colors}";
    const EXP_FILTER      = "{filter_%s}";
    const EXP_FILTER_CASE = "{filter_%s:%s}";
    const EXP_ATTR        = "{attribute_%s}";
    const EXP_ATTR_CASE   = "{attribute_%s:%s}";
    const EXP_ALT         = "\{([A-z_\d])+\|([A-zА-яЇїЄє\d\s])+\}";

    const CASE_SINGLE = "_single";
    const CASE_MULTI  = "_multi";
    const CASE_MALE   = "_male";
    const CASE_FEMALE = "_female";
    const CASE_EXTRA  = "_extra";

    protected static function replaceAltKeys($meta) {
        if (preg_match("/".self::EXP_ALT."/u", $meta, $matches)) {
            $arReplace = [];
            foreach ($matches as $substring) {
                $replace = "";
                $arr = explode("|", trim($substring, "{}"));
                if (!empty($arr)) $replace = end($arr);
                $arReplace[$substring] = $replace;
            } $meta = str_replace(array_keys($arReplace), array_values($arReplace), $meta);
        } return $meta;
    }

    protected static function clean($meta){
        $meta = preg_replace("/[{]+(.*?)[}]+/u", "", $meta);
        $meta = str_replace(["&nbsp;","&ensp;","&emsp;"], " ", $meta);
        $meta = preg_replace("/(\s){2,}/u", " ", $meta);
        return trim($meta);
    }
}

class CategoryMetaHelper extends MetaHelper {

    public static function generate($meta, array $filters = []) {
        if ($meta = unScreenData($meta)) {
            $meta = self::replaceFilterKeys($meta, $filters);
            $meta = parent::replaceAltKeys($meta);
        } return ucfirst(self::clean($meta));
    }

    protected static function clean($meta) {
        return parent::clean($meta);
    }

    private static function replaceKeys ($meta, $item) {
        return ProductMetaHelper::replaceKeys($meta, $item);
    }

    private static function replaceFilterKeys($meta, $filters = []) {
        if (!empty($filters["items"])) {
//            var_export($filters);
//            exit;
            // array of values for replace
            $arReplace = [];
            foreach ($filters["items"] as $filterID=>$filter) {
                // skip if empty children or not selected
                if (empty($filter["children"]) or $filter["selectedCount"]==0) continue;
                // walk through children values
                foreach ($filter["children"] as $childrenID=>$children) {
                    // for category filter (singular selection)
                    if ($filter["alias"]=="category") {
                        // walk through children categories
                        if ($children["selected_children"]) {
                            foreach ($children["subcategories"] as $catID=>$subcategory) {
                                if ($subcategory["selected"]) {
                                    $arReplace[sprintf(parent::EXP_FILTER, "$filterID(\|([A-zА-яЇїЄє\d\s])+)?")] = mb_strtolower($subcategory["title"]);
                                    break;
                                }
                            } break;
                        } elseif ($children["selected"]) {
                            $arReplace[sprintf(parent::EXP_FILTER, $filterID)] = mb_strtolower($children["title"]);
                            break;
                        }
                    }
                    // attribute|color filter (multiple selection)
                    elseif ($children["selected"]) {
                        // normal case
                        $case_key = sprintf(parent::EXP_FILTER, $filterID);
                        if (strpos($meta, $case_key)!==false) {
                            $arReplace[$case_key][] = unScreenData($children["title"]);
                        }
                        // different cases
                        $value = null;
                        if ($filter["aid"] > 0) {
                            $value = getSimpleItemRow($childrenID, ATTRIBUTES_VALUES_TABLE);
                        } elseif ($filter["alias"] == "color") {
                            $value = getSimpleItemRow($childrenID, COLORS_TABLE);
                        }
                        if (!$value) continue;
                        // single case
                        $case_key = sprintf(parent::EXP_FILTER_CASE, $filterID, parent::CASE_SINGLE);
                        if (strpos($meta, $case_key)!==false) {
                            $case_title = unScreenData(!empty($value["title".parent::CASE_SINGLE]) ? $value["title".parent::CASE_SINGLE] : $value["title"]);
                            $arReplace[$case_key][] = $case_title;
                        }
                        // multi case
                        $case_key = sprintf(parent::EXP_FILTER_CASE, $filterID, parent::CASE_MULTI);
                        if (strpos($meta, $case_key)!==false) {
                            $case_title = unScreenData(!empty($value["title".parent::CASE_MULTI]) ? $value["title".parent::CASE_MULTI] : $value["title"]);
                            $arReplace[$case_key][] = $case_title;
                        }
                        // male case
                        $case_key = sprintf(parent::EXP_FILTER_CASE, $filterID, parent::CASE_MALE);
                        if (strpos($meta, $case_key)!==false) {
                            $case_title = unScreenData(!empty($value["title".parent::CASE_MALE]) ? $value["title".parent::CASE_MALE] : $value["title"]);
                            $arReplace[$case_key][] = $case_title;
                        }
                        // female case
                        $case_key = sprintf(parent::EXP_FILTER_CASE, $filterID, parent::CASE_FEMALE);
                        if (strpos($meta, $case_key)!==false) {
                            $case_title = unScreenData(!empty($value["title".parent::CASE_FEMALE]) ? $value["title".parent::CASE_FEMALE] : $value["title"]);
                            $arReplace[$case_key][] = $case_title;
                        }
                        // extra case
                        $case_key = sprintf(parent::EXP_FILTER_CASE, $filterID, parent::CASE_EXTRA);
                        if (strpos($meta, $case_key)!==false) {
                            $case_title = unScreenData(!empty($value["title".parent::CASE_EXTRA]) ? $value["title".parent::CASE_EXTRA] : $value["title"]);
                            $arReplace[$case_key][] = $case_title;
                        }
                    }
                }
            }
            foreach ($arReplace as $key=>$values) {
                if (is_array($values)) $arReplace[$key] = implode(", ", $values);
            }
            $meta = str_replace(array_keys($arReplace), array_values($arReplace), $meta);
        } return $meta;
    }

    private static function replaceAttributeKeys($meta, $attributes = []) {
        //replace
        if (!empty($attributes)) {
            $arReplace = [];
            foreach ($attributes as $aid=>$attribute) {
                if (!empty($attribute["values"])) {
                    // replace in normal case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, $aid))!==false) {
                            array_push($replace, unScreenData($value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, $aid)] = mb_strtolower(implode(", ", $replace));
                    // replace in single case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_SINGLE))!==false) {
                            $case_title = "title" . parent::CASE_SINGLE;
                            array_push($replace, unScreenData(!empty($value[$case_title]) ? $value[$case_title] : $value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_SINGLE)] = mb_strtolower(implode(", ", $replace));
                    // replace in multi case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_MULTI))!==false) {
                            $case_title = "title" . parent::CASE_MULTI;
                            array_push($replace, unScreenData(!empty($value[$case_title]) ? $value[$case_title] : $value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_MULTI)] = mb_strtolower(implode(", ", $replace));
                    // replace in male case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_MALE))!==false) {
                            $case_title = "title" . parent::CASE_MALE;
                            array_push($replace, unScreenData(!empty($value[$case_title]) ? $value[$case_title] : $value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_MALE)] = mb_strtolower(implode(", ", $replace));
                    // replace in female case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_FEMALE))!==false) {
                            $case_title = "title" . parent::CASE_FEMALE;
                            array_push($replace, unScreenData(!empty($value[$case_title]) ? $value[$case_title] : $value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_FEMALE)] = mb_strtolower(implode(", ", $replace));
                    // replace in extra case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_EXTRA))!==false) {
                            $case_title = "title" . parent::CASE_EXTRA;
                            array_push($replace, unScreenData(!empty($value[$case_title]) ? $value[$case_title] : $value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_EXTRA)] = mb_strtolower(implode(", ", $replace));
                }
            } $meta = str_replace(array_keys($arReplace), array_values($arReplace), $meta);
        }
        //clear garbage
        return preg_replace("/".sprintf(parent::EXP_ATTR, "\d+")."/", "", $meta);
    }

    public static function compareFiltersHash (array $selectedFilters, $strict = false) {
        ksort($selectedFilters, SORT_NUMERIC);
        if ($strict) {

        } else {

        }
    }

    public static function shuffleFilters (array $selectedFilters = []) {
        if (!empty($selectedFilters)) {
            end($selectedFilters);
            $key = key($selectedFilters);
            $val = array_pop($selectedFilters);
            $pop = [$key => $val];
            $selectedFilters = $pop + $selectedFilters;
        } return $selectedFilters;
    }
};

class ProductMetaHelper extends MetaHelper {

    public static function generate($metaTemplate, $item) {
        $meta = '';
        if (($meta = unScreenData($metaTemplate))) {
            //prepare
            $meta = self::replaceKeys($meta, $item);
            //sizes
            $meta = self::replaceSizeKeys($meta, $item["sizes"]);
            // attributes
            $meta = self::replaceAttributeKeys($meta, $item["attributes"]);
        } return mb_ucfirst(self::clean($meta), WLCMS_SYSTEM_ENCODING);
    }

    public static function replaceKeys ($meta, $item) {
        $arReplace = [
            '{substrate}'     => '',
            '{substrate_l}'   => '',
            '{substrate_s}'   => '',
            '{substrate_sl}'  => '',
            '{substrate_p}'   => '',
            '{substrate_pl}'  => '',
            '{sizes}'    => '',
            '{title}'    => ((isset($item["title"]) and !empty($item["title"]))             ? unScreenData($item["title"])       : ''),
            '{price}'    => ((isset($item["price"]) and !empty($item["price"]))             ? 'цена '.number_format($item["price"], 0).' грн' : ''),
            '{brand}'    => ((isset($item["brand_title"]) and !empty($item["brand_title"])) ? unScreenData($item["brand_title"]) : ''),
            '{color}'    => ((isset($item["color_title"]) and !empty($item['color_title'])) ? unScreenData($item["color_title"]) : ''),
            '{series}'   => ((isset($item["series"]) and !empty($item['series']) and !empty($item['series']["title"])) ? unScreenData($item['series']["title"])     : ''),
            '{category}' => ((isset($item["arCategory"]) and !empty($item['arCategory']) and !empty($item['arCategory']['title'])) ? unScreenData($item['arCategory']['title']) : ''),
            '{pcode}'    => ((isset($item["pcode"]) and !empty($item['pcode'])) ? $item['pcode'] : ''),
        ];
        //type
        if (isset($item["substrate"]) and ($type = $item['substrate'])) {
            $arReplace['{substrate}']    = (!empty($type["title"])      ? unScreenData($type["title"])            : '');
            $arReplace['{substrate_l}']  = ($arReplace['{substrate}']   ? mb_strtolower($arReplace['{substrate}'])     : '');
            $arReplace['{substrate_s}']  = (!empty($type['title_s'])    ? unScreenData($type["title_s"])        : '');
            $arReplace['{substrate_sl}'] = ($arReplace['{substrate_s}'] ? mb_strtolower($arReplace['{substrate_s}']) : '');
            $arReplace['{substrate_p}']  = (!empty($type['title_p'])    ? unScreenData($type["title_p"])        : '');
            $arReplace['{substrate_pl}'] = ($arReplace['{substrate_p}'] ? mb_strtolower($arReplace['{substrate_p}']) : '');
        } return str_replace(array_keys($arReplace), array_values($arReplace), $meta);
    }

    protected static function clean($meta){
        return parent::clean($meta);
    }

    private static function replaceSizeKeys($meta, $sizes = []) {
        if (!empty($sizes)) {
            $arReplace = $replace = [];
            foreach($sizes as $size) {
                $replace[] = $size['title'];
            } $arReplace["{sizes}"] = implode(", ", $replace);
            $meta = str_replace(array_keys($arReplace), array_values($arReplace), $meta);
        } return str_replace("{sizes}", "", $meta);
    }

    private static function replaceAttributeKeys($meta, $attributes = []) {
        //replace
        if (!empty($attributes)) {
            $arReplace = [];
            foreach ($attributes as $aid=>$attribute) {
                if (!empty($attribute["values"])) {
                    // replace in normal case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, $aid))!==false) {
                            array_push($replace, unScreenData($value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, $aid)] = mb_strtolower(implode(", ", $replace));
                    // replace in single case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_SINGLE))!==false) {
                            $case_title = "title" . parent::CASE_SINGLE;
                            array_push($replace, unScreenData(!empty($value[$case_title]) ? $value[$case_title] : $value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_SINGLE)] = mb_strtolower(implode(", ", $replace));
                    // replace in multi case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_MULTI))!==false) {
                            $case_title = "title" . parent::CASE_MULTI;
                            array_push($replace, unScreenData(!empty($value[$case_title]) ? $value[$case_title] : $value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_MULTI)] = mb_strtolower(implode(", ", $replace));
                    // replace in male case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_MALE))!==false) {
                            $case_title = "title" . parent::CASE_MALE;
                            array_push($replace, unScreenData(!empty($value[$case_title]) ? $value[$case_title] : $value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_MALE)] = mb_strtolower(implode(", ", $replace));
                    // replace in female case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_FEMALE))!==false) {
                            $case_title = "title" . parent::CASE_FEMALE;
                            array_push($replace, unScreenData(!empty($value[$case_title]) ? $value[$case_title] : $value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_FEMALE)] = mb_strtolower(implode(", ", $replace));
                    // replace in extra case
                    $replace = [];
                    foreach ($attribute["values"] as $value) {
                        if (strpos($meta, sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_EXTRA))!==false) {
                            $case_title = "title" . parent::CASE_EXTRA;
                            array_push($replace, unScreenData(!empty($value[$case_title]) ? $value[$case_title] : $value["title"]));
                        }
                    } $arReplace[sprintf(parent::EXP_ATTR, "$aid:" . parent::CASE_EXTRA)] = mb_strtolower(implode(", ", $replace));
                }
            } $meta = str_replace(array_keys($arReplace), array_values($arReplace), $meta);
        }
        //clear garbage
        return preg_replace("/".sprintf(parent::EXP_ATTR, "\d+")."/", "", $meta);
    }
}