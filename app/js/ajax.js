$(document).ready(function () {
    $('#myTable').DataTable();
});


const showLoading = function () {
    swal({
        title: 'Now loading',
        allowEscapeKey: false,
        allowOutsideClick: false,
        timer: 2000,
        onOpen: () => {
            swal.showLoading();
        }
    }).then(
        () => { },
        (dismiss) => {
            if (dismiss === 'timer') {
                console.log('closed by timer!!!!');
                swal({
                    title: 'Finished!',
                    type: 'success',
                    timer: 2000,
                    showConfirmButton: false
                })
            }
        }
    )
};

$(document).on('change', "#importFile", () => {
    const file = $("#importFile")[0].files[0]
    $("#filenameTag").text(file.name)
})

$(document).on("submit", "#importFrm", (e) => {
    e.preventDefault();
    const sheetName = $("#sheetName").val()
    const file = $("#importFile")[0].files[0]
    if (!sheetName || !file) {
        swal({
            icon: "warning",
            title: "Please fill up the form",
            text: "",
        });
        return;
    }


    //Read the Excel  file and Extract the data

    const reader = new FileReader();
    reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        let sheet;

        //validate  excel sheetname
        if (!workbook.Sheets[sheetName]) {
            swal({
                icon: "warning",
                title: "Sheet  not found",
                text: "Please select a valid sheet.",
            });
            return;
        }

        sheet = workbook.Sheets[sheetName];
        const jsonData = XLSX.utils.sheet_to_json(sheet);

        // console.log(jsonData);


        //validate excel data
        if (jsonData.length === 0) {
            swal({
                icon: "warning",
                title: "Data not  found",
                text: "Please select a valid sheet with data.",
            });
            return;
        }
        // console.log(jsonData);
        ExcelValidation(jsonData);
    };
    reader.readAsArrayBuffer(file);
})


const ExcelValidation = (emp) => {
    let newEmp = [];
    //validate excel data if it is a table of employee
    if (!emp.some(obj => obj.__EMPTY_2 && obj.__EMPTY_2.toUpperCase() == "EMPLOYEE NAME")) {
        swal({
            icon: "warning",
            title: "Data is not valid",
            text: "Please select a valid sheet with data.",
        });
        return;
    }
    for (let i = 0; i < Object.keys(emp).length; i++) {
        if (Object.keys(emp[i]).length >= 8 && emp[i].__EMPTY_2 != "Employee Name") {
            if (emp[i].__EMPTY_2 && emp[i].__EMPTY_2 != "Employee Name") {
                const empName = _splitName(emp[i]?.__EMPTY_2, emp[i].__rowNum__);
                if (empName) {
                    if (empName.Error) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: `${empName.Error}. Please fix the issue before proceeding`,
                        });
                        break;
                    }
                }
                newEmp.push({ emp_Id: emp[i]['__EMPTY_1'], ..._splitName(emp[i]?.__EMPTY_2, emp[i].__rowNum__), position: emp[i]['__EMPTY_3'], assignment: emp[i]['__EMPTY_4'], region: emp[i]['__EMPTY_5'], rate: emp[i]['__EMPTY_6'], row: emp[i].__rowNum__ + 1 })
            }
        }
    }
    const filteredEmp = newEmp.filter((item, index, self) => {
        return self.findIndex((t) => t.emp_Id === item.emp_Id) === index;
    });
    // console.log(filteredEmp);
    _addEmployee(filteredEmp)
}

const _splitName = (fullName, row) => {
    if (!fullName) {
        console.log("Name is missing");
        return { Error: "Name is missing" };
    }

    let name = fullName.split(', ');
    if (name.length < 2) {
        return { Error: `Invalid name format->${fullName} at row->${row + 1}. It should be (LASTNAME, FIRSTNAME MIDDLENAME)` };
    }
    let lastName = name[0];
    let otherNames = name[1].split(' ');
    let firstName, middleName;

    if (otherNames.length > 1) {
        middleName = otherNames.pop();
        firstName = otherNames.join(' ');
    } else {
        firstName = otherNames[0];
        middleName = '';
    }

    return {
        firstName: firstName.toUpperCase(),
        middleName: middleName.toUpperCase(),
        lastName: lastName.toUpperCase()
    };
}

const _addEmployee = (emp, row) => {
    $.ajax({
        url: "query/Employee.php",
        type: "POST",
        dataType: "json",
        data: {
            "Employees": JSON.stringify(emp),
            "StartingRow": row
        },
        beforeSend: () => {
            showLoading();
        },
        success: (response) => {
            if (!response.Error) {
                swal({
                    icon: "success",
                    title: "Success",
                    text: "Data has been imported.",
                });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                swal({
                    icon: "error",
                    title: "Error",
                    text: response.msg,
                });
            }
        },
        error: (jqXHR, textStatus, errorThrown) => {
            console.log(errorThrown);
            console.log(textStatus);
            console.log(jqXHR);

        }
    })
}


$(document).on("submit", "#frm-login", (e) => {
    e.preventDefault();
    const username = $("#username").val();
    const password = $("#password").val();
    if (!username || !password) {
        swal({
            icon: "warning",
            title: "Warning",
            text: "Please fill up the form.",
        });
        return;
    }

    const data = {
        "username": username.trim(),
        "password": password.trim()
    }
    doLogin(data)
})


const doLogin = (data) => {
    $.ajax({
        url: "../query/login_employee.php",
        type: "POST",
        dataType: "json",
        data: data,
        beforeSend: () => {
            // showLoading();
        },
        success: (response) => {
            // console.log(response);
            // return;
            if (!response.Error) {
                if (response.msg.count == 1) {
                    swal({
                        icon: "success",
                        title: "Success",
                        text: "Logged in successfully",
                    });
                    setTimeout(() => {
                        window.location.href = "/dashboard.php"
                    }, 3000);
                } else {
                    swal({
                        icon: "error",
                        title: "Error",
                        text: "Incorrect Credentials",
                    });
                }
            } else {
                swal({
                    icon: "error",
                    title: "Error",
                    text: response.msg,
                });
            }
        },
        error: (jqXHR, textStatus, errorThrown) => {
            console.log(errorThrown);
            console.log(textStatus);
            console.log(jqXHR);

        }
    })
}