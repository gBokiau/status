<?php
class StatusBehavior extends ModelBehavior {

/**
 * defaultSettings property
 *
 * @var array
 * @access protected
 */
	var $settings = array();
	protected $_defaultSettings = array(
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
	public function setup(Model $Model, $settings = array()) {
		$this->settings[$Model->alias] = am ($this->_defaultSettings, $settings);
		if (!isset($Model->includes)) {
			$Model->includes = array();
		}
		$Model->live = null;
		$Model->includes['statuses'] = $this->settings[$Model->alias]['statuses'];
	}
	
	public function beforeFind(Model $Model, $query) {
		$field = $Model->alias.'.'.$this->settings[$Model->alias]['foreignKey'];
		if (!$Model->live) 
			return $query;
		if (!isset($query['conditions'][$field])) {
			$query['conditions'][$field] = 1;
		}
		foreach($Model->associations() as $type) {
			foreach ($Model->{$type} as $assoc => $assocData) {
				$linkModel = $Model->{$assoc};
				if (isset($assocData['live'])) {
					$condition = array($assoc.'.'.$assocData['live'] => '1');
					$Model->{$type}[$assoc]['conditions'] = array_merge($Model->{$type}[$assoc]['conditions'], $condition);
				}
			}
		}
		return $query;
	}
}
?>