<?php
/**
.---------------------------------------------------------------------.
|  @package: Lazy Menu (a.k.a. add-on Menu)
|  @version: Latest on Github
|  @link:    http://italinux.com/addon-menu
|  @docs:    http://italinux.com/addon-menu/docs
|
|  @author: Matteo Montanari <matteo@italinux.com>
|  @link:   http://matteo-montanari.com
'---------------------------------------------------------------------'
.---------------------------------------------------------------------------.
| @copyright (c) 2020                                                       |
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
defined('C5_EXECUTE') or die("Access Denied.");
?>

  <div class="ccm-summary-selected-item" data-page-selector="<%=uniqueID%>">
    <div class="ccm-summary-selected-item-inner">
      <span class="ccm-summary-selected-item-label" style="white-space: pre"><%=pageName%></span>
    </div>

    <a href="<?php echo View::url('/tools')?>/required/sitemap_search_selector?cID=0"
       class="ccm-sitemap-select-page"
       data-page-selector-launch="<%=uniqueID%>"
       dialog-width="90%"
       dialog-height="70%"
       dialog-append-buttons="true"
       dialog-modal="false"
       dialog-title="Choose Page"
       dialog-on-close="Concrete.event.fire('fileselectorclose', '{$fieldName}');"><?php echo t('Browse')?></a>

    <a href="javascript:void(0)"
       class="ccm-sitemap-clear-selected-page"
       dialog-sender="<%=uniqueID%>"
       data-page-selector-clear="<%=uniqueID%>"
       style="margin-top: 0px; display: none">
        <img src="<?php echo $hUrl->getBlockTypeAssetsURL($bt)?>/images/remove.png" style="vertical-align: middle; margin-left: 3px">
    </a>

    <input type="hidden" name="pageID[]" data-page-selector="cID" value="<%=pageID%>">
  </div>

  <% print("<sc" + "ript type='text/javascript'>"); %>
      $(function() {

          var ccmActivePageField;
          var launcher = $('a[data-page-selector-launch="<%=uniqueID%>"]'), name = '<%=uniqueID%>', openEvent, openEvent2;
          var container = $('div[data-page-selector="' + name + '"]');
        
          if (container.find('span').text() != '') {
              container.find('.ccm-sitemap-clear-selected-page').show();
          }

          launcher.dialog();

          ConcreteEvent.bind('fileselectorclose', function(field_name) {
              ConcreteEvent.unbind('ConcreteSitemap.' + name);
              ConcreteEvent.unbind('SitemapSelectPage.' + name);
              ConcreteEvent.unbind('ConcreteSitemapPageSearch.' + name);

          });

          launcher.on('click', function () {
              var selector = $(this);

              var handle_select = function(e, data) {
                  ConcreteEvent.unbind(e);

                  var handle = selector.attr('data-page-selector-launch');

                  container.find('.ccm-summary-selected-item-label').html(data.title);
                  container.find('.ccm-sitemap-clear-selected-page').show();
                  container.find('input[data-page-selector=cID]').val(data.cID);

                  $.fn.dialog.closeTop();
              };
              ConcreteEvent.bind('ConcreteSitemap.' + name, function (event, sitemap) {
                  ConcreteEvent.subscribe('SitemapSelectPage.' + name, function (e, data) {
                      if (data.instance === sitemap) {
                          handle_select(e, data);
                      }
                  });
              });
              ConcreteEvent.bind('ConcreteSitemapPageSearch.' + name, function (event, search) {
                  ConcreteEvent.subscribe('SitemapSelectPage.' + name, function (e, data) {
                      if (data.instance === search) {
                          handle_select(e, data);
                      }
                  });
              });
          });

          $('a[data-page-selector-clear="<%=uniqueID%>"]').click(function () {
              var container = $('div[data-page-selector="<%=uniqueID%>"]');

              container.find('.ccm-summary-selected-item-label').html('');
              container.find('.ccm-sitemap-clear-selected-page').hide();
              container.find('input[data-page-selector=cID]').val('');
          });
      });
  <% print("</sc"+"ript>"); %>
