<?php
/**
* Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
*
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
* HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT 
* LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON 
* ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
* USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
* EButtonColumnWithClearFilters class file.
*
* @author Marton Kodok http://www.yiiframework.com/forum/index.php?/user/8824-pentium10/
* @link http://www.yiiframework.com/
* @version 1.0.2
*
* The EButtonColumnWithClearFilters extension adds up some functionality to the default
* possibilites of zii's CButtonColumn implementation.
*
* An image will be placed in the top column(on same line of AJAX filters). When clicked
* the filters will be cleared, the content will be refreshed with all items available.
*
* ##Step 1
*
* To use this extension, copy this file to your components/ directory,
* add 'import' => 'application.components.EButtonColumnWithClearFilters', [...] to your
* config/main.php and use this column on each widget's Column array you would like to
* inherit the new possibilities:
*
* array(
*   'class'=>'EButtonColumnWithClearFilters',
*   //'clearVisible'=>true,
*   //'onClick_BeforeClear'=>'alert('this js fragment executes before clear');',
*   //'onClick_AfterClear'=>'alert('this js fragment executes after clear');',
*   //'clearHtmlOptions'=>array('class'=>'custom-clear'),
*   //'imageUrl'=>'/path/to/custom/image/delete.png',
*   //'url'=>'Yii::app()->controller->createUrl(Yii::app()->controller->action->ID,array("clearFilters"=>1))',
*   //'label'=>'My Custom Label',
* ),
*
*
* In your controller in the same action the widget is displayed, you have to add
*
* if (intval(Yii::app()->request->getParam('clearFilters'))==1) {
*    $model->unsetAttributes();
*    $this->redirect(array($this->action->ID));
* }
*
* All posible customizations have been enumerated above, you shall comment out those that
* you won't override. The minial setup is just the class type for the Columns.
*
* clearVisible: a PHP expression for determining whether the button is visible
*
* onClick_BeforeClear: If you want to execute certain JS code before the filters are cleared out,
* use this property to pass your custom code. You are allowed to use 'return false;' only, when you want
* to stop the clear to happen. This will stop all further JS code, and HTTP request to be executed.
* You are not allowed to use 'return true;' it will break the components usage.
*
* onClick_AfterClear: If you want to execute certain JS code after clear, but before the AJAX call
* use this property to pass your custom code. You are allowed to use 'return false' only, when you want
* to stop the AJAX call to happen. This will stop the form to be reloaded.
* If you want to clear the form by classic GET request, and not by ajax you shall 'return true;' here.
*
* clearHtmlOptions: Associative array of html elements to be passed for the button
* default is: array('class'=>'clear','id'=>'cbcwr_clear','style'=>'text-align:center;display:block;');
*
* imageUrl: image URL of the button. If not set or false, a text link is used
* Default is: $this->grid->baseScriptUrl.'/delete.png'
*
* url: a PHP expression for generating the URL of the button
* Default is: Yii::app()->controller->createUrl(Yii::app()->controller->action->ID,array("clearFilters"=>1))
*
* label: Label tag to be used on the button when no URL is given
* Default is: Clear Filters
*
* ##Step 2
*
* You need to add to your controller the following code.
*
* if (intval(Yii::app()->request->getParam('clearFilters'))==1) {
*    EButtonColumnWithClearFilters::clearFilters($controller,$model);
* }
*
*
* If you don't need the clear filters button capabilities you can also pass a `clearFilters` parameter with a 1(one) value to the controller, for this you can use a link or a button. 
*
* This extension comes handy when you use Remember Filters extension for GridView
* http://www.yiiframework.com/extension/remember-filters-gridview
*
* Please VOTE this extension if helps you at:
* http://www.yiiframework.com/extension/clear-filters-gridview
*/

Yii::import('zii.widgets.grid.CButtonColumn');

class EButtonColumnWithClearFilters extends CButtonColumn {

    /**
     * Private member to store internally the button definition
     *
     * @var array
     */
    private $_clearButton;
    /**
     * Private member to store as a backup the template usage.
     *
     * @var string
     */
    private $_templateB;

    /**
     * a PHP expression for determining whether the button is visible
     *
     * @var string
     */
    public $clearVisible;
    /**
     * JS code to be invoked when the button is clicked, this is invoked before clearing the form fields;
     * Returning false from this code fragment prevents the AJAX to be executed. Only use 'return' block when you want to stop further steps execution.
     *
     * @var string
     */
    public $onClick_BeforeClear;
    /**
     * JS code to be invoked when the button is clicked, this is invoked after clearing the form fields, before AJAX;
     * Returning false from this code fragment prevents the AJAX to be executed. Only use 'return' block when you want to stop further steps execution.
     *
     * @var string
     */
    public $onClick_AfterClear;

    /**
     * Associative array of html elements to be passed for the button
     * default is: array('class'=>'clear','id'=>'cbcwr_clear','style'=>'text-align:center;display:block;');
     *
     * @var array
     */
    public $clearHtmlOptions;

    /**
     * image URL of the button. If not set or false, a text link is used
     * Default is: $this->grid->baseScriptUrl.'/delete.png'
     *
     * @var string
     */
    public $imageUrl;

    /**
     * a PHP expression for generating the URL of the button
     * Default is: Yii::app()->controller->createUrl(Yii::app()->controller->action->ID,array("clearFilters"=>1))
     *
     * @var string
     */
    public $url;

    /**
     * Label tag to be used on the button when no URL is given
     * Default is: Clear Filters
     *
     * @var unknown_type
     */
    public $label;

    public function init()
    {

        if ($this->grid->filter) {
            //initializ variables
            $_customJS=null;
            $_beforeAjax=null;
            $_click=null;
            $_visible=null;
            $_options=null;
            $_imageUrl=null;

            //define defaults
            $_optionsDefault=array('class'=>'clear','id'=>'cbcwr_clear','style'=>'text-align:center;display:block;');

            // handle custom JS setup
            if (!empty($this->onClick_BeforeClear)) {
                $_customJS=$this->onClick_BeforeClear.';';
            }
            if (!empty($this->onClick_AfterClear)) {
                $_beforeAjax=$this->onClick_AfterClear.";\r\n";
            }
            // turn custom setup into representative output
            $_click="js:function() {{$_customJS} return cbcwr_clearFields() }";
            $_visible=is_bool($this->clearVisible)?( ($this->clearVisible)?'true':'false'):$this->clearVisible;
            if (empty($this->clearHtmlOptions)) {
                $this->clearHtmlOptions=array();
            }
            $_options=@array_merge($_optionsDefault,$this->clearHtmlOptions);

            if (!empty($this->imageUrl)) {
                $_imageUrl=$this->imageUrl;
            } else {
                $_imageUrl=$this->grid->baseScriptUrl.'/delete.png';
            }

            if (!empty($this->url)) {
                $_url=$this->url;
            } else {
                $_url='Yii::app()->controller->createUrl(Yii::app()->controller->action->ID,array("clearFilters"=>1))';
            }

            if (!empty($this->label)) {
                $_label=Yii::t('app',$this->label);
            } else {
                $_label=Yii::t('app','Clear Filters');
            }


            // define the button structure to be used
            $this->_clearButton = array(
            'label'=>$_label,     // text label of the button
            'url'=>$_url,       // a PHP expression for generating the URL of the button
            'imageUrl'=>$_imageUrl,  // image URL of the button. If not set or false, a text link is used
            'options'=>$_options, // HTML options for the button tag
            'click'=>$_click,     // a JS function to be invoked when the button is clicked
            'visible'=>$_visible,   // a PHP expression for determining whether the button is visible
            );



            $this->buttons=CMap::mergeArray(
                $this->buttons,
                array('clear' => $this->_clearButton,)
            );

            $this->_templateB=$this->template;
            $this->template.="{clear}";

            $script=<<<HTMLEND
$.fn.clearFields = $.fn.clearInputs = function() {
    return this.each(function() {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (t == 'text' || t == 'password' || tag == 'textarea') {
            this.value = '';
        }
        else if (t == 'checkbox' || t == 'radio') {
            this.checked = false;
        }
        else if (tag == 'select') {
            this.selectedIndex = -1;
        }
    });
};
        
function cbcwr_clearFields() {
    try
    {    
        $('#{$this->grid->id} :input').clearFields(); // this will clear all input in the current grid
        {$_beforeAjax} $('#{$this->grid->id} .{$this->grid->filterCssClass} :input').first().trigger('change');// to submit the form
        return false;
    }
    catch(cbwr_err)
    {
        return false;
    }
}
HTMLEND;
            Yii::app()->clientScript ->registerScript(__CLASS__.'clearFields',$script,CClientScript::POS_HEAD);
        }

        // call parent to initialize other buttons
        parent::init();
    }


    public function renderFilterCell()
    {
        // initialise variables
        $row=null;
        $data=null;
        // restore template
        $this->template=$this->_templateB;
        // output
        echo "<td>";
        echo $this->renderButton('clear',$this->_clearButton,$row=array(),$data=array());
        echo "</td>";
    }
    
    /**
         * Static method to check if a model uses a certain behavior class
         *
         * @param CModel $model
         * @param string $behaviorClass
         * @return boolean
         */
    private static function modelUsesBehavior($model,$behaviorClass) {
        $behaviors=$model->behaviors();
        if (is_array($behaviors)) {
            foreach ($behaviors as $behavior => $behaviorDefine) {
                if (is_array($behaviorDefine)) {
                    $className=$behaviorDefine['class'];
                } else {
                    $className=$behaviorDefine;
                }
                if (strpos($className,$behaviorClass)!==false) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public static function clearFilters($controller,$model) {
        $model->unsetAttributes();
        try {
            if (EButtonColumnWithClearFilters::modelUsesBehavior($model,'ERememberFiltersBehavior')) {
    
                $model->unsetFilters();
    
            }
        }
        catch (Exception $e) {
    
        }
        $controller->redirect(array($controller->action->ID));
    }
}
