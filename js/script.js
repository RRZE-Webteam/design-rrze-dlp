/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function($) {

    $('.hide-if-js').hide();

    $('.nav-button').show();
    
    $('.nav-button').click(function () {
        var trigger = $(this); 
        var content = $(this).next();
        content.slideToggle('300', function() {
            if(content.is(':hidden')) {
                trigger.text('≪ Menü ausklappen ≫');                
            } else {
                trigger.text('≪ Menü zuklappen ≫');                

            }

         });

    });

});