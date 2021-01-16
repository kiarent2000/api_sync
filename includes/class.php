<?php
class GetId{
	private $login;
	private $password;
	private $url;
	private $params;
	
	 public function __construct($login, $password, $url)
    {
        $this->login = $login;
		$this->password = md5($password);
		$this->url = $url;
		$this->params = array('login' => $this->login, 'password' => $this->password);
    }
	
	public function sid() {
		$result = file_get_contents($this->url, false, stream_context_create(array('http' => array('method'  => 'POST','header'  => 'Content-type: application/x-www-form-urlencoded','content' => http_build_query($this->params)))));
		$array = json_decode($result,true); 
		return  $array['result'];
		}

	}

class GetItems{
	private $url_category;
	private $offset;
	
	 public function __construct($url_category, $offset)
    {
        $this->url_category = $url_category;
		$this->offset = $offset;
			
    }
	
	public function counted() {
	 
		$array = json_decode(file_get_contents($this->url_category),true); 
		return (ceil(($array['result']['count'])/1000));
	
		}
		
		public function items() {
	 		return (json_decode(file_get_contents($this->url_category.'?offset='.$this->offset),true)); 
		}
		
		public function item() {
	 		return (json_decode(file_get_contents($this->url_category),true)); 
		}

	}
	
		
	

class GetParameters{
	private $parameters;
	
	
	 public function __construct($parameters)
    {
        $this->parameters = $parameters;
		$this->productID  = $parameters['productID'];
		$this->priceUSD  = $parameters['price'];
		$this->priceUAH  = $parameters['price_uah'];
		$this->price_recomended  = $parameters['recommendable_price'];
		$this->retail_price_uah  = $parameters['retail_price_uah'];
		$this->available  = $parameters['available'];
		$this->stocks_expected  = $parameters['stocks_expected'];
    }
	
	public function productID() {
	 		return ($this->productID);
		}
		
	public function priceUSD() {
	 		return ($this->priceUSD);
		}	
		
	public function priceUAH() {
	 		return ($this->priceUAH);
		}
		
	public function price_recomended() {
	 		return ($this->price_recomended);
		}
		
	public function retail_price_uah() {
	 		return ($this->retail_price_uah);
		}
		
	public function stock_status() {
			if((!empty($this->available))||(!empty($this->stocks_expected))):
				return 7;
			else:
				return 5;
			endif;
		}	
		
	public function quantity() {
		    if((empty($this->available))&&(!empty($this->stocks_expected)))
			{
				return 5;
			}
			if(!empty($this->available)):
			    $this->available = max($this->available);
				switch ($this->available) {
					case 1:
					return 5;
					break;
					case 2:
					return 20;
					break;
					case 3:
					return 50;
					break;
			}
			else:
					return 0;
					
			endif;
		}		
	}

class DataBase{
	private $sql;
	private $conn;
	
	
	public function __construct($conn)
    {
        $this->sql = 'SELECT product_id_brain from oc_product where last_mod !="'.date("Ymd").'"';
		$this->conn = $conn;
    }
	
	public function Products_id_brain() {
	 		$result = mysqli_query($this->conn, $this->sql);
			while($row = mysqli_fetch_array($result)){
				$products_id_brain[] = $row['product_id_brain'];
		}
		return ($products_id_brain);
		}
		
}	


class DataBaseNewItems{
	private $sql;
	private $conn;
	
	
	public function __construct($conn)
    {
        $this->sql = "SELECT product_brain FROM modnew";
		$this->conn = $conn;
    }
	
	public function Products_id_brain() {
	 		$result = mysqli_query($this->conn, $this->sql);
			while($row = mysqli_fetch_array($result)){
				$products_id_brain[] = $row['product_brain'];
		}
		return ($products_id_brain);
		}
		
}	



class DataBaseUpdate{
	private $sql;
	private $conn;
	private $stock_status_id;
	private $price;
	private $quantity;
	private $retail_price_uah;
	private $product_id_brain;
	private $main_category;
	private $status;
		
	public function __construct($conn, $product_id_brain, $stock_status_id, $price, $quantity, $retail_price_uah, $main_category)
    {
        
		$this->conn = $conn;
		$this->stock_status_id = $stock_status_id;
		$this->price=$price;
		$this->quantity=$quantity;
		$this->retail_price_uah=$retail_price_uah;
		$this->product_id_brain=$product_id_brain;
		$this->main_category = $main_category;
			if($this->stock_status_id==5):
			$this->quantity = 0;
			$this->status = 0;
			else:
			$this->quantity = 5;
			$this->status = 1;
			endif;
	}
	
	public function update() {
		$this->sql = 'UPDATE oc_product  SET status='.$this->status.', stock_status_id='.$this->stock_status_id.', price='.$this->price.', quantity='.$this->quantity.', retail_price_uah='.$this->retail_price_uah.', last_mod = "'.date("Ymd").'", main_category="'.$this->main_category.'"  where product_id_brain='.$this->product_id_brain;
	if(mysqli_query($this->conn, $this->sql)){
		return ("Обновление товара с кодом $this->product_id_brain выполнено"); 
	}}
	
	public function update_short() {
		if($this->stock_status_id==5):
			$this->quantity = 0;
			$this->status = 0;
			else:
			$this->quantity = 5;
			$this->status = 1;
			endif;
			
			
					
			
		$this->sql = 'UPDATE oc_product  SET status='.$this->status.', stock_status_id='.$this->stock_status_id.', price='.$this->price.', quantity='.$this->quantity.', retail_price_uah='.$this->retail_price_uah.', last_mod = "'.date("Ymd").'", main_category="'.$this->main_category.'"  where product_id_brain='.$this->product_id_brain;
	if(mysqli_query($this->conn, $this->sql)){
		return ("Обновление товара с кодом $this->product_id_brain выполнено"); 
	}
	
	}
				
				
	public function select() {
		$this->sql = 'SELECT product_id_brain, price, retail_price_uah, stock_status_id FROM oc_product  where main_category="'.$this->main_category.'"';
		$result = mysqli_query($this->conn, $this->sql);
		return $result; 
		}
		
}	
		
		
class DataBaseInsert{
	private $sql;
	private $conn;
	private $product_id_brain;
	
	
	public function __construct($conn, $product_id_brain)
    {
        
		$this->conn = $conn;
		$this->product_id_brain=$product_id_brain;
    }
	
	public function insert() {
		$this->sql = 'INSERT INTO modnew  SET product_brain='.$this->product_id_brain.', status=1';
	if(mysqli_query($this->conn, $this->sql)){
		return ("Обновление товара с кодом $this->product_id_brain выполнено"); 
	}
				}
		
}	

class DataBaseLogsStart{
	private $sql;
	private $conn;
	private $start_date;
	private $start;
	private $comment;
	
	public function __construct($conn, $comment)
    {
       $this->start_date = date('Y-m-d H:i:s');
	   $this->start = microtime(true);
	   $this->comment = $comment;
       $this->conn = $conn;
		
    }
	
	public function insert_start() {
		$this->sql = 'INSERT INTO logs  SET start_time="'.$this->start_date.'", comments = "'.$this->comment.'"';
		mysqli_query($this->conn, $this->sql);
		return mysqli_insert_id($this->conn); 
	
				}
	
		
}	


class DataBaseLogsEnd{
	private $sql;
	private $conn;
	private $duration;
	private $end;
	private $start;
	private $log_id_last;
	
	public function __construct($conn, $log_id_last, $start)
    {
       $this->start = $start;
	   $this->end_date = date('Y-m-d H:i:s');
	   $this->end = microtime(true); //конец измерения
	   $this->duration=ceil(($this->end - $this->start)/60);
	   $this->conn = $conn;
	   $this->log_id_last = $log_id_last;
		
	
    }
	
	public function insert_end() {
		$this->sql = 'UPDATE logs  SET end_time="'.$this->end_date.'", duration = "'.$this->duration.'" where id='.$this->log_id_last;
		mysqli_query($this->conn, $this->sql);
		return mysqli_insert_id($this->conn); 
	
				}
	
		
}	


