<?php
namespace Application\Model;

class UserModel extends AModel
{
  public $is_active;
  public $name;
  public $color;
  public $room_id;
  public $created_at;
  public $last_hb;

  public function exchangeArray($data)
  {
    $this->id = (isset($data['id']))? +$data['id']:0;
    $this->is_active = (isset($data['is_active']))? $data['is_active']:true;
    $this->name = (isset($data['name']))? $data['name']:"";
    $this->color = (isset($data['color']))? $data['color']:"";
    $this->room_id = (isset($data['room_id']))? +$data['room_id']:0;
    $this->created_at = (isset($data['created_at']))? +$data['created_at']:null;
    $this->last_hb = (isset($data['last_hb']))? +$data['last_hb']:null;
  }

  public function exchangeToArray()
  {
    return array(
      'id' => $this->id,
      'is_active' => $this->is_active,
      'name' => $this->name,
      'color' => $this->color,
      'room_id' => $this->room_id,
      'created_at' => $this->created_at,
      'last_hb' => $this->last_hb,
    );
  }
}
