var isShowingLoader = false;
var isShowingNotification = false;
init();

function init() {
    $("#arrive_date").val(getToday);
}

$('#cancel_btt').click(function () {
    cleanFields();
});
$('#arrive_date_div .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: true,
    autoclose: true,
    format: 'yyyy-mm-dd', //check change
});
$('#report_date_div .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: true,
    autoclose: true,
    format: "yyyy-mm-dd"

});
$(document).ready(function () {
    $("div#file_upload").dropzone({
        url: "/file/post",
        headers: $('[name="_token"]').val(),
        paramName: "file",
        maxFilesize: "10Mb",
        acceptedFiles: ".pdf,.docx,.xlsx", // Accept images only
        addRemoveLinks: true,
        uploadMultiple: true,
        maxFiles: 4,
        autoProcessQueue: false,
    });
    Dropzone.autoDiscover = false;
    $('#claim_submit').click(function () {
        $("#claim_form").submit();
    });
    $("#claim_form").submit(function () {
        var formObj = $(this);
        var formData = new FormData(this);
        var url = path + "/claims";
        $.ajax({
            type: "POST",
            url: url,
            data: formData, // de forma seriada los elementos del form
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            success: function (response)
            {
                var response = $.parseJSON(response);
                prepareResponse(response);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("Status: " + textStatus);
                alert("Error: " + errorThrown);
            }
        });
        return false; // evitar la ejecucion del form si algo falla
    });
});
$(document).ajaxStart(function () {
    if (isShowingLoader == false) {
        isShowingLoader = true;
        $("#loader_modal").modal('show');
    }
});
async function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function closeLoader() {
    if (isShowingLoader == true) {
        isShowingLoader = false;
        $("#loader_modal").modal('hide');
    }
}

function launchNotification() {
    if (isShowingNotification == false) {
        isShowingNotification = true;
        $("#notification_modal").modal('show');
    }
}

function closeNotification() {
    if (isShowingNotification == true) {
        isShowingNotification = false;
        $("#notification_modal").modal('hide');
    }
}
async function prepareResponse(response) {
    if (response.status.http_response == 400) {
        await sleep(1500);
        $("#notification_title").text("Existen errores en la información proporcionada");
        $("#notification_subtitle").text("Verifique los campos");
        $("#notification_icon").attr('class', 'fa fa-info modal-icon');
        $("#notification_icon").css('color', 'orange');
        var erroMessage = "";
        jQuery.each(response.errors, function (i, val) {
            var item = "<li>" + val + "</li>";
            erroMessage = erroMessage + item;
        });
        erroMessage = "<ul>" + erroMessage + "</ul>";
        $("#notification_content").html(erroMessage);

        closeLoader();
        $("#notification_modal").modal('show');
    } else if (response.status.http_response == 200) {
        await sleep(1500);
        closeLoader();
        cleanFields()
        swal({
            title: "Registrada con exito!",
            text: "Se ha almacenado exitosamente el Claim con VIN \n" + response.response,
            type: "success"
        });
    } else if (response.status.http_response == 500) {

    } else if (response.status.http_response == 401) {

    } else {

    }
}


function getToday() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }

    today = yyyy + '-' + mm + '-' + dd;
    return today;
}

function cleanFields() {
    $("#vin").val("");
    $("#arrive_date").val(getToday());
    $("#report_date").val("");
    $("#amount_smx").val("");
    $("#amount_gmx").val("");
    $("#car_model").val("0");
    $("#damage_area").val("0");
    $("#damage_type").val("0");
    $("#carrier").val("0");
    $("#status").val("0");

}