<?php
/**
.---------------------------------------------------------------------.
|  @package: Lazy Menu (a.k.a. add-on Menu)
|  @version: Latest on Github
|  @link:    http://italinux.com/addon-menu
|  @docs:    http://italinux.com/addon-menu/docs
|
|  @author: Matteo Montanari <matteo@italinux.com>
|  @link:   https://matteo-montanari.com
'---------------------------------------------------------------------'
.---------------------------------------------------------------------------.
| @copyright (c) current year                                               |
| ------------------------------------------------------------------------- |
| @license: Concrete5.org Marketplace Commercial Add-Ons & Themes License   |
|           https://concrete5.org/help/legal/commercial_add-on_license      |
|           or just: file://lazy_menu/LICENSE.TXT                           |
|                                                                           |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
'---------------------------------------------------------------------------'
*/
namespace Concrete\Package\LazyMenu\Block\LazyMenu;

use Concrete\Package\LazyMenu\Block\LazyMenu\Src\Utils as BlockUtils;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\Localization\Localization;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController
{

    protected $btTable = "btLazyMenu";
    protected $btExportTables = array('btLazyMenu', 'btLazyMenuItems');
 
    protected static $btHandlerId = "menu";
    protected $btDefaultSet = 'lazy';

    // Custom Image Thumb Width X Height (pixels)
    protected static $btCustomImageThumbWidth = 250;
    protected static $btCustomImageThumbHeight = 50;

    // Style Background & Foreground Colours
    protected static $btStyleOpacity = '0.5';

    // Style Upload Background Image size in KBytes (1KB = 1024b)
    protected static $btStyleUploadImageSize = 450;

    // Style Background Image size: Width X Height (pixels)
    protected static $btStyleUploadThumbWidth = 1190;
    protected static $btStyleUploadThumbHeight = 650;

    // Style Background Over Image default Opacity
    protected static $bgOverImageOpacity = 1;

    // Window Overlay size: Width X Height (pixels)
    protected $btInterfaceWidth = "1500";
    protected $btInterfaceHeight = "900";

    protected $btWrapperClass = 'ccm-ui';
    protected $btWrapperForm = 'lazy-ui';
    
    // Support for Inline Editing
    protected $btSupportsInlineEdit = false;
    protected $btSupportsInlineAdd = false;

    // Bootstrap theme Grid Support
    protected $btIgnorePageThemeGridFrameworkContainer = false;

    // Cache block's database calls
    protected $btCacheBlockRecord = true;

    // Cache block's actual view output
    protected $btCacheBlockOutput = false;

    // Serve cached version even if the result of a post request
    protected $btCacheBlockOutputOnPost = true;

    // Server cached version even if user is logged in
    protected $btCacheBlockOutputForRegisteredUsers = true;

    /**
    * When block caching is enabled and output caching is enabled for a block,
    * this is the value in seconds before cache being refreshed. Default (0) is no limit.
    */
    protected $btCacheBlockOutputLifetime = 0;

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Block Fields: All Default Values in Window Overlay
    * @description Prefill Fields with Values
    * @return Mixed (string|boolean|integer)
    */
    protected static function getDefaultValue($id)
    {
        $o = array(
          'title' => null,
          'showLogo' => true,
          'showLanguage' => false,
        );

        return array_key_exists($id, $o) ? $o[$id] : false;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Retrieve Menu Items All
    */
    protected function getItemsDefaultsAll()
    {
        $cName  = 'item';
        $config = self::$btHandlerId . '.' . $cName;
        $dValue = array(
              0 => array(
                  'name' => t('Who I am'),
                'target' => 'blank',
                   'url' => 'http://italinux.com/addon-menu/docs',
                'anchor' => 'none',
              ),
          );

          $items = count($this->getItemsAll()) > 0 ? $this->getItemsAll() : BlockUtils::getDefaultValue($config, $dValue, $this->{$cName});

        return (array) $items;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Addons list for Hash options (Window Overlay)
    */
    protected function getAddonsAll()
    {
        return array(
                'banner' => array('name' => 'Banner',           'installed' => false),
             'what-i-do' => array('name' => 'What I do',        'installed' => false),
              'about-me' => array('name' => 'About Me',         'installed' => false),
                  'team' => array('name' => 'Team',             'installed' => false),
              'services' => array('name' => 'Services',         'installed' => false),
             'my-skills' => array('name' => 'My Skills',        'installed' => false),
      'curriculum-vitae' => array('name' => 'Curriculum Vitae', 'installed' => false),
             'portfolio' => array('name' => 'Portfolio',        'installed' => false),
                'prices' => array('name' => 'Prices',           'installed' => false),
          'testimonials' => array('name' => 'Testimonials',     'installed' => false),
               'clients' => array('name' => 'Clients',          'installed' => false),
              'contacts' => array('name' => 'Contacts',         'installed' => false),
          'social-media' => array('name' => 'Social Media',     'installed' => false),
                'footer' => array('name' => 'Footer',           'installed' => false),
               );
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Block Fields: (Styles)
    * @description Labels for additonal Fields
    * @return array
    */
    protected static function get_btStyles()
    {
        return array(
            'bgColorRGBA' => t('Background Colour'),
            'bgColorOpacity' => t('Adjust Background Opacity'),
            'bgFID' => t('Background Image'),
            'fgColorRGB' => t('Foreground Colour'),
            'isAnimated' => t('Animation / Transition'),
            'o1_fID' => t('1st %s',t('Custom image')),
            'showLogo' => t('Show logo'),
            'showLanguage' => t('Show language switch'),
        );
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Block Fields: (Main)
    * @description Labels & Validation
    * @return array
    */
    protected static function get_btFields()
    {
        return array(
            'title' => array(
                'label' => t('Title'),
            ),
        );
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Block Fields: (Items)
    * @description Labels & TableItems Fields
    * @return array
    */
    protected static function get_btTableItems()
    {
        return array(
            'name' => array(
                'label' => t('Name'),
                'encodeEntity' => true,
                'allowEmpty' => false,
            ),
            'target' => array(
                'label' => t('Target of link'),
                'allowEmpty' => false,
            ),
            'pageID' => array(
                'label' => t('Select a Page'),
            ),
            'url' => array(
                'label' => t('Menu item URL'),
            ),
            'anchor' => array(
                'label' => t('Autoscroll to'),
                'allowEmpty' => false,
            ),
            'hash' => array(
                'label' => t('Custom anchor'),
            ),
            'addon' => array(
                'label' => t('Dedicated Add-on'),
            ),
            'sort' => array(
                'label' => t('Sort Order'),
            ),
        );
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * This Block: Add Extra Values to Window Overlay
    */
    protected static function get_btFormExtraValues()
    {
        return array(
            'bgColorOpacityOptions' => array(
                'label' => t('Options adjust background opacity'),
            ),
            'addonsAll' => array(
                'label' => t('All recommended add-ons hash tags'),
            ),
            'itemsDefaultsAll' => array(
                'label' => t('All items information'),
            ),
        );
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Block info Methods
    */
    public function getBlockTypeName()
    {

        return t('Lazy Menu');
    }

    public function getBlockTypeDescription()
    {

        return t('Add Lazy Menu to your website');
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * This block Main Methods
    */
    public function getTitle()
    {
        $cName  = 'title';
        $config = self::$btHandlerId . '.' . $cName;
        $dValue = self::getDefaultValue($cName);

        return BlockUtils::getDefaultValue($config, $dValue, $this->{$cName});
    }

    public function getShowLogo()
    {
        $cName  = 'showLogo';
        $config = self::$btHandlerId . '.' . $cName;
        $dValue = self::getDefaultValue($cName);

        return filter_var(BlockUtils::getDefaultValue($config, $dValue, $this->{$cName}), FILTER_VALIDATE_BOOLEAN);
    }

    public function getO1_fID()
    {
        if ($this->o1_fID > 0) {
            $fObj = BlockUtils::getFileObject($this->o1_fID);
        }

        return (isset($fObj) && is_object($fObj)) ? $fObj : null;
    }

    public function getShowLanguage()
    {
        $cName  = 'showLanguage';
        $config = self::$btHandlerId . '.' . $cName;
        $dValue = self::getDefaultValue($cName);

        return filter_var(BlockUtils::getDefaultValue($config, $dValue, $this->{$cName}), FILTER_VALIDATE_BOOLEAN);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Other Methods
    */
    public function getLanguageSwitch()
    {
        $bObj = BlockType::getByHandle('switch_language');

        return Page::getCurrentPage()->addBlock($bObj, 'Language Switch', null);
    }

    protected function getLocale()
    {
        $ms = Section::getCurrentSection();

        $locale = is_object($ms) ? $ms->getLocale() : Config::get('concrete.locale');

        return strtolower(substr($locale, -2));
    }

    protected function getItemsAll()
    {
        $db = BlockUtils::getThisApp()->make('database')->connection();

        $items = $db->GetAll('SELECT * FROM ' . $this->btExportTables[1] . ' WHERE bID = ? ORDER BY sort', array($this->bID));

        return (array) $items;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * This Block: Insert / Duplicate / Delete Items Methods
    */
    protected function insertItems($bID, $rows, $args)
    {
        $db = BlockUtils::getThisApp()->make('database')->connection();

        for ($i=0; $i < $rows; $i++) {

            $fValues = $fFields = $fNames = array();

            // Create Values to insert in Table Item
            foreach (self::get_btTableItems() as $key => $value) {
                $fFields[] = '?';
                 $fNames[] = $key;

                // Make sure there's a dash if hash
                $dash = (($key == 'hash') && (trim($args[$key][$i]) == true) && (substr($args[$key][$i], 0, 1) != '#')) ? '#' : null;

                // Encode HTML Entities
                $fValues[] = (isset($value['encodeEntity']) && ($value['encodeEntity'] === true)) ? htmlspecialchars($args[$key][$i], ENT_QUOTES) : $dash.trim($args[$key][$i]);
            }

            // Merge values, ready to insert
            $all = array_merge(array($bID), $fValues);

            $db->executeQuery('INSERT INTO ' . $this->btExportTables[1] . ' (bID, ' . implode(",", $fNames) . ') values(?, ' . implode(",", $fFields) . ')', $all);
        }
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Get the Navbar Items
    *
    * @return array
    */
    protected function getNavbarItems()
    {
        $items = array();

        foreach ($this->getItemsAll() as $key => $value){

            // - - - - - - - - - - - - - - - - - - - - -
            // get Anchor (hash)
            switch ($value['anchor']) {
            case 'hash':
                $anchor = trim($value['hash']);
                break;
            case 'addon':
                $anchor = '#' . trim($value['addon']);
                break;
            case 'none':
                $anchor = null;
            }

            // - - - - - - - - - - - - - - - - - - - - -
            // get navbar items by target
            switch ($value['target']) {
            case 'self':
                $page = $value['pageID'] == true ? Page::getByID($value['pageID']) : null;

                // link for self
                $items[$key]['link'] = is_object($page) == true ? parse_url(BlockUtils::getThisApp()->make('helper/navigation')->getLinkToCollection($page), PHP_URL_PATH) : null;

                // a class for self
                $items[$key]['a-class'] = (is_object($page) == false && $anchor == true) ? 'scroll' : null;

                break;
            case 'blank':
                // link for blank
                $items[$key]['link'] = $value['url'];
            }

            // active
            $items[$key]['li-class'] = (parse_url(Page::getCurrentPage()->getCollectionLink(), PHP_URL_PATH) == strtok($items[$key]['link'], '#')) == true ? 'active' : null;

            // name
            $items[$key]['name'] = $value['name'];

            // target
            $items[$key]['target'] = '_' . $value['target'];
            $items[$key]['pageID'] = $value['pageID'];

            // anchor (hash)
            $items[$key]['anchor'] = $anchor;

            $items[$key] = array_map('trim', $items[$key]);
        }

        return $items;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Retrieve Logo uploaded otherwise default image
    *
    * @return array
    */
    protected function getLogoImage()
    {
        $logoThumb = $this->getLogoImageObject();

        return ($logoThumb == true ? array('path' => $logoThumb->src,
                                          'width' => $logoThumb->width,
                                         'height' => $logoThumb->height,
                                        'default' => false) : array('path' => $this->getBlockDefaultImageURL(),
                                                                   'width' => $this->getBlockDefaultImageWidth(),
                                                                  'height' => $this->getBlockDefaultImageHeight(),
                                                                 'default' => true));
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Retrieve Logo thumbnail 
    *
    * @return object
    */
    protected function getLogoImageObject()
    {
        // get logo image object
        $fID = $this->getO1_fID();

        // get logo thumbnail object
        return (is_object($fID) ? BlockUtils::getThisApp()->make('helper/image')->getThumbnail($fID, self::$btCustomImageThumbWidth, self::$btCustomImageThumbHeight, false) : null);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Retrieve Class + Path to HomePage
    *
    * @return array
    */
    protected function getToHome()
    {
        // get Current Path
        $cPath = Page::getCurrentPage()->getCollectionPath();

        // get Current Language
        $cLang = Localization::activeLanguage();

        return ($cPath == null || $cPath == '/' . $cLang) ? array('path' => '#', 'class' => 'scroll-top') : array('path' => '/', 'class' => null);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * This Block RegisterViewAssets
    */
    public function registerViewAssets($outputContent = '')
    {

        // Import JQuery JS framework
        $this->requireAsset('javascript', 'jquery');

        // Import Bootstrap CSS framework
        $this->requireAsset('css', 'bootstrap/*');

        // Import this Block view Assets (css|js)
        $this->requireAsset('jst.block.' . $this->getBlockAssetsHandle() . '-view.assets');

        /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
        * load assets if animation required:
        */
        if ($this->getIsAnimationEnabled() === true) {
            // Import Animations Assets CSS & JS
            $this->requireAsset('jst.animate.assets');

            // Import Animations CSS & JS Configuration
            $this->requireAsset('jst.animate.conf');
        }

        if ($this->getShowLanguage() === true) {
            // Import CSS Flags Image Paths
            $this->requireAsset('css', 'cst.flags');
        }

        // Import Logo & Title Scroll-top
        $this->requireAsset('javascript', 'jst.scroll-top');

        // Import CSS Toggle Menu
        $this->requireAsset('jst.hamburgers');

        // Import CSS Font-Awesome
        $this->requireAsset('css', 'font-awesome');
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * This Block View/Validate/Save Methods
    */
    public function view()
    {

        if ($this->getIsAnimationEnabled() === true) {
            // class which master animation at its best
            $this->set('nopaque', 'nopaque');
        } else {
            $this->set('nopaque', null);
        }

        if ($this->getShowLanguage() === true) {
            // Get Locale for Current Page (switch language flag)
            $this->set('locale', $this->getLocale());

            // Get Language Switch Block Type
            $this->set('languageSwitch', $this->getLanguageSwitch());
        }

        // Set style values
        $this->set('sID', $this->getSectionId());
        $this->set('viewPoint', self::getViewPointId());
        $this->set('cTemplate', $this->getCustomTemplateName());
        $this->set('cFgColorClass', $this->getCustomFgColorClassName());
        $this->set('statusClass', Page::getCurrentPage()->isEditMode() == true ? 'editMode' : (User::isLoggedIn() == true ? 'loggedIn' : null));

        if ($this->getShowLogo() === true) {
            // display logo
            $this->set('logo', $this->getLogoImage());
        }

        // Set Home link Class + Path to logo ('/' or #)
        $this->set('toHome', $this->getToHome());

        // Set main values: Navbar
        $this->set('navbarItems', $this->getNavbarItems());

        // Import Custom Css3 inline
        $this->set('cStyle', $this->getCustomStyle());
    }

    public function on_start()
    {
        $al = AssetList::getInstance();

        $pf = array(
            'position' => Asset::ASSET_POSITION_FOOTER,
            'minify' => true,
            'combine' => true
        );

        $ph = array(
            'position' => Asset::ASSET_POSITION_HEADER,
            'minify' => true,
            'combine' => true
        );

        $cf = array(
            'position' => Asset::ASSET_POSITION_FOOTER,
            'minify' => false,
            'combine' => false
        );

        // Register Assets this Block
        $al->register('css', 'cst.flags', 'blocks/' . $this->getBlockHandle() . '/style/flags.css', $ph, $this->getPackageHandle());

        // Register Assets this Block
        $al->register('javascript', 'jst.scroll-top', 'blocks/' . $this->getBlockHandle() . '/jscript/scroll-top.js', $pf,$this->getPackageHandle());

        // Register Assets this Block
        $al->register('javascript', 'hamburgers-init', 'blocks/' . $this->getBlockHandle() . '/jscript/hamburgers.init.js', $cf, $this->getPackageHandle());
        $al->register('css', 'hamburgers-style',  'blocks/' . $this->getBlockHandle() . '/style/hamburgers.min.css', $ph, $this->getPackageHandle());

        $al->registerGroup(
            'jst.hamburgers', array(
               array(
                   'javascript',
                   'hamburgers-init'
               ),
               array(
                   'css',
                   'hamburgers-style'
               ),
            )
        );

        /** - - - - - - - - - - - - - - - - - - - - - - - - -
         * Register JS / CSS Animate for this Block
         */
        $al->register('javascript', 'jt.jquery.waypoints', 'blocks/' . $this->getBlockHandle() . '/jscript/min/jquery.waypoints.min.js', $pf, $this->getPackageHandle());

        // Register Assets Animate
        $al->register('javascript', 'animate-lib', 'blocks/' . $this->getBlockHandle() . '/jscript/min/jquery.lazy.animate.min.js', $pf, $this->getPackageHandle());

        $al->register('css', 'style.animate', 'blocks/' . $this->getBlockHandle() . '/style/animate.min.css', $ph, $this->getPackageHandle());
        $al->register('css', 'style.animate.delay', 'blocks/' . $this->getBlockHandle() . '/style/animate.delay.min.css', $ph, $this->getPackageHandle());

        $al->registerGroup(
            'jst.animate.assets', array(
                array(
                    'javascript',
                    'jt.jquery.waypoints'
               ),
               array(
                   'javascript',
                   'animate-lib'
               ),
               array(
                   'css',
                   'style.animate'
               ),
               array(
                   'css',
                   'style.animate.delay'
               ),
            )
        );

        // Register Assets Animate Configuration
        $al->register('javascript', $this->getJSelectorId() . '.animate-conf', 'blocks/' . $this->getBlockHandle() . '/jscript/lazy-animate.conf.js', $cf, $this->getPackageHandle());
        $al->register('javascript-inline', $this->getJSelectorId() . '.animate-init',  '$("section#' . $this->getSectionId()  . '").lazyAnimate(' . $this->getSelectorBlock() . ');', $cf, $this->getPackageHandle());

        $al->registerGroup(
            'jst.animate.conf', array(
               array(
                   'javascript',
                   $this->getJSelectorId() . '.animate-conf'
               ),
               array(
                   'javascript-inline',
                   $this->getJSelectorId() . '.animate-init'
               ),
            )
        );
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Custom inline Style main
    * Stream
    */
    protected function getCustomStyle()
    {
        $o=null;

        if (BlockUtils::isValidColor($this->bgColorRGBA) ||
            BlockUtils::isValidImage($this->getBgFID()) ||
            BlockUtils::isValidColor($this->fgColorRGB) ||
            $this->isCustomOverImageOpacity($this->bgColorOpacity)) {

            ob_start();
            ?>

            <style>
              <?php
                if (BlockUtils::isValidColor($this->bgColorRGBA) ||
                    $this->isCustomOverImageOpacity($this->bgColorOpacity)) {
                ?>
                  section<?php echo $this->getStyleSelector()?>.over-image::before {
                    <?php
                      if (BlockUtils::isValidColor($this->bgColorRGBA)) { ?>
                          background-color: <?php echo $this->getOverImageBgColor()?> !important;
                    <?php } ?>
                    <?php
                      if ($this->isCustomOverImageOpacity($this->bgColorOpacity)) { ?>
                          opacity: <?php echo $this->bgColorOpacity?>;
                    <?php } ?>
                  }
              <?php } ?>

              <?php
                if (BlockUtils::isValidColor($this->bgColorRGBA)) {
                ?>
                  section<?php echo $this->getStyleSelector()?> nav div.menu-wrapper.fixed {
                      background-color: <?php echo $this->getOverImageBgColor()?> !important;
                  }
              <?php } ?>

              <?php
                if (BlockUtils::isValidImage($this->getBgFID()) ||
                    BlockUtils::isValidColor($this->fgColorRGB)) {
                ?>
                  section<?php echo $this->getStyleSelector()?>.over-image {
                    <?php
                      if (BlockUtils::isValidImage($this->getBgFID())) { ?>
                          background-image: url('<?php echo parse_url($this->getCustomStyleImagePath(), PHP_URL_PATH)?>') !important;
                    <?php } ?>

                    <?php
                      if (BlockUtils::isValidColor($this->fgColorRGB)) { ?>
                          color: <?php echo $this->fgColorRGB?> !important;
                    <?php } ?>
                  }
               <?php } ?>

               <?php
                 if (BlockUtils::isValidColor($this->fgColorRGB)) { ?>
                   section<?php echo $this->getStyleSelector()?> .hamburger .hamburger-inner,
                   section<?php echo $this->getStyleSelector()?> .hamburger .hamburger-inner::before,
                   section<?php echo $this->getStyleSelector()?> .hamburger .hamburger-inner::after {
                       background-color: <?php echo $this->fgColorRGB?> !important;
                       }
               <?php } ?>
            </style>

            <?php
            $o = ob_get_contents();

            ob_end_clean();

            $o = BlockUtils::getCustomStyleSanitised($o);
        }

        return $o;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Main Validate
    */ 
    public function validate($args)
    {
        $e = BlockUtils::getThisApp()->make('error');

        // Validate Table Item
        foreach (self::get_btTableItems() as $key => $value) {

            // Field is Empty
            if (isset($value['allowEmpty']) && ($value['allowEmpty'] === false)) {

                if (is_array($args[$key])) {
                    foreach (array_keys($args[$key]) as $k) {
                        if (empty($args[$key][$k])) {
                            $e->add(t('Cannot be empty: %s', $value['label'] . ' ' . ($k+1)));
                        }
                    }
                } else {
                    $e->add(t('Cannot be empty: %s', t('Menu')));
                    break 1;
                }
            }
        }

        // Validate Styles
        foreach (array_keys(self::get_btStyles()) as $key) {

            switch ($key) {
            case (lcfirst(substr($key, -3)) == 'fID'):

                if (!empty($args[$key])) {
                    $f = BlockUtils::getFileObject($args[$key]);

                    if (is_object($f)) {
                        switch ($f->getMimeType()) {
                        case 'image/jpeg':
                        case 'image/jpg':
                        case 'image/png':
                            break;
                        default:
                            $e->add(t('File type required: JPG or PNG'));
                            break;
                        }

                        switch (strtolower($f->getExtension())) {
                        case 'jpeg':
                        case 'jpg':
                        case 'png':
                            break;
                        default:
                            $e->add(t('File extension required: JPG or PNG'));
                            break;
                        }

                        $uploadImageSize = BlockUtils::getUploadImageSize(self::$btStyleUploadImageSize);

                        if ($f->getFullSize() > $uploadImageSize) {
                            $e->add(t('File size exceeded: Max %s', BlockUtils::getHumanReadUploadImageSize($uploadImageSize)));
                        }

                        if ($f->isError()) {
                            $e->add(t('File image is invalid'));
                        }
                    } else {
                        $e->add(t('File image is invalid'));
                    }
                }
                break;
            }
        }

        // Validate Fields
        foreach (self::get_btFields() as $value) {

            // Field is Empty
            if (isset($value['allowEmpty']) && ($value['allowEmpty'] === false)) {

                if (empty($args[$key])) {
                    $e->add(t('Cannot be empty: %s', $value['label']));
                }
            }
        }

        return $e;
    }

    public function duplicate($newBID) {

        $rows = array();

        parent::duplicate($newBID);

        $res = $this->getItemsAll();

        // Duplicate:
        // Convert array structure to use existing values on a new bID
        for ($i=0; $i < count($res); $i++) {
            if (is_array($res[$i])) {
                foreach ($res[$i] as $key => $value) {
                    $rows[$key][] = $value;
                }
            }
        }

        $args = count($rows) > 0 ? $rows : $args;

        $this->insertItems($newBID, count($res), $args);
    }

    public function delete()
    {
        $db = BlockUtils::getThisApp()->make('database')->connection();

        $db->delete($this->btExportTables[1], array('bID' => $this->bID));

        parent::delete();
    }
 
    public function save($args)
    {

        // Save entries in Menu & Menu Items Tables
        $db = BlockUtils::getThisApp()->make('database')->connection();

        $db->executeQuery('DELETE from ' . $this->btExportTables[1] . ' WHERE bID = ?', array($this->bID));

        $this->insertItems($this->bID, count((array)$args['sort']), $args);

        // Update custom Styles for all pages 
        foreach (array_keys(self::get_btStyles()) as $key) {

            switch ($key) {
            case (lcfirst(substr($key, -3)) == 'fID'):
                if (empty($args[$key])) {
                    $args[$key] = 0;
                }
                break;
            case 'bgColorRGBA':
            case 'fgColorRGB':
                if (empty($args[$key])) {
                    $args[$key] = 'transparent';
                }
                break;
            }
        }

        // Sanitize input for DB
        foreach (array_keys(self::get_btFields()) as $key) {

            // Prevent Default Value from setting
            if (empty($args[$key])) {
                $args[$key] = ' ';
            }
        }

        parent::save($args);
    }
 
    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Block Handlers Design Methods
    */
    protected static function getViewPointId()
    {
        return self::$btHandlerId;
    }

    protected function getSectionId()
    {
        return 's' . $this->bID;
    }

    protected function getStyleSelector()
    {
        return '#' . $this->getSectionId() . '.' . self::$btHandlerId;
    }

    protected function getJSelectorId()
    {
        return $this->getSectionId() . '.' . self::$btHandlerId;
    }
 
    protected function getSelectorBlock()
    {
        return str_replace('-', '_', self::$btHandlerId);
    }

    protected function getCustomTemplateName()
    {
        $tName = 'no_template';
        $block = $this->getBlockObject();

        if (is_object($block)) {
          $tName = ($block->getBlockFilename() == true ? $block->getBlockFilename() : $tName);
        } 

        return $tName;
    }

    protected function getCustomFgColorClassName()
    {
        return (BlockUtils::isValidColor($this->fgColorRGB) ? 'cfg-color' : null);
    }

    protected function getPackageHandle()
    {
        return $this->getBlockHandle();
    }

    protected function getBlockHandle()
    {
        return $this->btHandle;
    }

    protected function getBlockAssetsHandle()
    {
        return self::$btHandlerId;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Template & Block - Default & Background Images URLs
    *
    * @return image URL (for HTML src attribute)
    */
    protected function getBlockDefaultImageURL()
    {
        $image = $this->getDefaultImageFullSrc();

        return $image['url'];
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Template & Block - Default & Background Images PATHs
    *
    * @return image full Path (file system)
    */
    protected function getBlockDefaultImagePath()
    {
        $image = $this->getDefaultImageFullSrc();

        return $image['path'];
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Template & Block - Default Images Width & Height
    *
    * @return image full Path (file system)
    */
    protected function getBlockDefaultImageWidth()
    {
        $size = @getimagesize($this->getBlockDefaultImagePath());

        // get default image width
        return $size[0];
    }

    protected function getBlockDefaultImageHeight()
    {
        $size = @getimagesize($this->getBlockDefaultImagePath());

        // get default image height
        return $size[1];
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Template & Block - Paths & URLs
    *
    * @param true / false
    * @return image base Path and URL (Template / Block)
    */
    protected function getBlockImagesBaseURL($tPath=false)
    {
        return $this->getBlockAssetsURL() . ($tPath !== false ? $this->getBlockTemplateRelativePath() : null) . '/images';
    }

    protected function getBlockImagesBasePath($tPath=false)
    {
        return __DIR__ . ($tPath !== false ? $this->getBlockTemplateRelativePath() : null) . '/images';
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Template - Paths & URLs
    *
    * @return image base Path and URL for a Template
    */
    protected function getBlockImagesBaseTemplateURL()
    {
        return $this->getBlockImagesBaseURL(true);
    }

    protected function getBlockImagesBaseTemplatePath()
    {
        return $this->getBlockImagesBasePath(true);
    }

    /** Get Template relative Path */
    protected function getBlockTemplateRelativePath()
    {
        return ($this->getCustomTemplateName() == false ? null : '/templates/' . $this->getCustomTemplateName());
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * get Template or Block images Paths & URLs
    * IMPORTANT: it uses inheritance
    * Returns image from Template or Block folders
    *
    * @param default / background
    * @return array (path, url)
    */
    private function getDefaultImageFullSrc($file='default')
    {
        $image = null;
        $ext = array('png', 'jpg');

        // check if Template Image folder has images
        if (is_dir($this->getBlockImagesBaseTemplatePath())) {
            foreach ($ext as $value) {
                $imgPath = $this->getBlockImagesBaseTemplatePath() . '/' . $file . '.' . $value;

                if (is_file($imgPath)) {
                    $image = array('path' => $imgPath,
                                   'url' => $this->getBlockImagesBaseTemplateURL() . '/' . $file . '.' . $value);
                }
            }
        }

        // otherwise use images within Block
        if ($image == false) {
            foreach ($ext as $value) {
                $imgPath = $this->getBlockImagesBasePath() . '/' . $file . '.' . $value;

                if (is_file($imgPath)) {
                    $image = array('path' => $imgPath,
                                   'url' => $this->getBlockImagesBaseURL() . '/' . $file . '.' . $value);
                }
            }
        }

        return (array) $image;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Custom inline Style Methods
    * Background inline Styles Methods
    */
    public function getBgFID()
    {
        if ($this->bgFID > 0) {
            $fObj = BlockUtils::getFileObject($this->bgFID);
        }

        return (isset($fObj) && is_object($fObj)) ? $fObj : null;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Check if Animation is enabled (ideal for JS variables)
    */
    public function getIsAnimationEnabled()
    {        
        return ($this->getIsAnimated() === true && Page::getCurrentPage()->isEditMode() == false) ? true : false;
    }

    public function getIsEditMode()
    {
        return (Page::getCurrentPage()->isEditMode() == false) ? 0 : 1;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Custom Animation / Transition
    */
    public function getIsAnimated()
    {
        $cName  = 'isAnimated';
        $config = self::$btHandlerId . '.' . $cName;
        $dValue = true;

        return filter_var(BlockUtils::getDefaultValue($config, $dValue, $this->{$cName}), FILTER_VALIDATE_BOOLEAN);
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Background Opacity Over Image
    */
    public function getBgColorOpacity()
    {
        $cName  = 'bgColorOpacity';
        $config = self::$btHandlerId . '.' . $cName;
        $dValue = self::$bgOverImageOpacity;

        return BlockUtils::getDefaultValue($config, $dValue, $this->{$cName});
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Background Opacity Over Image Options
    */
    public function getBgColorOpacityOptions()
    {
        return array(
            '-75%' => 0.25,
            '-50%' => 0.5,
            '-25%' => 0.75,
            'default' => 1
        );
    }

    protected function getCustomStyleImagePath()
    {
        return BlockUtils::getThisApp()->make('helper/image')->getThumbnail($this->getBgFID(), self::$btStyleUploadThumbWidth, self::$btStyleUploadThumbHeight, false)->src;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Custom inline Style Methods
    */
    protected function isCustomOverImageOpacity($value)
    {
        return (($value == true) && ($this->getBgColorOpacity() != 1)) == true ? true : false;
    }

    protected function getOverImageBgColor()
    {
        return $this->bgColorRGBA;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Form Overlay (Edit/Add) Methods
    */
    public function getBlockAssetsURL()
    {
        $bt = BlockType::getByHandle($this->getBlockHandle());
        $bPath = BlockUtils::getThisApp()->make('helper/concrete/urls')->getBlockTypeAssetsURL($bt);

        return $bPath;
    }

    protected function addLocalAssets($path, $type = 'css')
    {
        $this->addHeaderItem(BlockUtils::getThisApp()->make('helper/html')->{$type}($this->getBlockAssetsURL() . $path));
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Add Form (Window Overlay) - default Values
    */
    protected function addFormDefaultValues()
    {
        // Retrieve defaults Values
        foreach (array_keys(self::get_btFields()) as $key) {
            if (method_exists($this, 'get' . ucfirst($key))) {
                $o = $this->{'get' . ucfirst($key)}();
                $o = is_array($o) ? $o : trim($o);
                $this->set($key, $o);
            }
        }
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Add Form (Window Overlay) - extra Values
    */
    protected function addFormExtraValues()
    {
        // Retrieve defaults Values
        foreach (array_keys(self::get_btStyles()) as $key) {
            if (method_exists($this, 'get' . ucfirst($key))) {
                $this->set($key, $this->{'get' . ucfirst($key)}());
            }
        }

        // Retrieve extra Values
        foreach (array_keys(self::get_btFormExtraValues()) as $key) {
            if (method_exists($this, 'get' . ucfirst($key))) {
                $this->set($key, $this->{'get' . ucfirst($key)}());
            }
        }
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Add Form (Window Overlay)
    */
    protected function add()
    {
        // Custom Styles Palettes
        $this->set('color', BlockUtils::getThisApp()->make('helper/form/color'));
        $this->set('asset', BlockUtils::getThisApp()->make('helper/concrete/asset_library'));

        $this->set('fgColorPalette', BlockUtils::getFgColorPalette(false, true));
        $this->set('bgColorPalette', BlockUtils::getBgColorPalette(true, true, self::$btStyleOpacity));

        $this->set('btWrapperForm', $this->btWrapperForm);

        // Add Assets Site-Map
        $this->requireAsset('core/sitemap');

        // Page Selector
        $this->set('pageSelector', BlockUtils::getThisApp()->make('helper/form/page_selector'));

        // Urls
        $this->set('hUrl', BlockUtils::getThisApp()->make('helper/concrete/urls'));

        $this->addFormDefaultValues();
        $this->addFormExtraValues();

        // Add Assets to Window Overlay
        $this->addLocalAssets('/css/tools/lazy-global-ui.css', 'css');
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Edit Form (Window Overlay)
    */
    protected function edit() 
    {
        $this->add();
    }
}
