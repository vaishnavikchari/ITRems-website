<?php
/*
 *
 * template for participants list shortcode output that includes a link to an editable record
 *
*/
//error_log('list object: '.print_r($this,1));
/* @var $this PDb_List */
/*
 * this is the name of the field where the link to the editable record is placed
 */
$edit_link_field = 'edit-link';
/*
 * if you want the edit links on this list to go to a different page than the global 
 * "Participant Record" page, put the name of that page here...otherwise, leave empty
 */
$record_edit_page = ''
?>
<div class="wrap <?php echo $this->wrap_class ?>">
<a name="<?php echo $this->list_anchor ?>" id="<?php echo $this->list_anchor ?>"></a>
<?php
  /*
   * SEARCH/SORT FORM
   *
   * the search/sort form is only presented when enabled in the shortcode.
   *
   */
  $this->show_search_sort_form();

  /* LIST DISPLAY */
?>
  <?php /*
   * IMPORTANT: the list container must have an id="pdb-list" in order for the
	 * AJAX-enabled search to work.
   */ ?>
  <table class="wp-list-table widefat fixed pages list-container" id="pdb-list" cellspacing="0" >

    <?php // print the count if enabled in the shortcode
		if ( $display_count ) : ?>
    <caption>
      Total Records Found: <?php echo $record_count ?>, showing <?php echo $records_per_page ?> per page
    </caption>
    <?php endif ?>

    <?php if ( $record_count > 0 ) : // print only if there are records to show ?>

      <thead>
        <tr>
          <?php /*
           * this function prints headers for all the fields
           * replacement codes:
           * %2$s is the form element type identifier
           * %1$s is the title of the field
           */
          $this->print_header_row( '<th class="%2$s" scope="col">%1$s</th>' );
          ?>
        </tr>
      </thead>

      <tbody>
      <?php while ( $this->have_records() ) : $this->the_record(); // each record is one row ?>
          <?php $field = new PDb_Template($this);?>
        <tr>
          <?php while( $this->have_fields() ) : $this->the_field(); // each field is one cell ?>

            <td class="<?php echo $this->field->name ?>-field">
              <?php 
              /**
               * set the link property to the edit link
               */
              if ( $this->field->name == $edit_link_field ) {
                $this->field->link = $field->get_edit_link( $record_edit_page );
              }
              $this->field->print_value();
              ?>
            </td>

        <?php endwhile; // each field ?>
        </tr>
      <?php endwhile; // each record ?>
      </tbody>

    <?php else : // if there are no records ?>

      <tbody>
        <tr>
          <td>No Records Found</td>
        </tr>
      </tbody>

    <?php endif; // $record_count > 0 ?>

	</table>
  <?php
  /*
   * this shortcut function presents a pagination control with default layout
   */
  $this->show_pagination_control();
  ?>
  <?php $this->csv_export_form( array( 'export_fields' => array( 'first_name','last_name','email' ) ) ) ?>
</div>