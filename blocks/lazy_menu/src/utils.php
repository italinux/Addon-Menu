<?php
/**
.---------------------------------------------------------------------.
|  @package: Lazy Menu (a.k.a. Menu)
|  @version: v1.0.8 (10 November 2018)
|  @link:    http://italinux.com/addon-menu
|  @docs:    http://italinux.com/addon-menu/docs
|
|  @author: Matteo Montanari <matteo@italinux.com>
|  @link:   http://matteo-montanari.com
'---------------------------------------------------------------------'
.---------------------------------------------------------------------------.
| @copyright (c) 2018                                                       |
| ------------------------------------------------------------------------- |
| @license: Concrete5.org Marketplace Commercial Add-Ons & Themes License   |
|           http://concrete5.org/help/legal/commercial_add-on_license       |
|           or just: file://lazy_menu/LICENSE.TXT                           |
|                                                                           |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
'---------------------------------------------------------------------------'
*/
namespace Concrete\Package\LazyMenu\Block\LazyMenu\Src;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Page\Page;
use Concrete\Core\File\File;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\Support\Facade\Config;

class Utils {

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Get Facade Application
    */
    public static function getThisApp()
    {

        // retrieve Facade Application
        return Application::getFacadeApplication();
    }

    /**
    * Direct to Values: 1. property local / 2. app/config / 3. method local
    */
    public static function getDefaultValue($key, $value, $name=null)
    {

        if (isset($name)) {

            $o = $name;

        } else {

            $config = (current(explode(".", $key)) == 'concrete' ? null : 'app.') . $key;
            $o = Config::get(trim($config));

            $o = (is_bool($o) === true ? (int) $o : (empty($o) === true ? $value : $o));
        }

        return $o;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Page Methods
    */
    public static function getPageObject($value)
    {

        // retrieve page: Object
        return Page::getByID($value);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * File Methods
    */
    public static function getFileObject($value)
    {

        // retrieve file: Object
        return File::getByID($value);
    }

    public static function getFileSetObject($value)
    {

        // retrieve file set: Object
        return FileSet::getByID($value);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Image Upload maximum size
    */
    public static function getUploadImageSize($size)
    {

        return (int) ($size * 1024);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Human Readable image size
    */
    public static function getHumanReadUploadImageSize($size)
    {

        return self::getHumanReadableFileSize($size);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Human Readable file size
    */
    public static function getHumanReadableFileSize($size)
    {

        $units = explode(' ','B KB MB GB TB PB');

        for ($i = 0; $size > 1024; $i++) {
             $size /= 1024;
        }

        return round($size, 2) . $units[$i];
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * File Upload maximum size
    */
    public static function getUploadFileSize($size)
    {

        return (int) ($size * 1024);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Get Ordinal number from Cardinal number
    */
    public static function getOrdinalNumberShort($value)
    {

        switch ($value) {
        case 1:
            $ord='st';
            break;
        case 2:
            $ord='nd';
            break;
        case 3:
            $ord='rd';
            break;
        default:
            $ord='th';
        }

        return $value.$ord;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Custom inline Style Methods
    */
    public static function getCustomStyleSanitised($buffer)
    {

        $search = array(
            '/\}[^\S ]+/s',  /* strip whitespaces after closing tag, except space */
            '/[^\S ]+\{/s',  /* strip whitespaces before opening tag, except space */
            '/(\s)+/s'       /* shorten multiple whitespace sequences */
        );
        $replace = array(
            '}',
            '{',
            '\\1'
        );

        $o = preg_replace($search, $replace, $buffer);

        return $o;
    }

    protected static function getRGBColors($rgba)
    {

        preg_match('/\((.*?)\)/', $rgba, $match);

        if (substr_count($match[1], ',') > 2) {
            $match[1] = substr($match[1], 0, strrpos($match[1], ','));
        }

        return explode(',', $match[1]);
    }

    public static function isValidColor($value)
    {

        return (substr(trim($value), 0, 3) =='rgb' ? true : false);
    }

    public static function isValidImage($value)
    {

        return (is_object($value) ? true : false);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Check if is a valid url
    * @return boolean (true | false)
    */
    public static function getIsValidURL($uri)
    {

        return ((substr($uri, 0, 4) == 'http' && filter_var($uri, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) !== false) ? true : false);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Custom inline Style Config (window overlay)
    * Background Colours Palette
    */
    public static function getBgColorPalette($showAlpha = false, $showPalette = true, $opacity)
    {

        return array(
                'showAlpha' => $showAlpha,
                'showPalette' => $showPalette,
                'palette' => array(
                    // greens
                    array(
                      "rgba(0, 141, 6, $opacity)",
                      "rgba(0,  95, 4, $opacity)",
                      "rgba(0,  58, 3, $opacity)"
                    ),
                    // lightblues
                    array(
                      "rgba(0, 90, 141, $opacity)",
                      "rgba(0, 61,  95, $opacity)",
                      "rgba(0, 37,  58, $opacity)"
                    ),
                    // blues
                    array(
                      "rgba(0, 23, 142, $opacity)",
                      "rgba(0, 15,  95, $opacity)",
                      "rgba(0,  9,  58, $opacity)"
                    ),
                    // purples
                    array(
                      "rgba(61, 0, 142, $opacity)",
                      "rgba(41, 0,  95, $opacity)",
                      "rgba(25, 0,  58, $opacity)"
                    )
              )
        );
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Custom inline Style Config (window overlay)
    * Foreground Colours Palette
    */
    public static function getFgColorPalette($showAlpha = false, $showPalette = true)
    {

        return array(
                'showAlpha' => $showAlpha,
                'showPalette' => $showPalette,
                'palette' => array(
                    // blacks
                    array(
                      "rgb(0, 0, 0)",
                      "rgb(34, 34, 34)",
                      "rgb(85, 85, 85)"
                    ),
                    // greens
                    array(
                      "rgb(0, 141, 6)",
                      "rgb(0,  95, 4)",
                      "rgb(0,  58, 3)"
                    ),
                    // purples
                    array(
                      "rgb(61, 0, 142)",
                      "rgb(41, 0,  95)",
                      "rgb(25, 0,  58)"
                    ),
                    // whites
                    array(
                      "rgb(255, 255, 255)",
                      "rgb(238, 238, 238)",
                      "rgb(204, 204, 204)"
                    )
              )
        );
    }
}
