/**
 * Creates a new list item to add more links to the JKL Site Linker Page
 * 
 * @since   0.0.1
 * 
 * @package     JKL_Site_Linker
 * @subpackage  JKL_Site_Linker/js
 * @author      Aaron Snowberger <jekkilekki@gmail.com>
 * 
 * @param       object  $   A reference to the jQuery object
 * @return      object      A new element to be added to the webpage
 */
function addLink( $ ) {
    
    var $listElement, $inputElement, $textareaElement, $removeElement, listCount;
    // alert( 'in function' );
    /*
     * First, count the number of input fields that already exist.
     * This is how we set the name and ID attributes of the element.
     */
    listCount = $( '#jklsl-links-list' ).children().length;
    listCount++;
    // alert( 'building element' );
    // Next, create the actual input element and return it
    $listElement = 
            $( '<li></li>' )
            .attr( 'class', 'sortable' );
    $inputElement = 
            $( '<input>' )
            .attr( 'type', 'url' )
            .attr( 'name', 'jklsl-link-label[' + listCount + ']' )
            .attr( 'id', 'jklsl-link-' + listCount )
            .attr( 'class', 'jklsl-link large-text' )
            .attr( 'placeholder', 'http://link.com' )
            .attr( 'value', '' );
//    $textareaElement = 
//            $( '<textarea></textarea>' )
//            .attr( 'name', 'jklsl-link-description[' + listCount + ']' )
//            .attr( 'id', 'jklsl-link-description-' + listCount )
//            .attr( 'class', 'jklsl-link-description large-text' )
//            .attr( 'placeholder', 'Enter notes about the site here.' );
    $removeElement = 
            $( '<input>' )
            .attr( 'type', 'submit' )
            .attr( 'name', 'jklsl-link-label-' + listCount + '-remove' )
            .attr( 'id', 'jklsl-link-' + listCount + '-remove' )
            .attr( 'class', 'jklsl-remove-item button' )
            .attr( 'value', 'x' );
    // alert( 'returning element' );
    return $listElement.append( $inputElement/*.add( $textareaElement )*/.add( $removeElement ) );
    
} // END addLink($)

/**
 * The Actual jQuery function that handles all the button clicks on the JKL Site Linker Post Type
 * 
 * @since   0.0.1
 * 
 * @param   jQuery  $
 * @return  void
 */
( function( $ ) {
  
    'use strict';
    
    $( function() {
      
        /* Make links list sortable using jQuery UI Sortable */
        $( '#jklsl-links-list' ).sortable();
        
        /* ADD links to the list */
        $( '.jklsl-add-item' ).on( 'click', function( e ) {
            // alert( 'clicked' );
            e.preventDefault();
            
            if( $( '#jklsl-link-1-remove' ).hasClass( 'hidden' ) ) {
                $( '#jklsl-link-1-remove' ).removeClass( 'hidden' );
            }
            /**
             * Create the new list item element that will be used to capture all
             * the user input and append it to the container just above this button. 
             */
            $( this ).prev().append( addLink( $ ) );
            
        }); // END live function
        
        /* REMOVE links from list */
        $( '.jklsl-remove-item' ).live( 'click', function() {
            $( this ).parent( 'li' ).remove();
        });
        
    }); // END main function
    
}) ( jQuery );
