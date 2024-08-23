/**
 * Generate new key and insert into input value
 */
var domainUrl = window.location.origin;
$('#keygen').on('click', function() {
    $('#apikey').val(generateUUID());
});
//$('[data-toggle="tooltip"]').tooltip();
//$('[data-toggle="popover"]').popover();
$(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});

function generateUUID() {
    var d = new Date().getTime();
    if (window.performance && typeof window.performance.now === "function") {
        d += performance.now();
    }
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (d + Math.random() * 16) % 16 | 0;
        d = Math.floor(d / 16);
        return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
    });
    return uuid;
}
//$('select').select2();
/***********kyc verification code start***************/
/** pan_num_get_details code*/
$(document).ready(function() {
    $('#kYcVerification').click(function() {
        var pan = $('#kycPanNum').val().toUpperCase();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: domainUrl + "/admin/profile/ajaxKycverification",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                pan: pan
            },
            success: function(data) {
                if (data.success == true) {
                    $('#kycPanNum').val('');
                    toastr.success(data.message, "Success", {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-full-width'
                    });
                } else {
                    toastr.error(data.message, "Success", {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-full-width'
                    });
                }
            }
        });
    });
});
/***********kyc verification code end****************/
/***********kyc verification code start***************/
/** pan_num_get_details code*/

/*  $(window).bind("load", function() {
   bankdetails();
}); */
bankdetails();

function bankdetails() {
    NioApp.DataTable("#bankdataTable1", {
        processing: true,
        serverSide: true,
        bDestroy: true,
        ajax: domainUrl + "/admin/bank/ajaxgetbanks",
        columns: [{
            data: 'created_at',
            orderable: false,
            sClass: 'text-center nk-tb-col'
        }, {
            data: 'bank_name',
            sClass: 'nk-tb-col'
        }, {
            data: 'bank_account',
            sClass: 'nk-tb-col'
        }, {
            data: 'bank_status',
            orderable: false,
            sClass: 'text-right nk-tb-col'
        }],
    })
}

$(document).ready(function() {


    $('#bankverification').click(function() {
        var bank_acc = $('#bank_account').val().toUpperCase();
        var ifsc_code = $('#bank_ifsc').val().toUpperCase();
        var bank_name = $('#bank_name').val();
        if (bank_acc != '' && ifsc_code != '' && bank_name != '') {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: domainUrl + "/admin/bank/ajaxBankverification",
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    bank_name: bank_name,
                    bank_acc: bank_acc,
                    ifsc_code: ifsc_code
                },
                success: function(data) {
                    console.log(data.success);
                    if (data.success == true) {
                        $('#bank_account').val('');
                        $('#bank_ifsc').val('');
                        $('#bank_name').val('');
                        bankdetails();
                        NioApp.Toast(data.message, "success", {
                            position: "top-center"
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    } else {
                        NioApp.Toast(data.message, "error", {
                            position: "top-center"
                        })
                    }
                },
                error: function(data) {
                    NioApp.Toast(data.responseJSON.message, "error", {
                        position: "top-center"
                    })
                }
            });
        } else {
            //NioApp.toastr.error('All fields are mandatory', "Success", {closeButton: true,progressBar:true,positionClass: 'toast-top-full-width'});
            NioApp.Toast("All fields are mandatory", "error", {
                position: "top-center"
            })
        }
    });
});
/***********kyc verification code end****************/
$('#select_all').on('click', function() {
    if (this.checked) {
        $('.checkbox').each(function() {
            this.checked = true;
        });
    } else {
        $('.checkbox').each(function() {
            this.checked = false;
        });
    }
});
$('.checkbox').on('click', function() {
    if ($('.checkbox:checked').length == $('.checkbox').length) {
        $('#select_all').prop('checked', true);
    } else {
        $('#select_all').prop('checked', false);
    }
});
/*********** get_gstin_details ***************/
$(document).ready(function() {

    NioApp.DataTable("#gstindataTable", {
        processing: true,
        serverSide: true,
        "bDestroy": true,
        ajax: domainUrl + "/admin/profile/gstin_details",
        columns: [{
            data: 'created_at',
            orderable: false,
            sClass: 'text-center nk-tb-col'
        }, {
            data: 'gstin_number',
            sClass: 'nk-tb-col'
        }, {
            data: 'status',
            orderable: false,
            serachable: false,
            sClass: 'text-right nk-tb-col'
        }],
    })



    $('#gstinverification').click(function() {
        var gstin_number = $('#gstin_number').val();
        if (gstin_number != '') {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: domainUrl + "/admin/profile/addgstindetails",
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    gstin_number: gstin_number
                },
                success: function(data) {
                    if (data.success == true) {
                        $('#gstin_number').val('');
                        NioApp.DataTable("#gstindataTable", {
                            processing: true,
                            serverSide: true,
                            "bDestroy": true,
                            ajax: domainUrl + "/admin/profile/gstin_details",
                            columns: [{
                                data: 'created_at',
                                orderable: false,
                                sClass: 'text-center nk-tb-col'
                            }, {
                                data: 'gstin_number',
                                sClass: 'nk-tb-col'
                            }, {
                                data: 'status',
                                orderable: false,
                                serachable: false,
                                sClass: 'text-right nk-tb-col'
                            }],
                        })
                        NioApp.Toast(data.message, "success", {
                            position: "top-center"
                        })
                    } else {
                        NioApp.Toast(data.message, "success", {
                            position: "top-center"
                        })
                    }
                }
            });
        } else {

            NioApp.Toast('This field is mandatory', "error", {
                position: "top-center"
            })
        }
    });
});
/*********** get_gstin_details ***************/
/*********** get_address ***************/
$(document).ready(function() {

    NioApp.DataTable("#addressdataTable", {
        processing: true,
        serverSide: true,
        "bDestroy": true,
        ajax: domainUrl + "/admin/profile/address",
        columns: [{
            data: 'created_at',
            orderable: false,
            sClass: 'text-center nk-tb-col'
        }, {
            data: 'address_line_one',
            sClass: 'nk-tb-col'
        }, {
            data: 'address_line_two',
            sClass: 'nk-tb-col'
        }, {
            data: 'state',
            sClass: 'nk-tb-col'
        }, {
            data: 'city',
            sClass: 'nk-tb-col'
        }, {
            data: 'postal_code',
            sClass: 'nk-tb-col'
        }, {
            data: 'status',
            orderable: false,
            serachable: false,
            sClass: 'text-right nk-tb-col'
        }],
    })



    $('#addressverification').click(function() {
        var address_line_one = $('#address_line_one').val();
        var address_line_two = $('#address_line_two').val();
        var state = $('#state').val();
        var city = $('#city').val();
        var postal_code = $('#postal_code').val();
        if (address_line_one != '' && address_line_two != '' && state != '' && city != '' && postal_code != '') {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: domainUrl + "/admin/profile/addaddress",
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    address_line_one: address_line_one,
                    address_line_two: address_line_two,
                    state: state,
                    city: city,
                    postal_code: postal_code
                },
                success: function(data) {
                    if (data.success == true) {
                        $('#address_line_one').val('');
                        $('#address_line_two').val('');
                        $('#state').val(null).trigger('change');
                        $("#city").append('<option value="">--Select City--</option>');
                        $('#postal_code').val('');
                        NioApp.DataTable("#addressdataTable", {
                            processing: true,
                            serverSide: true,
                            "bDestroy": true,
                            ajax: domainUrl + "/admin/profile/address",
                            columns: [{
                                data: 'created_at',
                                orderable: false,
                                sClass: 'text-center nk-tb-col'
                            }, {
                                data: 'address_line_one',
                                sClass: 'nk-tb-col'
                            }, {
                                data: 'address_line_two',
                                sClass: 'nk-tb-col'
                            }, {
                                data: 'state',
                                sClass: 'nk-tb-col'
                            }, {
                                data: 'city',
                                sClass: 'nk-tb-col'
                            }, {
                                data: 'postal_code',
                                sClass: 'nk-tb-col'
                            }, {
                                data: 'status',
                                orderable: false,
                                serachable: false,
                                sClass: 'text-right nk-tb-col'
                            }],
                        })
                        NioApp.Toast(data.message, "success", {
                            position: "top-center"
                        })
                    } else {
                        NioApp.Toast(data.message, "success", {
                            position: "top-center"
                        })
                    }
                }
            });
        } else {
            NioApp.Toast('This field is mandatory', "error", {
                position: "top-center"
            })
        }
    });
});
/*********** get_address ***************/

//$('#userdataTable .toggle-class').change(function() {
$('#userdataTable').on("click", ".toggle-class", function() {
    var status = $(this).prop('checked') == true ? 1 : 0;
    var user_id = $(this).data('uid');
    $.ajax({
        type: "GET",
        dataType: "json",
        url: domainUrl + "/admin/users/userChangeStatus",
        data: { 'status': status, 'user_id': user_id },
        success: function(data) {
            NioApp.Toast(data.success, "success", {
                position: "top-center"
            });
            location.reload();
        }
    });
})

$('#userdataTable').on("click", ".resetpassword-class", function() {
    var user_id = $(this).data('uid');
    var site_id = $(this).data('siteid');
    $.ajax({
        type: "GET",
        dataType: "json",
        url: domainUrl + "/admin/users/resetpassword",
        data: { 'user_id': user_id, 'site_id': site_id },
        success: function(data) {
            NioApp.Toast(data.success, "success", {
                position: "top-center"
            });
        }
    });
})

$('#sitesdataTable').on("click", ".site-toggle-class", function() {
    var status = $(this).prop('checked') == true ? 1 : 0;
    var site_id = $(this).data('sid');
    $.ajax({
        type: "GET",
        dataType: "json",
        url: domainUrl + "/admin/sites/updateSiteStatus",
        data: { 'status': status, 'site_id': site_id },
        success: function(data) {
            NioApp.Toast(data.success, "success", {
                position: "top-center"
            });
            location.reload();
        }
    });
})
