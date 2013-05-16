<?php defined('C5_EXECUTE') or die(_("Access Denied."));

//Outputs a "list table" -- a listing of records in a styled html table with various action buttons in each row.
//Note that the markup outputted by this class relies on some CSS and JS in dashboard.css/dashboard.js

class CrudListTable {
	
	private $view = null;
	private $columns = array();
	private $actions = array();
	private $sort_action_key = null;
	private $id_field_name = 'id';
	
	public function __construct(&$c5_view_object) {
		$this->view =& $c5_view_object;
	}
	
	public function addColumn($field, $label) {
		$this->columns[$field] = $label;
	}
	
	//$action: which controller action to direct this link to
	//$placement: 'left' or 'right' (display this link to the left or right of the data columns)
	//$title: title of the link (appears when user hovers over the icon)
	//$icon: class name of twitter bootstrap icon glyph (see http://twitter.github.io/bootstrap/base-css.html#icons )
	//$is_sort_action: true if this is the "sort" action (we do a bunch of special things for this, because it is unlike other actions)
	//$override_id_value: IF you don't want the record's id being passed to the action, set this (e.g. for sorting within a "segment" / parent id)
	public function addAction($action, $placement, $title, $icon, $is_sort_action = false, $override_id_value = null) {
		$this->actions[$action] = array(
			'action' => $action,
			'placement' => $placement,
			'title' => $title,
			'icon' => $icon,
			'sort' => $is_sort_action,
			'value' => $override_id_value,
		);
		
		if ($is_sort_action) {
			$this->sort_action_key = $action;
		}
	}
	
	//if the name of the id field is something other than 'id', set that here
	public function overrideIdFieldName($field) {
		$this->id_field_name = $field;
	}
	
	public function display($records) {
		
		$out = array();
		
		//open wrappers
		if (!empty($this->sort_action_key)) {
			$action = $this->actions[$this->sort_action_key];
			$out[] =  '<div class="sortable-container"'
		           . ' data-sortable-save-url="' . $this->view->action($action['action'], $action['value']) . '"'
		           . ' data-sortable-save-token="' . Loader::helper('validation/token')->generate() . '"'
		           . '>';
		}
		$out[] = '<table class="table table-striped">';
		
		//headings
		$out[] = '<thead>';
		$out[] = '<tr>';
		foreach ($this->actions as $action) {
			$out[] = ($action['placement'] == 'left') ? '<th>&nbsp;</th>' : '';
		}
		foreach ($this->columns as $field => $label) {
			$out[] = "<th>{$label}</th>";
		}
		foreach ($this->actions as $action) {
			$out[] = ($action['placement'] == 'right') ? '<th>&nbsp;</th>' : '';
		}
		$out[] = '</tr>';
		$out[] = '</thead>';
		
		//rows
		$out[] = '<tbody>';
		foreach ($records as $record) {
			$out[] = empty($this->sort_action_key) ? '<tr>' : '<tr data-sortable-id="' . $record[$this->id_field_name] . '">';
			foreach ($this->actions as $action) {
				$out[] = $this->getActionCellMarkup($action, $record[$this->id_field_name], 'left');
			}
			$last_field = array_pop(array_keys($this->columns));
			foreach ($this->columns as $field => $label) {
				$out[] = ($field === $last_field) ?'<td class="last-field">' : '<td>';
				$out[] = htmlentities($record[$field]);
				$out[] = '</td>';
			}
			foreach ($this->actions as $action) {
				$out[] = $this->getActionCellMarkup($action, $record[$this->id_field_name], 'right');
			}
			$out[] = '</tr>';
		}
		$out[] = '</tbody>';
		
		//close wrappers
		$out[] = '</table>';
		$out[] = empty($this->sort_action_key) ? '' : '</div><!-- .sortable-container -->';
		
		//output
		$nonempty_lines = array();
		foreach ($out as $line) {
			if (!empty($line)) {
				$nonempty_lines[] = $line;
			}
		}
		echo implode("\n", $nonempty_lines);
	}
	
	private function getActionCellMarkup($action, $id, $must_have_placement = '') {
		if (!empty($must_have_placement) && ($action['placement'] != $must_have_placement)) {
			return ''; //this action did not have the 'placement' that we're looking for
		}
		
		if ($action['sort']) {
			return '<td><span class="row-button sortable-handle" title="' . $action['title'] . '"><i class="' . $action['icon'] . '"></i></span></td>';
		} else {
			$href = $this->view->action($action['action'], $id);
			return '<td><a class="row-button" href="' . $href . '" title="' . $action['title'] . '"><i class="' . $action['icon'] . '"></i></a></td>';
		}
	}
	
}
