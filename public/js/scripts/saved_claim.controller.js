var i_damage = 0;
var damages = null;
var damageAreas = null;
var damageSeverities = null;
var $dropZone = null;
var $claimStored = null;
var labelErrors = null;
var claimObject = null;
var claimLoaded = null;
$(document).ajaxStart(function () {
    showSpinner();
});

$(document).ready(function () {
    init();
    getDamages();


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

    $("#claim_form").submit(function () {
        var formObj = $(this);
        var formData = new FormData(this);
        var url = path + "/claims/" + claimLoaded;
        $.ajax({
            type: "POST",
            url: url,
            data: formData, // de forma seriada los elementos del form
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
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
        addDamage(false, null);
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
        await sleep(500);
        damages = response.response.damages;
        damageAreas = response.response.damage_areas;
        damageSeverities = response.response.damage_severities;
        hideSpinner();
        loadClaimStored();


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
    addDamage(false, null);
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
    claimLoaded = $("#claim_stored").val();
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

function addDamage(hasData, damage) {
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


    if (hasData) {
        var amount_pieces = damage.damage_quotation.amount_pieces === 0 ? "0.00" : damage.damage_quotation.amount_pieces;
        var amount_paint = damage.damage_quotation.amount_paint === 0 ? "0.00" : damage.damage_quotation.amount_paint;
        var amount_hand = damage.damage_quotation.amount_hand === 0 ? "0.00" : damage.damage_quotation.amount_hand;
        var amount_subtotal = damage.damage_quotation.subtotal === 0 ? "0.00" : damage.damage_quotation.subtotal;
        var amount_reparation = damage.damage_quotation.iva === 0 ? "0.00" : damage.damage_quotation.iva;
        var amount_total = damage.damage_quotation.total === 0 ? "0.00" : damage.damage_quotation.total;
    } else {
        var amount_pieces = "0.00";
        var amount_paint = "0.00";
        var amount_hand = "0.00";
        var amount_subtotal = "0.00";
        var amount_reparation = "0.00";
        var amount_total = "0.00";
    }



    var quotationHtml = "<div id='report_quoation_" + i_damage + "'><div class='form-group' >"
            + "<label class='col-sm-2 control-label'>Costo refacciones</label><div class='col-sm-2'>"
            + "<div class='input-group m-b'><span class='input-group-addon'>$</span>"
            + "<input type='text' class='form-control' id='amount_pieces_" + i_damage + "' name='amount_pieces_" + i_damage + "' value='" + amount_pieces + "' onfocusout='calculateQuotation(this," + i_damage + ")' onkeypress='return validateFloatKeyPress(this, event);'>"
            + "</div></div><label class='col-sm-2 control-label'>Costo pintura</label><div class='col-sm-2'>"
            + "<div class='input-group m-b'><span class='input-group-addon'>$</span>"
            + "<input type='text' class='form-control' id='amount_paint_" + i_damage + "' name='amount_paint_" + i_damage + "' value='" + amount_paint + "' onfocusout='calculateQuotation(this," + i_damage + ")' onkeypress='return validateFloatKeyPress(this, event);'>"
            + "</div></div><label class='col-sm-2 control-label'>Costo mano de obra</label><div class='col-sm-2'>"
            + "<div class='input-group m-b'><span class='input-group-addon'>$</span>"
            + "<input type='text' class='form-control' id='amount_hand_" + i_damage + "' name='amount_hand_" + i_damage + "' value='" + amount_hand + "' onfocusout='calculateQuotation(this," + i_damage + ")' onkeypress='return validateFloatKeyPress(this, event);'>"
            + "</div></div></div><div class='form-group'><label class='col-sm-2 control-label'>Subtotal</label><div class='col-sm-2'>"
            + "<div class='input-group m-b'><span class='input-group-addon'>$</span> <input type='text' class='form-control' id='amount_subtotal_" + i_damage + "' value='" + amount_subtotal + "' name='amount_subtotal_" + i_damage + "' onkeypress='return validateFloatKeyPress(this, event);' readonly='true'>"
            + "</div></div><label class='col-sm-2 control-label'>Trabajo por reparaci&oacute;n</label>"
            + "<div class='col-sm-2'><div class='input-group m-b'><span class='input-group-addon'>$</span>"
            + "<input type='text' class='form-control' id='amount_reparation_" + i_damage + "' name='amount_reparation_" + i_damage + "' value='" + amount_reparation + "' onkeypress='return validateFloatKeyPress(this, event);' readonly='true'>"
            + "</div></div><label class='col-sm-2 control-label'>Total</label><div class='col-sm-2'>"
            + "<div class='input-group m-b'><span class='input-group-addon'>$</span> <input type='text' class='form-control' id='amount_total_" + i_damage + "' value='" + amount_total + "' name='amount_total_" + i_damage + "' onkeypress='return validateFloatKeyPress(this, event);' readonly='true'>"
            + "</div></div></div></div>";
    $("#damages").append("<div id='damage_"
            + i_damage + "'>" + damageHtml + quotationHtml
            + getHrLine() + "</div>");
    addDataToDamage(i_damage, hasData, damage);
}


function addDataToDamage(i_damage, hasData, damage) {
    for (var i = 0; i < damages.length; i++) {
        $("#damage_type_" + i_damage).append("<option value='" + damages[i].id + "'>" + damages[i].number + " - " + damages[i].name + "</option>");
    }
    for (var i = 0; i < damageAreas.length; i++) {
        $("#damage_area_" + i_damage).append("<option value='" + damageAreas[i].id + "'>" + damageAreas[i].number + " - " + damageAreas[i].name + "</option>");
    }

    for (var i = 0; i < damageSeverities.length; i++) {
        $("#damage_severity_" + i_damage).append("<option value='" + damageSeverities[i].id + "'>" + damageSeverities[i].number + " - " + damageSeverities[i].name + "</option>");
    }
    if (hasData) {
        $("#damage_type_" + i_damage).val(damage.damage.id);
        $("#damage_area_" + i_damage).val(damage.damage_area.id);
        $("#damage_severity_" + i_damage).val(damage.damage_severity.id);

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
        cleanFields();
        hideSpinner();
        $("#claim_submit").css("display", "none");
        swal({
            title: "Registrada con exito!",
            text: "El Claim se registro exitosamente.\nPero ocurrio un error al guardar las imagenes Codigo 400.",
            type: "warning"
        });
    } else if (response.status.http_response == 200) {
        await sleep(1500);
        onSuccessStore(response.response);

    } else if (response.status.http_response == 500) {
        await sleep(1500);
        cleanFields();
        hideSpinner();
        $("#claim_submit").css("display", "none");
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
    cleanFields();
    $("#notification_title").text("La reclamación se registro con exito!.");
    $("#notification_subtitle").text("VIN: " + vin);
    $("#notification_icon").attr('class', 'fa fa-check modal-icon');
    $("#notification_icon").css('color', 'green');
    var message = '<center><p>Descargar ficha de registro</p></center><p><center><a href="' + path + '/claimreport/' + auxClaimStored + '" target="_blank"><img src="' + path + '/img/pdf_download.png" alt="' + vin + '" height="85" width="65"/></a></center></p>' +
            "<center><p><a href='" + path + "/orders/saved'> Ir a reclamaciones guardadas </a></p></center>";
    $("#notification_content").html(message);
    $("#claim_submit").css("display", "none");
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
            console.log(i);
            $("#" + i + "_error_control").removeClass("has-error");
            $("#" + i + "_error_control").removeAttr("data-toggle");
            $("#" + i + "_error_control").removeAttr("data-placement");
            $("#" + i + "_error_control").removeAttr("data-original-title");
            $("#" + i + "_error_control").removeAttr("title");
        });
    }
}

function loadClaimStored() {
    $.ajax({
        type: "GET",
        url: path + "/claims/" + claimLoaded,
        data: {_token: $('[name="_token"]').val()},
        success: function (response)
        {
            var response = $.parseJSON(response);
            prepareResponseClaimStored(response);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus);
            alert("Error: " + errorThrown);
        }
    });
}

async function prepareResponseClaimStored(response) {
    if (response.status.http_response == 400) {
        await sleep(1500);

    } else if (response.status.http_response == 200) {
        await sleep(500);
        claimObject = response.response.claim;
        setFieldValues(response.response.claim);
        hideSpinner();
    } else if (response.status.http_response == 500) {
        await sleep(1500);
        hideSpinner();
    } else if (response.status.http_response == 401) {
        await sleep(1500);
    } else {

    }
}

function setFieldValues(claim) {
    $("#vin").val(claim.vin);
    $("#vin_confirmation").val(claim.vin);
    $("#car_model").val(claim.car_model === null ? 0 : claim.car_model.id);
    $("#arrive_date").val(claim.arrive_date);

    var arrive_date_time = claim.arrive_date_time;
    var res = arrive_date_time.split(":");

    $("#arrive_date_time").val(res[0] + ":" + res[1]);
    $("#carrier").val(claim.carrier === null ? 0 : claim.carrier.id);
    $("#responsable_name").val(claim.responsable_name);
    $("#responsable_phone").val(claim.responsable_phone);
    $("#responsable_email").val(claim.responsable_email);
    if (claim.src_carry_letter != null) {
        $('#carry_letter_loaded').css("display", 'block');
        $("#href_carry_letter").attr("href", "http://localhost:8888/kdem/public/storage/" + claim.src_carry_letter);
    }
    if (claim.src_checklist != null) {
        $('#checklist_loaded').css("display", 'block');
        $("#href_checklist").attr("href", "http://localhost:8888/kdem/public/storage/" + claim.src_checklist);
    }
    if (claim.damage_orders === null || claim.damage_orders.length === 0) {
        addDamage(false, null);
    } else {
        for (var i = 0; i < claim.damage_orders.length; i++) {
            addDamage(true, claim.damage_orders[i]);
        }
    }

    $("#previous_loaded_images").html("");

    if (claim.claim_pics !== null) {
        for (var i = 0; i < claim.claim_pics.length; i++) {
            $("#previous_loaded_images").append("<li style='background-image: url(http://localhost:8888/kdem/public/storage/" + claim.claim_pics[i].src_pic + "); background-size: 110px 110px'"
                    + "class='li-thumnail'><span class='span-thumnail'>Ver</span><span class='span-thumnail' onclick='deletePic(this," + claim.claim_pics[i].id + ")'>Borrar</span></li>");
        }

    }
}

$('.ul-thumnail').on('click', '.li-thumnail .span-thumnail:first-child',
        function () {
            if ($(this).parent().hasClass('selected')) {
                $(this).parent().removeClass('selected');
            } else {
                $(this).parent().addClass('selected');
            }
        }
);

function deletePic(element, id) {
    swal({
        title: "¿Deseas borrar la foto de esta reclamación?",
        text: "Una vez borrada no la podras recuperar",
        type: "warning",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#ed5565",
        confirmButtonText: "Borrar",
        closeOnConfirm: true
    }, function () {
        $.ajax({
            type: "POST",
            url: path + "/pics/" + id,
            data: {_token: $('[name="_token"]').val(), _method: 'DELETE'},
            success: function (response)
            {
                var response = $.parseJSON(response);
                prepareResponseDeletePic(response, element);

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("Status: " + textStatus);
                alert("Error: " + errorThrown);
            }
        });
    });

}


async function prepareResponseDeletePic(response, element) {
    if (response.status.http_response == 500) {
        await sleep(500);
        hideSpinner();
        swal({
            title: "Ocurrio un error al intentar borrar la foto!",
            text: response.response,
            type: "warning"
        });
    } else if (response.status.http_response == 200) {
        await sleep(1500);
        if (response.response.code == 0) {
            $(element).parent().fadeOut(300, function () {
                $(element).remove();
            });
            hideSpinner();
            swal({
                title: "Foto borrada con exito!",
                text: "La foto fue borrada y no la podras recuperar",
                type: "success"
            });
        } else {
            swal({
                title: "Ocurrio un error al intentar borrar la foto!",
                text: response.response,
                type: "warning"
            });
        }
        hideSpinner();
    } else {
        await sleep(1500);
        hideSpinner();
        swal({
            title: "Ocurrio un error al intentar borrar la foto!",
            text: response.response,
            type: "warning"
        });
    }
}