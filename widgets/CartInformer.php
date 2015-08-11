<?php

namespace pistol88\cart\widgets;

use yii\helpers\Html;
use yii\helpers\Url;

class CartInformer extends \yii\base\Widget {

    public $text = NULL;
    public $offerUrl = NULL;
    public $cssClass = NULL;
    public $htmlTag = 'a';

    public function init() {
        parent::init();

        \pistol88\cart\assets\WidgetAsset::register($this->getView());

        if ($this->offerUrl == NULL) {
            $this->offerUrl = Url::toRoute("/cart/default/index");
        }
        
        if ($this->text === NULL) {
            $this->text = '{c} '. Yii::t('cart', 'on').' {p}';
        }
    }

    public function run() {
        $cartModel = \pistol88\cart\models\Cart::my();
        $this->text = str_replace(['{c}', '{p}'],
            ['<span class="pistol88-cart-count">'.$cartModel->getCount().'</span>', '<strong class="pistol88-cart-price">'.$cartModel->getPriceFormatted().'</strong>'],
            $this->text
        );
        return Html::tag($this->htmlTag, $this->text, [
            'href' => Url::toRoute('/cart/element/create'),
            'class' => "pistol88-cart-informer {$this->cssClass}",
            'data-id' => $model->id
        ]);
    }

}
