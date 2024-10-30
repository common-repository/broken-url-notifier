<?php 
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class TT_Example_List_Table extends WP_List_Table {
	var $current_key = '';
	var $current_count = 0;


    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'BrokenUrlNotifier',     //singular name of the listed records
            'plural'    => 'BrokenUrlNotifier',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }


    function column_default($item, $column_name){
		echo $column_name .'<br/>';
		return print_r($item,true); //Show the whole array for troubleshooting purposes
    }

    function column_title($item){
		
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&movie=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&movie=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
        );
        
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }


    function column_cb($item){
	
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }

	/**
	 * Generate the table rows
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display_rows() {
		foreach ( $this->items as $itemK => $item ){
			$item['ID'] = $itemK;
			$this->single_row( $item );
		}
	}	

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param object $item The current item
	 */
	public function single_row( $item ) {
		echo '<tr id="'.$item['ID'].'" data-type="'.$item['type'].'"  >';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
	
	function column_sn($item){
		$this->current_count  = $this->current_count + 1; 
		return $this->current_count; 
	}
	function column_type($item){return $item['type'];}
	function column_url($item){return $item['url']; }
	function column_refpage($item) {return $item['page'];}
	function column_hits($item){return $item['hits'];}
	function column_actions($item){ 
		//var_dump($item); exit;
		return '<button type="button" data-delete-key="'.$item['ID'].'" class="button button-secondary issue_fixed">'.__('Issue Fixed',BUN_TXT).'</button>';
		//return '<input data-delete-key="'.$this->current_key.'" type="button" class="button button-secondary issue_fixed"  value="'.__('Issue Fixed',BUN_TXT).'"/>';
	
	}
	
    function get_columns(){
        $columns = array(
            'sn'        => __('SN',BUN_TXT), //Render a checkbox instead of text
            'type'     => __('Type',BUN_TXT),
            'url'    => __('URL',BUN_TXT),
            'refpage'  => __('Ref Page',BUN_TXT),
			'hits' => __("Hits",BUN_TXT),
			'actions' => __('Action',BUN_TXT),
        );
        return $columns;
    }


    function get_sortable_columns() {
        $sortable_columns = array(
            'type'     => array('type',false),     //true means it's already sorted
            'hits'    => array('hits',true),
        );
        return $sortable_columns;
    }


    function get_bulk_actions() {
        $actions = array();
        return $actions;
    }

	protected function bulk_actions( $which = '' ) {
		$return = '';
		if($which == 'top'){
			$return .= '<select id="filter_by_section">';
			$return .= '<option value="">Filter By</option>';
			$return .= '<option value="page">'.__('Page',BUN_TXT).'</option>';
			$return .= '<option value="image">'.__('Image',BUN_TXT).'</option>';
			$return .= '</select>';
		}
		echo $return;
	}

    function process_bulk_action() {
                
    }

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function no_items() {
		_e( 'No Broken Links Found',BUN_TXT );
	}	

    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries
        $per_page = 20;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        //$data = $this->example_data;
		
		$data = get_option(BUN_DB.'reports');
        
		
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}
 


function tt_render_list_page(){
    $testListTable = new TT_Example_List_Table();
    $testListTable->prepare_items();
?>
<div class="wrap">
	<h2><?php _e('Broken Url Reports',BUN_TXT); ?></h2>
	<form id="movies-filter" method="get">
		<?php $testListTable->display() ?>
	</form>

</div>
<?php
}