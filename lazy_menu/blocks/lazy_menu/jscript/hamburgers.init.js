/**
* ============================================
* File type: Conf
* File provides: Menu Toggle Animation
*
* @author:  Matteo Montanari <matteo@italinux.com>
*
* TERMS OF USE
* Open source under the MIT License.
*
* Permission is hereby granted, free of charge, to any person obtaining
* a copy of this software and associated documentation files (the "Software"),
* to deal in the Software without restriction, including without limitation
* the rights to use, copy, modify, merge, publish, distribute, sublicense,
* and/or sell copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included
* in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
* IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
* ============================================
* Hamburger Menu Toggle animation
* ============================================
*
*  Here’s the list of hamburger-type classes you can choose from:
*    hamburger--3dx
*    hamburger--3dx-r
*    hamburger--3dy
*    hamburger--3dy-r
*    hamburger--3dxy
*    hamburger--3dxy-r
*    hamburger--arrow
*    hamburger--arrow-r
*    hamburger--arrowalt
*    hamburger--arrowalt-r
*    hamburger--arrowturn
*    hamburger--arrowturn-r
*    hamburger--boring
*    hamburger--collapse
*    hamburger--collapse-r
*    hamburger--elastic
*    hamburger--elastic-r
*    hamburger--emphatic
*    hamburger--emphatic-r
*    hamburger--minus
*    hamburger--slider
*    hamburger--slider-r
*    hamburger--spin
*    hamburger--spin-r
*    hamburger--spring
*    hamburger--spring-r
*    hamburger--stand
*    hamburger--stand-r
*    hamburger--squeeze
*    hamburger--vortex
*    hamburger--vortex-r
*/

$(function() {

  $(".hamburger").on("click", function(e) {

    $(this).addClass('hamburger--squeeze');
    $(this).toggleClass("is-active");

    // display toggle menu (show|hide)
    $(this).closest('nav').find(".menu-wrapper").toggleClass("fixed");

    // Do something else, if you like
  });

});
