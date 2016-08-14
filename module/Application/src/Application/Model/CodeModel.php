<?php
namespace Application\Model;

class CodeModel extends AModel
{
  public $user_id;
  public $room_id;
  public $code;
  public $created_at;

  public function exchangeArray($data)
  {
    $this->id = (isset($data['id']))? +$data['id']:0;
    $this->user_id = (isset($data['user_id']))? +$data['user_id']:0;
    $this->room_id = (isset($data['room_id']))? +$data['room_id']:0;
    $this->code = (isset($data['code']))? $data['code']:"";
    $this->created_at = (isset($data['created_at']))? $data['created_at']:"";
  }

  public function exchangeToArray()
  {
    return array(
      'id' => $this->id,
      'user_id' => $this->user_id,
      'room_id' => $this->room_id,
      'code' => $this->code,
      'created_at' => $this->created_at,
    );
  }

  public function exchangeToArrayWithoutCreatedAt()
  {
    return array(
      'id' => $this->id,
      'user_id' => $this->user_id,
      'room_id' => $this->room_id,
      'code' => $this->code,
    );
  }
}
