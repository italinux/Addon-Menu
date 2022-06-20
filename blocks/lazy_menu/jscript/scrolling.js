/**
* ============================================
* File type: Conf
* File provides: SCROLL ALL Menu Navigation 
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
*/

$(function() {

    if (CCM_EDIT_MODE === false) {

    /**
    * ============================================
    * NAVIGATION ITEMS: SCROLL to HASH TARGET on Click
    * ============================================
    */
        $("section.menu div.menu-wrapper .scroll").click(function(e) {

            e.preventDefault();

            var speedFactor = 2; // + o - speed (play with decimals first)

            // minimum animation duration in millisecs
            var animDurationMsecs = 400;

            // get handler from hash
            var thisHandler = this.hash;

            /**
            * Get handler Offset:
            *
            * 1. Scroll Top (zero offset) if # (Home)
            * 2. Scroll to anchor if matches: (i.e. #contacts)
            * 3. Do NOT Scroll anywhere if NO match
            */
            var thisHandlerOffset = (thisHandler === '') ? 0 : ($(thisHandler).length) ? $(thisHandler).first().offset().top : $(document).scrollTop();

            // Scroll + Animate (fadeOut at completion)
            $("html, body").animate({
                scrollTop: thisHandlerOffset
            }, {
               duration: animDurationMsecs,
               easing: "swing",
               complete: function(){
                 // close menu hamburger
                 $("nav").find(".hamburger").removeClass("is-active");

                 // hide toggle menu (fullscreen)
                 $("nav").find(".fixed").removeClass('animated').fadeOut(250, "swing", function() {
                     $(this).removeClass("fixed").addClass('animated');
                 });
               }
            });
        });
    /*
    * ============================================
    * NAVIGATION TITLE: APPEARING (FadeIn)
    * ============================================
    */
        var thisNavbar = $("section.menu");

        var showClass = "show";
        var showPosition = 400;

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
        showPosition += thisOffset;

        /**
        * Scrolling
        */
        $(window).scroll(function() {
            // fade (in|out)
            if ($(this).scrollTop() > showPosition) {
                thisNavbar.find('.nav-title').addClass(showClass);
            } else {
                thisNavbar.find('.nav-title').removeClass(showClass);
            }
        });
    /*
    * ============================================
    * NAVIGATION LOGO & TITLE: SCROLL-TOP on Click
    * ============================================
    */
        $("section.menu nav .scroll-top").click(function(e) {

            e.preventDefault();

            var speedFactor = 3.5; // + o - speed (play with decimals first)

            // minimum animation duration in millisecs
            var animDurationMsecsDefault = 500;

            // get current element offset
            var thisElementOffset = $(this).offset().top;

            // calculate speed (animation)
            var animDurationMsecs = Math.ceil(thisElementOffset / speedFactor);

            // final speed (animation)
            var thisAnimDurationMsecs = (animDurationMsecs < animDurationMsecsDefault) ? animDurationMsecsDefault : animDurationMsecs;

            // Scroll Animate
            $("html, body").animate({
                scrollTop: 0
            }, thisAnimDurationMsecs, "swing");
        });
    }
});
