var init = [];

$(document).ready(function() {

    $('body').on('click', '#replace-div th a', function() {

        //$('#mySearchForm')[0].reset();
        var obj = $('#mySearchForm').serializeArray();
        var url = $(this).attr("href");
        $('#replace-div').load(url, obj);
        return false;

    });

    $('#mySearchForm').submit(function(event) {

        event.preventDefault();
        search();
    });

    setTimeout(function() {

        $('#flashMessage').fadeOut();
    }, 6000);

    function startChange() {
        var startDate = start.value(),
            endDate = end.value();

        if (startDate) {
            startDate = new Date(startDate);
            startDate.setDate(startDate.getDate());
            end.min(startDate);
        } else if (endDate) {
            start.max(new Date(endDate));
        } else {
            endDate = new Date();
            start.max(endDate);
            end.min(endDate);
        }
    }

    function endChange() {
        var endDate = end.value(),
            startDate = start.value();

        if (endDate) {
            endDate = new Date(endDate);
            endDate.setDate(endDate.getDate());
            start.max(endDate);
        } else if (startDate) {
            end.min(new Date(startDate));
        } else {
            endDate = new Date();
            start.max(endDate);
            end.min(endDate);
        }
    }

    // var today = new Date();

    // var start = $(".startDate").kendoDatePicker({
    //     max: today,
    //     format: "yyyy-M-dd",
    //     dateInput: false,
    //     change: startChange
    // }).data("kendoDatePicker");

    // $(".startDate").attr("readonly", true);

    // var end = $(".endDate").kendoDatePicker({
    //     max: today,
    //     format: "yyyy-M-dd",
    //     dateInput: false,
    //     change: endChange
    // }).data("kendoDatePicker");

    // $(".startDate").attr("readonly", true);
    // $(".endDate").attr("readonly", true);

    $('body').on('change', ".input-file", function(e) {
        if (!_.isUndefined(e.target.files[0])) {
            let name = e.target.files[0].name;
            $('#input-file-placeholder').val(name);
        }
    })
});

function status(action, status_id, status) {

    $.ajax({
        type: 'POST',
        url: '/karicare-admin/status',
        data: { action: action, status_id: status_id, status: status },
        beforeSend: function() { $.LoadingOverlay("show"); },
        dataType: 'json',
        success: function(response) {
            $.LoadingOverlay("hide");

            if (response) {
                //console.log('#status_'+status_id);
                $('#status_' + status_id).addClass('status_success');
                setInterval(function() {
                    $('#status_' + status_id).removeClass('status_success');
                }, 4000)

            }
            return false;
        }
    });
}

function delete_record(action, delete_id) {


    bootbox.confirm({
        title: 'Remove Record Confirmation',
        message: 'Are you sure to want to delete this record?',
        buttons: {
            confirm: {
                label: 'Yes, Continue',
                className: "btn-success"

            },
            cancel: {
                label: 'Cancel',
                className: "btn-danger"

            }
        },
        callback: function(result) {
            if (true === result) {
                $.ajax({
                    type: 'POST',
                    url: '/karicare-admin/deleterecords',
                    data: { action: action, delete_id: delete_id },
                    beforeSend: function() { $('.loading-top').fadeIn(); },
                    success: function(msg) {
                        if (msg == 'Success') {

                        }
                        $('.loading-top').fadeOut();
                        return false;
                    }
                });
            }
        }
    });
}

function resetSearchForm() {
    $('#mySearchForm')[0].reset();

    $("#port_id").select2("val", "All");

    search();
}

function resetSearchFormZone() {
    $('#ZoneName').val('');
    $('#ZoneCreated').val('');
    $('#ZoneTodate').val('');
    $('#ZoneSearchStatus').val('');
    window.location.href = window.location.href;
}

function remove_error(div_id) {

    $('#' + div_id).removeClass('has-error');
    $('.form-group').removeClass('has-error');
    $('.help-block').html('').hide();
}

function error_remove() {

    $('.form-group').removeClass('has-error');
    $('.help-block').html('').hide();
}

function ucwords(str) {
    return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
        return $1.toUpperCase();
    });
}

function showMsg(msg, msg_type) {
    $.notify({
        message: msg
    }, {
        type: msg_type,
        z_index: 99999,
        delay: 5000,
    });
}