<?php
namespace sashsvamir\sortableBehavior;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\helpers\Json;
use yii\web\HttpException;


/**
 *
 */
class SortableAction extends Action
{
	/**
	 * @var string  Owner model class name, for example: 'common\models\Slider' or Slider::className()
	 */
	public $parent;

    /** @var ActiveRecord */
    protected $owner;

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		if (!is_subclass_of($this->parent, ActiveRecordInterface::class)) {
			throw new InvalidConfigException('Wrong Parent model');
		}

		parent::init();

		// set owner
		$this->owner = new $this->parent;
	}

	/**
	 * @param $action string
	 * @return string
	 */
    public function run()
    {
	    $order = Yii::$app->request->post('order');

	    return $this->actionSortable($order);
    }

	/**
	 * Сортируем слайды по новому поряду.
	 * @param array $order  Новый порядок:
	 * [
	 *      order => [
	 *          [[relative index]] => [[model id]],
	 *      ]
	 * ]
	 * @return string   json объект где ключи это id картинки, а его значение это порядковый номер (поле sort)
	 * @throws HttpException
	 */
	public function actionSortable($order)
	{
		if (count($order) == 0) {
			throw new HttpException(400, 'No data, to save');
		}

		$result = $this->owner->arrange($order);

		return Json::encode($result);
	}

}
