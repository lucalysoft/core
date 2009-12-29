<?php
/**
 * Корзина товаров
 *
 * @author pirrat <mrakobesov@gmail.com>
 * @version 0.5 rc
 * @package ShoppingCart
 */


class CShoppingCart extends CMap {

    /**
     * Обновлять модели при востановлении из сессии?
     * @var boolean
     */
    public $refresh = true;

    /**
     * При иницализации копируем из сессии корзину
     */
    public function init() {
        $this->copyFrom(Yii::app()->user->getState(__CLASS__));
    }

    /**
     * Добавляет в коллекцию объект товара
     * @param ICartPosition $product
     * @param int кол-во элементов позиции
     */
    public function put(ICartPosition $product, $quantity = 1) {
        $product->attachBehavior("CartPosition", new CartPositionBehaviour());
        $product->setRefresh($this->refresh);

        $product->setQuantity($quantity);
        $key = $product->getId();
        
        if($product->getQuantity() < 1)
            $this->remove($key);
        else
            parent::add($key, $product);

        $this->saveState();
    }

    /**
     * @param mixed $key
     * @param ICartPosition $value
     */
    public function add($key, ICartPosition $value) {
        $this->put($value, 1);
    }

    /**
     * Удаляет из коллекции элемент по ключу
     * @param mixed $key
     */
    public function remove($key) {
        parent::remove($key);
        $this->saveState();
    }

    /**
     * Сохраняет состояние объекта
     */
    protected function saveState() {
        Yii::app()->user->setState(__CLASS__, $this->toArray());
    }

    /**
     * Возращает кол-во товаров в корзине
     * @return int
     */
    public function getItemsCount() {
        $count = 0;
        foreach($this as $product) {
            $count += $product->getQuantity();
        }

        return $count;
    }


    /**
     * Возращает суммарную стоимость всех позиций в корзине
     * @return float
     */
    public function getCost() {
        $price = 0.0;
        foreach($this as $product) {
            $price += $product->getSummPrice();
        }

        return $price;
    }


}
