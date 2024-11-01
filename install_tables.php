<?php

global $simple_db_version;
$simple_db_version = '1.0';

function simple_install($name) {

switch ($name) {

  case wp_simple_customer:
        global $wpdb;
        global $simple_db_version;
        $table_name = $wpdb->prefix . 'simple_customer';
        $sql = "CREATE TABLE $table_name (
        customer_id mediumint(9) NOT NULL AUTO_INCREMENT,
        f_name varchar(20) DEFAULT NULL,
        l_name varchar(20) DEFAULT NULL,
        address varchar(40) DEFAULT NULL,
        city varchar(20) DEFAULT NULL,
        state varchar(20) DEFAULT NULL,
        tele varchar(20) DEFAULT NULL,
        zip int(10) DEFAULT NULL,
        email varchar(40) DEFAULT NULL,
        date date DEFAULT NULL,
        PRIMARY KEY  (customer_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    break;

  case wp_simple_product:
        global $wpdb;
        global $simple_db_version;
        $prod_table_name = $wpdb->prefix . 'simple_product';
	$prod_sql = "CREATE TABLE $prod_table_name (
        product_id mediumint(9) NOT NULL AUTO_INCREMENT,
        description varchar(60) DEFAULT NULL,
        product_name varchar(30) DEFAULT NULL,
        quantity_in_stock int(20) DEFAULT NULL,
        cost_per decimal(9,2) DEFAULT NULL,
	shipping decimal(9,2) DEFAULT NULL,
        list_price decimal(9,2) DEFAULT NULL,
	image_path varchar(128) DEFAULT NULL,
        receive_date date DEFAULT NULL,
        PRIMARY KEY  (product_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $prod_sql );
    
    break;

  case wp_simple_sales:
        global $wpdb;
        global $simple_db_version;
        $sales_table_name = $wpdb->prefix . 'simple_sales';
	$sales_sql = "CREATE TABLE $sales_table_name (
        payment decimal(9,2) DEFAULT NULL,
        payment_fee decimal(9,2) DEFAULT NULL,
        customer_id mediumint(9) NOT NULL,
        f_name varchar(20) DEFAULT NULL,
        l_name varchar(20) DEFAULT NULL,
        address varchar(40) DEFAULT NULL,
        city varchar(20) DEFAULT NULL,
        state varchar(20) DEFAULT NULL,
        tele varchar(20) DEFAULT NULL,
        zip int(10) DEFAULT NULL,
        email varchar(40) DEFAULT NULL,
        product_id mediumint(9) NOT NULL ,
	list  decimal(10,2) NOT NULL,
	shipping decimal(9,2) DEFAULT NULL,
	sale_id mediumint(9) NOT NULL,
        date date DEFAULT NULL
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sales_sql );
    break;

  case wp_simple_owner_profile:
        global $wpdb;
        global $simple_db_version;
        $owner_table_name = $wpdb->prefix . 'simple_owner_profile';
	$owner_sql = "CREATE TABLE $owner_table_name (
        company_name varchar(20) DEFAULT NULL,
        telephone varchar(20) DEFAULT NULL,
        address varchar(40) DEFAULT NULL,
        city varchar(20) DEFAULT NULL,
        state varchar(20) DEFAULT NULL,
        zip int(10) DEFAULT NULL,
        email varchar(40) DEFAULT NULL,
        date_updated date DEFAULT NULL
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $owner_sql );
    break;
  default:
	echo "";
}

        add_option( 'simple_db_version', $simple_db_version );
}

//simple_install("wp_simple_owner_profile");

?>
