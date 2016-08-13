<?php
namespace Application\Model;

abstract class AModel
{
  public $id;

  abstract public function exchangeArray($data);
  abstract public function exchangeToArray();
}
