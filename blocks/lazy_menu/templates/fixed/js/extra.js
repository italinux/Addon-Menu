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
*  Fixed Menu
* ============================================
*/

$(function() {

  var thisNavbar = $("section.fixed.menu");

  var fadingClass = "with-fade";
  var fadingPosition = 200;

  var thisOffset = 0;

  /**
  * Configure Offset (if admin mode add some more offset)
  */
  if (thisNavbar.hasClass('loggedIn')) {
      thisOffset += 48;
  }

  /**
  * This is the actual position when menu starts fading (in|out)
  */
  fadingPosition += thisOffset;

  /**
  * Scrolling
  */
  $(window).scroll(function() {
      // fade (in|out)
      if ($(this).scrollTop() > fadingPosition) {
          thisNavbar.addClass(fadingClass);
      } else {
          thisNavbar.removeClass(fadingClass);
      }
  });

});
