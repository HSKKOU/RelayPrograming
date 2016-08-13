<?php
namespace Application\Model;

class UserModel
{
  public $id;
  public $name;
  public $color;

  public function exchangeArray($data)
  {
    $this->id = (isset($data['id']))? $data['id']:0;
    $this->name = (isset($data['name']))? $data['name']:"User None";
    $this->color = (isset($data['color']))? $data['color']:"";
  }

  public function exchangeToArray()
  {
    return array(
      'id' => $this->id,
      'name' => $this->name,
      'color' => $this->color,
    );
  }
}
