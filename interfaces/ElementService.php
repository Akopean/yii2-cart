<?php
namespace pistol88\cart\interfaces;

interface ElementService
{
    public function getId();

    public function getItemId();
    
    public function getCount();

    public function getPrice();
    
    public function getModel($withCartElementModel);
    
    public function getOptions();

    public function setItemId($itemId);
    
    public function setCount($count);
    
    public function countIncrement($count);

    public function setPrice($price);
    
    public function setModel($model);
    
    public function setOptions($options);
}
