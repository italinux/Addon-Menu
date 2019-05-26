<?php
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
    protected $btExportTables = array('btLazyMenu', 'btLazyMenuItem');
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
    protected function getMenuItemsDefaultsAll()
    {
        $cName  = 'item';
        $config = self::$btHandlerId . '.' . $cName;
        $dValue = array(
              0 => array(
                  'name' => t('Who I am'),
                'target' => 'self',
                'anchor' => 'none',
                 'addon' => 'about-me',
              ),
          );

          $items = count($this->getMenuItemsAll()) > 0 ? $this->getMenuItemsAll() : BlockUtils::getDefaultValue($config, $dValue, $this->{$cName});

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
               'clients' => array('name' => 'Clients',          'installed' => false),
              'contacts' => array('name' => 'Contacts',         'installed' => false),
          'social-media' => array('name' => 'Social Media',     'installed' => false),
                'footer' => array('name' => 'Footer',           'installed' => false),
               );
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * Block Fields: (Styles)
    * @description Labels for additonal Fields
    * @return Array
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
    * @return Array
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
    * @return Array
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
            'menuItemsDefaultsAll' => array(
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

    protected function getMenuItemsAll()
    {
        $db = BlockUtils::getThisApp()->make('database')->connection();

        $items = $db->GetAll('SELECT * FROM ' . $this->btTable . 'Item WHERE bID = ? ORDER BY sort', array($this->bID));

        return (array) $items;
    }

    /** - - - - - - - - - - - - - - - - - - - - - - - - - - -
    * This Block: Insert / Duplicate / Delete Items Methods
    */
    protected function insertMenuItems($bID, $rows, $args)
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

            $db->executeQuery('INSERT INTO ' . $this->btTable . 'Item (bID, ' . implode(",", $fNames) . ') values(?, ' . implode(",", $fFields) . ')', $all);
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

        foreach($this->getMenuItemsAll() as $key => $value){

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
                $items[$key]['link'] = is_object($page) == true ? BlockUtils::getThisApp()->make('helper/navigation')->getLinkToCollection($page) : null;

                // a class for self
                $items[$key]['a-class'] = (is_object($page) == false && $anchor == true) ? 'scroll' : null;

                break;
            case 'blank':
                // link for blank
                $items[$key]['link'] = $value['url'];
            }

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

        if ($this->getIsAnimated() === true && Page::getCurrentPage()->isEditMode() == false) {
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

        if ($this->getIsAnimated() === true && Page::getCurrentPage()->isEditMode() == false) {
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

        $pf = Array(
            'position' => Asset::ASSET_POSITION_FOOTER,
            'minify' => true,
            'combine' => true
        );

        $ph = Array(
            'position' => Asset::ASSET_POSITION_HEADER,
            'minify' => true,
            'combine' => true
        );

        $cf = Array(
            'position' => Asset::ASSET_POSITION_FOOTER,
            'minify' => false,
            'combine' => false
        );

        // Register Assets this Block
        $al->register('css', 'cst.flags', 'blocks/lazy_menu/style/flags.css', $ph, 'lazy_menu');

        // Register Assets this Block
        $al->register('javascript', 'jst.scroll-top', 'blocks/lazy_menu/jscript/scroll-top.js', $pf,'lazy_menu');

        // Register Assets this Block
        $al->register('javascript', 'hamburgers-init', 'blocks/lazy_menu/jscript/hamburgers.init.js', $cf, 'lazy_menu');
        $al->register('css', 'hamburgers-style',  'blocks/lazy_menu/style/hamburgers.min.css', $ph, 'lazy_menu');

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
        $al->register('javascript', 'jt.jquery.waypoints', 'blocks/lazy_menu/jscript/min/jquery.waypoints.min.js', $pf, 'lazy_menu');

        // Register Assets Animate
        $al->register('javascript', 'animate-lib', 'blocks/lazy_menu/jscript/min/jquery.lazy.animate.min.js', $pf, 'lazy_menu');

        $al->register('css', 'style.animate', 'blocks/lazy_menu/style/animate.min.css', $ph, 'lazy_menu');
        $al->register('css', 'style.animate.delay', 'blocks/lazy_menu/style/animate.delay.min.css', $ph, 'lazy_menu');

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
        $al->register('javascript', $this->getJSelectorId() . '.animate-conf', 'blocks/lazy_menu/jscript/lazy-animate.conf.js', $cf, 'lazy_menu');
        $al->register('javascript-inline', $this->getJSelectorId() . '.animate-init',  '$("section#' . $this->getSectionId()  . '").lazyAnimateInit();', $cf, 'lazy_menu');

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
