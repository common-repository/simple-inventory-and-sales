<?php 

function export_start()
{
	get_header();
	?>
		<h4>You can search by putting the customers last name in the text box below.<p>
		If you leave the text box empty you will get all sales records.<p></h4>
		<form action="<?php echo esc_attr(admin_url('admin-post.php')); ?>" method="post">
		<?php wp_nonce_field( 'simple_export', 'simple_nonce' );?>
		<input type="hidden" name="action" value="simple_data_export">
		<input type="text" name="keyName" placeholder="Search Term">
		<input type="submit" value="Submit">

		</form>

		<?php

}


function simple_data_export()
{
	$Lname = sanitize_text_field( $_POST['keyName'] );
	$Dload= plugin_dir_url( __FILE__ ) . 'download.php';
	echo "<html><body style='background-color: white;'>";
	get_header();


	if (
         ! isset( $_POST['simple_nonce'] )
         || ! wp_verify_nonce( $_POST['simple_nonce'], 'simple_export' )
        ) {

        print 'Sorry, your nonce did not verify.';
        exit;
        } else {

	?>
		<div class="container">
		<h4>Click on the Export button below and you will<p>download a CSV file that you can open in Excel.</h4>
		<?php echo "<form method='post' action='$Dload'>";
	?>
		<input type='submit' value='Export' name='Export'>
		<p> 
		<table border='1' style='border-collapse:collapse;'>
		<tr>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Address</th>
		<th>City</th>
		<th>State</th>
		<th>Zip Code</th>
		<th>Telephone</th>
		<th>Email</th>
		<th>Sale_ID</th>
		<th>Product_ID</th>
		<th>Shipping</th>
		<th>Payment</th>
		<th>Payment_fee</th>
		<th>Date</th>
		</tr>
		<?php 


		global $wpdb;
	global $simple_db_version;

	$results = $wpdb->get_results("SELECT * FROM wp_simple_sales where l_name LIKE '%$Lname%'", ARRAY_A);
	if ( !empty( $results ) ) {

		$user_arr = array();
		$user_arr[0] = array('First Name','Last Name','Address','City','State','Zip Code','Telephone','Email','Sale_ID','Product_ID','Shipping','Payment','Payment_fee','Date');
		foreach ($results as $row){
			$First = $row['f_name'];
			$Last = $row['l_name'];
			$Address = $row['address'];
			$City = $row['city'];
			$State = $row['state'];
			$Zip = $row['zip'];
			$Tele = $row['tele'];
			$Email = $row['email'];
			$Sale_ID = $row['sale_id'];
			$Product_ID = $row['product_id'];
			$Shipping = $row['shipping'];
			$Payment = $row['payment'];
			$Payment_fee = $row['payment_fee'];
			$Date = $row['date'];
			$user_arr[] = array($First,$Last,$Address,$City,$State,$Zip,$Tele,$Email,$Sale_ID,$Product_ID,$Shipping,$Payment,$Payment_fee,$Date);
			?>
				<tr>
				<td><?php echo $First; ?></td>
				<td><?php echo $Last; ?></td>
				<td><?php echo $Address; ?></td>
				<td><?php echo $City; ?></td>
				<td><?php echo $State; ?></td>
				<td><?php echo $Zip; ?></td>
				<td><?php echo $Tele; ?></td>
				<td><?php echo $Email; ?></td>
				<td><?php echo $Sale_ID; ?></td>
				<td><?php echo $Product_ID; ?></td>
				<td><?php echo $Shipping; ?></td>
				<td><?php echo $Payment; ?></td>
				<td><?php echo $Payment_fee; ?></td>
				<td><?php echo $Date; ?></td>
				</tr>
				<?php
		}
		?>
			</table>
			<?php 
			$serialize_user_arr = serialize($user_arr);
		?>
			<textarea name='export_data' style='display: none;'><?php echo $serialize_user_arr; ?></textarea> 
			</form>
			</div>

			<?php

	}
}
}

add_action( 'admin_post_simple_data_export', 'simple_data_export' );

if ( isset( $_POST['ProdSubmitButton'] ) ) {
	add_action( 'init', 'ProdPostData' );
}

// Function to Insert Data into Product DB

function prod_install_data($desc,$prod_name,$quantity,$cost,$shipping,$list,$image_name,$date) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'simple_product';

	$result=$wpdb->insert(
			$table_name,
			array(
				'description' => $desc,
				'product_name' => $prod_name,
				'quantity_in_stock' => $quantity,
				'cost_per' => $cost,
				'shipping' => $shipping,
				'list_price' => $list,
				'image_path' => $image_name,
				'receive_date' => $date,
			     )
		     );

	if ($result === false) {echo "Operation Failed";}  // Fail -- the "===" operator compares type as well as value
        if ($result === 0) {echo "No rows were updated.";} // Success, but no rows were updated
        if ($result > 0) { echo "Operation was successful.";} // Success, and updates were done. $result is the number of affected rows.

}



function ProdPostData() {

	$desc=sanitize_text_field($_POST['description']);
	$prod_name=sanitize_text_field($_POST['prod_name']);
	$quantity=sanitize_text_field($_POST['quantity']);
	$cost=sanitize_text_field($_POST['cost']);
	$shipping=sanitize_text_field($_POST['shipping']);
	$list=sanitize_text_field($_POST['list']);
	$image_name=sanitize_text_field($_POST['image_name']);
	$date=sanitize_text_field($_POST['date']);

	// 	print_r($_POST);


	if (empty($desc)) {
		echo "<h3>Description is required.<br>";
		echo "<a href='javascript: history.back()'>Go Back</a>";
		exit;
	}

	if (empty($prod_name)) {
		echo "<h3>Product name required.<br>";
		echo "<a href='javascript: history.back()'>Go Back</a>";
		exit;
	}
	if (empty($quantity)) {
		echo "<h3>Quantity  is required.<br>";
		echo "<a href='javascript: history.back()'>Go Back</a>";
		exit;
	}

	if (empty($cost)) {
		echo "<h3>Cost is required.<br>";

		echo "<a href='javascript: history.back()'>Go Back</a>";
		exit;
	}

	if (empty($list)) {
		echo "<h3>List Price is required.<br>";
		echo "<a href='javascript: history.back()'>Go Back</a>";
		exit;
	}

	if ( isset( $_POST['ProdSubmitButton'] ) ) {


		// Insert Data into Product DB
	prod_install_data($desc,$prod_name,$quantity,$cost,$shipping,$list,$image_name,$date);




	}
}


function search_tables($atts) {
	global $wpdb;
	global $simple_db_version;

	$choice = $atts['choice'];
	$i_width = $atts['i_width'];
	$i_height = $atts['i_height'];
	$i_border = $atts['i_border'];
	if ($choice=='customer') {

		$results = $wpdb->get_results("SELECT * FROM wp_simple_customer");
		if ( !empty( $results ) ) {

			echo "<table width='100%' border='0'>"; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
			echo "<tbody>";      
			foreach($results as $row){   
				$First = $row->f_name;               //putting the user_ip field value in variable to use it later in update query
				echo "<tr>";                           // Adding rows of table inside foreach loop
				echo "<th>First Name</th>" . "<td>" . $row->f_name . "</td>";
				echo "</tr>";
				echo "<td colspan='2'><hr size='1'></td>";
				echo "<tr>";                           // Adding rows of table inside foreach loop
				echo "<th>Last Name</th>" . "<td>" . $row->l_name . "</td>";
				echo "</tr>";
				echo "<td colspan='2'><hr size='1'></td>";
				echo "<tr>";                           // Adding rows of table inside foreach loop
				echo "<th>State</th>" . "<td>" . $row->state . "</td>";
				echo "</tr>";
				echo "<td colspan='2'><hr size='1'></td>";
				echo "<tr>";        
				echo "<th>City</th>" . "<td>" . $row->city . "</td>";   //fetching data from user_ip field
				echo "</tr>";
				echo "<td colspan='2'><hr size='1'></td>";
				echo "<tr>";        
				echo "<th>Tele</th>" . "<td>" . $row->tele . "</td>";
				echo "</tr>";
				echo "<td colspan='2'><hr size='1'></td>";
				echo "<tr>";        
				echo "<th>Zip</th>" . "<td>" . $row->zip . "</td>";
				echo "</tr>";
				echo "<td colspan='2'><hr size='1'></td>";
				echo "<tr>";
				echo "<tr>";
				echo "<tr>";
			}
			echo "</tbody>";
			echo "</table>";

		}



	}
	if ($choice=='product') {

		$results = $wpdb->get_results("SELECT * FROM wp_simple_product");
		if ( !empty( $results ) ) {

			foreach($results as $row){
				$product_name=stripslashes($row->product_name);
				echo "<hr style='height:2px;border-width:0;color:gray;background-color:gray'>";
				echo "<table width='100%' border='0'>"; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
				echo "<tbody>";
				echo "<tr>";                           // Adding rows of table inside foreach loop
				echo "<th><img src='$row->image_path' alt='$product_name' Title='$product_name' style='width:$i_width;height:$i_height; border:$i_border'></th>";
				echo "</tr>";
				echo "<td colspan='2'></td>";
				echo "<tr>"; 
				echo "<th>Product ID </th>" . "<td>" . $row->product_id . "</td>";
				echo "</tr>";
				echo "<td colspan='2'><hr size='1'></td>";
				echo "<tr>";                           // Adding rows of table inside foreach loop
				echo "<th>Description</th>" . "<td>" . $row->description . "</td>";
				echo "</tr>";
				echo "<td colspan='2'><hr size='1'></td>";
				echo "<tr>";                           // Adding rows of table inside foreach loop
				echo "<th>Product Name</th>" . "<td>" . $product_name . "</td>";
				echo "</tr>";
				echo "<td colspan='2'><hr size='1'></td>";
				echo "<tr>";
				echo "<th>List Price</th>" . "<td>" . $row->list_price . "</td>";   //fetching data from user_ip field
				echo "</tr>";
				echo "<tr><td colspan='2'><hr size='1'></td>";
				echo "<tr>";
				echo "<th>Shipping</th>" . "<td>" . $row->shipping . "</td>";   //fetching data from user_ip field
				echo "</tr>";
				echo "</tbody>";
				echo "</table>";
			}

		}
	}
}

function check_role () {
                if( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $roles = ( array ) $user->roles;
        return  $roles[0];
        } else {
 }
}



function display_product_scodes($atts) {

	$id = $atts['id'];
//	$simple_request = $atts['request'];
	$i_width = $atts['i_width'];
	$i_height = $atts['i_height'];
	$i_border = $atts['i_border'];
	$upload = wp_upload_dir();
	$upload_dir = $upload['baseurl'];

	global $wpdb;
	global $simple_db_version;

	$results = $wpdb->get_results("SELECT * FROM wp_simple_product WHERE product_id='$id'");
	if ( !empty( $results ) ) {
		foreach($results as $row){   
			$product_name=$row->product_name;
			$list=$row->list_price;
			$shipping=$row->shipping;
			echo "<img src='$row->image_path' alt='$product_name' Title='$product_name' style='width:$i_width;height:$i_height; border:$i_border'> <p>";

			echo "<table style='border: 0px solid black'>";

			echo "<tr><td>"; 
			echo "<b>";
			echo stripslashes($row->product_name);
			echo "<br>";
			echo stripslashes($row->description); 
			echo "<p>";
			echo "List: " .$list;
			echo "<p>";
			echo "Shipping: " . $shipping; 
			echo "</b>";
			echo "</td><tr></table>";

			?>
	<?php

// Admins can't purchase.
$answer=check_role();
if ($answer!=='administrator'){

?>

                                <form action="<?php echo esc_attr(admin_url('admin-post.php')); ?>" method="get">
                                <?php wp_nonce_field( 'simple_sale', 'simple_nonce' ); ?>
                                <?php
                                echo"<input type='hidden' name='action' value='sales_next'>
                                <input type='submit' name='Purchase' value='Purchase'/>
                                <input type='hidden' name='Product_id' value='$id'>
                                <input type='hidden' name='List' value=$list>
                                <input type='hidden' name='Shipping' value=$shipping>
                                <input type='hidden' name='Product Name' value='$product_name'>
                                </form>";
			echo '<hr style="height:6px;border-width:0;color:gray;background-color:gray">';
}else{
echo "<center><b>Admins cannot purchase from this page.</b></center>";
}



		}
	}

}

function admin_search_prods($atts) {
	echo "<table style='border: 1px solid black'>";
	echo " <tr><td>";

	?>
		<form action="<?php echo esc_attr(admin_url('admin-post.php')); ?>" method="post">
		<?php wp_nonce_field( 'simple_search', 'simple_nonce' );?>
		<input type="hidden" name="action" value="admin_browse_prods">
		<input type="text" name="keyName" placeholder="Search Term">
		<input type="submit" value="Submit">
		</form>

		<?php
		echo "</td></tr>";
	echo "<td> </td>";
	echo "</td></tr></table>";
}





function search_prods($atts) {
	echo "<table style='border: 1px solid black'>";
	echo " <tr><td>";

	?>
		<form action="<?php echo esc_attr(admin_url('admin-post.php')); ?>" method="post">
		<input type="hidden" name="action" value="browse_prods">
		<input type="text" name="keyName" placeholder="Search Term">
		<input type="submit" value="Submit">
		</form>

		<?php
		echo "</td></tr>";
	echo "<td> </td>";
	echo "</td></tr></table>";
}


add_action( 'admin_post_admin_browse_prods', 'simple_inv_admin_browse_prods' );
//this next action version allows users not logged in to submit requests
//if you want to have both logged in and not logged in users submitting, you have to add both actions!
add_action( 'admin_post_nopriv_browse_prods', 'simple_inv_browse_prods' );


function simple_inv_admin_browse_prods() {
	$simple_prod_search= sanitize_text_field("{$_REQUEST['keyName']}");

	do_action('go_simple_check_username');

	get_header();
	echo "<p>";


	//Begin Inventory Management
	global $simple_db_version;
	$simple_db_version = '1.0';
	echo "<html><body style='background-color: white;'>";
 	if (
         ! isset( $_POST['simple_nonce'] )
          || ! wp_verify_nonce( $_POST['simple_nonce'], 'simple_search' )
        ) {

        print 'Sorry, your nonce did not verify.';
        exit;

        } else {

	global $wpdb;
	global $simple_db_version;

	$results = $wpdb->get_results("select * from wp_simple_product WHERE product_name Like '%$simple_prod_search%'");

	if ( !empty( $results ) ) {

		foreach($results as $row){
			echo "<center><table style = 'width:90%; border:0px'><tr style= 'padding: 25px'><td style='padding:35px'>";
			echo "<table style='width:60%; border: 0px solid black'><tr style= 'padding: 25px'><td style='padding:35px'>";
			echo "<h4 style='color:dark grey;'>";
			$product_name=stripslashes($row->product_name); // Used in Image tags.
			$product_id=stripslashes($row->product_id); // Used in manage_prod.
			echo "Product ID:  " .  $row->product_id;
			echo "<p>";
			echo "<img src='$row->image_path' alt='$product_name' Title='$product_name' style='max-width:300px;max-height:450px;'>";
			echo "<p>";
			echo "Name:  " .  stripslashes($row->product_name);
			echo "<p>";
			echo "Description:  " .  stripslashes($row->description);
			echo "<p>";
			echo "Quantity:  " .  $row->quantity_in_stock;
			echo "<p>";
			echo "Cost:  " .  $row->cost_per;
			echo "<p>";
			echo "List:  " .  $row->list_price;
			echo "<p>";
			echo "Shipping:  " .  $row->shipping;
			echo "<p>";
			echo "Receive Date:  " .  $row->receive_date;
			echo "<p>";
			echo "</h4>";
			echo "<hr>";

			echo "<form action='admin-post.php'>";?>
			<?php wp_nonce_field( 'simple_edit_confirm', 'simple_nonce' );?>
		<?php echo "<input type='hidden' name='action' value='manage_prod'>
				<input type='submit' name='Edit' value='Edit'/>
				<input type='submit' name='Delete_Choice' value='Delete'/>
				<input type='hidden' name='Product_id' value='$product_id'>
				<input type='hidden' name='Product_name' value='$product_name'>
				</form method='post'> ";

			echo "<hr>";
			echo "</table></table>";

		}
	}
	else{
		echo "No Results";
		echo "<p>";
		?>
			<button onclick="goBack()">Go Back</button>

			<script>
			function goBack() {
				window.history.back();
			}
		</script>
			<?php
	}

	echo "</body></html>";
}
}
add_action('admin_post_manage_prod','manage_prod');

function manage_prod()
{
	$user = wp_get_current_user();
	$allowed_roles = array( 'editor', 'administrator');
	if ( array_intersect( $allowed_roles, $user->roles ) ) {
		// Stuff here for allowed roles

		get_header();
		?>
			<html><head><body style='background-color: white;'>
<?php		if (
         ! isset( $_GET['simple_nonce'] )
          || ! wp_verify_nonce( $_GET['simple_nonce'], 'simple_edit_confirm' )
        ) {

        print 'Sorry, your nonce did not verify.';
        exit;

        } else { ?>

			<?php
			echo "<center><table style = 'width:90%; border:0px'><tr style= 'padding: 25px'><td style='padding:35px'>";
		echo "<table style='width:60%; border: 0px solid black'><tr style= 'padding: 25px'><td style='padding:35px'>";
		echo "<h4 style='color:dark grey;'>";

		$product_id=sanitize_text_field($_GET['Product_id']);
		$product_name=sanitize_text_field($_GET['Product_name']);
		$Delete_Choice=sanitize_text_field($_GET['Delete_Choice']);

		if ($Delete_Choice == 'Delete') 
		{ 
			echo "Delete entire record of $product_name / Product ID: $product_id?";

			?>
				<form <?php echo esc_attr(admin_url('admin-post.php')); ?>" method="post">
				<?php wp_nonce_field( 'simple_del_confirm', 'simple_nonce' );?> 
				<input type='hidden' name='action' value='delete_next'>
				<input type='hidden' name='Product_id' <?php echo "value = $product_id"; ?>>
				<input type="checkbox" id="confirm" name="confirm" value="Yes">
				<label for="confirm"> Continue with Deletion</label><br>
				<input type=submit value='Delete'>
				</form >
				</table></table>
				<?php

				exit;

		}

		global $wpdb;
		global $simple_db_version;

		$results = $wpdb->get_results("select * from wp_simple_product WHERE product_id='$product_id'");

		if ( !empty( $results ) ) {

			foreach($results as $row){
				echo "<center><table style = 'width:80%; border:0px'><tr style= 'padding: 0px'><td style='padding:0px'>";
				echo "<table style='width:70%; border: 0px solid black'><tr style= 'padding: 0px'><td style='padding:0px'>";
				$product_name=stripslashes($row->product_name); // Used in Image tags.
				$product_id=stripslashes($row->product_id); // Used in Purchase routine.
				$list=stripslashes($row->list_price); // Used in Purchase routine.
				$description=stripslashes($row->description); // Used in Purchase routine.
				$quantity=stripslashes($row->quantity_in_stock); // Used in Purchase routine.
				$cost=stripslashes($row->cost_per); // Used in Purchase routine.
				$shipping=stripslashes($row->shipping); // Used in Purchase routine.
				$image_path=stripslashes($row->image_path); // Used in Purchase routine.
				$receive_date=stripslashes($row->receive_date); // Used in Purchase routine.
				echo "<img src='$row->image_path' alt='$product_name' Title='$product_name' style='max-width:300px;max-height:450px'>";
				echo "<p>";
				echo "</table>";
				echo "</table>";
				?>
					<section id="SimpleProd-wrapper">
					<form action="<?php echo esc_attr(admin_url('admin-post.php')); ?>" method="post">
					<input type='hidden' name='action' value='edit_next'>
					<?php wp_nonce_field( 'simple_save_confirm', 'simple_nonce' );?>
					<?php
					echo "<input type='hidden' name='Product_id' value='$product_id'>";
				?>
					<left>
					<table style='width:80%; border: 0px solid black'>
					<tr valign="top">
					<td width="" >
					<p>Description (max 60 char)</p>
					</td>
					<td width="">
					<p>
					<?php
					echo "<input type=text style=' text-align: center; font-size: 18pt;' name=description value = '$description' maxlength = '60'>";
				?>

					</p>
					</td>
					</tr>
					<tr valign="top">
					<td width="">
					<p>Product Name (max 30 char)</p>
					</td>
					<td width="">
					<p>
					<?php
					echo "<input type=text style=' text-align: center; font-size: 18pt;' name=prod_name value = '$product_name' maxlength = '30'>";
				?>

					<br/>
					</p>
					</td>
					</tr>
					<tr valign="top">
					<td width="">
					<p>Quantity *</p>
					</td>
					<td width="">
					<p>
					<?php
					echo "<input type=int style=' text-align: center; font-size: 18pt;' name=quantity value = '$quantity'>";
				?>
					<br/>
					</p>
					</td>
					</tr>
					<tr valign="top">
					<td width="">
					<p>Cost</p>
					</td>
					<td width="">
					<p>
					<?php
					echo "<input type=int style=' text-align: center; font-size: 18pt;' name=cost value = '$cost'>";
				?>
					<br/>
					</p>
					</td>
					</tr>
					<tr valign="top">
					<td width="">
					<p>List Price
					</p>
					</td>
					<td width="">
					<p>
					<?php
					echo "<input type=int style=' text-align: center; font-size: 18pt;' name=list value = '$list'>";
				?>
					<br/>
					</p>
					</td>
					</tr>
					<tr valign="top">
                                        <td width="">
                                        <p>Shipping
                                        </p>
                                        </td>
                                        <td width="">
                                        <p>
                                        <?php
                                        echo "<input type=int style=' text-align: center; font-size: 18pt;' name=shipping = '$shipping'>";
                                ?>
                                        <br/>
                                        </p>
                                        </td>
                                        </tr>

					<tr valign="top">
					<td width="">
					<p>Image name</p>
					</td>
					<td width="">
					<p>
					<?php
					echo "<input type=text style=' text-align: center; font-size: 18pt;' name=image_name value = '$image_path'>";
				?>
					<br/>
					</p>
					</td>


					<tr valign="top">
					<td width="">
					<p>Recieve Date</p>
					</td>
					<td width="">
					<p>
					<?php
					echo "<input type=date style=' text-align: center; font-size: 18pt;' name=date value = '$receive_date'>" ;
				?>
					<br/>
					</p>
					</td>
					</tr>
					</table>

					<input type=submit value='Save'>
					<p>
					<p>
					</form>
					</body></html>
					<?php
			}
		}







	}
}
}
add_action('admin_post_edit_next','edit_next');
add_action('admin_post_delete_next','delete_next');

function edit_next()
{

	//echo "Edit Next";
	$product_id=sanitize_text_field($_POST['Product_id']);
	$desc=sanitize_text_field($_POST['description']);
	$prod_name=sanitize_text_field($_POST['prod_name']);
	$quantity=sanitize_text_field($_POST['quantity']);
	$cost=sanitize_text_field($_POST['cost']);
	$shipping=sanitize_text_field($_POST['shipping']);
	$list=sanitize_text_field($_POST['list']);
	$image_name=sanitize_text_field($_POST['image_name']);
	$date=sanitize_text_field($_POST['date']);

	//Update the Prod DB
	get_header();
	
if (
         ! isset( $_POST['simple_nonce'] )
          || ! wp_verify_nonce( $_POST['simple_nonce'], 'simple_save_confirm' )
        ) {

        print 'Sorry, your nonce did not verify.';
        exit;

        } else {

	
	global $wpdb;
	$table_name = $wpdb->prefix . 'simple_product';

	$result = $wpdb->update(
			$table_name,
			array(
				'description' => $desc,
				'product_name' => $prod_name,
				'quantity_in_stock' => $quantity,
				'cost_per' => $cost,
				'shipping' => $shipping,
				'list_price' => $list,
				'image_path' => $image_name,
				'receive_date' => $date,
			     ),

			array(
				'product_id' =>$product_id,
			     )
			);
	echo "<html><body style='background-color: white;'>";
	echo "<table style = 'width:90%; border:0px'><tr style= 'padding: 25px'><td style='padding:35px'>";
	echo "<table style='width:60%; border: 0px solid black'><tr style= 'padding: 25px'><td style='padding:35px'>";
	echo "<h4 style='color:dark grey;'>";

	if ($result === false) {echo "Operation Failed";}  // Fail -- the "===" operator compares type as well as value
	if ($result === 0) {echo "No rows were updated.";} // Success, but no rows were updated
	if ($result > 0) { echo "Operation was successful.";} // Success, and updates were done. $result is the number of affected rows.
	echo "<p><p>";
	echo "<a href='/'>Home</a>";
	echo "</table></table>";
}
}

function delete_next()
{
	get_header();
	//print_r($_POST);
	//print_r($_GET);
	echo "<html><body style='background-color: white;'>";
  
	if (
   	 ! isset( $_POST['simple_nonce'] )
  	  || ! wp_verify_nonce( $_POST['simple_nonce'], 'simple_del_confirm' )
 	) {

   	print 'Sorry, your nonce did not verify.';
  	exit;

	} else {



	$confirm=sanitize_text_field($_POST['confirm']);
	$product_id=sanitize_text_field($_POST['Product_id']);
	echo "<center><table style = 'width:90%; border:0px'><tr style= 'padding: 25px'><td style='padding:35px'>";
	echo "<table style='width:60%; border: 0px solid black'><tr style= 'padding: 25px'><td style='padding:35px'>";
	echo "<h4 style='color:dark grey;'>";

	if ($confirm=='Yes')
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'simple_product';
		$result = $wpdb->delete(
				$table_name, 
				array('product_id' => $product_id) 
				);
		if ($result === false) {echo "<b>Operation Failed</b>";}  // Fail -- the "===" operator compares type as well as value
		if ($result === 0) {echo "<b>No rows were updated.</b>";} // Success, but no rows were updated
		if ($result > 0) { echo "<b>Operation was successful.</b>";} // Success, and updates were done. $result is the number of affected rows.
	}
}
	echo "<p><p><a href='/'>Home</a>";
	echo "</table></table>";
}

//StandardSearch
function simple_inv_browse_prods() {
	$simple_prod_search= sanitize_text_field("{$_REQUEST['keyName']}");

	echo "<p>";
	//echo "Here";

	global $simple_db_version;
	$simple_db_version = '1.0';

	echo "<html><body style='background-color: white;'>";
	echo "<head>";
	echo "</head><body>";
	get_header();


	global $wpdb;
	global $simple_db_version;

	$results = $wpdb->get_results("select * from wp_simple_product WHERE product_name Like '%$simple_prod_search%'");

	if ( !empty( $results ) ) {

		foreach($results as $row){
			echo "<center><table style = 'width:90%; border:0px'><tr style= 'padding: 25px'><td style='padding:35px'>";
			echo "<table style='width:60%; border: 0px solid black'><tr style= 'padding: 25px'><td style='padding:35px'>";
			$product_name=stripslashes($row->product_name); // Used in Image tags.
			$product_id=stripslashes($row->product_id); // Used in Purchase routine.
			$list=stripslashes($row->list_price); // Used in Purchase routine.
			$shipping=stripslashes($row->shipping); // Used in Purchase routine.
			$quantity=stripslashes($row->quantity_in_stock); // Used in Purchase routine.
			echo "<img src='$row->image_path' alt='$product_name' Title='$product_name' style='max-width:300px;max-height:450px'>";
			echo "<p>";
			//echo $product_id;

			echo "<form action='admin-post.php'>";?>
				<?php wp_nonce_field( 'simple_sale', 'simple_nonce' ); ?>
				<?php echo "<input type='hidden' name='action' value='sales_next'>
				<input type='submit' name='Purchase' value='Purchase'/>
				<input type='hidden' name='Product_id' value='$product_id'>
				<input type='hidden' name='List' value='$list'>
				<input type='hidden' name='Shipping' value='$shipping'>
				<input type='hidden' name='Product Name' value='$product_name'>
				<input type='hidden' name='Quantity' value='$quantity'>
				</form method='post'> ";


			echo "<p>";
			echo "Name:  " .  stripslashes($row->product_name);
			echo "<p>";
			echo "Description:  " .  stripslashes($row->description);
			echo "<p>";
			echo "List:  " .  $row->list_price;
			echo "<p>";
			echo "Shipping:  " .  $row->shipping;
			echo "<p>";
			echo "</h4>";
			echo "<hr>";
			echo "<hr>";
			echo "</td></tr></table>";
			echo "</td></tr></table>";
			echo "</body></html>";

		}
	}else{
		echo "No Results";
		echo "<p>";
		?>
			<button onclick="goBack()">Go Back</button>

			<script>
			function goBack() {
				window.history.back();
			}
		</script>
			<?php
	}
	get_footer();
	echo "</body></html>";
}



//Function to retrieve admin email account from plugin settings
function get_settings_data() {
	$get_variable = get_field('admin_email_account', 'option');
	return $get_variable;
}

add_action('admin_post_nopriv_sales_next','sale_next');

//Sales Next



function add_styles() {
	wp_enqueue_style( 'fontawesome-style', get_theme_file_uri( '/css/blocks.css' ), array(), null );
}
add_action( 'wp_enqueue_scripts', 'add_styles' );



function sale_next(){
//print_r($_POST);

	if (
         ! isset( $_GET['simple_nonce'] )
          || ! wp_verify_nonce( $_GET['simple_nonce'], 'simple_sale' )
        ) {

        print 'Sorry, your nonce did not verify.';
        exit;

        } else {
	$product_id=sanitize_text_field($_GET['Product_id']);
	$list=sanitize_text_field($_GET['List']);
	$shipping=sanitize_text_field($_GET['Shipping']);
	$product_name=sanitize_text_field($_GET['Product_Name']);

	echo "<html><head>";
	?>
		<?php
		echo "</head>";
	get_header();
	echo "<body style='background-color: white;'>";
	$On_the_mobile=wp_is_mobile();

	if ($On_the_mobile==1) {
	echo "<center><table style = 'width:90%; border:0px'><tr style= 'padding: 25px'><td style='padding:35px'>";
	}else{
	echo "<center><table style = 'width:90%; border:0px'><tr style= 'padding: 25px'><td style='padding:35px'>";
	echo "<table style='width:60%; border: 0px solid black'><tr style= 'padding: 25px'><td style='padding:35px'>";
	}

	echo "<tr><td>";
	echo "<center>";
	echo "<tr><td>";
	echo "<left>";
	echo "<b>You are purchasing $product_name for $$list and shipping $$shipping";

	echo "<p>";
	echo "<form action='admin-post.php'>
		<input type='hidden' name='action' value='pay_next'>
		<input type='hidden' name='Product_id' value='$product_id'>
		<input type='hidden' name='List' value='$list'>
		<input type='hidden' name='Shipping' value='$shipping'>
		<input type='hidden' name='Product Name' value='$product_name'>"; ?>
		<?php wp_nonce_field( 'simple_sale_continue', 'simple_nonce' );?>
		<h4>Please give use your name, shipping and email address for our records.</h4>
		<table border="0" cellpadding="1" cellspacing="1">
		<tr>
		<td><b>First Name *</b></td>
		<td> <input type='text' name='First Name' value=''/></td>
		</tr>
		<tr>
		<td ><b>Last Name *</b></td>
		<td ><input type='text' name='Last Name' value=''/></td>
		</tr>
		<tr>
		<td ><b>Address *</b></td>
		<td ><input type='text' name='address' value=''/></td>
		</tr>
		<tr>
		<td ><b>City *</b></td>
		<td ><input type='text' name='city' value=''/></td>
		</tr>
		<tr>
		<td ><b>State *</b></td>
		<td ><input type='text' name='state' value=''/></td>
		</tr>
		<tr>
		<td ><b>Zip Code *</b></td>
		<td ><input type='text' name='zip' value=''/></td>
		</tr>
		<tr>
		<td ><b>Telephone</b></td>
		<td ><input type='text' name='tele' value=''/></td>
		</tr>
		<tr>
		<td ><b>Email *</b> </td>
		<td> <input type='text' name='Email' value=''/></td>
		</tr>
		</table>
		<p>
		<input type='submit' name='Continue' value='Continue'/>
		<p><b>Asterisk (*) indicates required information.<p><p>
		</form method='post'> 
		</table>
		</table>
		</td>
		</tr>
		</table>
		<?php

}
}
add_action('admin_post_nopriv_pay_next','pay_next');

function pay_next() {

//print_r($_GET);
	if (
         ! isset( $_GET['simple_nonce'] )
          || ! wp_verify_nonce( $_GET['simple_nonce'], 'simple_sale_continue' )
        ) {

        print 'Sorry, your nonce did not verify.';
        exit;

        } else {

	$get_url = get_field('application_url', 'option');
	//print_r($_GET);
	///Generate unique sale_id tnd date his will be needed for paypal IPN

	$custom_id= mt_rand(1000, 500000);
	//$now=date("Y-m-d");
	$now=date("Y-m-d H:i:s");


	$product_id=sanitize_text_field($_GET['Product_id']);
	$list=sanitize_text_field($_GET['List']);
	$shipping=sanitize_text_field($_GET['Shipping']);
	$f_name=sanitize_text_field($_GET['First_Name']);
	$l_name=sanitize_text_field($_GET['Last_Name']);
	$address=sanitize_text_field($_GET['address']);
	$city=sanitize_text_field($_GET['city']);
	$state=sanitize_text_field($_GET['state']);
	$zip=sanitize_text_field($_GET['zip']);
	$tele=sanitize_text_field($_GET['tele']);
	$email=sanitize_email($_GET['Email']);
	$product_name=sanitize_text_field($_GET['Product_Name']);


	//Look for required data

	 if (empty($f_name)) {
                echo "<h3>First Name is required.<br>";
                echo "<a href='javascript: history.back()'>Go Back</a>";
                exit;
        }
	if (empty($l_name)) {
                echo "<h3>Last Name is required.<br>";
                echo "<a href='javascript: history.back()'>Go Back</a>";
                exit;
        }

        if (empty($address)) {
                echo "<h3>Address required.<br>";
                echo "<a href='javascript: history.back()'>Go Back</a>";
                exit;
        }
        if (empty($city)) {
                echo "<h3>City  is required.<br>";
                echo "<a href='javascript: history.back()'>Go Back</a>";
                exit;
        }
        if (empty($state)) {
                echo "<h3>State  is required.<br>";
                echo "<a href='javascript: history.back()'>Go Back</a>";
                exit;
        }
        if (empty($zip)) {
                echo "<h3>Zip Code  is required.<br>";
                echo "<a href='javascript: history.back()'>Go Back</a>";
                exit;
        }
        if (empty($email)) {
                echo "<h3>Email is required.<br>";
                echo "<a href='javascript: history.back()'>Go Back</a>";
                exit;
        }

	// Do some math
	$tot_charge=$list+$shipping;

	// Insert Data into Sales DB
	global $wpdb;
	$table_name = $wpdb->prefix . 'simple_sales';

	$wpdb->insert(
			$table_name,
			array(
				'date' => $now,
				'f_name' => $f_name,
				'l_name' => $l_name,
				'address' => $address,
				'city' => $city,
				'state' => $state,
				'zip' => $zip,
				'tele' => $tele,
				'list' => $list,
				'shipping' => $shipping,
				'product_id' => $product_id,
				'email' => $email,
				'sale_id' => $custom_id,
			     )
		     );
	echo "<html><head><body>";
	if ( !is_plugin_active( 'wp-paypal/main.php' ) ):
		echo 'The WP-PayPal plugin is NOT activated, please download it and activate it.';
	exit;
	endif;

	echo do_shortcode("[wp_paypal button='buynow' name='$product_name' amount='$tot_charge' cancel_return='$get_url/index.php/response/?resp=Sorry' return='$get_url/index.php/response/?resp=Thanks' target='_blank' custom='$custom_id' ]");

	echo "</body>";

}
}

add_action('wp_paypal_ipn_processed', 'paypal_callback');

function paypal_callback($ipn_response) {
	$now=date("Y-m-d H:i:s");
	if (isset($ipn_response['custom'])) {
		$custom = sanitize_text_field($ipn_response['custom']);
		$payment_gross = sanitize_text_field($ipn_response['payment_gross']);
		$payment_fee = sanitize_text_field($ipn_response['payment_fee']);
		do_action ('send_email_notify',$custom,$payment_gross);
	}


	global $wpdb;
	$table_name = $wpdb->prefix . 'simple_sales';

	$wpdb->update(
			$table_name,
			array(
				'payment' => $payment_gross,
				'payment_fee' => $payment_fee,
			     ),

			array(
				'sale_id' =>$custom,
			     )

		     );

}




//}
add_action('go_simple_check_username', 'simple_check_username');

add_action('send_email_notify', 'simple_mail', 10, 2 );

function simple_mail( $custom, $payment_gross ) {

	$get_admin_email_account = get_field('admin_email_account', 'option');

	$to      = $get_admin_email_account;
	$subject = 'Sale Notice';
	$message = 'Sale complete for Sale ID: '.$custom."\n"
                .'Gross Payment  recieved: '.$payment_gross."\n" .'Adjust inventory as needed.';
	$headers = $get_admin_email_account;

	wp_mail( $to, $subject, $message, $headers );


}



function simple_check_username()
{
	//////////////Start Identify Check
	$get_variable = get_field('Simple_admin_user', 'option');

	$simple_user = wp_get_current_user();
	if ( $simple_user->user_login ==$get_variable OR $simple_user->user_login =='admin') {

		$Simple_authed = $simple_user->user_login;
		echo "Access Granted -- Welcome $Simple_authed ";
		echo "<p>";
	}else{
		echo "No Access";
		echo "<p>";
		?>
			<button onclick="goBack()">Go Back</button>

			<script>
			function goBack() {
				window.history.back();
			}
		</script>

			<?php

			exit;
	}
}

function simple_response($atts)
{

	// Attributes
	$atts = shortcode_atts( array(
				'resp'    => isset($_GET['resp']) ? sanitize_key($_GET['resp']) : '',
				), $atts, 'SimpleResponse' );

	// Variables to be used
	$response = $atts['resp']; 



	if ($response=='thanks') {
		echo "<h1>Thanks for the Order!!<p>";
		echo "<a href='/'>Home</a></h1>";
	}else{
		echo "<h1>Sorry it didn't work out - lets try again.<p>
			<a href='/'>Home</a></h1>";
	}
}


function reverse_simple_check_username()
{
	$get_variable = get_field('Simple_admin_user', 'option');

	$simple_user = wp_get_current_user();
	if ( $simple_user->user_login ==$get_variable OR $simple_user->user_login =='admin') {
		echo "<B>You are an Admin - please use 'Admin Product Search'</B>";
		echo "<p>";
		echo "<p>";
		exit;
	}else{
		echo "";
		echo "<p>";

	}
}

//// Begin Product Input


function prod_input() {
	global $post;
	?>
		<br/>
		<h3>Add Product</h3>
		<section id="SimpleProd-wrapper">
		<form name="SimpleProd" id="SimpleProd" method="post" action="" autocomplete="on">
		<center>
		<table class="table2">
		<tr valign="top">
		<td width="50%" >
		<p>Description (max 60 char)</p>
		</td>
		<td width="50%">
		<p>
		<input type=text style=' text-align: center; font-size: 18pt;' name=description value = '' placeholder='Description *' maxlength = '60'>
		</p>
		</td>
		</tr>
		<tr valign="top">
		<td width="50%">
		<p>Product Name (max 30 char)</p>
		</td>
		<td width="50%">
		<p>
		<input type=text style=' text-align: center; font-size: 18pt;' name=prod_name value = '' placeholder='Product Name *' maxlength = '30'>
		<br/>
		</p>
		</td>
		</tr>
		<tr valign="top">
		<td width="50%">
		<p>Quantity *</p>
		</td>
		<td width="50%">
		<p>
		<input type=int style=' text-align: center; font-size: 18pt;' name=quantity value = '' placeholder='Quantity *'>
		<br/>
		</p>
		</td>
		</tr>
		<tr valign="top">
		<td width="50%">
		<p>Cost</p>
		</td>
		<td width="50%">
		<p>
		<input type=int style=' text-align: center; font-size: 18pt;' name=cost value = '' placeholder='Cost'>
		<br/>
		</p>
		</td>
		</tr>
		<tr valign="top">
		<td width="50%">
		<p>List Price
		</p>
		</td>
		<td width="50%">
		<p>
		<input type=int style=' text-align: center; font-size: 18pt;' name=list value = '' placeholder='List Price'>
		<br/>
		</p>
		</td>
		</tr>

		<tr valign="top">
                <td width="50%">
                <p>Shipping
                </p>
                </td>
                <td width="50%">
                <p>
                <input type=int style=' text-align: center; font-size: 18pt;' name=shipping value = '' placeholder='Shipping'>
                <br/>
                </p>
                </td>
                </tr>


		<tr valign="top">
		<td width="50%">
		<p>Image name</p>
		</td>
		<td width="50%">
		<p>
		<input type=text style=' text-align: center; font-size: 18pt;' name=image_name value = '' placeholder='Image Name'>
		<br/>
		</p>
		</td>


		<tr valign="top">
		<td width="50%">
		<p>Recieve Date</p>
		</td>
		<td width="50%">
		<p>
		<input type=date style=' text-align: center; font-size: 18pt;' name=date value = '' placeholder='Date'>
		<br/>
		</p>
		</td>
		</tr>
		</table>
		</body>

		<b>Asterisk (*) indicates required information.<p><p>
		<p>
		<p>
		<input type='submit' name='action' value='Cancel'>
		<input name='ProdSubmitButton' type="submit" >
		</form>
		</section>
		<br /> <?php

} //Closing Paren to Prod_Input

function test() {
//Used for testing of course
}
 



//Shortcodes

add_shortcode('Simple_Check', 'simple_check_username');
add_shortcode('Simple_Check_Reverse', 'reverse_simple_check_username');
add_shortcode('SimpleResponse','simple_response');
add_shortcode( 'SimpleExport', 'export_start');
add_shortcode( 'GetData', 'get_settings_data');
add_shortcode( 'SimpleSearch', 'search_tables');
add_shortcode( 'ShowProd', 'display_product');
add_shortcode( 'ShowProdStripped', 'display_product_scodes');
$headers[] = 'From: Me Myself <me@example.net>';add_shortcode( 'StandardSearch', 'search_prods');
add_shortcode( 'StandardAdminSearch', 'admin_search_prods');
add_shortcode( 'SimpleProd', 'prod_input');
add_shortcode( 'SimpleTest', 'test');
?>
