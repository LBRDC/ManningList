<?php
session_start();
require '../database/connection.php';
// if (!isset($_SESSION['USER'])) {
//     header("location: view/login.php");
// }
$result = Connection::_executeQuery("SELECT A.emp_id, A.emp_fname, A.emp_mname, A.emp_lname, A.emp_sfname, A.emp_status, A.emp_created, A.assignment, A.emp_position, B.id, B.region, B.rate  FROM employee_tbl as A INNER JOIN region as B ON B.id = A.region order by A.emp_id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../app/css/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.dataTables.css" />
    <title>Import Excel</title>
</head>

<body>
    <div class="mt-5 main-container">
        <div class="frm-container">
            <form id="importFrm">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Upload</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="importFile">
                        <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                    </div>

                </div>
                <span class="filenameTag" id="filenameTag"></span>
                <input type="text" class="form-control mb-3" id="sheetName" placeholder="Sheet name"
                    value="Janitorial Field Units">
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>

        <div class="container mt-5">
            <div class="table-responsive">
                <table id="myTable" class="display">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Assignment</th>
                            <th>Region</th>
                            <th>Rate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result['data'] as $list): ?>
                            <tr>
                                <td><?= $list['emp_id'] ?></td>
                                <td><?= $list['emp_lname'] . ', ' . $list['emp_fname'] . ' ' . $list['emp_mname'] ?></td>
                                <td><?= $list['emp_position'] ?></td>
                                <td><?= $list['assignment'] ?></td>
                                <td><?= $list['region'] ?></td>
                                <td><?= $list['rate'] ?></td>
                                <td><?= $list['emp_status'] == 1 ? "Active" : "Inactive" ?></td>

                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>




    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.0/jquery.min.js"
        integrity="sha512-b6lGn9+1aD2DgwZXuSY4BhhdrDURVzu7f/PASu4H1i5+CRpEalOOz/HNhgmxZTK9lObM1Q7ZG9jONPYz8klIMg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script type="module" src="../app/js/ajax.js"></script>
    <script src="../app/js/xlsx.full.min.js"></script>
</body>

</html>