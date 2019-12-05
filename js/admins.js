var currentActiveConnections = 0;
var currentMaximumConnections = 1;
var currentProfilePicture = "";
var admins;

$(document).ready(function() {
    getAdmins();
});

function getAdmins() {
    $("#admins").find("*").remove();
    showProgress("Memuat admin");
    $.ajax({
        type: 'GET',
        url: PHP_PATH+'get-admins.php',
        dataType: 'text',
        cache: false,
        success: function(a) {
            admins = JSON.parse(a);
            for (var i=0; i<admins.length; i++) {
                var admin = admins[i];
                var trial = "Tidak";
                if (parseInt(admin["is_trial"]) == 1) {
                    trial = "Ya";
                }
                $("#admins").append(""+
                    "<tr>"+
                    "<td><div style='background-color: #2f2e4d; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; color: white;'>"+i+"</div></td>"+
                    "<td>"+admin["first_name"]+" "+admin["last_name"]+"</td>"+
                    "<td>"+admin["phone"]+"</td>"+
                    "<td>"+admin["password"]+"</td>"+
                    "<td>"+admin["email"]+"</td>"+
                    "<td><a class='edit-admin link'>Ubah</a></td>"+
                    "<td><a class='delete-admin link'>Hapus</a></td>"+
                    "</tr>"
                );
            }
            hideProgress();
            setAdminClickListener();
        }
    });
}

function setAdminClickListener() {
    $(".edit-admin").on("click", function() {
        var tr = $(this).parent().parent();
        var index = tr.parent().children().index(tr);
        var admin = admins[index];
        $("#edit-admin-title").html("Ubah Admin");
        $("#edit-admin-name").val(admin["name"]);
        $("#edit-admin-phone").val(admin["phone"]);
        $("#edit-admin-email").val(admin["email"]);
        $("#edit-admin-password").val(admin["password"]);
        if (admin["verified"] == 0) {
            $("#accepted option")[0].selected = true;
        } else {
            $("#accepted option")[1].selected = true;
        }
        $("#edit-admin-container").css("display", "flex").hide().fadeIn(300);
        $("#edit-admin-ok").html("Ubah").unbind().on("click", function() {
            var name = $("#edit-admin-name").val().trim();
            var phone = $("#edit-admin-phone").val().trim();
            var email = $("#edit-admin-email").val().trim();
            var password = $("#edit-admin-password").val().trim();
            var accepted = $("#accepted option:selected").index();
            if (name == "") {
                show("Mohon masukkan nama");
                return;
            }
            if (phone == "") {
                show("Mohon masukkan nomor HP");
                return;
            }
            if (password == "") {
                show("Mohon masukkan kata sandi");
                return;
            }
            /*if (activeConnections <= 0) {
                show("Mohon masukkan jumlah koneksi aktif minimal 1");
                return;
            }*/
            showProgress("Menambah admin");
            var fd = new FormData();
            fd.append("id", admin["id"]);
            fd.append("name", name);
            fd.append("phone", phone);
            fd.append("password", password);
            fd.append("email", email);
            fd.append("verified", accepted);
            $.ajax({
                type: 'POST',
                url: PHP_PATH+'edit-admin.php',
                data: fd,
                processData: false,
                contentType: false,
                cache: false,
                success: function(a) {
                    hideProgress();
                    var response = a;
                    console.log("Response: "+response);
                    if (response == 0) {
                        $("#edit-admin-container").fadeOut(300);
                        getAdmins();
                    } else if (response == -1) {
                        show("Nama admin sudah digunakan");
                    } else if (response == -2) {
                        show("Nomor HP sudah digunakan");
                    } else if (response == -3) {
                        show("Email sudah digunakan");
                    } else {
                        show("Kesalahan: "+response);
                    }
                }
            });
        });
    });
    $(".delete-admin").on("click", function() {
        var tr = $(this).parent().parent();
        var index = tr.parent().children().index(tr);
        var admin = admins[index];
        $("#confirm-title").html("Hapus Admin");
        $("#confirm-msg").html("Apakah Anda yakin ingin menghapus admin ini?");
        $("#confirm-ok").unbind().on("click", function() {
            $("#confirm-container").hide();
            if (admins.length == 1) {
                show("Tidak bisa menghapus admin. Minimal harus ada 1 admin yang terdaftar.");
                return;
            }
            showProgress("Menghapus admin");
            $.ajax({
                type: 'GET',
                url: PHP_PATH+'delete-admin.php',
                data: {'id': admin["id"]},
                dataType: 'text',
                cache: false,
                success: function(a) {
                    hideProgress();
                    show("Admin berhasil dihapus");
                    getAdmins();
                }
            });
        });
        $("#confirm-cancel").unbind().on("click", function() {
            $("#confirm-container").fadeOut(300);
        });
        $("#confirm-container").css("display", "flex").hide().fadeIn(300);
    });
}

function addAdmin() {
    currentActiveConnections = 0;
    currentMaximumConnections = 1;
    currentProfilePicture = "img/profile-picture.jpg";
    $("#edit-admin-title").html("Tambah Admin");
    $("#edit-admin-name").val("");
    $("#edit-admin-phone").val("");
    $("#edit-admin-email").val("");
    $("#edit-admin-password").val("");
    $("#accepted option")[0].selected = true;
    $("#edit-admin-container").css("display", "flex").hide().fadeIn(300);
    $("#edit-admin-ok").html("Tambah").unbind().on("click", function() {
        var name = $("#edit-admin-name").val().trim();
        var phone = $("#edit-admin-phone").val().trim();
        var email = $("#edit-admin-email").val().trim();
        var password = $("#edit-admin-password").val().trim();
        var accepted = $("#accepted option:selected").index();
        if (name == "") {
            show("Mohon masukkan nama");
            return;
        }
        if (phone == "") {
            show("Mohon masukkan nomor HP");
            return;
        }
        if (password == "") {
            show("Mohon masukkan kata sandi");
            return;
        }
        showProgress("Membuat admin");
        var fd = new FormData();
        fd.append("name", name);
        fd.append("phone", phone);
        fd.append("password", password);
        fd.append("email", email);
        fd.append("verified", accepted);
        fd.append("register_date", new Date().getTime());
        $.ajax({
            type: 'POST',
            url: PHP_PATH+'create-admin.php',
            data: fd,
            processData: false,
            contentType: false,
            cache: false,
            success: function(a) {
                hideProgress();
                var response = a;
                console.log("Response: "+response);
                if (response == 0) {
                    $("#edit-admin-container").fadeOut(300);
                    getAdmins();
                } else if (response == -1) {
                    show("Nama admin sudah digunakan");
                } else if (response == -2) {
                    show("Nomor HP sudah digunakan");
                } else if (response == -3) {
                    show("Email sudah digunakan");
                } else {
                    show("Kesalahan: "+response);
                }
            }
        });
    });
}

function closeEditAdminDialog() {
    $("#edit-admin-container").fadeOut(300);
}