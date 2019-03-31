<?php
/**
.---------------------------------------------------------------------.
|  @package: Lazy Menu (a.k.a. add-on Menu)
|  @version: v1.0.9 (31 March 2019)
|  @link:    http://italinux.com/addon-menu
|  @docs:    http://italinux.com/addon-menu/docs
|
|  @author: Matteo Montanari <matteo@italinux.com>
|  @link:   http://matteo-montanari.com
'---------------------------------------------------------------------'
.---------------------------------------------------------------------------.
| @copyright (c) 2019                                                       |
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

<div class="<?php echo $btWrapperForm ?>">
  <section>
    <div>
      <div class="row main">
        <div class="col-lg-10 col-sm-9 col-xs-12">
          <div class="container-fluid">
            <div class="col-lg-4 col-lg-offset-0 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2">
              <div class="form-group center double-space-top no-space-bottom">
                <?php echo $form->label('o1_fID', t('Logo'))?>
                <div class="input-group center p90">
                  <?php echo $asset->image('ccm-b-image-o1_fID', 'o1_fID', t('Choose Image'), $o1_fID, array())?>
                </div>
              </div>
              <div class="form-group center double-space-top">
                <?php echo $form->label('showLogo', t('show logo?'))?>
                <div class="input-group center">
                  <div class="radio">
                    <label>
                      <?php echo $form->radio('showLogo', 1, (int) $showLogo)?>
                      <span><?php echo t('Yes')?></span>
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <?php echo $form->radio('showLogo', 0, (int) $showLogo)?>
                      <span><?php echo t('No')?></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5">
              <div class="form-group center double-space-top">
                <?php echo $form->label('title', t('Title: %s', '<span>(' . t('site name') . ')</span>'))?>
                <div class="input-group center p90">
                  <?php echo $form->text('title', $title, array('maxlength' => '50'))?>
                </div>
                <label class="single-space-top">
                  <span class="nota-bene"><?php echo t('%1$sNB:%2$s ', '<strong>','</strong>') . t('Title displays only on mobile view')?></span>
                </label>
              </div>
            </div>
            <div class="col-lg-3">
              <div class="form-group center double-space-top">
                <?php echo $form->label('showLanguage', t('show language switch?'))?>
                <div class="input-group center">
                  <div class="radio">
                    <label>
                      <?php echo $form->radio('showLanguage', 1, (int) $showLanguage)?>
                      <span><?php echo t('Yes')?></span>
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <?php echo $form->radio('showLanguage', 0, (int) $showLanguage)?>
                      <span><?php echo t('No')?></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="wrapper">

            <div class="double-space-bottom info-items-list-title">
              <h4><?php echo t('all your menu items are listed below')?></h4>
            </div>

            <!-- START SCRIPT -->
            <div id="items-wrapper">
              <!-- DYNAMIC ITEMS WILL GET LOADED INTO HERE -->
            </div>
            <div id="add-item-wrapper" class="single-space-top double-space-bottom text-center">
              <input type="button" data-id="btn-add-item" class="btn btn-success" value="<?php echo t('Add menu item')?>" />
            </div>

            <!-- THE TEMPLATE WE'LL USE FOR EACH ITEM -->
            <script type="text/template" id="item-template">
              <div class="item panel panel-default" data-item="<%=parseInt(sort)%>">

                <div class="panel-heading" style="cursor: move;">
                  <div class="row">
                    <div class="col-lg-6">
                      <h5><i class="fa fa-arrows drag-handle"></i> <?php echo t('Menu item')?> <span data-id="sort-show"><%=parseInt(sort)+1%></span></h5>
                      <input type="hidden" name="sort[]" data-id="sort-hidden" value="<%=sort%>" />
                    </div>
                    <div class="col-lg-6 text-right">
                      <input type="button" data-id="btn-delete-item" class="btn btn-danger" value="<?php echo t('Delete')?>" />
                    </div>
                  </div>
                </div>

                <div class="panel-body">
                  <div class="row item_1">
                    <div class="col-lg-3 col-lg-offset-0 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2">
                      <div class="form-group center">
                        <label class="control-label"><?php echo t('Name')?></label>
                        <div class="input-group center p90">
                          <input type="text" name="name[]" class="form-control" value="<%=name%>" maxlength="50" placeholder="<?php echo t('Nome')?>" />
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-lg-offset-0 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">
                      <div class="form-group light-title center">
                        <label class="control-label"><?php echo t('Target of link')?></label>
                        <div class="input-group center">
                          <select name="target[]" data-id="target-selector" class="form-control">
                            <option value="self" <%= target=='self' ? 'selected="selected"' : '' %>><?php echo t('Same window')?></option>
                            <option value="blank" <%= target=='blank' ? 'selected="selected"' : '' %>><?php echo t('New window')?></option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 col-lg-offset-0 col-sm-12 col-sm-offset-0">
                      <div data-id="target-self" <%= target=='self' ? '' : 'style="display: none"' %>>

                        <!-- START PAGE INPUT -->
                        <div class="form-group light-title align-center">
                          <label class="control-label"><?php echo t('Select a Page')?>
                            <br />
                            <span class="nota-bene"><?php echo t('%1$sNB:%2$s ', '<strong>','</strong>') . t('You can leave this blank if you want to link to this same page')?></span>
                          </label>
                          <div class="input-group center">

                            <!-- INCLUDE PAGE SELECTOR -->
                              <?php $this->inc('elements/page_selector.php')?>
                            <!-- END PAGE SELECTOR -->

                          </div>
                        </div>
                        <!-- END PAGE INPUT -->
                      </div>

                      <div data-id="target-blank" <%= target=='blank' ? '' : 'style="display: none"' %>>
                        <div class="form-group light-title">
                          <label class="control-label" style="text-align:left; padding-left:30px"><?php echo t('Link to a web page')?></label>
                          <div class="input-group center">
                            <input type="text" name="url[]" class="form-control" value="<%=link%>" maxlength="255" placeholder="http://my-url.ext" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row item_2">
                    <div class="col-lg-3 col-lg-offset-3 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">
                      <div class="form-group light-title center">
                        <label class="control-label"><?php echo t('Autoscroll to')?></label>
                        <div class="input-group center">
                          <select name="anchor[]" data-id="anchor-selector" class="form-control">
                            <option value="hash" <%= anchor=='hash' ? 'selected="selected"' : '' %>><?php echo t('Custom anchor')?></option>
                            <option value="addon" <%= anchor=='addon' ? 'selected="selected"' : '' %>><?php echo t('Dedicated Add-on')?></option>
                            <option value="none" <%= anchor=='none' ? 'selected="selected"' : '' %>><?php echo t('NO auto scrolling')?></option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-3 col-lg-offset-0 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3"> 
                      <div data-id="anchor-addon" <%= anchor=='addon' ? '' : 'style="display: none"' %>>
                        <div class="form-group light-title">
                          <label class="control-label"><?php echo t('List of Add-ons')?></label>
                          <div class="input-group center">
                            <select name="addon[]" data-id="addon-selector" class="form-control">
                            <?php
                              if ($addonsAll) {
                                  foreach ($addonsAll as $key => $value) {
                                  ?>
                                <option value="<?php echo $key?>" <%= addon=='<?php echo $key?>' ? 'selected="selected"' : '' %> data-install="<?php echo $value['installed']?>"><?php echo $value['name']?></option>
                                  <?php
                                  }
                              }
                            ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div data-id="anchor-hash" <%= anchor=='hash' ? '' : 'style="display: none"' %>>
                        <div class="form-group light-title">
                          <label class="control-label"><?php echo t('Custom anchor')?>
                            <sup class="tooltip info">
                              <div>
                                <dl>
                                  <dt><?php echo t('Here you define:')?></dt>
                                  <dd><?php echo t('an %1$s anchor %2$s to scroll to a %1$s section %2$s of a page', '<strong>', '</strong>')?></dd>
                                </dl>

                                <?php echo t('documentation:')?>
                                <a class="goto" href="http://italinux.com/howto/themes/docs/links#tip2" target="_blank"><span><?php echo t('click here')?></span></a>
                              </div>
                            </sup>
                          </label>
                          <div class="input-group center">
                            <input type="text" name="hash[]" class="form-control" value="<%=hash%>" maxlength="80" placeholder="#<?php echo t('anchor-name')?>" />
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 no-sides-paddings">
                      <div data-id="anchor-message" class="little-space-top little-space-bottom" <%= anchor=='addon' ? '' : 'style="display: none"' %>>
                        <div class="form-group center light-title no-margins addon-ko" <%= isInstalled==false ? 'style="display: block"' : '' %>>
                          <label class="control-label"><?php echo t('if you have not installed it yet')?></label>
                          <p><span><?php echo t('visit:')?></span> <a href="http://matteo-montanari.com/addon-<%=addon%>" class="goto" target="_blank"><u>addon <%=addon%></u></a></p>
                        </div>
                        <div class="form-group center light-title single-space-top addon-ok" <%= isInstalled==true ? 'style="display: block"' : '' %>>
                          <label class="control-label"><?php /* echo t('OK good') */?></label>
                        </div>
                      </div>
                   </div>
                </div>
              </div>
            </script>
            <!-- END THE TEMPLATE WE'LL USE FOR EACH ITEM -->

            <!-- START JS SCRIPT -->
              <?php $this->inc('elements/jscript.php')?>
            <!-- END JS SCRIPT -->
            
          </div>
        </div>
        <div class="col-lg-2 col-sm-3 col-xs-12">
          <section class="style">
            <div>
              <div class="row main">
                <div class="title">
                  <?php echo t('Customise Style')?>
                </div>
                <div class="col-lg-12">
                  <div class="form-group center light-title single-space-top no-sides-paddings single-space-bottom">
                    <?php echo $form->label('bgColorRGBA', t('background colour %s', '<br /><span>(' . t('with or without transparency') . ')</span>'))?>
                    <div class="input-group">
                      <!-- Show a Color Palette in RGB Color Format with Transparency Slider (RGBA) -->
                      <?php $color->output('bgColorRGBA', $bgColorRGBA, $bgColorPalette)?>
                    </div>
                  </div>
                  <div class="form-group center light-title no-margins no-sides-paddings single-space-bottom">
                    <?php echo $form->label('bgColorOpacity', t('adjust top opacity'))?>
                    <div class="input-group">
                      <!-- Adjust Background Color (top) Opacity: Over Image -->
                      <?php
                        if (is_array($bgColorOpacityOptions)) {
                          foreach ($bgColorOpacityOptions as $key => $value) {
                        ?>
                      <div class="col-xs-3 no-paddings">
                        <div class="radio">
                          <span style="color:#333"><?php echo t($key)?></span>
                          <br />
                          <label>
                            <?php echo $form->radio('bgColorOpacity', $value, (float) $bgColorOpacity)?>
                          </label>
                        </div>
                      </div>
                      <?php
                          }
                        }
                        ?>
                    </div>
                  </div>
                </div>

                <div class="col-lg-12">
                  <div class="form-group center light-title single-space-top single-space-bottom">
                    <?php echo $form->label('bgFID', t('background image'))?>
                    <div class="input-group">
                      <?php echo $asset->image('ccm-b-image-bgFID', 'bgFID', t('Choose Image'), $bgFID, array())?>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="form-group center light-title single-space-bottom">
                    <?php echo $form->label('fgColorRGB', t('font colour'))?>
                    <div class="input-group">
                      <!-- Show a Color Palette in RGB Color Format -->
                      <?php $color->output('fgColorRGB', $fgColorRGB, $fgColorPalette)?>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="form-group center light-title single-space-bottom">
                    <?php echo $form->label('isAnimated', t('with Animation'))?>
                    <div class="input-group">
                      <div class="radio col-sm-6 col-sm-offset-0 col-xs-3 col-xs-offset-3">
                        <label>
                          <?php echo $form->radio('isAnimated', 1, (int) $isAnimated)?>
                          <span class"on"><?php echo t('Yes')?></span>
                        </label>
                      </div>
                      <div class="radio col-sm-6 col-xs-3">
                        <label>
                          <?php echo $form->radio('isAnimated', 0, (int) $isAnimated)?>
                          <span class="off"><?php echo t('No')?></span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

        </div>
      </div>
    </div>
  </section>
</div>
