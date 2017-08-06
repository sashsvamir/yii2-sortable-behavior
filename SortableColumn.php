<?php
namespace sashsvamir\sortableBehavior;

use sashsvamir\sortablejs\SortablejsAsset;
use yii\grid\Column;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;


class SortableColumn extends Column
{
	/**
	 * @var string the name of the model sort attribute.
	 */
	public $attribute;

	/**
	 * @var string route of action to sort model.
	 */
	public $action;

	/**
	 * @inheritdoc
	 * @throws \yii\base\InvalidConfigException if [[sort]] or [[action]] is not set.
	 */
	public function init()
	{
		parent::init();
		if (empty($this->attribute)) {
			throw new InvalidConfigException('The "attribute" property must be set.');
		}
		if (empty($this->action)) {
			throw new InvalidConfigException('The "action" property must be set.');
		}

		$this->registerClientScript();
	}

	/**
	 * @inheritdoc
	 */
	protected function renderDataCellContent($model, $key, $index)
	{
		$options['class'] = 'drag-handle';

		return Html::tag('div', '☰', $options);
	}

	/**
	 * Registers the needed JavaScript and styles
	 */
	public function registerClientScript()
	{
		$id = $this->grid->options['id'];
		$view = $this->grid->getView();

		$view->registerAssetBundle(SortablejsAsset::className());
		// $view->registerJsFile('https://raw.githubusercontent.com/RubaXa/Sortable/master/Sortable.min.js');

		$options = Json::encode([
			'group' => $id . '-group',
			// 'filter' => '.ignore-elements',
			'handle' => '.drag-handle',
			'draggable' => 'tr',
			'dataIdAttr' => 'data-key',
			'onSort' => new JsExpression(<<< JS

				function (evt) {
					// console.log(evt);
					var order = {};
					var items = evt.item.parentNode.querySelectorAll('tr');
					for (var i=0; i<items.length; i++) {
						order[i] = items[i].getAttribute('data-key');
					}
					var data = {
						order: order,
					}
					// data[window.yii.getCsrfParam()] = window.yii.getCsrfToken();
					
					$.ajax('{$this->action}', {
						method: 'POST',
						data: data,
						beforeSend: function() {
							// add ajax class
							$('#{$id}').addClass('ajax-loading');
						},
						complete: function() {
							// remove ajax class
							$('#{$id}').removeClass('ajax-loading');
						},
						success: function(response) {
							console.log(response);
						},
						error: function (response) {
							alert('Произошла ошибка (см. консоль)');
							console.log(response);
						},
					});
				}
JS
			),
		]);
		$view->registerJs("var sortable_{$id} = Sortable.create(document.querySelector( '#{$id} tbody'), $options)");

		$view->registerCss('
			.drag-handle {
				cursor: move;
				cursor: -webkit-grabbing;
				padding: 1px 5px;
				font-weight: bold;
				color: #23527c;
			}
		');

	}

}
