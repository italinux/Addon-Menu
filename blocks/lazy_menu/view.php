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

echo $cStyle;
?>
<section id="<?php echo $sID?>" class="<?php echo $cTemplate?> pre-hand over-image <?php echo $cFgColorClass?> menu <?php echo $statusClass?>">
  <div class="container-fluid" id="<?php echo $viewPoint?>">

    <!-- Start Navigation -->
    <nav class="navbar">

      <!-- Start Header Navigation -->
      <div class="navbar-header">
        <!-- Toggle -->
        <div data-animation="toggle" class="navbar-toggle <?php echo $nopaque?>">
          <div class="hamburger" tabindex="0" aria-label="Menu" role="button" aria-controls="navigation">
            <div class="hamburger-box">
              <div class="hamburger-inner"></div>
            </div>
          </div>
        </div>
        <div class="navbar-left">
           <?php
             // Display Logo
             if ($showLogo == true) {
             ?>
              <!-- Logo -->
             <a data-animation="logo" class="<?php echo $toHome['class']?> nav-logo <?php echo $nopaque?>" href="<?php echo $toHome['path']?>">
               <img class="img-responsive" width="<?php echo $logo['width']?>" height="<?php echo $logo['height']?>" src="<?php echo $logo['path']?>" alt="" />
             </a>
             <?php
             }
           ?>
        </div>
        <!-- Menu items -->
        <div class="navbar-center">
           <?php
             // Display Title
             if (trim($title) == true) {
             ?>
              <!-- Title -->
             <div class="title-wrapper">
               <h5 data-animation="title" class="nav-title <?php echo $nopaque?>">
                 <a class="scroll-top" href="#"><?php echo h($title)?></a>
               </h5>
             </div>
             <?php
             }
           ?>
          <div data-animation="menu" class="menu-wrapper <?php echo $nopaque?>">
            <ul class="nav navbar-nav">
             <?php
               foreach ($navbarItems as $key => $item) {
               ?>
               <li class="<?php echo $item['li-class']?>">
                 <a class="<?php echo $item['a-class']?>" href="<?php echo $item['link']?><?php echo $item['anchor']?>" target="<?php echo $item['target']?>"><?php echo h($item['name'])?></a>
               </li>
               <?php
               }
             ?>
            </ul>
          </div>
        </div>
        <div class="navbar-right">
          <!-- Language Switch -->
          <div data-animation="languages" class="lang-switch <?php echo $nopaque?>" data-lang="<?php echo $locale?>">
           <?php
            // Display Language Switch
            $languageSwitch == true ? $languageSwitch->display() : null;
           ?>
          </div>
        </div>
      </div>
      <!-- End Header Navigation -->
    </nav>
    <!-- End Navigation -->

  </div>
</section>
