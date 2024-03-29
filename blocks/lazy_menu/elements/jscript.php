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
defined('C5_EXECUTE') or die("Access Denied.");
?>

<script type="text/javascript">

  $(document).ready(function(e) {

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Index Menu items
    var indexMenuItems = function(){
        $("div#items-wrapper > div[data-item]").each(function(i){

          $(this).find("input[data-id='sort-hidden']").val(i);
          $(this).find("[data-id='sort-show']").hide(600, function(){
              $(this).html(i+1);
          }).delay(200).fadeIn(600);

          $(this).attr("data-item", i);
        });
    };

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Generate Random ID for Page Selector
    // Math.random should be unique because of its seeding algorithm.
    // Convert it to base 36 (numbers + letters), and grab the first 9 characters after the decimal
    var genRandomID = function () {
        return '_' + Math.random().toString(36).substr(2, 9);
    };

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // init Menu
    var _init_Menu = function(){

      // Get wrapper
      var wrapper = $('div#items-wrapper');

      wrapper.sortable({
          handle: ".panel-heading",
          update: function(){
              indexMenuItems();
          }
      });

      // Get template
      var template = _.template($('script#item-template').html());

    <?php
      if ($itemsDefaultsAll) {
          foreach ($itemsDefaultsAll as $key => $value) {

             // HOT-FIX: PHPv8 Compatibility CHECK ARRAY KEY EXISTS
             // get Sort Order
             $value['sort'] = isset($value['sort']) ? $value['sort'] : $key;

             // HOT-FIX: PHPv8 Compatibility CHECK ARRAY KEY EXISTS
             $page = isset($value['pageID']) ? Page::getByID($value['pageID']) : null;

             // get Page Name for self
             $pageName = is_object($page) == true ? $page->getCollectionName() : null;

             // HOT-FIX: PHPv8 Compatibility CHECK ARRAY KEY EXISTS
             // get if the add-on is supposedly installed or not
             $isInstalled = (isset($value['addon']) && array_key_exists($value['addon'], $addonsAll)) ? ($addonsAll[$value['addon']]['installed'] === true ? 1 : 0) : 0;
          ?>
          wrapper.append(template({
              pageID: '<?php echo (isset($value['pageID']) ? $value['pageID'] : null)?>',
            pageName: '<?php echo $pageName?>',

              target: '<?php echo $value['target']?>',
                link: '<?php echo (isset($value['url']) ? $value['url'] : null)?>',
                name: '<?php echo t($value['name'])?>',

              anchor: '<?php echo $value['anchor']?>',
                hash: '<?php echo (isset($value['hash']) ? $value['hash'] : null)?>',

               addon: '<?php echo (isset($value['addon']) ? $value['addon'] : null)?>',
         isInstalled: <?php echo $isInstalled?>,

            uniqueID: genRandomID(),
                sort: '<?php echo $value['sort']?>'
          }));
          <?php
          }
      }
    ?>

      // Show All current menu items
      wrapper.children('div[data-item]').show(0);
    };

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // NOW init Menu
    _init_Menu();

     // SET default addon to display on a new menu item just added
     <?php
       $addonDefault = ($addonsAll == true ) ? reset(array_keys($addonsAll)) : 'banner';
     ?>

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Add menu item (new)
    $("div#add-item-wrapper").on('click', "input[data-id='btn-add-item']", function(e){
        e.preventDefault();

        // Get wrapper
        var wrapper = $('div#items-wrapper');

        // Get template
        var template = _.template($('script#item-template').html());

        // Get number of items
        var count = wrapper.children('div[data-item]').length;

        // Create new menu item (append)
        wrapper.append(template({

            pageID: '',
          pageName: '',

            target: 'self',
              link: '',
              name: '',

            anchor: 'hash',
              hash: '',

             addon: '<?php echo $addonDefault?>',
       isInstalled: false,

          uniqueID: genRandomID(),
              sort: (count)
        }));

        // add with transition
        wrapper.children('div[data-item]').last().slideDown(500);
    });

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Delete menu item
    $("div#items-wrapper").on('click', "[data-id='btn-delete-item']", function(e){
        e.preventDefault();
        var r = confirm("<?php echo t('Delete this Menu item')?>\n\n\t<?php echo t('Are you sure?')?>");

        var thisItem = $(this).closest('div[data-item]');

        if (r == true) {

            // delete item attribute
            thisItem.prop("data-item", null); // IE compatible
            thisItem.removeAttr("data-item");$

            // delete with transition
            thisItem.slideUp(500, function(){ 
                $(this).remove();
            });

            indexMenuItems();
        }
    });

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Select Target Window (self|blank)
    $("div#items-wrapper").on('change', "select[data-id='target-selector']", function(e){
        e.preventDefault();

        var thisItem = $(this).closest('div[data-item]');

        var thisSelf = thisItem.find("div[data-id='target-self']");
       var thisBlank = thisItem.find("div[data-id='target-blank']");

        switch ($(this).val()) {
        case 'self':
            thisSelf.show(); thisBlank.hide();
            break;
        case 'blank':
            thisSelf.hide(); thisBlank.show();
            break;
        }
    });

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Select Anchor (addon|hash|none)
    $("div#items-wrapper").on('change', "select[data-id='anchor-selector']", function(e){
        e.preventDefault();

        var thisItem = $(this).closest('div[data-item]');

           var addon = thisItem.find("div[data-id='anchor-addon']");
            var hash = thisItem.find("div[data-id='anchor-hash']");
            var none = thisItem.find("div[data-id='anchor-none']");

        var message = thisItem.find("div[data-id='anchor-message']");

        switch ($(this).val()) {
        case 'addon':
            addon.show(); hash.hide(); message.show();
            break;
        case 'hash':
            addon.hide(); hash.show(); message.hide();
            break;
        case 'none':
            addon.hide(); hash.hide(); message.hide();
            break;
        }
    });

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Display Anchor Message
    $("div#items-wrapper").on('change', "select[data-id='addon-selector']", function(e){
        e.preventDefault();

        var thisItem = $(this).closest('div[data-item]');

         var message = thisItem.find("div[data-id='anchor-message']");
              var ko = message.children('.addon-ko');
              var ok = message.children('.addon-ok');

        var isInstalled = $(this).find(':selected').attr('data-install');

        if (isInstalled == true) {
            ko.hide(); ok.show();
        } else {
            ko.show(); ok.hide();

            ko.find("a").attr("href", "http://matteo-montanari.com/addon-" + $(this).val());
            ko.find("u").html("addon " + $(this).val());
        }
    });
});
</script>
