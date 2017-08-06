<?php
namespace sashsvamir\sortableBehavior;

use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\base\InvalidConfigException;


/**
 * Behavior update model sort attribute on save/update owner model
 */
class SortableBehavior extends Behavior
{
	/**
	 * @var ActiveRecord
	 */
	public $owner;

	/**
	 * @var string
	 */
	public $attribute = 'sort';

	/**
	 * @inheritdoc
	 */
	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
			ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
		];
	}

	/**
	 * Update sort attribute
	 */
	public function beforeSave()
	{
		if ($this->owner->{$this->attribute} === null) {
			$nextNum = (int) $this->owner::find()->max($this->attribute) + 1;
			$this->owner->{$this->attribute} = $nextNum;
		}
	}

	/**
	 * @param array $order With order like:
	 * [index => id, index => id, ...]
	 * @return array    With new order
	 */
	public function arrange($order)
	{
		$orders = [];
		$i = 0;
		foreach ($order as $id) {
			$model = $this->owner::findOne($id);
			if ($model) {
				$model->{$this->attribute} = $i;
				$model->update();

				$orders[$model->{$model::primaryKey()[0]}] = $i;
			}
			$i++;
		}

		return $orders;
	}

}
