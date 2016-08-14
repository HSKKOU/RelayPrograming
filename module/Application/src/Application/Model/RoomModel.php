<?php
namespace Application\Model;

class RoomModel extends AModel
{
  public $modified;
  public $turn_user_id;

  public function exchangeArray($data)
  {
    $this->id = (isset($data['id']))? +$data['id']:0;
    $this->modified = (isset($data['modified']))? $data['modified']:"";
    $this->turn_user_id = (isset($data['turn_user_id']))? +$data['turn_user_id']:0;
  }

  public function exchangeToArray()
  {
    return array(
      'id' => $this->id,
      'modified' => $this->modified,
      'turn_user_id' => $this->turn_user_id,
    );
  }
}
