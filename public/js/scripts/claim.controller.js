
function deleteClaim(claim) {
    swal({
        title: "¿Deseas borrar esta reclamación?",
        text: "Una vez borrada no la podras recuperar",
        type: "warning",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#ec4758",
        confirmButtonText: "Borrar",
        closeOnConfirm: false
    }, function () {
        swal.close();
        showSpinner();
        $.ajax({
            type: "DELETE",
            url: path + "/claims/" + claim,
            data: {_token: $('[name="_token"]').val()},
            success: function (response)
            {
                var response = $.parseJSON(response);
                prepareResponseDelete(response);

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("Status: " + textStatus);
                alert("Error: " + errorThrown);
            }
        });
    });
}

async function prepareResponseDelete(response) {
    if (response.status.http_response == 400) {
        await sleep(1500);

        hideSpinner();
    } else if (response.status.http_response == 200) {
        await sleep(1500);
        hideSpinner();
        claimsTable.row('.selected').remove().draw(false);
        swal({
            title: "Reclamación borrada con exito!.",
            text: "VIN: " + response.response.claimVin,
            type: "success"
        });

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






