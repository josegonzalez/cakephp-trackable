<?php
/**
 * Trackable Behavior class file
 *
 * Combines WhoDidIt behavior (the setup phase) with Matt Curry's Trackable Behavior
 * 
 * @author Jose Diaz-Gonzalez
 * @package app
 * @subpackage app.models.behaviors
 * @version 1.2
 */
class TrackableBehavior extends ModelBehavior {
/**
 * Default settings for a model that has this behavior attached.
 *
 * @var array
 * @access protected
 */
	var $__settings = array(
		'fields' => array(
			'created_by' => array(
				'create',
			),
			'updated_by' => array(
				'update',
			),
			'modified_by' => array(
				'update',
			),
		),
		'user_model' => 'User',                // name of User model
		'user_primaryKey' => 'id',             // field to use as user_id
		'created_by_field' => 'created_by',    // the name of the "created_by" field in DB (default 'created_by')
		'modified_by_field' => 'modified_by',  // the name of the "modified_by" field in DB (default 'modified_by')
		'auto_bind' => true,                   // automatically bind the model to the User model (default true)
		'user_singleton' => true,              // User the User::get() syntax
		'require_id' => false                  // Require that the trackable_id be set beforeSave()
	);

/**
 * Initiate behaviour for the model using settings.
 *
 * @param object $Model Model using the behaviour
 * @param array $settings Settings to override for model.
 * @access public
 */
	function setup(&$model, $config = array()) {
		$this->settings[$model->alias] = $this->__settings;

		//merge custom config with default settings
		$this->settings[$model->alias] = array_merge($this->settings[$model->alias], (array)$config);

		$on = array(
			'create' => array(),
			'read' => array(),
			'update' => array(),
			'delete' => array(),
		);

		$validFields = array();
		if ($this->settings[$model->alias]['created_by_field']) {
			if ($model->hasField($this->settings[$model->alias]['created_by_field'])) {
				$validFields[] = $this->settings[$model->alias]['created_by_field'];
				$on['create'][] = $this->settings[$model->alias]['created_by_field'];
			}
		}
		if ($this->settings[$model->alias]['modified_by_field']) {
			if ($model->hasField($this->settings[$model->alias]['modified_by_field'])) {
				$validFields[] = $this->settings[$model->alias]['modified_by_field'];
				$on['update'][] = $this->settings[$model->alias]['modified_by_field'];
			}
		}

		foreach ($fields as $field => $callbacks) {
			foreach ($callbacks as $callback) {
				if (in_array($field, $on[$callback]) && $model->hasField($field)) {
					$validFields[] = $field;
					$on[$callback][] = $field;
				}
			}
		}

		if ($this->settings[$model->alias]['auto_bind']) {
			foreach ($validFields as $field) {
				$alias = Inflector::classify($field);
				$commonBelongsTo = array($alias => array(
					'className' => $this->settings[$model->alias]['user_model'],
					'foreignKey' => $field,
				));
				$model->bindModel(array('belongsTo' => $commonBelongsTo), false);
			}
		}

		$this->settings[$model->alias]['on'] = $on;
	}

/**
 * Called during validation operations, before validation. Sets the
 * 'create' and 'update fields to the current user_id
 *
 * @param object $Model	Model using the behavior
 * @param array $options Options passed from model::save(), see $options of model::save().
 * @return boolean True if validate operation should continue, false to abort
 * @access public
 */
	function beforeValidate(&$model, $options = array()) {
		$trackable_id = $this->getTrackableId($model);

		if (!$trackable_id) {
			return !$this->settings[$model->alias]['require_id'];
		}

		if (empty($model->data[$model->alias][$model->primaryKey])) {
			foreach ($this->settings[$model->alias]['on']['create'] as $field) {
				$model->data[$model->alias][$field] = $trackable_id;
			}
		}

		foreach ($this->settings[$model->alias]['on']['update'] as $field) {
			$model->data[$model->alias][$field] = $trackable_id;
		}

		return true;
	}

/**
 * Called before every deletion operation. Sets the 'delete' fields 
 * to the current user_id
 *
 * @param object $Model	Model using the behavior
 * @param boolean $cascade If true records that depend on this record will also be deleted
 * @return boolean True if the operation should continue, false if it should abort
 * @access public
 */
	function beforeDelete(&$model, $cascade = true) {
		$trackable_id = $this->getTrackableId($model);

		if (!$trackable_id) {
			return !$this->settings[$model->alias]['require_id'];
		}

		foreach ($this->settings[$model->alias]['on']['delete'] as $field) {
			$model->data[$model->alias][$field] = $trackable_id;
		}

		return true;
	}

/**
 * Called before each find operation. Sets the 'read' fields 
 * to the current user_id
 *
 * @param object $Model	Model using the behavior
 * @param array $queryData Data used to execute this query, i.e. conditions, order, etc.
 * @return mixed true if the operation should continue, false if it should abort; or, modified
 *               $queryData to continue with new $queryData
 * @access public
 */
	function beforeFind(&$model, $queryData) {
		$trackable_id = $this->getTrackableId($model);

		if (!$trackable_id) {
			return !$this->settings[$model->alias]['require_id'];
		}

		foreach ($this->settings[$model->alias]['on']['read'] as $field) {
			$queryData[$model->alias][$field] = $trackable_id;
		}

		return $queryData;
	}

/**
 * Retrieves the user_id for the current model. Can be overriden in model
 *
 * @param object $Model	Model using the behavior
 * @return mixed user_id integer if available, false otherwise
 * @access public
 */
	function getTrackableId(&$model) {
		$trackable_id = null;

		if ($model->trackable_id) {
			$trackable_id = $model->trackable_id;
		}

		if (!$trackable_id && class_exists('Authsome')) {
			$trackable_id = Authsome::get($this->settings[$model->alias]['user_primaryKey']);
		}

		if (!$trackable_id && $this->settings[$model->alias]['user_singleton']) {
			$trackable_id = User::get($this->settings[$model->alias]['user_primaryKey']);
		}

		return $trackable;
	}

}