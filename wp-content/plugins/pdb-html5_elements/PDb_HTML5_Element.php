<?php

/*
 * models a single form element
 *
 * @package    WordPress
 * @subpackage Participants Database Plugin
 * @author     Roland Barker <webdesign@xnau.com>
 * @copyright  2016  xnau webdesign
 * @license    GPL2
 * @version    0.8
 * @link       http://xnau.com/wordpress-plugins/
 * @depends    
 */

class PDb_HTML5_Element {

  /**
   * @vat object holds the instantiating object properties
   */
  private $field;

  /**
   * @var string the element yype
   */
  private $type;

  /**
   * @param object $field
   */
  private function __construct( $field, $type )
  {
    $this->field = $field;
    $this->type = $type;
  }

  /**
   * sets the form element html
   * 
   * @param object $field the field object
   * @param string  $type the form element type
   */
  public static function form_elemnt_html( $field, $type )
  {
    $element = new self( $field, $type );
    return $element->build_input_element();
  }

  /**
   * provides the value display html
   * 
   * @param object $field the field object
   * @param string  $type the form element type
   */
  public static function display_value( $field, $type )
  {
    $field = new self( $field, $type );
    return $field->_display_value();
  }

  /**
   * alters the field attribute for field we're not building the HTML for
   * 
   * @param object $field the field definition
   * 
   */
  public static function alter_field_attributes( $field )
  {
    $element = new self( $field, $field->type );
    $element->_alter_field_attributes();
  }

  /**
   * builds an input element
   * 
   */
  protected function build_input_element()
  {
    $html = '';
    $this->set_attributes();
    $params = array(
        'type' => 'text',
        'attributes' => $this->field->attributes,
        'class' => 'html5-element input-' . $this->type,
        'name' => $this->field->name,
        'value' => $this->field->value,
    );
    switch ( $this->type ) {
      case 'range':
        $html = $this->range_element( $params );
        break;
      case 'date5':
        $params['class'] .= ' date_field';
      default:
        $html = PDb_FormElement::get_element( $params );
    }
    return $html;
  }

  /**
   * 
   * @return string
   */
  function _display_value()
  {
    switch ( $this->type ) {
      case 'email':
        $return = '<span class="' . $this->type . '-element">' . PDb_FormElement::make_link( $this->field ) . '</span>';
        break;
      case 'color':
        $return = '<div class="' . $this->type . '-element" style="background:' . $this->field->value . '"><span style="mix-blend-mode: difference;display: block;text-align: center;padding: 0.5em 0;color:#FFF;">' . $this->field->value . '</span></div>';
        break;
      case 'tel':
        if ( Participants_Db::plugin_setting( 'make_links', true ) ) {
          $pattern = '<div class="%1$s-element" ><a href="tel:%2$s">%2$s</a></div>';
        } else {
          $pattern = '<div class="%1$s-element" >%2$s</div>';
        }
        $return = sprintf( $pattern, $this->type, $this->field->value );
        break;
      default:
        $return = '<span class="' . $this->type . '-element">' . $this->field->value . '</span>';
    }
    return $return;
  }

  /**
   * sets arbitrary tag attributes
   * 
   * any named option will get added to the tag attributes 
   * 
   * @param object $field
   * @param string $type
   */
  protected function set_attributes()
  {
    if ( !is_array( $this->field->attributes ) ) {
      $this->field->attributes = array();
    }
    $this->field->attributes += $this->options();
    $this->field->attributes['type'] = $this->input_type( $this->type );
    if ( isset( $this->field->attributes['required'] ) && ! apply_filters( 'pdb-html5_add_required_attribute', true )  ) {
      unset( $this->field->attributes['required'] );
    }
  }

  /**
   * alters the field attributes of the regular fields
   */
  private function _alter_field_attributes()
  {
    switch ( $this->field->type ) {
      case 'link':
        $this->field->attributes['type'] = 'url';
        break;
      case 'date':
        if ( apply_filters( 'pdb-html5_use_date_type', false ) ) {
          $this->field->attributes['type'] = 'date';
        }
        break;
    }
    $this->add_required();
  }

  /**
   * adds the "required" attribute to required fields
   * 
   */
  protected function add_required()
  {
    if ( apply_filters( 'pdb-html5_add_required_attribute', true ) && array_key_exists( $this->field->name, Participants_Db::$fields ) ) {
      $field_definition = Participants_Db::$fields[$this->field->name];
      if ( in_array( $field_definition->validation, array('yes', 'email-regex') ) ) {
        // skip all form elements where the required attribute won't work
        /**
         * @filter pdb-html5_dont_apply_required_att
         * @param array of form elements that should not have the "required" attribute set
         * @return array
         */
        if ( in_array( $field_definition->form_element, apply_filters( 'pdb-html5_dont_apply_required_att', array('hidden', 'placeholder', 'rich-text', 'text-area', 'multi-checkbox', 'multi-dropdown', 'multi-select-other', 'image-upload', 'file-upload') ) ) ) {
          return;
        }
        // filter out readonly fields
        if ( $field_definition->readonly != '0' ) {
          return;
        }
        $this->field->attributes['required'] = 'required';
      }
    }
  }

  /**
   * folds the field options into the attributes array
   * 
   * @return array the options array (could be empty)
   */
  private function options()
  {
    $skip_atts = array('type', 'value', 'name');
    $options = array();

    if ( is_array( $this->field->options ) ) {
      foreach ( $this->field->options as $name => $value ) {
        if ( is_int( $name ) )
          continue; // non-associative elements aren't added
        if ( in_array( $name, $skip_atts ) )
          continue; // skip these attributes
        $options[$name] = $value;
      }
    }
    return $options;
  }

  /**
   * takes the values parameter and builds it into the attributes array
   * 
   */
  function set_special_options()
  {
    switch ( $this->type ) {
      case 'number':
        if ( is_array( $this->field->options ) ) {
          list($min, $max) = $this->field->options;
          $this->field->attributes['min'] = $min;
          $this->field->attributes['max'] = $max;
        }
        break;
    }
  }

  /**
   * provides the HTML for a range element
   * 
   * @param array $params
   * 
   * @return string html
   */
  private function range_element( $params )
  {
    // replace hyphens to make a valid id
    if ( isset($params['attributes']['id']) ) {
      $id = str_replace( '-', '_', $params['attributes']['id'] );
    } else {
      $id = uniqid('r');
    }
    $params['attributes']['id'] = $id;
    /*
     * range control adds an output element to show the value
     */
    // set up the output element
    $output_id = str_replace( '-', '_', $id . '_output' );
    $output_tag = '<output name="' . $output_id . '" for="' . $id . '">' . $this->field->value . '</output>';
    // add the javascript to update the output
    $params['attributes']['oninput'] = $output_id . '.value = ' . $id . '.value';

    return '<div class="range-slider">' . sprintf( apply_filters( 'pdb-html5_range_element_structure', '%1$s%2$s' ), PDb_FormElement::get_element( $params ), $output_tag ) . '</div>';
  }

  /**
   * provides a html5 input type string
   * 
   * @param string $type
   * @return string
   */
  private function input_type( $type )
  {
    switch ( $type ) {
      case 'date5':
        $type = 'date';
        break;
    }
    return $type;
  }

}
