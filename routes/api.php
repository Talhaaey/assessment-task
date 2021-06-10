<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Config\JsonData;
use Stichoza\GoogleTranslate\GoogleTranslate;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/question', function (Request $request) {

  $data = getData();
  $array = [];
  $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
  $tr->setSource('en'); // Translate from English
  $tr->setTarget($request->input('lang'));
  foreach ($data as $item) {
    $item=json_decode(json_encode($item));
    $text=$item->text;
    $choices=$item->choices;
    // $choices=json_decode(json_encode($choices));
    $choicesList = [];
    foreach ($choices as $choiceItem) {
      $choiceItem->text = $tr->translate($choiceItem->text);
      array_push($choicesList,$choiceItem);
    }

    $text = $tr->translate($text);
    $dataToSend =  array(
        "text" => $text,
        "choices" => $choicesList,
    );
    array_push($array,$dataToSend);
  }
  return $array;
});

Route::post('/question', function (Request $request) {
  $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
  $tr->setSource(); // Translate from English
  $tr->setTarget('en');
  $requestData = json_decode($request->getContent());
  $requestData->createdAt = date("Y-m-d h:i:s");
  if(count($requestData->choices)!=3){
    return "Each question should have only 3 choices";
  }
  $requestData->text = $tr->translate($requestData->text);
  foreach($requestData->choices as $choice){
    $choice->text = $tr->translate($choice->text);
  }
  writeData($requestData);

  return [$requestData];
});
