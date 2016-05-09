<?php
namespace pistol88\cart\controllers;

use pistol88\cart\models\Cart;
use pistol88\cart\models\CartElement;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use yii;

class ElementController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    
    public function actionDelete()
    {
        $json = ['result' => 'undefind', 'error' => false];
        $elementId = Yii::$app->request->post('elementId');

        if(CartElement::findOne($elementId)->delete()) {
            $json['result'] = 'success';
        }
        else {
            $json['result'] = 'fail';
        }

        return $this->_cartJson($json);
    }
	
    public function actionCreate()
    {
        $json = ['result' => 'undefind', 'error' => false];

        $cartModel = yii::$app->cart;

        $json['cartId'] = $cartModel->id;

        if ($cartModel->id) {
            $postData = Yii::$app->request->post();

            $model = $postData['CartElement']['model'];
            if($model) {
                $productModel = new $model();
                $productModel = $productModel::findOne($postData['CartElement']['item_id']);

                $options = null;
                if(isset($postData['CartElement']['options'])) {
                    $options = $postData['CartElement']['options'];
                }

                $elementModel = $cartModel->put($productModel, $postData['CartElement']['count'], $options);

                $json['elementId'] = $elementModel->getCartId();
                $json['result'] = 'success';
            }
            else {
                $json['result'] = 'fail';
                $json['error'] = 'empty model';
            }
        }

        return $this->_cartJson($json);
    }

    public function actionUpdate()
    {
        $json = ['result' => 'undefind', 'error' => false];

        $cartModel = yii::$app->cart;
        
        $json['cartId'] = $cartModel->id;

        $postData = Yii::$app->request->post();
        
        $elementModel = CartElement::find()->andWhere(['cart_id' => $cartModel->id, 'id' => $postData['CartElement']['id']])->one();

        if ($elementModel->load($postData) && $elementModel->save()) {
            $json['elementId'] = $elementModel->getCartId();
            $json['result'] = 'success';
        } else {
            $json['result'] = 'fail';
            $json['error'] = $elementModel->getErrors();
        }

        return $this->_cartJson($json);
    }

    private function _cartJson($json)
    {
        if ($cartModel = yii::$app->cart) {
            $json['elementsHTML'] = \pistol88\cart\widgets\ElementsList::widget();
            $json['count'] = $cartModel->getCount();
            $json['price'] = $cartModel->getCostFormatted();
        } else {
            $json['count'] = 0;
            $json['price'] = 0;
        }
        return Json::encode($json);
    }

}
