<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Preflight 요청에 대한 응답
    exit(0);
}

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
    // Database 생성 성공 시 로그만 기록하고 출력하지 않음
    error_log("Database created successfully");
} else {
    // Database 생성 실패 시 에러 로그 기록
    error_log("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db("population");

// XML 파일 로드
$xml = simplexml_load_file('UNdata_Export_20241128_044942230.xml') or die("Error: Cannot create object");

// API 사용에 필요한 국가명 목록
// 사용 가능한 국가명은 XML 파일에서 추출되며, $countries 배열에 저장되어 있습니다.

// 모든 국가 목록 가져오기 및 테이블 생성
$countries = array(); // 국가명 목록
foreach ($xml->children() as $record) {
    $country = (string)$record->Country_or_area;
    $tableName = preg_replace('/\s+/', '_', strtolower($country));
    if (!in_array($tableName, $countries)) {
        $countries[] = $tableName;
        // 테이블 생성
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
            year INT PRIMARY KEY,
            population BIGINT
        )";
        $conn->query($sql);
    }
}

// 데이터 삽입
foreach ($xml->children() as $record) {
    $country = (string)$record->Country_or_area; //국가명 추출
    $year = (int)$record->Year; //연도 추출
    $population = (int)$record->Value; //인구 추출

    $tableName = preg_replace('/\s+/', '_', strtolower($country)); //테이블 명은 국가명을 소문자로 변환하여 사용
    $sql = "INSERT INTO `$tableName` (year, population) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE population = ?"; //각 물음표에 값은 bind_param으로 바인딩
    $stmt = $conn->prepare($sql); //sql문을 실행할 준비를 함

    $stmt->bind_param("iii", $year, $population, $population);
    //sql문의 물음표 3개에 각각 year, population, population을 바인딩
    //iii는 각각 int, int, int를 의미 뒤에 나오는 변수들을 바인딩

    $stmt->execute(); //sql문 실행
    // 실행 결과에 대한 출력 방지
}

// 요청된 국가의 데이터 반환 (DB API 부분)
if (isset($_GET['country'])) { //php 링크 뒤에 ?country=국가명 을 붙여서 요청
    $country = $_GET['country'];
    $tableName = preg_replace('/\s+/', '_', strtolower($country));

    $sql = "SHOW TABLES LIKE '$tableName'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows == 1) {
        $stmt = $conn->prepare("SELECT * FROM `$tableName`");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        $stmt->close();
        // 데이터 JSON 형식으로 반환
        echo json_encode($data);
        exit();
    } else {
        // 데이터가 없을 경우 빈 배열 반환
        echo json_encode([]);
    }
}

// $countries 목록 반환 추가
if (isset($_GET['action']) && $_GET['action'] === 'get_countries') {
    echo json_encode($countries);
    exit();
}

$conn->close();
