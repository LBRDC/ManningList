<?php
include '../database/connection.php';
$response = array("Error" => "", "msg" => "");
$add_Status = 1;
$add_Created = date("Y-m-d");
// $json_data = file_get_contents('php://input');
// $data = json_decode($json_data, true);
// $employees = $data['Employees'];
$employees = json_decode($_POST['Employees']);
if (empty($employees)) {
    $response['Error'] = "Empty File";
    $response['msg'] = "Please provide a valid file.";
    echo json_encode($response);
    exit();
}
$instance = Connection::getInstance();
try {
    //Begin Transaction
    $instance->begin_transaction();

    //Iterate array of employees and insert in database
    foreach ($employees as $val) {
        $curr_Region = getRegionID($val->region);
        if (!checkDuplicate($val->emp_Id)) {
            $params = [$val->emp_Id, $val->firstName, $val->middleName, $val->lastName, $curr_Region['id'], $val->assignment, $val->position, $add_Status, $add_Created];
            $result = Connection::_executePostQuery("insert into employee_tbl (emp_number, emp_fname, emp_mname, emp_lname, region, assignment, emp_position, emp_status, emp_created) values(?,?,?,?,?,?,?,?,?)", $params);
            if ($result['Error']) {
                $instance->rollback();
                exit();
            }
        }
    }

    $instance->commit();
    $response['Error'] = false;
    $response['msg'] = "Employee has been imported";
    echo json_encode($response);
    exit();
} catch (Exception $e) {
    $instance->rollback();
    $response['Error'] = true;
    $response['msg'] = $e->getMessage();
    echo json_encode($response);
    exit();
}


// Get the ID of region base on the given value
function getRegionID($regionName)
{
    $result = Connection::_executeQuery("select id from region where region='$regionName'");
    return $result['data'][0];
}


//  Check if the employee ID already exist in the database
function checkDuplicate($emp_number)
{
    $result = Connection::_executeQuery("select emp_id from employee_tbl where emp_number='$emp_number'");
    return $result['count'] > 0 ? true : false;
}