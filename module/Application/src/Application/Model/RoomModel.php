<?php
namespace Application\Model;

class RoomModel extends AModel
{
  public $modified;
  public $turn_user_id;
  public $room_issue;
  public $room_lang;

  public function exchangeArray($data)
  {
    $this->id = (isset($data['id']))? +$data['id']:0;
    $this->modified = (isset($data['modified']))? $data['modified']:NULL;
    $this->turn_user_id = (isset($data['turn_user_id']))? +$data['turn_user_id']:0;
    $this->room_issue = (isset($data['room_issue']))? $data['room_issue']:"";
    $this->room_lang = (isset($data['room_lang']))? $data['room_lang']:"c";
  }

  public function exchangeToArray()
  {
    return array(
      'id' => $this->id,
      'modified' => $this->modified,
      'turn_user_id' => $this->turn_user_id,
      'room_issue' => $this->room_issue,
      'room_lang' => $this->room_lang,
    );
  }
}
