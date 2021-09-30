<?php

class Tolong
{
  private $dir;
  private $ids;
  private $store;
  
  public function __construct($store){
    $this->dir = $store->getStorePath();
    $this->ids = $this->dir."_ids";
    $this->store = $store;
  }
  
  private function ambilDataIDs(){
    $ids = file($this->ids);
    foreach ($ids as $value) {
      $ar = explode(' ',$value);
      $data[trim($ar[0])] = trim($ar[1]);
    }
    return $data;
  }
  
  private function ambilIDs(){
    $dataIDs = $this->ambilDataIDs();
    foreach ($dataIDs as $key=>$value) {
      $ids[] = $key;
    }
    return $ids;
  }
  
  public function ambilDataUser(){
    $tg = Bot::message();
    $id = $tg['from']['id'];
    $data = $this->ambilDataIDs();
    foreach ($data as $key=>$value) {
      if($id == $key){
        $hasil = trim($value);
        break;
      }
    }
    $hasil = isset($hasil) ? (int)$hasil : false;
    if(false === $hasil){
      throw new Exception('hasil false');
    }
    
    $data_user = json_decode(file_get_contents($this->dir."/data/$hasil.json"),true);
    
    return $data_user;
  }
  
  private function daftarkanPenggunaBaru($draft,$store){
    $tg = Bot::message();
    $id = $tg['from']['id'];
    $data_user['id'] = $id;
    $data_user['Pendaftar'] = $tg['from']['first_name'];
    foreach ($draft as $key=>$value) {
      $data_user[$key] = false;
    }
    $store->insert($data_user);
  }
  
  public function sesuaikanPenggunaBaru($draft,$store){
    
    $tg = Bot::message();
    $id = $tg['from']['id'];
    if(!file_exists($this->ids))file_put_contents($this->ids,"$id 1");
    $data = $this->ambilDataIDs();
    $ids = $this->ambilIDs();
    $ids = isset($ids) ? $ids : [];
    
    if(!file_exists($this->dir."/data/1.json")){
      $this->daftarkanPenggunaBaru($draft,$store);
    }
    
    if(!in_array($id,$ids)){
      $no = count(file($this->ids)) + 1;
      $this->daftarkanPenggunaBaru($draft,$store);
      file_put_contents($this->ids,"\n$id $no",FILE_APPEND | LOCK_EX);
    }
  }
  
  public static function ulangi($data_user,$draft,$store){
    foreach($draft as $key=>$value){
      $data_user[$key] = false;
    }
    $store->update($data_user);
  }
  
}