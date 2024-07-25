<?php

namespace System;

class Security
{
    public static function array_xss_clean(&$array)
    {
        foreach ($array as $k => $v) {
            if (!str_contains($k, '__vs__')) {
                if (is_array($v)) self::array_xss_clean($array[$k]);
                else $array[$k] = self::xss_clean($v);
            }
        }
    }

    /**
     * Changed double quotes to single quotes, changed indenting and spacing
     * Removed magic_quotes stuff
     * Increased regex readability:
     * Used delimeters that aren't found in the pattern
     * Removed all unneeded escapes
     * Deleted U modifiers and swapped greediness where needed
     * Increased regex speed:
     * Made capturing parentheses non-capturing where possible
     * Removed parentheses where possible
     * Split up alternation alternatives
     * Made some quantifiers possessive
     * @param $str
     * @return array|string|string[]|null
     */
    public static function xss_clean($str)
    {
        // Fix &entity\n;
        $str = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $str);
        $str = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $str);
        $str = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $str);
        $str = html_entity_decode($str, ENT_COMPAT, CHARSET);

        // Remove any attribute starting with "on" or xmlns
        $str = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $str);

        // Remove javascript: and vbscript: protocols
        $str = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $str);
        $str = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $str);
        $str = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $str);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $str = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $str);
        $str = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $str);
        $str = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $str);

        // Remove namespaced elements (we do not need them)
        $str = preg_replace('#</*\w+:\w[^>]*+>#i', '', $str);

        do {
            // Remove really unwanted tags
            $old = $str;
            $str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);
        } while ($old !== $str);

        return $str;
    }

//    public static function check_csrf()
//    {
//        if (components::IsHttps() && !empty($_POST))
//        {
//            if (!empty($_SERVER['HTTP_REFERER']))
//            {
//                $ref = parse_url($_SERVER['HTTP_REFERER']);
//                $ref = $ref['host'];
//
//                if (preg_match('/(\.)?siberianhealth\.com$/', $ref, $matches) == 0 && strpos($_SERVER['HTTP_REFERER'],'payments.chronopay.com')===false)
//                {
//                    /**
//                     *@todo дописать это
//                     */
//                    // exit();
//                }
//            }
//        }
//    }
}