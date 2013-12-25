yii-clear-filters-gridview
=============================

The EButtonColumnWithClearFilters Yii extension adds up some functionality to the default possibilites of CButtonColumn implementation when you use extensions to remember filter values. This extension helps you to **clear the remembered filter values**.

Check out [Remember Filters Gridview](http://www.yiiframework.com/extension/remember-filters-gridview/) extension also.

**An image will be placed in the top column(on same line of AJAX filters). When clicked the filters will be cleared, the content will be refreshed with all items available.**

![Please login to see the Demo image!](https://raw.github.com/pentium10/yii-clear-filters-gridview/master/res/clear_filters_10.png "Demo")

Requirements
--------------------

- Yii 1.1

Donate
----------

[Click here to donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K9TM6HR8JQ4Z8 "Donate")

Resources
---------------

- **[Extension page](http://www.yiiframework.com/extension/clear-filters-gridview/) (don't forget to cast your support vote)**
- [Report a bug](http://github.com/pentium10/yii-clear-filters-gridview/issues "Report a bug")
- [Forum](http://www.yiiframework.com/forum/index.php?/topic/15845-extension-clear-filters-gridview/ "Forum")
- [Remember Filters Gridview extension](http://www.yiiframework.com/extension/remember-filters-gridview)

Install
---------

We recommend installing the extension with [Composer](http://getcomposer.org/). Add this to the `require` section of your `composer.json`:

    "pentium10/yii-clear-filters-gridview" : "dev-master"

You also need to include composer's autoloader:

```php
    require_once __DIR__.'/protected/vendor/autoload.php';
```


Usage
---------

Step 1
--------

To use this extension, copy this file to your components/ directory, add 'import' => 'application.components.EButtonColumnWithClearFilters', [...] to your config/main.php and use this column on each widget's Column array you would like to inherit the new possibilities:


```php
array(
  'class'=>'EButtonColumnWithClearFilters',
   //'clearVisible'=>true,
   //'onClick_BeforeClear'=>'alert('this js fragment executes before clear');',
   //'onClick_AfterClear'=>'alert('this js fragment executes after clear');',
   //'clearHtmlOptions'=>array('class'=>'custom-clear'),
   //'imageUrl'=>'/path/to/custom/image/delete.png',
   //'url'=>'Yii::app()->controller->createUrl(Yii::app()->controller->action->ID,array("clearFilters"=>1))',
   //'label'=>'My Custom Label',
 ),
```

All posible customizations have been enumerated above, you shall comment out those that you won't override. The minial setup is just the class type for the Columns. In addition to this you can still use/override the CButtonColumn to suit your needs. 

- **clearVisible**: a PHP expression for determining whether the button is visible

- **onClick_BeforeClear**: If you want to execute certain JS code before the filters are cleared out, use this property to pass your custom code. You are allowed to use 'return false;' only, when  you want to stop the clear to happen. This will stop all further JS code, and HTTP request to be executed. You are not allowed to use 'return true;' it will break the components usage.

- **onClick_AfterClear**: If you want to execute certain JS code after clear, but before the AJAX call use this property to pass your custom code. You are allowed to use 'return false' only, when you want to stop the AJAX call to happen. This will stop the form to be reloaded.  If you want to clear the form by classic GET request, and not by ajax you shall 'return true;' here.

- **clearHtmlOptions**: Associative array of html elements to be passed for the button 
Default is: array('class'=>'clear','id'=>'cbcwr_clear','style'=>'text-align:center;display:block;');

- **imageUrl**: image URL of the button. If not set or false, a text link is used 
Default is: $this->grid->baseScriptUrl.'/delete.png'

- **url**: a PHP expression for generating the URL of the button
Default is: 'Yii::app()->controller->createUrl(Yii::app()->controller->action->ID,array("clearFilters"=>1))'

- **label**: Label tag to be used on the button when no URL is given
Default is: Clear Filters


Step 2
---------

If you are using the [Remember Filters Gridview](http://www.yiiframework.com/extension/remember-filters-gridview "http://www.yiiframework.com/extension/remember-filters-gridview") extension, you need to add to your controller the following code. This is placed in the action method that handles the gridview display, after you have initialized your model.


```php
if (intval(Yii::app()->request->getParam('clearFilters'))==1) {
    EButtonColumnWithClearFilters::clearFilters($this,$model);//where $this is the controller
}
```

Sample actionAdmin()

```php
public function actionAdmin() {
        $model = new registration('search');
		if (intval(Yii::app()->request->getParam('clearFilters'))==1) {
			EButtonColumnWithClearFilters::clearFilters($this,$model);//where $this is the controller
		}
        $this->render('admin', array(
            'model' => $model,
        ));
}
```

If you don't need the clear filters button capabilities you can also pass a `clearFilters` parameter with a 1(one) value to the controller, for this you can use a link or a button. 


This extension has also a pair [Remember Filters Gridview](http://www.yiiframework.com/extension/remember-filters-gridview "http://www.yiiframework.com/extension/remember-filters-gridview")

yii, clear, filters, cgridview, gridview, store, reload, controller, model, behavior, interface, widget, stick, scenario

Change Log 
-----------------

[CHANGELOG.md](http://github.com/pentium10/yii-clear-filters-gridview/blob/master/CHANGELOG.md)

Contributing
------------

1. Fork it.
2. Create a branch (`git checkout -b my_enhancement_name`)
3. Commit your changes (`git commit -am "Enhanced Javascript"`)
4. Push to the branch (`git push origin my_enhancement_name`)
5. Open a [Pull Request][1]
6. Enjoy a refreshing Diet Coke and wait

[1]: http://github.com/pentium10/yii-clear-filters-gridview/pulls