<?php
header("Access-Control-Allow-Origin: *");

$servername = "localhost";
$username = "root"; // XAMPP 기본 사용자 이름
$password = "";     // XAMPP 기본 비밀번호
$dbname = "population";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS population";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("population");

// Array of country names
$countries = [
    'usa',
    'china',
    'japan',
    'south_korea',
    'india',
    'brazil',
    'russia',
    'germany',
    'uk',
    'france',
    'italy',
    'canada',
    'australia',
    'spain',
    'mexico',
    'indonesia',
    'netherlands',
    'saudi_arabia',
    'turkey',
    'switzerland'
];

// Create tables for each country
foreach ($countries as $country) {
    $sql = "CREATE TABLE IF NOT EXISTS $country (
        year INT PRIMARY KEY,
        population BIGINT
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Table $country created successfully<br>";
    } else {
        echo "Error creating table $country: " . $conn->error . "<br>";
    }
}

$country = $_GET['country'];

// Validate the country parameter
if (!in_array($country, $countries)) {
    die("Invalid country specified");
}

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM $country");
$stmt->execute();
$result = $stmt->get_result();

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$stmt->close();
$conn->close();
echo json_encode($data);
