<?php
namespace sashsvamir\sortableBehavior;

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

		$view->registerAssetBundle(\yii\jui\JuiAsset::className());

		$options = Json::encode([
			'handle' => '.drag-handle',
			'items' => '> tr',
			// fix table row size
			'helper' => new \yii\web\JsExpression(
<<< JS
				function(e, ui) {
	                ui.children().each(function() {
	                   $(this).width($(this).width());
	                });
	                return ui;
	            }
JS
            ),
			'update' => new JsExpression(
<<< JS
				function (e, ui) {
					
					var order = {};
					var items = ui.item[0].parentNode.querySelectorAll('tr');
					for (var i = 0; i < items.length; i++) {
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
		$view->registerJs("var sortable_{$id} = $('#{$id} tbody').sortable($options);");

		$view->registerCss(
<<< CSS
			.drag-handle {
				cursor: move;
				cursor: -webkit-grabbing;
				padding: 1px 5px;
				font-weight: bold;
				color: #23527c;
			}
CSS
		);

	}

}
