<?php
include("config.php");
include("includes/connects.php");
include("includes/class.php");
$category_url = "http://api.brain.com.ua/products/1266/";  //адрес категории брейн
$comment = "Быстрое обновление категории Смартфоны";
$main_category = 5;
$log_id_last = (new DataBaseLogsStart($conn, $comment))->insert_start();
$sid = (new GetId(LOGIN, PASSWORD, URL_AUTH))->sid(); //получение идентификатора сессии
$count = (new GetItems($category_url.$sid, $offset))->counted(); //получение количества товаров в категории
$brain_ids = (new DataBase($conn))->Products_id_brain(); //получение массива id brain
while($count>0){
$array = (new GetItems($category_url.$sid, $offset))->items(); //получение позиций
foreach($array['result']['list'] as $parameters){ // получение данных каждой позиции
$parameter = new GetParameters($parameters);
$product_id_brain = $parameter->productID();  // id brain
$price_usd = $parameter->priceUSD();   // цена в долларах
$price_uah = $parameter->priceUAH();   // цена в гривнах
$price_recomended = $parameter->price_recomended();   // рекомендованная цена
$retail_price_uah = $parameter->retail_price_uah();   // розничная цена
$stock_status = $parameter->stock_status();   // розничная цена
$quantity = $parameter->quantity();   // розничная цена
//echo "$product_id_brain - $price_usd - $price_uah - $price_recomended - $retail_price_uah - $stock_status - $quantity <br> ";
$entry_brain = array("product_id_brain"=>$product_id_brain, "retail_price_uah"=>round($retail_price_uah, 2),  "stock_status"=>$stock_status,  "price"=>round($price_usd, 2));
$massiv_brain[]=serialize($entry_brain);
unset($parameter, $product_id_brain, $price_usd, $price_uah, $price_recomended, $retail_price_uah, $stock_status, $quantity);}// конец получение данных каждой позиции
$offset=$offset+1000;
$count--;}
$row = (new DataBaseUpdate($conn, 0, 0, 0, 0, 0, $main_category))->select();
while($select = mysqli_fetch_array($row)){
	//echo  $select['product_id_brain'].' - '.$select['price'].' - '. $select['retail_price_uah'] .' - '. $select['stock_status_id'] .'<br>';
	$entry_88 = array("product_id_brain"=>$select['product_id_brain'], "retail_price_uah"=>round($select['retail_price_uah'], 2),  "stock_status"=>intval($select['stock_status_id']),  "price"=>round($select['price'], 2));
	$massiv_88[]=serialize($entry_88);
}
$dif_mass = array_diff($massiv_88, $massiv_brain);
foreach($dif_mass as $dif_mass_entry)
{
	$dif_mass_new[] = unserialize($dif_mass_entry);
}

if(!empty($dif_mass_new)){
foreach($dif_mass_new as $dif_mass_new_entry)
{
	$item_url = 'http://api.brain.com.ua/product/'.$dif_mass_new_entry['product_id_brain'].'/'.$sid;
	$item = (new GetItems($item_url, 0))->item(); //получение позиций
	$parameter = new GetParameters($item['result']);
	$product_id_brain = $parameter->productID();  // id brain
	$price_usd = $parameter->priceUSD();   // цена в долларах
	$price_uah = $parameter->priceUAH();   // цена в гривнах
	$price_recomended = $parameter->price_recomended();   // рекомендованная цена
	$retail_price_uah = $parameter->retail_price_uah();   // розничная цена
	$stock_status = $parameter->stock_status();   // розничная цена
	$quantity = $parameter->quantity();   // розничная цена
	//echo "$product_id_brain - $price_usd - $price_uah - $price_recomended - $retail_price_uah - $stock_status - $quantity <br> ";
	$update = (new DataBaseUpdate($conn, $product_id_brain, $stock_status, $price_usd, $quantity, $retail_price_uah, $main_category))->update_short();
	echo $update.'<br>';
}}




	
$log_end = (new DataBaseLogsEnd($conn, $log_id_last, $start))->insert_end();
	
	