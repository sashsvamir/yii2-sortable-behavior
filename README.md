# Yii2 sortable behavior for ActiveRecord #
 
## Installation ##

add co composer.json:

	"repositories": [
		...
		{"type": "vcs", "url": "https://github.com/sashsvamir/yii2-sortable-behavior"}
	],

Either run

    php composer.phar require --prefer-dist sashsvamir/yii2-sortable-behavior "*@dev"


 
 
## Requirements ##

To perform sorting your model with this behavior, model should have `sort` attribute (or any name you want),
so you must provide existing model attribute with `int` type to store sorting index of model.




## SortableBehavior ##

This behavior allow you to add sort logic with ActiveRecord behavior.

### Usage ###

Attach the behavior to your model class:

	use sashsvamir\sortableBehavior\SortableBehavior;
	
    public function behaviors()
    {
        return [
            [
                'class' => SortableBehavior::className(),
                'attribute' => 'sort', // model attribute with sorting index
            ],
            // OR (will be used 'sort' attribute by default)
            SortableBehavior::className()
        ];
    }

Now your model store sorting index, which created in save/update model event or when you sorting your model in GridView (see below).





## SortableColumn and SortableAction ##

This allow you to perform sorting rows in GridView table with saving new order of items by ajax.

### Usage ###

To perform sorting in GridView add to widget follow column:

    GridView::widget([
		'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table'],
	    'columns' => [
	        ...
		    [
		        'class' => SortableColumn::className(),
		        'attribute' => 'sort',
		        'action' => \yii\helpers\Url::to('/site/slider/sort'), // route to controller with SortableAction
		    ],
	    ]
    ]);

Also add SortableAction to controller (see in example above grid-view column action param):

	public function actions()
    {
        return [
            'sort' => [
                'class' => SortableAction::className(),
                'parent' => Slider::className(), // class name of sortable model
            ],
        ];
    }


    
    
    
## Additional ##

Also you probably want to add follow sorting for your model:

in backend ModelSearch:

	$dataProvider = new ActiveDataProvider([
		'query' => $query,
		'sort' => [
			'defaultOrder' => ['sort' => SORT_ASC],
			'attributes' => ['sort'], // allow sorting by [[sort]] attribute only
		],
	]);

in frontend ModelQuery:

	public function init()
    {
        $modelClass = $this->modelClass;
		$this->orderBy([$modelClass::tableName() . '.sort' => SORT_ASC]);
		parent::init();
	}








