<?php

	# Establishing the connection to the Database
	function connect()
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "newhope";

		$connection = new mysqli($servername, $username, $password, $dbname);
	
		// Check connection
		if ($connection->connect_error) 
		{
		    return null;
		}
		else
		{
			return $connection;
		}
	}

	# Callback error messages
	function errors($type)
	{
		$header = "HTTP/1.1 ";

		switch($type)
		{
			case 306:	$header .= "306 Wrong Credentials";
						break;
			case 400:	$header .= "400 User Not Found";
						break;
			case 404:	$header .= "404 Request Not Found";
						break;
			case 409:	$header .= "409 Your action was not completed correctly, please try again later";
						break;
			case 412:	$header .= "412 Username already in use";
						break;
			case 417:	$header .= "417 No content set in the cookie/session";
						break;	
			case 500:	$header .= "500 Bad connection to Database";
						break;
			case 500:	$header .= "500 Bad connection to Database";
						break;
			case 350:	$header .= "Not enough stock";
						break;
			default:	$header .= "404 Request Not Found";
		}

		header($header);
		return array('status' => 'ERROR', 'code' => $type);
	}
	function obtainItemsForSale($sort, $sortField){
		# Open and validate the Database connection
    	$conn = connect();
    	if ($conn != null){
        	//Connection to database was successful
        	if($sort == 'ASC'){
        		if($sortField == 'price'){
        			$sql = "SELECT * FROM items ORDER BY price ASC";
        		}
        		else{
        			$sql = "SELECT * FROM items ORDER BY item ASC";
        		}
        		
        	}
        	else{
        		if($sortField == 'price'){
        			$sql = "SELECT * FROM items ORDER BY price DESC";
        		}
        		else{
        			$sql = "SELECT * FROM items ORDER BY item DESC";
        		}
        	}
        	$result = $conn->query($sql);
        	$itemsArray = array();
        	//Return all items in the query with inventory
			while($row = $result->fetch_assoc()) 
	    	{
				array_push($itemsArray,
					array(
						"item" => $row['item'],
						"price" => $row['price'], 
						"quantity" => $row['quantity'], 
						"description" => $row['description'],
						"developer" => $row['developer'],
						"image" => $row['img_loc']
					)
				);
			}
			$returnArray = array(
        		'status' => 'COMPLETE',
        		'action' => 'LOAD_MARKETPLACE',
				'data' => $itemsArray);

			return $returnArray;
        }
        else{
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
	}

	function modifyUserCart($orderId, $newStatus, $item, $quantity){
		$conn = connect();
		if ($conn != null){
			if($newStatus == 'B'){
				$stock = 0;
				$sql = "SELECT quantity FROM items WHERE item = '$item'";
				$result = $conn->query($sql);
				while($row = $result->fetch_assoc()) 
		    	{
					$stock = $row['quantity'];
				}
				if($stock >= $quantity){
					//There is available stock. Update stock...
					$newQuant = $stock - $quantity;
					
					$sql = "UPDATE items
					SET quantity='$newQuant'
					WHERE item='$item'";

					if (mysqli_query($conn, $sql)){
						//...And change order to bought
						$sql = "UPDATE orders
						SET status='$newStatus'
						WHERE orderId='$orderId'";

						if (mysqli_query($conn, $sql)){
				    		$conn->close();
						    return array("status" => "COMPLETE");
						} 
						else{
							$conn->close();
							return errors(409);
						}
					} 
					else{
						$conn->close();
						return errors(409);
					}
				}
				else{
					$conn->close();
					return array('status' => 'OUT_OF_STOCK');
				}
			}
			else{
				$sql = "UPDATE orders
						SET status='$newStatus'
						WHERE orderId='$orderId'";
				if (mysqli_query($conn, $sql)){
		    		$conn->close();
				    return array("status" => "COMPLETE");
				} 
				else{
					$conn->close();
					return errors(409);
				}
			}
		}
		else{
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
	}

	function obtainItemData($item){
		$conn = connect();

		if ($conn != null)
        {
        	$sql = "SELECT * FROM items WHERE item = '$item'";
			$result = $conn->query($sql);
			while($row = $result->fetch_assoc()) 
	    	{
				return array("status" => "COMPLETE", 
							 "item" => $row['item'], 
							 "price" => $row['price'], 
							 "quantity" => $row['quantity'],
							 "description" => $row['description'],
							 "developer" => $row['developer'],
							 "img_loc" => $row['img_loc']
				);
			}
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
	}

	function getUserData($user){
		$conn = connect();

		if ($conn != null)
        {
        	$sql = "SELECT * FROM users WHERE username = '$user'";
			$result = $conn->query($sql);
			while($row = $result->fetch_assoc()) 
	    	{
				return array("fName" => $row['fName'], 
							 "lName" => $row['lName'], 
							 "username" => $row['username'],
							 "email" => $row['email']
				);
			}
			return 0;
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
	}

	function updateCartDatabase(){
		# Open and validate the Database connection
		session_start();

		//Obtain userID from current session
    	$conn = connect();
    	$user = $_SESSION['userName'];

    	if ($conn != null){
    		//Get counter of orders from database
    		$sql = "SELECT COUNT(*) AS count FROM orders";
    		$result = $conn->query($sql);
    		$row = $result->fetch_assoc();

    		//Assign orderID for current order
    		$orderId = $row['count'] + 1;

    		//Retrieve data
    		$data = ( $_POST['data'] );
    		$sql = "";

    		foreach( $data as $currItem ){
    			$itemName = $currItem['item'];
    			$itemQuant = $currItem['quantity'];
    			$sql .= "INSERT INTO orders 
    					VALUES ('$orderId', '$user', '$itemName', '$itemQuant','P');";
    			$orderId += 1;
    		}
    		if ($conn->multi_query($sql)) 
	    	{
	    		$conn->close();
			    return array("status" => "COMPLETE");
			} 
			else 
			{
				$conn->close();
				return errors(409);
			}
    	}
    	else{
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
	}

	function userCartData($action){
		//Open and validate connection
		$conn = connect();
		if ($conn != null){
			//Successful connection
			//Obtain userName information
			session_start();
    		$user = $_SESSION['userName'];
    		//SQL query
    		if($action == 'LOAD_USER_CART'){
    			$sql = "SELECT orderId, item, quantity,status FROM orders 
						WHERE status='P' AND userName = '$user'";
    		}
    		else if($action == 'LOAD_PAST_ORDERS'){
    			$sql = "SELECT orderId, item, quantity,status FROM orders 
						WHERE userName = '$user' AND (status='B' OR status='C')";
    		}
			
			$result = $conn->query($sql);
        	$itemsArray = array();
        	//Return all items
        	while($row = $result->fetch_assoc()) 
	    	{
				array_push($itemsArray,
					array(
						"orderId" => $row['orderId'],
						"item" => $row['item'], 
						"quantity" => $row['quantity'],
						"orderStatus"=>$row['status'],
						"total" => obtainItemPrice($row['item'], $conn)*$row['quantity']
					)
				);
			}
			$returnArray = array(
        		'status' => 'COMPLETE',
        		'action' => $action,
				'data' => $itemsArray);

			return $returnArray;

		}
		else{
        	//Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }

	}
	function obtainItemPrice($itemName, $conn){
		$sql = "SELECT price FROM items WHERE item = '$itemName'";
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()) 
    	{
			return $row['price'];
		}
		return 0;
	}
	
	# Query to retrieve a user data
    function validateUserCredentials($userName)
    {
        # Open and validate the Database connection
    	$conn = connect();

        if ($conn != null)
        {
        	$sql = "SELECT * FROM users WHERE userName = '$userName'";
			$result = $conn->query($sql);
			
			# The current user exists
			if ($result->num_rows > 0)
			{
				while($row = $result->fetch_assoc()) 
		    	{
					$conn->close();
					return array("status" => "COMPLETE", "fName" => $row['fName'], "lName" => $row['lName'], "password" => $row['passwrd']);
				}
			}
			else
			{
				# The user doesn't exists in the Database
				$conn->close();
				return errors(400);
			}
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
    }

    # Query to find out if the user already exist in the Database
    function verifyUser($userName)
    {
    	# Open and validate the Database connection
    	$conn = connect();

        if ($conn != null)
        {
        	$sql = "SELECT * FROM users WHERE userName = '$userName'";
			$result = $conn->query($sql);

			if ($result->num_rows > 0)
			{
				# The current user already exists
				$conn->close();
				return errors(412);
			}
			else
			{
				$conn->close();
				return array("status" => "COMPLETE");
			}
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
    }

    # Query to insert a new user to the Database
    function registerNewUser($userFirstName, $userLastName, $userName, $email, $userPassword, $mentalCond)
    {
    	# Open and validate the Database connection
    	$conn = connect();

        if ($conn != null)
        {
        	$sql = "INSERT INTO users(fName, lName, username, email, passwrd, conditionM) 
        			VALUES ('$userFirstName', '$userLastName', '$userName', '$email', '$userPassword', '$mentalCond')";		
			if (mysqli_query($conn, $sql)) 
	    	{
	    		$conn->close();
			    return array("status" => "COMPLETE");
			} 
			else 
			{
				$conn->close();
				return errors(409);
			}
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
    }
	
	function loadComment(){
		$conn = connect();
		
		if($conn != null){
			$sql = "SELECT * FROM comments";
			$result = $conn->query($sql);
			
			if($result ->num_rows > 0){
				$response = array();
				while($row = $result->fetch_assoc()){
						array_push($response,
								array(
									"username" => $row['username'], 
									"userComment" => $row['userComment']
								)
							);
				}
				$conn->close();
				return $response;
			}
		}
		else{
			$conn -> close();
			return("Bad connection with the database");
			
		}
	}
	
	function pComment($userN, $theC){
		$conn = connect();
		
		if($conn != null){
			$sql = "INSERT INTO comments (username, userComment) VALUES ('$userN', '$theC')";
			
			if (mysqli_query($conn, $sql)){
	    		
				$response = array("statusTxt" => "SUCCESS", "userName" => $userN, "theComment" => $theC);
				$conn->close();
				return $response;
			} 
			else{
				$conn->close();
				return ("Your action was not completed correctly, please try again later");
			}
		}
		
		else{
			$conn->close();
        	return ("500 Bad connection to Database");
		}
	}
	
	function getMeetings(){
		$conn = connect();
		if($conn != null){
			$sql = "SELECT * FROM meetings";
			$result = $conn->query($sql);
			
			if($result ->num_rows > 0){
				
				$response = array();
				while($row = $result->fetch_assoc()){
						array_push($response,
								array(
									"meetName" => $row['meetName'], 
									"fecha" => $row['fecha'],
									"place" => $row['place'],
									"conditionM" => $row['conditionM'],
									"extrainfo" => $row['extrainfo']
								)
							);
				}
				$conn->close();
				return $response;
			}
		}
	}
	
	function AddMeeting($meetName, $thePlace, $theDate, $theExtra){
		$conn = connect();
		
		if($conn != null){
			$sql = "INSERT INTO meetings (meetName, fecha, place, conditionM, extrainfo) VALUES ('$meetName', '$theDate', '$thePlace', 'C', '$theExtra')";
			
			if (mysqli_query($conn, $sql)){
	    		
				$response = array("statusTxt" => "SUCCESS");
				$conn->close();
				return $response;
			} 
			else{
				$conn->close();
				return ("Your action was not completed correctly, please try again later");
			}
		}
		
		else{
			$conn->close();
        	return ("500 Bad connection to Database");
		}
	}
	
	function getOneMeet($meetName){
		$conn = connect();
		
		if($conn != null){
			$sql = "SELECT * FROM meetings WHERE meetName = '$meetName'";
			$result = $conn->query($sql);
			if($result ->num_rows > 0){
				$response = array();
				while($row = $result->fetch_assoc()){
					return array("meetName" => $row['meetName'], 
									"fecha" => $row['fecha'],
									"place" => $row['place'],
									"conditionM" => $row['conditionM'],
									"extrainfo" => $row['extrainfo']
									);
				}
			}
			else{
				$conn->close();
				return ("Your action was not completed correctly, please try again later");
			}
		}
	}
	
	function AddMyMeeting($user, $fMeetName, $fDate, $fPlace, $fExtraInfo){
		$conn = connect();
		
		if($conn != null){
			$sql = "INSERT INTO mymeetings (username, mname, fecha, place, extrainfo) VALUES ('$user', '$fMeetName', '$fDate', '$fPlace', '$fExtraInfo')";
		
			if (mysqli_query($conn, $sql)){
					
					$response = array("statusTxt" => "SUCCESS");
					$conn->close();
					return $response;
				} 
				else{
					$conn->close();
					return ("Your action was not completed correctly, please try again later");
				}
		}
	}
	
	function getMyMeeeting($username){
		$conn = connect();
		
		if($conn != null){
			$sql = "SELECT * FROM mymeetings WHERE username = '$username'";
			$result = $conn->query($sql);
			if($result ->num_rows > 0){
				$response = array();
				
				while($row = $result->fetch_assoc()){
					$return= array("mname" => $row['mname'], 
								 "fecha" => $row['fecha'],
								 "place" => $row['place'],
								 "extrainfo" => $row['extrainfo']
								 );
					array_push($response, $return);
				}
				return $response;
			}
			else{
				$conn->close();
				return ("Your action was not completed correctly, please try again later");
			}
		}
	}
?>
