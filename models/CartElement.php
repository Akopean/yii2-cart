<?php

namespace pistol88\cart\models; 

use pistol88\cart\models\Cart;
use Yii;

class CartElement extends \yii\db\ActiveRecord {
    
    function getPriceFormatted() {
        $price = \yii\helpers\Html::encode($this->model->getCartPrice());
        $currency = Yii::$app->getModule('cart')->currency;
        if (Yii::$app->getModule('cart')->currencyPosition == 'after') {
            $price = "$price$currency";
        } else {
            $price = "$currency$price";
        }
        return $price;
    }
    
    public static function tableName() {
        return 'cart_element';
    }

    public function rules() {
        return [
            [['cart_id', 'model', 'item_id'], 'required'],
            [['model'], 'validateModel'],
            [['description'], 'string'],
            [['price'], 'double'],
            [['item_id', 'count', 'parent_id'], 'integer'],
        ];
    }

    public function validateModel($attribute, $param) {
        $model = $this->model;
        if (class_exists($model)) {
            $elementModel = new $model();
            if (!$elementModel instanceof \pistol88\cart\models\tools\CartElementInterface) {
                $this->addError($attribute, 'Model implement error');
            }
        } else {
            $this->addError($attribute, 'Model not exists');
        }
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('cart', 'ID'),
            'parent_id' => Yii::t('cart', 'Parent element'),
            'price' => Yii::t('cart', 'Price'),
            'description' => Yii::t('cart', 'Description'),
            'model' => Yii::t('cart', 'Model name'),
            'cart_id' => Yii::t('cart', 'Cart ID'),
            'item_id' => Yii::t('cart', 'Item ID'),
            'count' => Yii::t('cart', 'Count'),
        ];
    }

    public function beforeSave($insert) {
        $cartModel = Cart::find()->my();
        $cartModel->updated_time = time();
        $cartModel->save();

        return true;
    }

}
