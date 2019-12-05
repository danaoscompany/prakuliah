const HOST = "localhost/prakuliah";
//const PHP_PATH = "http://iptvjoss.com/prakuliah/php/";
const PHP_PATH = "http://"+HOST+"/php/";

function show(msg) {
    $("#toast-msg").html(msg);
    $("#toast-container").css("display", "flex").hide().fadeIn(500);
    setTimeout(function() {
        $("#toast-container").fadeOut(500);
    }, 3000);
}

function showProgress(msg) {
    $("#loading-blocker").fadeIn(200);
    $("#loading-msg").html(msg+" . . .");
    $("#loading-container").css("margin-bottom", "0");
    var currentDotCount = 3;
    var progressMsgUpdater = function() {
        if (currentDotCount < 6) {
            currentDotCount++;
        } else {
            currentDotCount = 3;
        }
        var dotMsg = "";
        for (var i=0; i<currentDotCount; i++) {
            dotMsg += " ";
            dotMsg += ".";
        }
        $("#loading-msg").html(msg + dotMsg);
        setTimeout(progressMsgUpdater, 500);
    };
    setTimeout(progressMsgUpdater, 500);
}

function hideProgress() {
    $("#loading-blocker").fadeOut(200);
    $("#loading-container").css("margin-bottom", "-45px");
}

function showAlert(title, msg) {
    $("#alert-title").html(title);
    $("#alert-msg").html(msg);
    $("#alert-container").css("display", "flex").hide().fadeIn(300);
    $("#close-alert").unbind().on("click", function() {
        $("#alert-container").fadeOut(300);
    });
    $("#alert-ok").unbind().on("click", function() {
        $("#alert-container").fadeOut(300);
    });
}

function logout() {
    $("#confirm-title").html("Keluar");
    $("#confirm-msg").html("Apakah Anda yakin ingin keluar?");
    $("#confirm-ok").unbind().on("click", function() {
        $("#confirm-container").hide();
        $("#loading-blocker").show();
        showProgress("Sedang keluar");
        $.ajax({
            type: 'GET',
            url: PHP_PATH+'logout.php',
            dataType: 'text',
            cache: false,
            success: function(a) {
                window.location.href = "login.html";
            }
        });
    });
    $("#confirm-cancel").unbind().on("click", function() {
        $("#confirm-container").hide();
    });
    $("#confirm-container").css("display", "flex");
}

function openCommon() {
    window.location.href = "common.html";
}

function openUsers() {
    window.location.href = "users.html";
}

function openAdmins() {
    window.location.href = "admins.html";
}

function openArticles() {
    window.location.href = "articles.html";
}

function openPurchases() {
    window.location.href = "purchases.html";
}

function openChannels() {
    window.location.href = "channels.html";
}

function openNotifications() {
    window.location.href = "notifications.html";
}

function generateRandomID(length) {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < length; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function uuidv4() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

function constructFormData(...values) {
    var fd = new FormData();
    for (var i=0; i<values.length; i+=2) {
        fd.append(values[i], values[i+1]);
    }
    return fd;
}