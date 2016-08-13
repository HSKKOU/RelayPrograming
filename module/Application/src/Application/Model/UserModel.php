<?php
namespace Application\Model;

class UserModel extends AModel
{
  public $name;
  public $color;
  public $room_id;

  public function exchangeArray($data)
  {
    $this->id = (isset($data['id']))? +$data['id']:0;
    $this->name = (isset($data['name']))? $data['name']:"User None";
    $this->color = (isset($data['color']))? $data['color']:"";
    $this->room_id = (isset($data['room_id']))? +$data['room_id']:0;
  }

  public function exchangeToArray()
  {
    return array(
      'id' => $this->id,
      'name' => $this->name,
      'color' => $this->color,
      'room_id' => $this->room_id,
    );
  }
}
