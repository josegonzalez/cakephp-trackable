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
		'user_model' => 'User',    //name of User model
		'created_by_field' => 'created_by',    //the name of the "created_by" field in DB (default 'created_by')
		'modified_by_field' => 'modified_by',  //the name of the "modified_by" field in DB (default 'modified_by')
		'auto_bind' => true     //automatically bind the model to the User model (default true)
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

		$hasFieldCreatedBy = $model->hasField($this->settings[$model->alias]['created_by_field']);
		$hasFieldModifiedBy = $model->hasField($this->settings[$model->alias]['modified_by_field']);
		
		$this->settings[$model->alias]['has_created_by'] = $hasFieldCreatedBy;
		$this->settings[$model->alias]['has_modified_by'] = $hasFieldModifiedBy;

		//handles model binding to the User model
		//according to the auto_bind settings (default true)
		if ($this->settings[$model->alias]['auto_bind']) {
		    if ($hasFieldCreatedBy) {
				$commonBelongsTo = array(
					Inflector::classify($this->settings[$model->alias]['created_by_field']) => array('className' => $this->settings[$model->alias]['user_model'],
						'foreignKey' => $this->settings[$model->alias]['created_by_field']));
				$model->bindModel(array('belongsTo' => $commonBelongsTo), false);
			}

			if ($hasFieldModifiedBy) {
				$commonBelongsTo = array(
					Inflector::classify($this->settings[$model->alias]['modified_by_field']) => array('className' => $this->settings[$model->alias]['user_model'],
						'foreignKey' => $this->settings[$model->alias]['modified_by_field']));
				$model->bindModel(array('belongsTo' => $commonBelongsTo), false);
			}
		}
	}

/**
 * Sets the User_id for the created_by and modified_by fields for this model
 *
 * @return void
 * @author Matt Curry
 **/
	function beforeValidate(&$model) {
		$settings = $this->settings[$model->alias];
		$trackable_id = (isset($model->trackable_id)) ? $model->trackable_id : User::get('id');
		if (empty($model->data[$model->alias][$model->primaryKey])) {
			$model->data[$model->alias][$settings['created_by_field']] = $trackable_id;
		}
		$model->data[$model->alias][$settings['modified_by_field']] = $trackable_id;
		return true;
	}
}
?>