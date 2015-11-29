<?php
/**
 * Trackable Behavior class file
 *
 * Combines WhoDidIt behavior (the setup phase) with Matt Curry's Trackable Behavior
 *
 * @author Jose Diaz-Gonzalez
 * @package trackable
 * @subpackage trackable.Model.Behavior
 * @version 2.0
 */
class TrackableBehavior extends ModelBehavior {

/**
 * Default settings for a model that has this behavior attached.
 *
 * @var array
 * @access protected
 */
	protected $_defaults = array(
		'fields' => array(
			'created_by' => array('create'),
			'modified_by' => array('update'),
		),
		'user_model' => 'User',
		'user_primaryKey' => 'id',
		'auto_bind' => true,
		'user_singleton' => true,
		'required' => array('save'),
	);

/**
 * Initiate behaviour for the model using settings.
 *
 * @param Model $Model instance of model
 * @param array $config array of configuration settings.
 * @return void
 */
	public function setup(Model $Model, $config = array()) {
		$settings = array_merge($this->_defaults, $config);

		$on = array(
			'create' => array(),
			'read' => array(),
			'update' => array(),
			'delete' => array(),
		);

		$validFields = array();

		foreach ($settings[$Model->alias]['fields'] as $field => $callbacks) {
			foreach ($callbacks as $callback) {
				if (in_array($field, $on[$callback]) && $Model->hasField($field)) {
					$validFields[] = $field;
					$settings[$Model->alias]['event'][$callback][] = $field;
				}
			}
		}

		$settings['required'] = (array)$settings['required'];

		if ($settings[$Model->alias]['auto_bind']) {
			foreach ($validFields as $field) {
				$alias = Inflector::classify($field);
				$commonBelongsTo = array($alias => array(
					'className' => $settings[$Model->alias]['user_model'],
					'foreignKey' => $field,
				));
				$Model->bindModel(array('belongsTo' => $commonBelongsTo), false);
			}
		}

		$this->settings[$Model->alias] = $settings;
	}

/**
 * Called during validation operations, before validation. Sets the
 * 'create' and 'update fields to the current user_id
 *
 * @param Model $Model instance of model
 * @return bool
 */
	public function beforeValidate(Model $Model) {
		$trackableId = $this->getTrackableId($Model);

		$create = empty($Model->data[$Model->alias][$Model->primaryKey]) && empty($Model->id);

		if (!$trackableId) {
			return !in_array($create ? 'create' : 'update', $this->settings[$Model->alias]['required']);
		}

		if ($create) {
			foreach ($this->settings[$Model->alias]['event']['create'] as $field) {
				$Model->data[$Model->alias][$field] = $trackableId;
			}
		}

		foreach ($this->settings[$Model->alias]['event']['update'] as $field) {
			$Model->data[$Model->alias][$field] = $trackableId;
		}

		return true;
	}

/**
 * Called before every deletion operation. Sets the 'delete' fields
 * to the current user_id
 *
 * @param Model $Model Model instance
 * @param bool $cascade whether to cascade deletes
 * @return bool
 */
	public function beforeDelete(Model $Model, $cascade = true) {
		$trackableId = $this->getTrackableId($Model);

		if (!$trackableId) {
			return !in_array('delete', $this->settings[$Model->alias]['required']);
		}

		foreach ($this->settings[$Model->alias]['event']['delete'] as $field) {
			$Model->data[$Model->alias][$field] = $trackableId;
		}

		return true;
	}

/**
 * Called before each find operation. Sets the 'read' fields
 * to the current user_id
 *
 * @param Model $Model Model using the behavior
 * @param array $query Query parameters as set by cake
 * @return array
 */
	public function beforeFind(Model $Model, $query) {
		$trackableId = $this->getTrackableId($Model);

		if (!$trackableId) {
				return !in_array('read', $this->settings[$Model->alias]['required']);
		}

		foreach ($this->settings[$Model->alias]['event']['read'] as $field) {
			$query[$Model->alias][$field] = $trackableId;
		}

		return $query;
	}

/**
 * Retrieves the user_id for the current model. Can be overriden in model
 *
 * This method tries to retrieve the trackable_id in the following order:
 *
 * - Model->getTrackableId()
 * - Model->trackable_id
 * - AuthComponent::user($user_primaryKey)
 * - Authsome::get($user_primaryKey)
 * - User::get($user_primaryKey)
 *
 * @param object $Model	 Model using the behavior
 * @return mixed user_id integer if available, false otherwise
 * @access public
 */
	public function getTrackableId(Model $Model) {
		$trackableId = null;

		if (method_exists($Model, 'getTrackableId')) {
			$trackableId = $Model->getTrackableId();
		}

		if (!empty($Model->trackable_id)) {
			$trackableId = $Model->trackable_id;
		}

		if (!$trackableId && class_exists('AuthComponent')) {
			$trackableId = AuthComponent::user($this->settings[$Model->alias]['user_primaryKey']);
		}

		if (!$trackableId && class_exists('Authsome')) {
			$trackableId = Authsome::get($this->settings[$Model->alias]['user_primaryKey']);
		}

		if (!$trackableId) {
			$className = get_class($Model);
			if (method_exists($className, 'get')) {
				$trackableId = $className::get($this->settings[$Model->alias]['user_primaryKey']);
			}
		}

		return $trackableId;
	}

}
