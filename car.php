<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$host = "localhost";
$database = "cars";
$user = "root";
$password = "";

function read_car()
{
    set_time_limit(0);

    global $host, $database, $user, $password;
    $connection = mysqli_connect($host, $user, $password, $database) or die("Error " . mysqli_error($connection));

    $sql = "select * from cars";
    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));

    $car_array = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $row["detail"] = "detail";
        $row["purchase"] = "purchase";
        $car_array[] = $row;
    }

    mysqli_close($connection);

    echo json_encode($car_array);
}

function search_car()
{
    set_time_limit(0);

    $make = $_POST['key']['make'];
    $model = $_POST['key']['model'];
    $reg = $_POST['key']['Reg'];
    $color = $_POST['key']['colour'];
    $from_mile = $_POST['key']['from_miles'];
    $to_mile = $_POST['key']['to_miles'];
    $from_prices = $_POST['key']['from_price'];
    $to_prices = $_POST['key']['to_price'];
    $dealer = $_POST['key']['dealer'];
    $town = $_POST['key']['town'];
    $description = $_POST['key']['description'];
    $region = $_POST['key']['region'];

    global $host, $database, $user, $password;
    $connection = mysqli_connect($host, $user, $password, $database) or die("Error " . mysqli_error($connection));

    $sql = "select * from cars where 1";

    $searchQuery = "";

    if ($from_mile != "" && $to_mile != "") {
        $searchQuery .= " and (miles >= ".$from_mile." and miles <=".$to_mile.")";
    }

    if (!empty($make)) {
        $searchQuery .= " and (make like '%" . $make . "%')";
    }
    if (!empty($model)) {
        $searchQuery .= " and (model like '%" . $model . "%')";
    }
    if (!empty($reg)) {
        $searchQuery .= " and (Reg like '%" . $reg . "%')";
    }
    if (!empty($color)) {
        $searchQuery .= " and (colour like '%" . $color . "%')";
    }
    if ($from_prices != "" && $to_prices != "") {
        $searchQuery .= " and (price >= ".$from_prices." and price <=".$to_prices.")";
    }
    if (!empty($dealer)) {
        $searchQuery .= " and (dealer like '%" . $dealer . "%')";
    }
    if (!empty($town)) {
        $searchQuery .= " and (town like '%" . $town . "%')";
    }
   
    if (!empty($description)) {
        $searchQuery .= " and (description like '%" . $description . "%')";
    }
    if (!empty($region)) {
        $searchQuery .= " and (region like '%" . $region . "%')";
    }

    $sql .= $searchQuery;

    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));

    $car_array = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $row["detail"] = "detail";
        $row["purchase"] = "purchase";
        $car_array[] = $row;
    }

    mysqli_close($connection);

    echo json_encode($car_array);
}
function purchase() { // customer info input
    $name = "'" .$_POST['purchase']['name']. "'";
    $address = "'" .$_POST['purchase']['address']. "'";
    $tel = "'" .$_POST['purchase']['tel']. "'";
    $email = "'" .$_POST['purchase']['email']. "'";
    $carIndex = "'" .$_POST['purchase']['carIndex']. "'";

    global $host, $database, $user, $password;//calling outside the method
    $connection = mysqli_connect($host, $user, $password, $database) or die("Error " . mysqli_error($connection));// db connextion

    //db connection
    $query = "SELECT id FROM purchase";
    $result = mysqli_query($connection, $query);//result should equal to query above

    if(empty($result)) {//if statement-no table of purchase create new
        $query = "CREATE TABLE purchase (
                          id int(11) AUTO_INCREMENT,
                          name varchar(255) NOT NULL,
                          address varchar(255) NOT NULL,
                          tel varchar(255) NOT NULL,
                          email varchar(255) NOT NULL,
                          car varchar(255) NOT NULL,
                          PRIMARY KEY  (id)
                          )";
        mysqli_query($connection, $query);//new one replaces old
    }

    $sql = "insert into purchase  (name, address, tel, email, car)  values (" . $name . ", " . $address . ", " . $tel . ", " . $email . ", " . $carIndex .")";
    mysqli_query($connection, $sql);//new query

    mysqli_close($connection);

    $response = array(
        "res" => "success"//
    );
    echo json_encode($response);//
}

function add_car()
{
    set_time_limit(0);

    $make = "'" . $_POST['add']['make'] . "'";
    $model = "'" . $_POST['add']['model'] . "'";
    $reg = "'" . $_POST['add']['Reg'] . "'";
    $color = "'" . $_POST['add']['colour'] . "'";
    $mile = "'" . $_POST['add']['miles'] . "'";
    $price = "'" . $_POST['add']['price'] . "'";
    $dealer = "'" . $_POST['add']['dealer'] . "'";
    $town = "'" . $_POST['add']['town'] . "'";
    $phone = "'" . $_POST['add']['telephone'] . "'";
    $description = "'" . $_POST['add']['description'] . "'";
    $region = "'" . $_POST['add']['region'] . "'";

    global $host, $database, $user, $password;
    $connection = mysqli_connect($host, $user, $password, $database) or die("Error " . mysqli_error($connection));

    //get last car index
    $result = mysqli_query($connection, "SELECT carIndex FROM cars ORDER BY carIndex DESC LIMIT 1");
    $last_index = mysqli_fetch_row($result)[0];

    $new_index = "'" . ($last_index + 1) . "'";

    $sql = "insert into cars values (" . $make . ", " . $model . ", " . $reg . ", " . $color . ", " . $mile . ", " . $price . ", " . $dealer . ", " . $town . ", " . $phone . ", " . $description . ", " . $new_index . ", " . $region . ")";


    mysqli_query($connection, $sql);

    mysqli_close($connection);

    $response = array(
        "res" => "success",
        "index" => $last_index + 1
    );

    echo json_encode($response);
}

function update_car()
{
    set_time_limit(0);
    $carIndex = $_POST['update']['carIndex'];
    $make = "'" . $_POST['update']['make'] . "'";
    $model = "'" . $_POST['update']['model'] . "'";
    $reg = "'" . $_POST['update']['Reg'] . "'";
    $color = "'" . $_POST['update']['colour'] . "'";
    $mile = "'" . $_POST['update']['miles'] . "'";
    $price = "'" . $_POST['update']['price'] . "'";
    $dealer = "'" . $_POST['update']['dealer'] . "'";
    $town = "'" . $_POST['update']['town'] . "'";
    $phone = "'" . $_POST['update']['telephone'] . "'";
    $description = "'" . $_POST['update']['description'] . "'";
    $region = "'" . $_POST['update']['region'] . "'";

    global $host, $database, $user, $password;
    $connection = mysqli_connect($host, $user, $password, $database) or die("Error " . mysqli_error($connection));

    $update_query = "make=".$make.","."model=".$model.","."Reg=".$reg.","."colour=".$color.","."miles=".$mile.","."price=".$price.","."dealer=".$dealer.","."town=".$town.","."telephone=".$phone.","."description=".$description.","."region=".$region;
    $sql = "update cars set ".$update_query." where carIndex=".$carIndex;


    mysqli_query($connection, $sql);

    mysqli_close($connection);

    $response = array(
        "res" => "success"
    );

    echo json_encode($response);
}


function delete_car()
{
    set_time_limit(0);
    $index = $_POST['delete']['index'];


    global $host, $database, $user, $password;
    $connection = mysqli_connect($host, $user, $password, $database) or die("Error " . mysqli_error($connection));

    $sql = "delete from cars where carIndex=" . $index;

    mysqli_query($connection, $sql);

    mysqli_close($connection);

    $response = array(
        "res" => "success"
    );

    echo json_encode($response);
}

function read_purchase()
{
    set_time_limit(0);

    $email = $_POST['email'];

    global $host, $database, $user, $password;
    $connection = mysqli_connect($host, $user, $password, $database) or die("Error " . mysqli_error($connection));

    $sql = "SELECT a.*, b.* FROM purchase a INNER join cars b on a.car=b.carIndex where a.email="."'".$email."'";

    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));

    $car_array = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $row["detail"] = "detail";
        $row["delete"] = "delete";
        $car_array[] = $row;
    }

    mysqli_close($connection);

    echo json_encode($car_array);
}

function delete_purchase()
{
    set_time_limit(0);

    $id = $_POST['purchase_id'];

    global $host, $database, $user, $password;
    $connection = mysqli_connect($host, $user, $password, $database) or die("Error " . mysqli_error($connection));

    $sql = "delete from purchase where id=" . $id;

    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));

    mysqli_close($connection);

    $response = array(
        "res" => "success"
    );

    echo json_encode($response);
}

if ($_REQUEST["functionname"] == "read_car") {
    read_car();
} else if ($_REQUEST["functionname"] == "search_car") {
    search_car();
} else if ($_REQUEST["functionname"] == "purchase") {
    purchase();
} else if ($_REQUEST["functionname"] == "add_car") {
    add_car();
} else if ($_REQUEST["functionname"] == "delete_car") {
    delete_car();
} else if ($_REQUEST["functionname"] == "update_car") {
    update_car();
} else if ($_REQUEST["functionname"] == "read_purchase") {
    read_purchase();
} else if ($_REQUEST["functionname"] == "delete_purchase") {
    delete_purchase();
}


?>