<?php
class Choice
{
    public $text;
}

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
function readCSV($csvFile, $array){
    $file_handle = fopen($csvFile, 'r');
    while (!feof($file_handle)) {
        $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
    }
    array_shift($line_of_text);
    array_pop($line_of_text);
    fclose($file_handle);
    return $line_of_text;
}

function writeCSV($csvFile, $array){
  $fp = fopen($csvFile, 'a');
  $item = [];
  $item[0] = $array->text;
  $item[1] = $array->createdAt;
  $item[2] = $array->choices[0]->text;
  $item[3] = $array->choices[1]->text;
  $item[4] = $array->choices[2]->text;
  fputcsv($fp, $item);
  fclose($fp);
}

function getData(){
  $path = '';
  $json = [];
  if(env('DATA_SOURCE')=="JSON"){
    $path = storage_path().env('JSON_PATH');
    $json = file_get_contents($path);
    $json = json_decode($json, true);
  } else{
    $path = storage_path().env('CSV_PATH');
    $temp = readCSV($path,array('delimiter' => "\n"));
    foreach ($temp as $item) {
      $item = explode(",",$item[0]);
      $choices = [];
      $choices[0] = new Choice();
      $choices[0]->text = $item[2];

      $choices[1] = new Choice();
      $choices[1]->text = $item[3];
      $choices[2] = new Choice();
      $choices[2]->text = $item[4];
      $dataToSend =  array(
          "text" => $item[0],
          "choices" => $choices
      );
      array_push($json,$dataToSend);
    }
  }
  return $json;
}
function writeData($data){
  $path = '';
  $json = '';
  if(env('DATA_SOURCE')=="JSON"){
    $path = storage_path().env('JSON_PATH');
    $json = file_get_contents($path);
    $json = json_decode($json, true);
    array_push($json, $data);
    $json = json_encode($json);
    file_put_contents($path, $json);
    $json = json_decode($json);
    foreach ($json as $item) {
      $item=json_decode(json_encode($item));
    }
  } else{
    $path = storage_path().env('CSV_PATH');
    $json = writeCSV($path,$data);
  }
  return $json;
}
