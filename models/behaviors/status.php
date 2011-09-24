<?php
class StatusBehavior extends ModelBehavior {

/**
 * defaultSettings property
 *
 * @var array
 * @access protected
 */
	var $settings = array();
	var $_defaultSettings = array(
		'foreignKey' => 'status_id',
		'statuses' => array(
			'0'=>'draft',
			'1'=>'live'
			)
	);

/**
 * setup method
 *
 * @param mixed $Model
 * @param array $config
 * @return void
 * @access public
 */
	function setup(&$Model, $config = array()) {
		$this->settings[$Model->alias] = am ($this->_defaultSettings, $config);
		if (!isset($Model->includes)) {
			$Model->includes = array();
		}
		$Model->live = null;
		$Model->includes['statuses'] = $this->settings[$Model->alias]['statuses'];
	}
	
	function beforeFind($Model, $queryData) {
		$field = $Model->alias.'.'.$this->settings[$Model->alias]['foreignKey'];
		if($Model->live && !isset($queryData['conditions'][$field])) {
			$queryData['conditions'][$field] = 1;
		}
		return $queryData;
	}
}
?>