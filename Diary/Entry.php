<?php

namespace EclecticDiary;

class DiaryEntry {

  public $id;
  public $time;
  public $data;
  public $text;

  public function __construct($entry){
    $this->time = new DateTime();
    $this->id = $entry->id;
    $this->title = $entry->title;
    $this->data = $entry->data;
    $this->text = $entry->text;
  }

  public function updateTime($newDateTime){
    $this->time = $newDateTime;
  }

  public function updateData($newData){
    $this->data = $newData;
  }

  public function updateText($newText){
    $this->text = $newText;
  }

}

?>
