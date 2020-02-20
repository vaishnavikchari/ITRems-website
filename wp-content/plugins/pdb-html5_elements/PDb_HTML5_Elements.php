<?php

/*
 * adds several HTML5 elements to Participants Database
 *
 * @package    WordPress
 * @subpackage Participants Database Plugin
 * @author     Roland Barker <webdesign@xnau.com>
 * @copyright  2016  xnau webdesign
 * @license    GPL2
 * @version    0.4
 * @link       http://xnau.com/wordpress-plugins/
 * @depends    
 */

class PDb_HTML5_Elements extends PDb_Aux_Plugin {

  // plugin slug
  var $aux_plugin_name = 'pdb-html5_elements';
  // shortname for the plugin
  var $aux_plugin_shortname = 'pdbh5e';
  // plugin title
  var $aux_plugin_title = 'HTML5 Elements';

  /**
   * 
   */
  function __construct( $base_file )
  {
    $this->settings_API_status = false;
    parent::__construct( __CLASS__, $base_file );
    $this->set_filters();
  }

  /**
   * sets up the filters
   */
  private function set_filters()
  {
    // this filter places the form element HTML
    foreach ( $this->html5_element_list() as $type => $title ) {
      $self = & $this;
      add_action( 'pdb-form_element_build_' . $type, function ( $field ) use ( &$self, $type ) {
        if ( !is_admin() ) {
          $self->add_required( $field );
        }
        $field->output[] = PDb_HTML5_Element::form_elemnt_html( $field, $type );
      } );
    }

    // this filter provides the field value display
    add_filter( 'pdb-before_display_form_element', array($this, 'element_display_value'), 1, 2 );

    // adds the new types to the form element selector
    add_filter( 'pdb-set_form_element_types', array($this, 'add_html5_types') );

    /*
     * adds the "required" attribute to all input types if enabled
     * 
     * this can be prevented by using a filter, by default it is added, but not in the admin
     */
    if ( apply_filters( 'pdb-html5_add_required_attribute', is_admin() === false ) ) {
      /*
       * handle the other fields we will be altering the attributes for
       */
      foreach ( $this->other_element_list() as $type ) {
        add_action( 'pdb-form_element_build_' . $type, array('PDb_HTML5_Element', 'alter_field_attributes') );
      }
    }

    // this enables the extended date types
    if ( apply_filters( 'pdb-html5_extended_elements', false ) ) {
      add_filter( 'pdb-set_form_element_types', array($this, 'add_extended_elements'), 20 );
    }

    // provide the correct MYSQL datatype for the new elements
    add_action( 'pdb-form_element_datatype', array($this, 'set_datatype'), 10, 2 );

    // sets up ranged searches for HTML5 date field in Combo Multisearch
    add_filter( 'pdb-combo-multisearch-search_control_type', array( $this, 'stock_form_element' ) );

    // sets the add to query mode for the form element
    add_filter( 'pdbcms-add_to_query_mode', array( $this, 'equivalent_form_element' ), 10, 2 );
  }

  /**
   * provides the display string for an HTML5 element
   * 
   * @param string  $display  the display string (typically blank)
   * @param object $field the field object
   * 
   * @return string the display string
   */
  public function element_display_value( $display, $field )
  {
    if ( in_array( $field->form_element, array_keys( $this->html5_element_list() ) ) ) {
      $display = PDb_HTML5_Element::display_value( $field, $field->form_element );
    }
    return $display;
  }

  /**
   * sets up the new HTML5 elements list
   * 
   * @return array
   */
  private function html5_element_list()
  {
    /**
     * @version 0.1
     * @filter pdb-html5_element_list filters the full list of form elements
     * @filter pdb-html5_use_extended_elements bool if true, all the specialized date time types are added
     */
    return apply_filters(
            'pdb-html5_element_list', apply_filters( 'pdb-html5_use_extended_elements', false ) ?
            $this->extended_element_definitions( $this->element_definitions() ) : $this->element_definitions()
    );
  }

  /**
   * sets up the new HTML5 elements list
   * 
   * @return array
   */
  private function element_definitions()
  {
    return array(
        'tel' => __( 'Telephone', 'pdb-html5_elements' ),
        'url' => __( 'URL', 'pdb-html5_elements' ),
        // number is implemented as a standard field
        //'number' => __('Number Selector', 'pdb-html5_elements' ),
        'email' => __( 'Email', 'pdb-html5_elements' ),
        'color' => __( 'Color Picker', 'pdb-html5_elements' ),
        'range' => __( 'Range Slider', 'pdb-html5_elements' ),
        // HTML5 date field as a separate type so standard field can be used also
        'date5' => __( 'HTML5 Date', 'pdb-html5_elements' ),
    );
  }
  
  /**
   * provides the equivalent form element for adding to the query
   * 
   * @param string $equivalent
   * @param string $form_element
   * @return string the equivalent form element
   * 
   */
  public function equivalent_form_element( $equivalent, $form_element ) {
    
    switch ( $form_element ) {
      case 'date5':
        $equivalent = 'date';
        break;
      case 'range':
        $equivalent = 'numeric';
        break;
    }
    
    return $equivalent;
  }
  
  /**
   * provides the stock equivalent form element for html5 form elements
   * 
   * @param string $form_element
   * @return string
   */
  public function stock_form_element( $form_element )
  {
    switch ( $form_element ) {
      case 'date5':
        $form_element = 'date';
        break;
      case 'range':
        $form_element = 'numeric';
        break;
    }
    return $form_element;
  }

  /**
   * adds the extended date and time types
   * 
   * @param array $element_list the main html5 list
   * @return array with the date tim elements added
   */
  public function extended_element_definitions( $element_list )
  {
    return array_merge( $element_list, array(
        'time' => __( 'Time', 'pdb-html5_elements' ),
        'week' => __( 'Week', 'pdb-html5_elements' ),
        'month' => __( 'Month', 'pdb-html5_elements' ),
        'datetime-local' => __( 'Local Date & Time', 'pdb-html5_elements' ),
            ) );
  }

  /**
   * provides the datatype to use for each form_element type
   * 
   * @param string $datatype the datatype found by the parent method
   * @param string  $form_element the name of the form element
   * @return string $datatype
   */
  public function set_datatype( $datatype, $form_element )
  {
    switch ( $form_element ) {
      case 'tel':
      case 'url':
      case 'email':
      case 'color':
        $datatype = 'TINYTEXT';
        break;
      case 'range':
      case 'date5':
        $datatype = 'BIGINT';
        break;
    }
    return $datatype;
  }

  /**
   * list of standard fields that are getting altered
   * 
   * these are the fields that are getting their attributes altered, such as 
   * adding the "required" attribute
   * 
   * @return array
   */
  private function other_element_list()
  {
    return apply_filters( 'pdb-html5_standard_element_list', array(
        'text-line',
        'text-area',
        'checkbox',
        'radio',
        'dropdown',
        'date',
        'numeric',
        'dropdown-other',
        'multi-checkbox',
        'multi-dropdown',
        'select-other',
        'multi-select-other',
        'link',
        'image-upload',
        'file-upload',
            ) );
  }

  /**
   * alters the field attributes of the regular fields
   * 
   * @param object $field the field object
   */
  public function alter_field_attributes( $field )
  {
    $this->add_required( $field );
  }

  /**
   * 
   * @param array $elements
   * @return array with the new types added
   */
  function add_html5_types( $elements )
  {
    return array_merge( $elements, $this->html5_element_list() );
  }

  /**
   * adds the "required" attribute to required fields
   * 
   * @param object $field the feild definition
   * @return null we alter the object directly
   */
  public function add_required( $field )
  {
    $field_definition = Participants_Db::$fields[$field->name];
    if ( in_array( $field_definition->validation, array('yes', 'email-regex') ) ) {
      $field->attributes['required'] = 'required';
    }
  }

}
