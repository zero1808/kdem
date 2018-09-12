var i_damage = 0;
var damages = null;
var damageAreas = null;
var damageSeverities = null;
var $dropZone = null;
var $claimStored = null;
var labelErrors = null;

$(document).ajaxStart(function () {
    showSpinner();
});

$(document).ready(function () {
    getDamages();
    init();



    $('#claim_submit').click(function () {
        swal({
            title: "¿Deseas registrar?",
            text: "Verifica la información proporcionada",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#1c84c6",
            confirmButtonText: "Registrar",
            closeOnConfirm: false
        }, function () {
            swal.close();
            $("#i_damage").val(i_damage);
            $("#will_be_saved").val("0");
            cleanLabelErrors();
            $("#claim_form").submit();
        });
    });

    $('#claim_save').click(function () {
        swal({
            title: "¿Deseas guardar?",
            text: "Podrás modificarlo posteriormente en la seccion de guardadas",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#1c84c6",
            confirmButtonText: "Guardar",
            closeOnConfirm: false
        }, function () {
            swal.close();
            $("#i_damage").val(i_damage);
            $("#will_be_saved").val("1");
            cleanLabelErrors();
            $("#claim_form").submit();

        });
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

    $('#delete_danger').click(function () {
        removeDamage();
    });

    $('#add_danger').click(function () {
        addDamage();
    });

    function getDamages() {
        $.ajax({
            type: "GET",
            url: path + "/damages",
            cache: false,
            processData: false,
            success: function (response)
            {
                var response = $.parseJSON(response);
                prepareGetInitialDamages(response);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("Status: " + textStatus);
                alert("Error: " + errorThrown);
            }
        });
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
    $('.clockpicker').clockpicker();


});

async function prepareResponse(response) {
    if (response.status.http_response == 400) {
        await sleep(1500);
        $("#validationerror_title").text("Existen errores en la información proporcionada");
        $("#validationerror_subtitle").text("Verifique los campos marcados en rojo");
        $("#validationerror_icon").attr('class', 'fa fa-info modal-icon');
        $("#validationerror_icon").css('color', 'orange');
        labelErrors = response.errors;
        jQuery.each(response.errors, function (i, val) {
            $("#" + i + "_error_control").addClass("has-error");
            $("#" + i + "_error_control").attr("data-toggle", "tooltip");
            $("#" + i + "_error_control").attr("data-placement", "top");
            $("#" + i + "_error_control").attr("data-original-title", val);
            $("#" + i + "_error_control").attr("title", "");
        });
        hideSpinner();
        $("#validationerror_modal").modal('show');
    } else if (response.status.http_response == 200) {
        $claimStored = response.response.claimStored;
        if ($dropZone[0].dropzone.files.length > 0) {
            $dropZone[0].dropzone.processQueue();
        } else {
            await sleep(1500);
            onSuccessStore(response.response.claimVin);
        }
    } else if (response.status.http_response == 500) {
        await sleep(1500);
        $("#notification_title").text("Ocurrio un error al registrar la reclamación");
        $("#notification_subtitle").text(response.message);
        $("#notification_icon").attr('class', 'fa fa-info modal-icon');
        $("#notification_icon").css('color', 'red');
        var message = '<p>' + response.exception + '</p>';
        $("#notification_content").html(message);
        hideSpinner();
        launchNotification();

    } else if (response.status.http_response == 401) {

    } else {

    }
}

async function prepareGetInitialDamages(response) {
    if (response.status.http_response == 400) {
        await sleep(1000);
        $("#notification_title").text("Existen errores en la información proporcionada");
        $("#notification_subtitle").text("Verifique los campos");
        $("#notification_icon").attr('class', 'fa fa-info modal-icon');
        $("#notification_icon").css('color', 'orange');
        hideSpinner();
        launchNotification();
    } else if (response.status.http_response == 200) {
        await sleep(1000);
        damages = response.response.damages;
        damageAreas = response.response.damage_areas;
        damageSeverities = response.response.damage_severities;
        addDamage();
        hideSpinner();

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
    $claimStored = null;
    i_damage = 0;
    $("#vin").val(null);
    $("#vin_confirmation").val(null);
    $("#arrive_date").val(null);
    $("#car_model").val("0");
    $("#carrier").val("0");
    $("#responsable_name").val(null);
    $("#responsable_phone").val(null);
    $("#responsable_email").val(null);
    document.getElementById("btt_reset_carryletter").click();
    document.getElementById("btt_reset_checklist").click();
    $dropZone[0].dropzone.removeAllFiles(true);
    reportDateTime();
    cleanDamage();
    addDamage();
    cleanLabelErrors();

}

//this function doesnt permit to copy and paste
window.onload = function () {

    reportDateTime();
    var vin = document.getElementById('vin');
    var vin_confirmation = document.getElementById('vin_confirmation');

    vin.onpaste = function (e) {
        e.preventDefault();
    }

    vin.oncopy = function (e) {
        e.preventDefault();
    }

    vin_confirmation.onpaste = function (e) {
        e.preventDefault();
    }

    vin_confirmation.oncopy = function (e) {
        e.preventDefault();
    }

}

function cleanDamage() {
    $("#damages").html("");
}

function init() {
    reportDateTime();
    bs_input_file_carryletter();
    bs_input_file_checklist();


    $dropZone = $("div#file_upload").dropzone({
        url: path + "/pics",
        paramName: "file",
        maxFilesize: "15Mb",
        acceptedFiles: ".jpg,.jpeg,.png", // Accept images only
        addRemoveLinks: true,
        uploadMultiple: true,
        maxFiles: 10,
        parallelUploads: 10,
        autoProcessQueue: false,
        init: function () {
            this.on("sendingmultiple", function (file, xhr, formData) {
                formData.append("_token", $('[name="_token"]').val());
                formData.append("claim_stored", $claimStored);
            });

            this.on("errormultiple", function (file, xhr) {
                var response = $.parseJSON(xhr);
                prepareResponsePics(response);
            });

            this.on("successmultiple", function (file, xhr) {
                var response = $.parseJSON(xhr);
                prepareResponsePics(response);
            });
        }
    });

    Dropzone.autoDiscover = false;

}

function addDamage() {
    i_damage += 1;
    var damageHtml = "<p><span class='label label-info'>" + i_damage + "</span></p><div class='form-group'><label class='col-sm-2 control-label'>&Aacute;rea daño</label>"
            + "<div class='col-sm-4' id='damage_area_" + i_damage + "_error_control'><select class='form-control' name='damage_area_" + i_damage + "' id='damage_area_" + i_damage + "'>"
            + "<option value='0'>------ Seleccione ------</option></select></div>"
            + "<label class='col-sm-2 control-label'>Tipo daño</label><div class='col-sm-4' id='damage_type_" + i_damage + "_error_control'>"
            + "<select class='form-control' name='damage_type_" + i_damage + "' id='damage_type_" + i_damage + "'>"
            + "<option value='0'>------ Seleccione ------</option></select></div></div>"
            + "<div class='form-group'><label class='col-sm-2 control-label'>Severidad del daño</label>"
            + "<div class='col-sm-10' id='damage_severity_" + i_damage + "_error_control'><select class='form-control' name='damage_severity_" + i_damage + "' id='damage_severity_" + i_damage + "'>"
            + "<option value='0'>------ Seleccione ------</option></select></div></div>";



    var quotationHtml = "<div id='report_quoation_" + i_damage + "'><div class='form-group' >"
            + "<label class='col-sm-2 control-label'>Costo refacciones</label><div class='col-sm-2'>"
            + "<div class='input-group m-b'><span class='input-group-addon'>$</span>"
            + "<input type='text' class='form-control' id='amount_pieces_" + i_damage + "' name='amount_pieces_" + i_damage + "' value='0.00' onfocusout='calculateQuotation(this," + i_damage + ")' onkeypress='return validateFloatKeyPress(this, event);'>"
            + "</div></div><label class='col-sm-2 control-label'>Costo pintura</label><div class='col-sm-2'>"
            + "<div class='input-group m-b'><span class='input-group-addon'>$</span>"
            + "<input type='text' class='form-control' id='amount_paint_" + i_damage + "' name='amount_paint_" + i_damage + "' value='0.00' onfocusout='calculateQuotation(this," + i_damage + ")' onkeypress='return validateFloatKeyPress(this, event);'>"
            + "</div></div><label class='col-sm-2 control-label'>Costo mano de obra</label><div class='col-sm-2'>"
            + "<div class='input-group m-b'><span class='input-group-addon'>$</span>"
            + "<input type='text' class='form-control' id='amount_hand_" + i_damage + "' name='amount_hand_" + i_damage + "' value='0.00' onfocusout='calculateQuotation(this," + i_damage + ")' onkeypress='return validateFloatKeyPress(this, event);'>"
            + "</div></div></div><div class='form-group'><label class='col-sm-2 control-label'>Subtotal</label><div class='col-sm-2'>"
            + "<div class='input-group m-b'><span class='input-group-addon'>$</span> <input type='text' class='form-control' id='amount_subtotal_" + i_damage + "' value='0.00' name='amount_subtotal_" + i_damage + "' onkeypress='return validateFloatKeyPress(this, event);' readonly='true'>"
            + "</div></div><label class='col-sm-2 control-label'>Trabajo por reparaci&oacute;n</label>"
            + "<div class='col-sm-2'><div class='input-group m-b'><span class='input-group-addon'>$</span>"
            + "<input type='text' class='form-control' id='amount_reparation_" + i_damage + "' name='amount_reparation_" + i_damage + "' value='0.00' onkeypress='return validateFloatKeyPress(this, event);' readonly='true'>"
            + "</div></div><label class='col-sm-2 control-label'>Total</label><div class='col-sm-2'>"
            + "<div class='input-group m-b'><span class='input-group-addon'>$</span> <input type='text' class='form-control' id='amount_total_" + i_damage + "' value='0.00' name='amount_total_" + i_damage + "' onkeypress='return validateFloatKeyPress(this, event);' readonly='true'>"
            + "</div></div></div></div>";
    $("#damages").append("<div id='damage_"
            + i_damage + "'>" + damageHtml + quotationHtml
            + getHrLine() + "</div>");
    addDataToDamage(i_damage);
}


function addDataToDamage(i_damage) {
    for (var i = 0; i < damages.length; i++) {
        $("#damage_type_" + i_damage).append("<option value='" + damages[i].id + "'>" + damages[i].number + " - " + damages[i].name + "</option>");
    }
    for (var i = 0; i < damageAreas.length; i++) {
        $("#damage_area_" + i_damage).append("<option value='" + damageAreas[i].id + "'>" + damageAreas[i].number + " - " + damageAreas[i].name + "</option>");
    }

    for (var i = 0; i < damageSeverities.length; i++) {
        $("#damage_severity_" + i_damage).append("<option value='" + damageSeverities[i].id + "'>" + damageSeverities[i].number + " - " + damageSeverities[i].name + "</option>");
    }
}

function removeDamage() {
    if (i_damage > 1) {
        $("#damage_" + i_damage).remove();
        i_damage -= 1;
    }
}

function getHrLine() {
    return "<div class='hr-line-dashed'></div>";
}

function calculateQuotation(field, i_damage) {
    if ($("#" + field.id).val() === null || $("#" + field.id).val() === "" || Number.isNaN($("#" + field.id).val())) {
        $("#" + field.id).val(0);
    }
    $("#" + field.id).val(parseFloat($("#" + field.id).val()).toFixed(2));
    var spareParts = parseFloat($("#amount_pieces_" + i_damage).val());
    var paint = parseFloat($("#amount_paint_" + i_damage).val());
    var hand = parseFloat($("#amount_hand_" + i_damage).val());
    var subtotal = parseFloat(spareParts + paint + hand).toFixed(2);
    var iva = parseFloat((subtotal) * (0.16)).toFixed(2);
    var total = parseFloat(subtotal) + parseFloat(iva);
    $("#amount_reparation_" + i_damage).val(parseFloat(iva).toFixed(2));
    $("#amount_subtotal_" + i_damage).val(parseFloat(subtotal).toFixed(2));
    $("#amount_total_" + i_damage).val(parseFloat(total).toFixed(2));

}

function reportDateTime() {
    var today = getToday();
    momentoActual = new Date()
    hora = momentoActual.getHours()
    minuto = momentoActual.getMinutes()
    segundo = momentoActual.getSeconds()

    str_segundo = new String(segundo)
    if (str_segundo.length == 1)
        segundo = "0" + segundo

    str_minuto = new String(minuto)
    if (str_minuto.length == 1)
        minuto = "0" + minuto

    str_hora = new String(hora)
    if (str_hora.length == 1)
        hora = "0" + hora


    if (str_hora >= 12 && str_hora < 24)
    {
        ampm = "P.M";
    }
    //Declaración de A.M. 
    if (str_hora < 12 && str_hora >= 0) {
        ampm = "A.M";
    }
    horaImprimible = hora + ":" + minuto + ":" + segundo;

    $("#report_date").val(today + " " + horaImprimible);

    setTimeout("reportDateTime()", 1000);
}

async function prepareResponsePics(response) {
    if (response.status.http_response == 400) {
        await sleep(1500);
        hideSpinner();
        console.log($("#will_be_saved").val());

        if ($("#will_be_saved").val() === "1") {
            swal({
                title: "Guardada con exito!",
                text: "El reclamación se guardo exitosamente.\nPero ocurrio un error al guardar las imagenes Codigo 400.",
                type: "warning"
            });
        } else {

            swal({
                title: "Registrada con exito!",
                text: "La reclamación se registro exitosamente.\nPero ocurrio un error al guardar las imagenes Codigo 400.",
                type: "warning"
            });
        }
        cleanFields();
    } else if (response.status.http_response == 200) {
        await sleep(1500);
        onSuccessStore(response.response);

    } else if (response.status.http_response == 500) {
        await sleep(1500);
        cleanFields();
        hideSpinner();
        swal({
            title: "Registrada con exito!",
            text: response.response,
            type: "warning"
        });
    } else if (response.status.http_response == 401) {

    } else {

    }
}


function onSuccessStore(vin) {
    var auxClaimStored = $claimStored;
    console.log($("#will_be_saved").val());
    if ($("#will_be_saved").val() === "1") {
        $("#notification_title").text("La reclamación se ha guardado con exito!.");
        $("#notification_subtitle").text("VIN: " + vin);
        $("#notification_icon").attr('class', 'fa fa-check modal-icon');
        $("#notification_icon").css('color', 'green');
        var message = '<center><p><h4>Podras consultarla en la sección de \n"Guardadas"</h4></p></center>';
        $("#notification_content").html(message);
    } else {
        $("#notification_title").text("La reclamación se registro con exito!.");
        $("#notification_subtitle").text("VIN: " + vin);
        $("#notification_icon").attr('class', 'fa fa-check modal-icon');
        $("#notification_icon").css('color', 'green');
        var message = '<center><p>Descargar ficha de registro</p></center><p><center><a href="' + path + '/claimreport/' + auxClaimStored + '" target="_blank"><img src="' + path + '/img/pdf_download.png" alt="' + vin + '" height="85" width="65"/></a></center></p>';
        $("#notification_content").html(message);
    }
    cleanFields();
    hideSpinner();
    launchNotification();
}

//input files carry letter and checklist

function bs_input_file_carryletter() {
    $("#carry_letter_input").before(
            function () {
                if (!$(this).prev().hasClass('input-ghost')) {
                    var element = $("<input type='file' class='input-ghost' accept='application/pdf' id='carry_letter' name='carry_letter' style='visibility:hidden; height:0'>");
                    element.attr("name", $(this).attr("name"));
                    element.change(function () {
                        element.next(element).find('input').val((element.val()).split('\\').pop());
                    });
                    $(this).find("button.btn-choose").click(function () {
                        element.click();
                    });
                    $(this).find("button.btn-reset").click(function () {
                        element.val(null);
                        $(this).parents("#carry_letter_input").find('input').val('');
                    });
                    $(this).find('input').css("cursor", "pointer");
                    $(this).find('input').mousedown(function () {
                        $(this).parents('#carry_letter_input').prev().click();
                        return false;
                    });
                    return element;
                }
            }
    );
}

function bs_input_file_checklist() {
    $("#checklist_input").before(
            function () {
                if (!$(this).prev().hasClass('input-ghost')) {
                    var element = $("<input type='file' class='input-ghost' accept='application/pdf' id='checklist' name='checklist' style='visibility:hidden; height:0'>");
                    element.attr("name", $(this).attr("name"));
                    element.change(function () {
                        element.next(element).find('input').val((element.val()).split('\\').pop());
                    });
                    $(this).find("button.btn-choose").click(function () {
                        element.click();
                    });
                    $(this).find("button.btn-reset").click(function () {
                        element.val(null);
                        $(this).parents("#checklist_input").find('input').val('');
                    });
                    $(this).find('input').css("cursor", "pointer");
                    $(this).find('input').mousedown(function () {
                        $(this).parents('#checklist_input').prev().click();
                        return false;
                    });
                    return element;
                }
            }
    );
}

function cleanLabelErrors() {
    if (labelErrors !== null) {
        jQuery.each(labelErrors, function (i, val) {
            $("#" + i + "_error_control").removeClass("has-error");
            $("#" + i + "_error_control").removeAttr("data-toggle");
            $("#" + i + "_error_control").removeAttr("data-placement");
            $("#" + i + "_error_control").removeAttr("data-original-title");
            $("#" + i + "_error_control").removeAttr("title");
        });
    }

}