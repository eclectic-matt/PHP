<?php

namespace EclecticDiary;

class Diary{

  public $title;
  private $entries;
  private $nextId;

  public function __construct($title){
    $this->title = $title;
    $this->entries = array();
    $this->nextId = 0;
  }

  public function listEntries($limit=5){
    foreach($this->entries as $entry){
      echo "ID: " + $entry->id . "\n\r";
      echo "Title: " + $entry->title . "\n\r";
      echo "Text: " + $entry->text . "\n\r";
      echo "\n\r";
    }
  }

  public function addEntry($title, $text, $data=[]){
    $newEntry = new stdClass();
    $newEntry->id = $this->nextId;
    $this->nextId++;
    $newEntry->title = $title;
    $newEntry->text = $text;
    $newEntry->data = $data;
    $entry = new EclecticDiary\DiaryEntry($newEntry);
    $this->entries[$entry->id] = $entry;
  }

  public function removeEntry($id){
    unset($this->entries[$id]);
  }

  public function loadDiary($file){
    $savedFile = fopen($file);
    //LOAD DATA FROM JSON INTO DIARY OBJECT
  }

}

?>
