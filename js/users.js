var currentMaximumConnections = 1;
var currentProfilePicture = "";
var allUsers;
var checkedUserIDs = [];
var users;

$(document).ready(function () {
    $("#check-all").change(function() {
        if ($('#check-all-checkbox').prop('checked')) {
            for (let j=0; j<users.length; j++) {
                checkedUserIDs.push(users[j]["id"]);
            }
            for (let i=0; i<users.length; i++) {
                $("#users").find("#defaultUnchecked"+(i+1)).prop("checked", true);
            }
            setDeleteButtonVisible(true);
        } else {
            checkedUserIDs = [];
            for (let i=0; i<users.length; i++) {
                $("#users").find("#defaultUnchecked"+(i+1)).prop("checked", false);
            }
            setDeleteButtonVisible(false);
        }
    });
    getUsers();
});

function setDeleteButtonVisible(visible) {
    if (visible) {
        $("#delete").css("display", "visible");
    } else {
        $("#delete").css("display", "none");
    }
}

function getUsers() {
    $("#users").find("*").remove();
    showProgress("Memuat pengguna");
    $.ajax({
        type: 'GET',
        url: PHP_PATH + 'get-users.php',
        dataType: 'text',
        cache: false,
        success: function (a) {
            users = JSON.parse(a);
            allUsers = JSON.parse(a);
            for (var i = 0; i < users.length; i++) {
                var user = users[i];
                var endDate = parseInt(user["end_date"]);
                var currentDate = new Date().getTime();
                var remainingTimeMillis = endDate - currentDate;

                var remainingSeconds = Math.floor((remainingTimeMillis / 1000) % 60);
                var remainingMinutes = Math.floor((remainingTimeMillis / 1000 / 60) % 60);
                var remainingHours = Math.floor((remainingTimeMillis / (1000 * 60 * 60)) % 24);
                var remainingDays = Math.floor(remainingTimeMillis / (1000 * 60 * 60 * 24));
                var remainingMonths = Math.floor(remainingTimeMillis / (1000 * 60 * 60 * 24 * 30));
                var remainingYears = Math.floor(remainingTimeMillis / (1000 * 60 * 60 * 24 * 30 * 12));

                var remainingTimeString = "";
                if (remainingSeconds > 0) {
                    remainingTimeString = (remainingSeconds + " detik ");
                }
                if (remainingMinutes > 0) {
                    remainingTimeString = (remainingMinutes + " menit ");
                }
                if (remainingHours > 0) {
                    remainingTimeString = (remainingHours + " jam ");
                }
                if (remainingDays > 0) {
                    remainingTimeString = (remainingDays + " hari ");
                }
                if (remainingMonths > 0) {
                    remainingTimeString = (remainingMonths + " bulan ");
                }
                if (remainingYears > 0) {
                    remainingTimeString = (remainingYears + " tahun ");
                }
                $("#users").append("" +
                    "<tr>" +
                    "<td><div style='background-color: #2f2e4d; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; color: white;'>" + (i + 1) + "</div></td>" +
                    "<td><div class='custom-control custom-checkbox'>"+
                        "<input type='checkbox' class='custom-control-input' id='defaultUnchecked"+(i+1)+"'>"+
                        "<label class='custom-control-label' for='defaultUnchecked"+(i+1)+"'></label>"+
                    "</div></td>"+
                    "<td>" + user["first_name"] +" " + user["last_name"] + "</td>" +
                    "<td>" + user["email"] + "</td>" +
                    "<td>" + user["phone"] + "</td>" +
                    "<td>" + user["nim"] + "</td>" +
                    "<td><a class='edit-user link'>Ubah</a></td>" +
                    "<td><a class='delete-user link'>Hapus</a></td>" +
                    "</tr>"
                );
            }
            hideProgress();
            setUserClickListener();
        }
    });
}

function deleteUsers() {
    $("#confirm-title").html("Hapus Pengguna");
    $("#close-confirm").unbind().on("click", function() {
        $("#confirm-container").fadeOut(300);
    });
    $("#confirm-msg").html("Apakah Anda yakin ingin menghapus pengguna yang dipilih?");
    $("#confirm-cancel").unbind().on("click", function() {
        $("#confirm-container").fadeOut(300);
    });
    $("#confirm-ok").unbind().on("click", function() {
        $("#confirm-container").hide();
        showProgress("Menghapus pengguna yang dipilih");
        var fd = new FormData();
        let ids = [];
        for (let i=0; i<checkedUserIDs.length; i++) {
            let userID = checkedUserIDs[i];
            ids.push(userID);
        }
        fd.append("id", JSON.stringify(ids));
        $.ajax({
            type: 'POST',
            url: PHP_PATH+'delete-users.php',
            data: fd,
            processData: false,
            contentType: false,
            cache: false,
            success: function(response) {
                console.log("Response: "+response);
                getUsers();
            }
        });
    });
    $("#confirm-container").css("display", "flex").hide().fadeIn(300);
}

function checkAllChecked() {
    var allChecked = false;
    for (var i=0; i<users.length; i++) {
        var checked = $("#users").find("#defaultUnchecked"+(i+1)).prop("checked");
        if (checked) {
            allChecked = true;
            break;
        }
    }
    if (allChecked) {
        $("#delete").css("display", "block");
    } else {
        $("#delete").css("display", "none");
    }
}

function setUserClickListener() {
    $(".custom-control-input").change(function() {
        checkAllChecked();
        var id = $(this).attr('id');
        var tr = $(this).parent().parent().parent();
        var index = $("#users").children().index(tr);
        checkedUserIDs.push(users[index]["id"]);
        if ($(this).prop('checked')) {
            $("#delete").css("display", "block");
        }
    });
    $(".edit-user").unbind().on("click", function () {
        var tr = $(this).parent().parent();
        var index = tr.parent().children().index(tr);
        var user = users[index];
        $("#edit-user-title").html("Ubah Pengguna");
        $("#edit-user-name").val(user["name"]);
        $("#edit-user-username").val(user["username"]);
        $("#edit-user-phone").val(user["phone"]);
        $("#edit-user-email").val(user["email"]);
        $("#edit-user-password").val(user["password"]);
        $("#edit-user-vip").val(user["vip_password"]);
        var endDate = parseInt(user["end_date"]);
        var currentDate = new Date().getTime();
        var remainingTimeMillis = endDate - currentDate;
        $("#edit-user-remaining-time").val(new Date(remainingTimeMillis).toLocaleDateString("id-ID"));
        $("#active-connections").css("display", "block");
        $("#active-connections").val("" + user["active_connections"]);
        $("#active-connections-div").css("display", "block");
        $("#maximum-connections").val("" + user["maximum_connections"]);
        var endTime = new Date(parseInt(user["end_date"]));
        console.log("End time: " + endTime);
        var year = endTime.getFullYear();
        var month = endTime.getMonth();
        if (month < 10) {
            month = "0" + month;
        }
        var day = endTime.getDate();
        console.log("Month: " + month);
        if (day < 10) {
            day = "0" + day;
        }
        $("#end-time").val(year + "-" + month + "-" + day);
        var confirmed = false;
        if (user["confirmed"] == 1) {
            confirmed = true;
        }
        $("#edit-user-confirmed").prop("checked", confirmed);
        $("#edit-user-city").val(user["city"]);
        if (user["profile_picture_url"] != "") {
            $("#edit-user-profile-picture").attr("src", user["profile_picture_url"]);
        }
        if (user["is_trial"] == 0) {
            $("#is-trial option")[0].selected = true;
        } else {
            $("#is-trial option")[1].selected = true;
        }
        $("#edit-user-container").css("display", "flex").hide().fadeIn(300);
        $("#edit-user-ok").html("Ubah").unbind().on("click", function () {
            var name = $("#edit-user-name").val().trim();
            var username = $("#edit-user-username").val().trim();
            var phone = $("#edit-user-phone").val().trim();
            var email = $("#edit-user-email").val().trim();
            var password = $("#edit-user-password").val().trim();
            var vipPassword = $("#edit-user-vip").val().trim();
            var city = $("#edit-user-city").val().trim();
            var endTimeString = $("#end-time").val();
            console.log("Time: " + endTimeString);
            var year = parseInt(endTimeString.split("-")[0]);
            var month = parseInt(endTimeString.split("-")[1]);
            var day = parseInt(endTimeString.split("-")[2]);
            var date = new Date();
            date.setFullYear(year);
            date.setMonth(month);
            date.setDate(day);
            var endDate = date.getTime();
            console.log("End date: " + endDate);
            var isTrial = $("#is-trial option:selected").index();
            var confirmed = 0;
            if ($("#edit-user-confirmed").prop("checked")) {
                confirmed = 1;
            }
            var activeConnections = $("#active-connections").val();
            var maximumConnections = currentMaximumConnections;
            if (username == "") {
                show("Mohon masukkan nama pengguna");
                return;
            }
            if (email == "") {
                show("Mohon masukkan email");
                return;
            }
            if (password == "") {
                show("Mohon masukkan kata sandi");
                return;
            }
            if (maximumConnections <= 0) {
                show("Mohon masukkan jumlah maksimal koneksi aktif");
                return;
            }
            showProgress("Mengubah pengguna");
            var fd = new FormData();
            fd.append("id", user["id"]);
            fd.append("name", name);
            fd.append("username", username);
            fd.append("phone", phone);
            fd.append("password", password);
            fd.append("vip_password", vipPassword);
            fd.append("active_connections", activeConnections);
            fd.append("maximum_connections", maximumConnections);
            fd.append("confirmed", confirmed);
            fd.append("city", city);
            fd.append("trial", isTrial);
            fd.append("end_date", endDate);
            if (currentProfilePicture != "img/profile-picture.jpg") {
                fd.append("profile_picture_set", 1);
            } else {
                fd.append("profile_picture_set", 0);
            }
            $.ajax({
                type: 'POST',
                url: PHP_PATH + 'edit-user.php',
                data: fd,
                processData: false,
                contentType: false,
                cache: false,
                success: function (a) {
                    hideProgress();
                    var response = a;
                    console.log("Response: " + response);
                    if (response == 0) {
                        $("#edit-user-container").fadeOut(300);
                        getUsers();
                    } else if (response == -1) {
                        show("Nama pengguna sudah digunakan");
                    } else if (response == -2) {
                        show("Nomor HP sudah digunakan");
                    } else if (response == -3) {
                        show("Email sudah digunakan");
                    } else {
                        show("Kesalahan: " + response);
                    }
                }
            });
        });
    });
    $(".delete-user").on("click", function () {
        var tr = $(this).parent().parent();
        var index = tr.parent().children().index(tr);
        var user = users[index];
        $("#confirm-title").html("Hapus Pengguna");
        $("#close-confirm").unbind().on("click", function () {
            $("#confirm-container").fadeOut(300);
        });
        $("#confirm-msg").html("Apakah Anda yakin ingin menghapus pengguna ini?");
        $("#confirm-ok").unbind().on("click", function () {
            $("#confirm-container").hide();
            showProgress("Menghapus pengguna");
            $.ajax({
                type: 'GET',
                url: PHP_PATH + 'delete-user.php',
                data: {'id': user["id"]},
                dataType: 'text',
                cache: false,
                success: function (a) {
                    firebase.database().ref("users/" + user["id"]).remove();
                    hideProgress();
                    getUsers();
                }
            });
        });
        $("#confirm-cancel").unbind().on("click", function () {
            $("#confirm-container").fadeOut(300);
        });
        $("#confirm-container").css("display", "flex").hide().fadeIn(300);
    });
}

function addUser() {
    currentMaximumConnections = 1;
    currentProfilePicture = "img/profile-picture.jpg";
    $("#edit-user-title").html("Tambah Pengguna");
    $("#edit-user-name").val("");
    $("#edit-user-username").val("");
    $("#edit-user-phone").val("");
    $("#edit-user-email").val("");
    $("#edit-user-password").val("");
    $("#edit-user-vip").val("");
    $("#active-connections-div").css("display", "none");
    $("#active-connections").val("").css("display", "none");
    $("#maximum-connections").val("1");
    $("#edit-user-confirmed").prop("checked", false);
    $("#edit-user-city").val("");
    $("#edit-user-profile-picture").attr("src", currentProfilePicture);
    $("#is-trial option")[0].selected = true;
    $("#edit-user-container").css("display", "flex").hide().fadeIn(300);
    $("#edit-user-ok").html("Tambah").unbind().on("click", function () {
        var name = $("#edit-user-name").val().trim();
        var username = $("#edit-user-username").val().trim();
        var phone = $("#edit-user-phone").val().trim();
        var email = $("#edit-user-email").val().trim();
        var password = $("#edit-user-password").val().trim();
        var vipPassword = $("#edit-user-vip").val().trim();
        var city = $("#edit-user-city").val().trim();
        var endDateString = $("#end-time").val();
        var year = parseInt(endDateString.split("-")[0]);
        var month = parseInt(endDateString.split("-")[1]);
        var day = parseInt(endDateString.split("-")[2]);
        var endDate = new Date();
        endDate.setFullYear(year);
        endDate.setMonth(month);
        endDate.setDate(day);
        var endDateInt = endDate.getTime();
        var isTrial = $("#is-trial option:selected").index();
        var confirmed = 0;
        if ($("#edit-user-confirmed").prop("checked")) {
            confirmed = 1;
        }
        var activeConnections = 0;
        var maximumConnections = currentMaximumConnections;
        if (username == "") {
            show("Mohon masukkan nama pengguna");
            return;
        }
        if (email == "") {
            show("Mohon masukkan email");
            return;
        }
        if (password == "") {
            show("Mohon masukkan kata sandi");
            return;
        }
        if (vipPassword == "") {
            show("Mohon masukkan kata sandi VIP");
            return;
        }
        if (maximumConnections <= 0) {
            show("Mohon masukkan jumlah maksimal koneksi aktif");
            return;
        }
        showProgress("Membuat pengguna");
        var fd = new FormData();
        fd.append("name", name);
        fd.append("username", username);
        fd.append("phone", phone);
        fd.append("password", password);
        fd.append("vip_password", vipPassword);
        fd.append("email", email);
        fd.append("active_connections", activeConnections);
        fd.append("maximum_connections", maximumConnections);
        fd.append("confirmed", confirmed);
        fd.append("city", city);
        fd.append("trial", isTrial);
        fd.append("end_date", endDateInt);
        if (currentProfilePicture != "img/profile-picture.jpg") {
            fd.append("profile_picture_set", 1);
        } else {
            fd.append("profile_picture_set", 0);
        }
        var userId = uuidv4();
        if (userId) {
            fd.append("user_id", userId);
            $.ajax({
                type: 'POST',
                url: PHP_PATH + 'create-user.php',
                data: fd,
                processData: false,
                contentType: false,
                cache: false,
                success: function (response) {
                    hideProgress();
                    if (response == 0) {
                        $.ajax({
                            type: 'GET',
                            url: PHP_PATH + 'get-user-by-email.php',
                            data: {'email': email},
                            dataType: 'text',
                            cache: false,
                            success: function (a) {
                                if (a != -1) {
                                    var userInfo = JSON.parse(a);
                                    firebase.database().ref("users/" + userInfo["id"]).set({
                                        email: email,
                                        username: username,
                                        password: password
                                    });
                                }
                            }
                        });
                        $("#edit-user-container").fadeOut(300);
                        getUsers();
                    } else if (response == -1) {
                        show("Nama pengguna sudah digunakan");
                    } else if (response == -2) {
                        show("Nomor HP sudah digunakan");
                    } else if (response == -3) {
                        show("Email sudah digunakan");
                    } else {
                        show("Kesalahan: " + response);
                    }
                }
            });
        }
    });
}

function closeEditUserDialog() {
    $("#edit-user-container").fadeOut(300);
}

function generateRandomUsername() {
    var userName = generateRandomID(14);
    $("#edit-user-username").val(userName);
}

function increaseMaxConn() {
    currentMaximumConnections++;
    $("#maximum-connections").val(currentMaximumConnections);
}

function decreaseMaxConn() {
    if (currentMaximumConnections > 1) {
        currentMaximumConnections--;
    }
    $("#maximum-connections").val(currentMaximumConnections);
}

function selectProfilePicture() {
    $("#edit-user-select-profile-picture").on("change", function () {
        var fr = new FileReader();
        fr.onload = function () {
            $("#edit-user-profile-picture").attr("src", fr.result);
        };
        fr.readAsDataURL($(this).prop("files")[0]);
    });
    $("#edit-user-select-profile-picture").click();
}

function search() {
    var keyword = $("#keyword").val().toLowerCase().trim();
    $("#users").find("*").remove();
    users = [];
    if (keyword == "") {
        for (var i = 0; i < allUsers.length; i++) {
            var user = allUsers[i];
            users.push(user);
            displayUser(user, i+1);
        }
    } else {
        for (var i = 0; i < allUsers.length; i++) {
            var user = allUsers[i];
            console.log("Searched user name: "+keyword);
            console.log("User name: "+user["username"]);
            if (user["username"].toLowerCase().trim().includes(keyword)) {
                users.push(user);
                displayUser(user, i+1);
            }
        }
    }
}

function displayUser(user, position) {
    var trial = "Tidak";
    if (parseInt(user["is_trial"]) == 1) {
        trial = "Ya";
    }
    var endDate = parseInt(user["end_date"]);
    var currentDate = new Date().getTime();
    var remainingTimeMillis = endDate - currentDate;

    var remainingSeconds = Math.floor((remainingTimeMillis / 1000) % 60);
    var remainingMinutes = Math.floor((remainingTimeMillis / 1000 / 60) % 60);
    var remainingHours = Math.floor((remainingTimeMillis / (1000 * 60 * 60)) % 24);
    var remainingDays = Math.floor(remainingTimeMillis / (1000 * 60 * 60 * 24));
    var remainingMonths = Math.floor(remainingTimeMillis / (1000 * 60 * 60 * 24 * 30));
    var remainingYears = Math.floor(remainingTimeMillis / (1000 * 60 * 60 * 24 * 30 * 12));

    var remainingTimeString = "";
    if (remainingSeconds > 0) {
        remainingTimeString = (remainingSeconds + " detik ");
    }
    if (remainingMinutes > 0) {
        remainingTimeString = (remainingMinutes + " menit ");
    }
    if (remainingHours > 0) {
        remainingTimeString = (remainingHours + " jam ");
    }
    if (remainingDays > 0) {
        remainingTimeString = (remainingDays + " hari ");
    }
    if (remainingMonths > 0) {
        remainingTimeString = (remainingMonths + " bulan ");
    }
    if (remainingYears > 0) {
        remainingTimeString = (remainingYears + " tahun ");
    }
    $("#users").append("" +
        "<tr>" +
        "<td><div style='background-color: #2f2e4d; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; color: white;'>" + position + "</div></td>" +
        "<td><div class='custom-control custom-checkbox'>"+
            "<input type='checkbox' class='custom-control-input' id='defaultUnchecked"+position+"'>"+
            "<label class='custom-control-label' for='defaultUnchecked"+position+"'></label>"+
        "</div></td>"+
        "<td>" + user["name"] + "</td>" +
        "<td>" + user["email"] + "</td>" +
        "<td>" + user["username"] + "</td>" +
        "<td>" + user["active_connections"] + "</td>" +
        "<td>" + trial + "</td>" +
        "<td>" + remainingTimeString + "</td>" +
        "<td><a class='edit-user link'>Ubah</a></td>" +
        "<td><a class='delete-user link'>Hapus</a></td>" +
        "</tr>"
    );
    setUserClickListener();
}