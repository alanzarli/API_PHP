<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');

include './bdconnect.php';

$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case "GET": 
        $sql = "SELECT * FROM tasks WHERE status  = 0";
        $path = explode("/", $_SERVER["REQUEST_URI"]);
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
        break;

    case "POST":
        $today = date("Y-m-d H:i:s");
        $user = json_decode(file_get_contents('php://input')) ;
        $sql = "INSERT INTO tasks VALUES(?,?,?,?)";
        $stmt = $conn->prepare($sql);
        if($stmt->execute([null, $user->content, 0, $today])){
            $response = ['status' => 1, 'message' => 'task added succesfully :)'];
        }else {
            $response = ['status' => 0, 'message' => "task can't be added succesfully :("];
        }
        echo json_encode($response);
        break;

    case "PUT":
        $user = json_decode(file_get_contents('php://input'));
        $path = explode("/", $_SERVER["REQUEST_URI"]);
        $id = explode(",", $path[3]);
        $sql = "UPDATE tasks SET status = $user->status + 1 WHERE id = $id[1]";  
        $stmt = $conn->prepare($sql);
        if($stmt->execute()){
            $response = ['status' => 1, 'message' => 'user updated succesfully :)'];
        }else {
            $response = ['status' => 0, 'message' => "user can't be updated succesfully :("];
        }
        echo json_encode($response);
        break;

    case "DELETE":
        $sql = "DELETE FROM tasks WHERE id = :id";
        $path = explode("/", $_SERVER["REQUEST_URI"]);
        $id = explode(",", $path[3]);
        var_dump($id);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id[0]);
            if($stmt->execute()){
                $response = ['status' => 1, 'message' => 'user deleted succesfully :)'];
            }else {
                $response = ['status' => 0, 'message' => "user couldn't be deleted succesfully :("];
            }
            echo json_encode($response);
            break;
}