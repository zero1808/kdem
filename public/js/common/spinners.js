/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var isShowingLoader = false;
var isShowingNotification = false;

async function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function hideSpinner() {
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

function showSpinner() {
    if (isShowingLoader == false) {
        isShowingLoader = true;
        $("#loader_modal").modal('show');
    }
}
