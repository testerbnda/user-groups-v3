// Call the dataTables jQuery plugin
var domainUrl = window.location.origin;
/*$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});*/
NioApp.DataTable("#sitesdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/sites/ajaxgetsites",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'site_name', sClass: 'nk-tb-col' },
        { data: 'epbalance', sClass: 'text-right nk-tb-col' },
        { data: 'epfeebalance', sClass: 'text-right nk-tb-col' },
        { data: 'site_domain', sClass: 'nk-tb-col' },
        { data: 'status', sClass: 'nk-tb-col' },
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
    ],
    "order": [
        [0, "desc"]
    ]
})


NioApp.DataTable("#templatesdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/template/ajaxgettemplates",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'temp_level', sClass: 'nk-tb-col' },
        { data: 'site_group', sClass: 'nk-tb-col' },
        { data: 'site_name', sClass: 'nk-tb-col' },
        { data: 'template_name', sClass: 'nk-tb-col' },
        { data: 'template_code', sClass: 'nk-tb-col' },
        { data: 'template_type', sClass: 'nk-tb-col' },
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
    ],
    "order": [
        [0, "desc"]
    ]
})


NioApp.DataTable("#billingsdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/billing/ajaxgetbillings",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'name', sClass: 'nk-tb-col' },
        { data: 'category', sClass: 'nk-tb-col' },
        { data: 'plan_type', sClass: 'nk-tb-col' },
        { data: 'package', sClass: 'nk-tb-col' },
        { data: 'validity', sClass: 'nk-tb-col' },
        { data: 'amount', sClass: 'text-right nk-tb-col' },
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
    ],
    "order": [
        [0, "desc"]
    ]
})

NioApp.DataTable("#sitebillingsdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/site_billing/ajaxgetsitebillings",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'site_name', sClass: 'nk-tb-col' },
        { data: 'category', sClass: 'nk-tb-col' },
        { data: 'name', sClass: 'nk-tb-col' },
        { data: 'balance', sClass: 'text-right nk-tb-col' },
        { data: 'start_date', sClass: 'nk-tb-col' },
        { data: 'end_date', sClass: 'nk-tb-col' },
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
    ],
    "order": [
        [0, "desc"]
    ]
})

$(document).ready(function() {
    var domainUrl = window.location.origin;
    var parentid = $(location).attr('href').split("/").splice(6, 7).join("/");
    $('#childuserdataTable').DataTable({
        processing: true,
        serverSide: true,
        // ajax: domainUrl+"/admin/users/ajaxgetchildlist",
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/users/ajaxgetchildlist",
            'data': {
                id: parentid,
            },
        },

        columns: [
            { data: 'created_at' },
            { data: 'name' },
            { data: 'email' },
            { data: 'roles_name', orderable: false },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center' },
        ],
    });
});


NioApp.DataTable("#rolesdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/roles/ajaxgetroles",
    columns: [
        { 'name': 'created_at', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, orderable: false, serachable: false, sClass: 'text-center nk-tb-col' },
        { data: 'rolename', sClass: 'nk-tb-col' },
        { data: 'sitename', sClass: 'nk-tb-col' },
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
    ],
    "order": [
        [0, "desc"]
    ]
})

NioApp.DataTable("#SoadrawdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/escrow/ajaxgettransaction",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'name', sClass: 'nk-tb-col' },
        { data: 'particular', orderable: false, sClass: 'nk-tb-col' },
        { data: 'deal_id', className: 'nk-tb-col' ,'render': function(data, type, row) {
            return data ? data : 'NA';
        } },
        { data: 'type', orderable: false, sClass: 'nk-tb-col' },
        { data: 'amount', sClass: 'text-right nk-tb-col' },
        { data: 'balance', orderable: false, sClass: 'text-right nk-tb-col' }
    ],
    "order": [
        [0, "asc"]
    ],
    "lengthMenu": [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
    ]
})



$(document).ready(function() {
    var domainUrl = window.location.origin;
    var userid = $(location).attr('href').split("/").splice(6, 7).join("/");
    NioApp.DataTable("#SoacollectiondrawdataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/collection/ajaxgettransaction",
            'data': {
                userid: userid,
            },
        },
        columns: [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col', orderable: false, },
            { data: 'particular', orderable: false, sClass: 'nk-tb-col' },
            { data: 'payin_src', orderable: false, sClass: 'nk-tb-col' },
            { data: 'type', orderable: false, sClass: 'nk-tb-col' },
            { data: 'amount', orderable: false, sClass: 'text-right nk-tb-col' },
            { data: 'balance', orderable: false, sClass: 'text-right nk-tb-col' }
        ],
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ]
    })

    NioApp.DataTable("#UserSoacollectiondrawdataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/collection/ajaxgetusertransaction",
            'data': {
                userid: userid,
            },
        },
        columns: [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col', orderable: false, },
            { data: 'name', sClass: 'nk-tb-col' },
            { data: 'particular', orderable: false, sClass: 'nk-tb-col' },
            { data: 'type', orderable: false, sClass: 'nk-tb-col' },
            { data: 'amount', orderable: false, sClass: 'text-right nk-tb-col' },
            { data: 'balance', orderable: false, sClass: 'text-right nk-tb-col' }
        ],
        "order": [
            [0, "asc"]
        ],
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ]
    })
});

$(document).ready(function() {
    var domainUrl = window.location.origin;
    var transid = $(location).attr('href').split("/").splice(6, 7).join("/");
    var show_deal_docs = $('#show_deal_docs').val();
    if (show_deal_docs == 1) {
        var tblcol = [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col', orderable: false, },
            { data: 'name', orderable: false, sClass: 'nk-tb-col' },
            { data: 'particular', orderable: false, sClass: 'nk-tb-col' },
            { data: 'user_id', orderable: false, sClass: 'nk-tb-col' },
            { data: 'type', orderable: false, sClass: 'nk-tb-col' },
            { data: 'amount', orderable: false, sClass: 'text-right nk-tb-col' },
            { data: 'balance', orderable: false, sClass: 'text-right nk-tb-col' }
        ];

    } else {
        var tblcol = [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col', orderable: false, },
            { data: 'name', orderable: false, sClass: 'nk-tb-col' },
            { data: 'particular', orderable: false, sClass: 'nk-tb-col' },
            { data: 'type', orderable: false, sClass: 'nk-tb-col' },
            { data: 'amount', orderable: false, sClass: 'text-right nk-tb-col' },
            { data: 'balance', orderable: false, sClass: 'text-right nk-tb-col' }
        ];

    }

    NioApp.DataTable("#SoatrusteedrawdataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/trustee/ajaxgettransaction",
            'data': {
                transid: transid,
            },
        },
        columns: tblcol,
        "order": [
            [0, "asc"]
        ],
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ]
    })

    NioApp.DataTable("#dealdocumentsdatatable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/documents",
            'data': {
                txn_id: transid,
            }
        },
        columns: [
            { data: 'created_at', sClass: 'nk-tb-col',orderable: false},
            { data: 'site_name', sClass: 'nk-tb-col',orderable: false },
            { data: 'deal_id', sClass: 'nk-tb-col', orderable: false },
            { data: 'doc_type', sClass: 'nk-tb-col', orderable: false },
            { data: 'doc_name', sClass: 'nk-tb-col', orderable: false },
            { data: 'view', sClass: 'text-center nk-tb-col', orderable: false }
        ]
    })

});


$(document).ready(function() {
    var domainUrl = window.location.origin;
    var dealid = $(location).attr('href').split("/").splice(6, 7).join("/");
    NioApp.DataTable("#SoarentaldrawdataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/users/ajaxgettransaction",
            'data': {
                dealid: dealid,
                actype: 3
            },
        },
        columns: [
            { data: 'created_at', orderable: false, sClass: 'text-center nk-tb-col' },
            { data: 'particular', orderable: false, sClass: 'nk-tb-col' },
            { data: 'type', orderable: false, sClass: 'nk-tb-col' },
            { data: 'amount', orderable: false, sClass: 'text-right nk-tb-col' },
            { data: 'balance', orderable: false, sClass: 'text-right nk-tb-col' }
        ],
    })

});



$(document).ready(function() {
    var domainUrl = window.location.origin;
    var userid = $(location).attr('href').split("/").splice(6, 7).join("/");

    $('#SoauserdrawdataTable').DataTable({
        processing: true,
        serverSide: true,
        // ajax: domainUrl+"/admin/users/ajaxgetchildlist",
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/users/ajaxgettransaction",
            'data': {
                userid: userid,
            },
        },
        columns: [
            { data: 'created_at', orderable: false, },
            { data: 'particular', orderable: false, },
            { data: 'type', orderable: false, },
            { data: 'amount', orderable: false, sClass: 'text-right' },
            { data: 'balance', orderable: false, sClass: 'text-right' }
        ],
    });
});

NioApp.DataTable("#transhistorylist", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/manager/ajaxGetMisCollection",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'type', sClass: 'nk-tb-col' },
        { data: 'filename', sClass: 'nk-tb-col' },
        { data: 'status', sClass: 'nk-tb-col' },
        { data: 'errormesg', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
    ],
    "order": [
        [0, "desc"]
    ]
})

NioApp.DataTable("#transreportslist", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/manager/reportsWithdrawal",
    columns: [
        { 'name': 'generated_at.timestamp', 'data': { '_': 'generated_at.display', 'sort': 'generated_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'filename', sClass: 'text-center nk-tb-col' },
        { data: 'count', sClass: 'text-center nk-tb-col' },
        { data: 'amount', sClass: 'text-right nk-tb-col' },
        { data: 'report_type', sClass: 'text-center nk-tb-col' },
    ],
    "order": [
        [0, "desc"]
    ]
});

NioApp.DataTable("#esigndigiodataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/esign/ajaxdigiogettrns",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'platform_id', sClass: 'text-center nk-tb-col' },
        { data: 'url', sClass: 'text-center nk-tb-col' },
        { data: 'action', sClass: 'text-center nk-tb-col', orderable: false, serachable: false },
    ],
    "order": [
        [0, "desc"]
    ]
});

var str = window.location.href;
var arr = str.split('/');
NioApp.DataTable("#signersdigiodataTable", {
    processing: true,
    serverSide: true,
    "ajax": {
        "url": domainUrl + "/admin/esign/ajaxagreementsigners",
        "data": function(d) {
            d.agr_id = arr[6];
        }
    },
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'name', sClass: 'text-center nk-tb-col' },
        { data: 'status', sClass: 'text-center nk-tb-col' },
    ],
    "order": [
        [0, "desc"]
    ]
});

NioApp.DataTable("#offtranslist", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/manager/get_offline_transactions",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'reference_no', sClass: 'text-center' },
        { data: 'mandatory_fields', sClass: 'text-center' },
        { data: 'name', sClass: 'text-center' },
        { data: 'transaction_amount', sClass: 'text-right' },
        { data: 'payment_status', sClass: 'text-center' },
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center' },
    ],
    "order": [
        [0, "desc"]
    ]
})


NioApp.DataTable("#pg_pending_trans_list", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/manager/get_pg_pending_transactions",
    columns: [
        { data: 'created_at', sClass: 'nk-tb-col' },
        { data: 'id', name: 'payments.id', sClass: 'nk-tb-col text-center' },
        { data: 'gateway_name', name: 'payment_gateways.name', sClass: 'nk-tb-col text-center' },
        { data: 'site_name', name: 'sites.site_name', sClass: 'nk-tb-col text-center' },
        { data: 'name', name: 'users.name', sClass: 'nk-tb-col text-center' },
        { data: 'utr_no', name: 'payments.utr_no', sClass: 'nk-tb-col text-center' },
        { data: 'reference_no', name: 'payments.reference_no', sClass: 'nk-tb-col text-center' },
        { data: 'amount', name: 'payments.total_amount', sClass: 'nk-tb-col text-right' },
        { data: 'status', name: 'payments.payment_status', sClass: 'nk-tb-col text-center' },
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'nk-tb-col text-center' },
        { data: 'response_status', name: 'payments.response_status', sClass: 'nk-tb-col text-center' },

    ],
    "order": [
        [0, "desc"]
    ],
    "lengthMenu": [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
    ]
})



NioApp.DataTable("#apidocdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/apidoc/ajaxgetrequest",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'name', sClass: 'text-center nk-tb-col' },
        { data: 'status', sClass: 'text-center nk-tb-col' },
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col' },
    ],
    "order": [
        [0, "desc"]
    ]
});

NioApp.DataTable("#emailgroupsdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/email_group/ajaxgetemailgroups",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'group_name', sClass: 'text-center nk-tb-col' },
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col' },
    ],
    "order": [
        [0, "desc"]
    ]
});

NioApp.DataTable("#campaignsdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/email_campaign/ajaxgetemailcampaigns",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'subject', sClass: 'text-center nk-tb-col' },
        /*{data: 'email_content'},*/
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col' },
    ],
    "order": [
        [0, "desc"]
    ]
});


$(document).ready(function() {
    var domainUrl = window.location.origin;
    $('#investordataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: domainUrl + "/admin/investor/ajaxgetlist",
        columns: [
            { data: 'created_at' },
            { data: 'investment_id' },
            { data: 'company_name' },
            { data: 'investment_name' },
            { data: 'balance', name: 'balance', sClass: 'text-right' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center' },
        ],
    });
});

$(document).ready(function() {
    var domainUrl = window.location.origin;
    var investmentid = $(location).attr('href').split("/").splice(6, 7).join("/");
    NioApp.DataTable('#SoaInvestmentdataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/investor/ajaxgetsoa",
            'data': {
                investmentid: investmentid,
            },
        },
        columns: [
            { data: 'created_at', sClass: 'text-center nk-tb-col' },
            { data: 'particular', sClass: 'nk-tb-col' },
            { data: 'type', sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'balance', sClass: 'text-right nk-tb-col' }
        ],
        "order": [
            [0, "desc"]
        ]
    });
});

$(document).ready(function() {
    var domainUrl = window.location.origin;
    var result = get_query();
    if (typeof(result.site_id) != "undefined" && result.site_id !== null) {
        var site_id = result.site_id;
    } else {
        var site_id = null;
    }

    if (typeof(result.site_group) != "undefined" && result.site_group !== null) {
        var site_group = result.site_group;
    } else {
        var site_group = null;
    }

    NioApp.DataTable('#dealdataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            // { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'address', sClass: 'nk-tb-col' },
            { data: 'deal_start_at', sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'security_amount', sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealauctiondataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            // { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'address', sClass: 'nk-tb-col' },
            { data: 'deal_start_at', sClass: 'nk-tb-col' },
            { data: 'deal_end_at', sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'security_amount', sClass: 'text-right nk-tb-col' },
            { data: 'deal_status', sClass: 'nk-tb-col' },
            { data: 'bid_status', sClass: 'nk-tb-col' },
            /*{ data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },*/
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealagridataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            // { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'id', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'deal_name', sClass: 'nk-tb-col' },
            { data: 'field1', sClass: 'text-center nk-tb-col' },
            { data: 'security_amount', sClass: 'text-right nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealinvestmentdataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            //  { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'created_at', name: 'deals.created_at', sClass: 'nk-tb-col' },
            { data: 'user_id', name: 'users.id', sClass: 'nk-tb-col' },
            { data: 'name', name: 'users.name', sClass: 'nk-tb-col' },
            { data: 'id', name: 'deals.id', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', name: 'deals.deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col', orderable: false, serachable: false },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealservicedataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            //  { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'name', sClass: 'nk-tb-col' },
            { data: 'deal_name', sClass: 'nk-tb-col' },
            { data: 'field1', sClass: 'nk-tb-col' },
            { data: 'deal_start_at', sClass: 'nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealfinancedataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            //  { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'created_at', name: 'deals.created_at', sClass: 'nk-tb-col' },
            { data: 'name', name: 'users.name', sClass: 'nk-tb-col' },
            { data: 'field1', name: 'deals.field1', sClass: 'nk-tb-col' },
            { data: 'va_status', name: 'virtual_escrow_accounts.status', orderable: false, serachable: false, sClass: 'nk-tb-col' },
            { data: 'balance', name: 'virtual_escrow_accounts.balance', orderable: true, searchable: true, sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealLendingDataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            { data: 'created_at', name: 'deals.created_at', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', name: 'deals.deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'field1', name: 'field1', sClass: 'nk-tb-col' },
            { data: 'deal_name', name: 'deal_name', sClass: 'nk-tb-col' },
            { data: 'name', name: 'name', sClass: 'nk-tb-col' },
            { data: 'va_status', name: 'virtual_escrow_accounts.status', orderable: false, serachable: false, sClass: 'nk-tb-col' },
            { data: 'balance', name: 'virtual_escrow_accounts.balance', orderable: true, searchable: true, sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealfinance2dataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            //  { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'created_at', name: 'deals.created_at', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', name: 'deals.deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'sub_deal_type', name: 'deal_type_sub_deal_type_mappings.sub_deal_type', sClass: 'nk-tb-col' },
            { data: 'name', name: 'users.name', sClass: 'nk-tb-col' },
            { data: 'field1', name: 'deals.field1', sClass: 'nk-tb-col' },
            { data: 'va_status', name: 'virtual_escrow_accounts.status', orderable: false, serachable: false, sClass: 'nk-tb-col' },
            { data: 'balance', name: 'virtual_escrow_accounts.balance', orderable: true, searchable: true, sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealfinance3dataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            //  { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'created_at', name: 'deals.created_at', sClass: 'nk-tb-col' },
            { data: 'sub_deal_type', name: 'deal_type_sub_deal_type_mappings.sub_deal_type', sClass: 'nk-tb-col' },
            // { data: 'deal_ref_id', name: 'deals.deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'name', name: 'users.name', sClass: 'nk-tb-col' },
            { data: 'field1', name: 'deals.field1', sClass: 'nk-tb-col' },
            { data: 'va_status', name: 'virtual_escrow_accounts.status', orderable: false, serachable: false, sClass: 'nk-tb-col' },
            { data: 'balance', name: 'virtual_escrow_accounts.balance', orderable: true, searchable: true, sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealdebtdataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            //{ 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'name', sClass: 'nk-tb-col' },
            { data: 'field1', sClass: 'nk-tb-col' },
            { data: 'va_status', sClass: 'nk-tb-col' },
            { data: 'balance', sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealsubscriptiondataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            // { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'name', sClass: 'nk-tb-col' },
            { data: 'id', sClass: 'nk-tb-col' },
            { data: 'deal_description', sClass: 'nk-tb-col' },
            { data: 'deal_start_at', sClass: 'nk-tb-col' },
            { data: 'deal_end_at', sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealmarketplacedataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', name: 'deals.deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'deal_name', sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealbxidataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'id', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', name: 'deals.deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'deal_name', sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable("#siteusersdataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'GET',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/sites/getusers",
            'data': {
                site_id: site_id
            },
        },
        columns: [
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'name', name: 'users.name', sClass: 'nk-tb-col' },
            { data: 'roles_name', name: 'roles.name', sClass: 'text-center nk-tb-col' },
            { data: 'mobile_no', name: 'users.mobile_no', sClass: 'nk-tb-col' },
            { data: 'pan', name: 'kyc.pan', sClass: 'text-center', sClass: 'nk-tb-col' },
            { data: 'balance', name: 'virtual_escrow_accounts.balance', orderable: false, serachable: false, sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#propertyDealDatatable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            { data: 'created_at', name: 'deals.created_at', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealtradedataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'id', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'deal_name', sClass: 'nk-tb-col' },
            { data: 'field1', sClass: 'text-center nk-tb-col' },
            { data: 'security_amount', sClass: 'text-right nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealprojectdataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            { data: 'created_at', name: 'deals.created_at', sClass: 'nk-tb-col' },
            { data: 'name', name: 'users.name', sClass: 'nk-tb-col' },
            { data: 'id', name: 'deals.id', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', name: 'deals.deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'freelancers', sClass: 'text-right nk-tb-col', orderable: false, serachable: false },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });

    //Site Agreements
    NioApp.DataTable("#siteAgrDataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'GET',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/sites/getagreements",
            'data': {
                site_id: site_id
            },
        },
        columns: [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'doc_path', sClass: 'nk-tb-col' },
            { data: 'nda_status', name: 'sites.onboard_nda', orderable: false, sClass: 'nk-tb-col' },
        ],
        "order": [
            [0, "asc"]
        ],
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ]
    });

    //Site Invoice
    NioApp.DataTable("#siteInvoicesDataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'GET',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/site/getinvoices",
            'data': {
                site_id: site_id
            },
        },
        columns: [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'invoice_date', sClass: 'nk-tb-col text-center' },
            { data: 'invoice_no', sClass: 'nk-tb-col' },
            { data: 'amount', orderable: false, sClass: 'nk-tb-col text-right' },
            { data: 'invoice_path', orderable: false, sClass: 'nk-tb-col text-center' }
        ],
        "order": [
            [0, "asc"]
        ],
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ]
    });

    //Site SOA
    NioApp.DataTable("#siteSoadataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'GET',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/site/ajaxgettransaction",
            'data': {
                site_id: site_id
            },
        },
        columns: [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'name', sClass: 'nk-tb-col' },
            { data: 'particular', orderable: false, sClass: 'nk-tb-col' },
            { data: 'type', orderable: false, sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'balance', orderable: false, sClass: 'text-right nk-tb-col' }
        ],
        "order": [
            [0, "asc"]
        ],
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ]
    });

    //Site Fee SOA
    NioApp.DataTable("#siteSoaFeeDataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'GET',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/site/ajaxgetfeetransaction",
            'data': {
                site_id: site_id
            },
        },
        columns: [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'name', sClass: 'nk-tb-col' },
            { data: 'particular', orderable: false, sClass: 'nk-tb-col' },
            { data: 'type', orderable: false, sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'balance', orderable: false, sClass: 'text-right nk-tb-col' }
        ],
        "order": [
            [0, "asc"]
        ],
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ]
    });

    //Site Deals
    NioApp.DataTable("#sitedealsdataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'GET',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/sites/getdeals",
            'data': {
                site_id: site_id
            },
        },
        columns: [
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'deal_id', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', sClass: 'text-center nk-tb-col' },
            { data: 'status', sClass: 'nk-tb-col' }
        ],
        "order": [
            [0, "desc"]
        ]
    });

    NioApp.DataTable('#dealbipartydataTable', {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/deal/ajaxgetdeals",
            'data': {
                site_id: site_id,
                site_group: site_group,
            },
        },
        columns: [
            { data: 'created_at', sClass: 'nk-tb-col' },
            { data: 'deal_ref_id', name: 'deals.deal_ref_id', sClass: 'nk-tb-col' },
            { data: 'name', sClass: 'nk-tb-col' },
            { data: 'amount', sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    });
});

NioApp.DataTable("#managereleasesdatatable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/manager/get_manage_releases",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'site_name', sClass: 'nk-tb-col' },
        { data: 'borrowername', sClass: 'nk-tb-col' },
        { data: 'file', sClass: 'nk-tb-col' },
        { data: 'doc_status', sClass: 'nk-tb-col' },
        { data: 'updated_at', sClass: 'text-center nk-tb-col' },
        { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
    ],
    "order": [
        [0, "desc"]
    ]
})


function get_query() {
    var url = document.location.href;
    var qs = url.substring(url.indexOf('?') + 1).split('&');
    for (var i = 0, result = {}; i < qs.length; i++) {
        qs[i] = qs[i].split('=');
        result[qs[i][0]] = decodeURIComponent(qs[i][1]);
    }
    return result;
}

NioApp.DataTable("#enachregistrationTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/enach/getregistrations",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'consumer_id', sClass: 'nk-tb-col' },
        { data: 'username', sClass: 'nk-tb-col' },
        { data: 'mandate_id', sClass: 'text-center nk-tb-col' },
        { data: 'debit_amount', sClass: 'text-right nk-tb-col' },
        { data: 'start_date', sClass: 'nk-tb-col' },
        { data: 'end_date', sClass: 'nk-tb-col' },
        { data: 'frequency', sClass: 'nk-tb-col' },
        { data: 'status', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
    ],
    "order": [
        [0, "desc"]
    ]
})

$(function() {
    var domainUrl = window.location.origin;
    var mandateid = $(location).attr('href').split("/").splice(6, 7).join("/");

    NioApp.DataTable("#enachtxnDataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'GET',
            'url': domainUrl + "/admin/enach/gettransactions",
            'data': {
                mandateid: mandateid,
            },
        },
        columns: [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'mandate_id', sClass: 'nk-tb-col' },
            { data: 'txn_id', sClass: 'nk-tb-col' },
            { data: 'debit_amount', sClass: 'text-right nk-tb-col' },
            { data: 'debit_date', sClass: 'nk-tb-col' },
            { data: 'statuscode', sClass: 'text-center nk-tb-col' },
            { data: 'statusmessage', sClass: 'text-center nk-tb-col' },
        ],
        "order": [
            [0, "desc"]
        ]
    })
});

$(document).ready(function() {
    var billPlanId = $(location).attr('href').split("/").splice(6, 7).join("/");
    NioApp.DataTable("#invoicesDataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/invoice/ajaxgetinvoices",
            'data': {
                billPlanId: billPlanId
            },
        },
        columns: [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'invoice_no', orderable: false, sClass: 'nk-tb-col' },
            { data: 'site_name', orderable: false, sClass: 'nk-tb-col' },
            { data: 'txn_id', orderable: false, sClass: 'nk-tb-col' },
            { data: 'amount', orderable: false, sClass: 'text-right nk-tb-col' },
            { data: 'amount_gst', orderable: false, sClass: 'text-right nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    })

    NioApp.DataTable("#billingHistoryDataTable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'POST',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/billing_history/ajaxgetbillinghistory",
            'data': {
                billPlanId: billPlanId
            },
        },
        columns: [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'entity', orderable: false, sClass: 'nk-tb-col' },
            { data: 'ipAddress', orderable: false, sClass: 'nk-tb-col' },
            { data: 'action', name: 'action', orderable: false, serachable: false, sClass: 'text-center nk-tb-col nk-tb-col-tools' },
        ],
        "order": [
            [0, "desc"]
        ]
    })

});
$(function() {
    var user_id = $(location).attr('href').split("/").splice(6, 7).join("/");
    NioApp.DataTable("#userDealDatatable", {
        processing: true,
        serverSide: true,
        "ajax": {
            'type': 'GET',
            'headers': { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
            'url': domainUrl + "/admin/user/deals",
            'data': {
                user_id: user_id
            }
        },
        columns: [
            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
            { data: 'dealid', sClass: 'nk-tb-col' },
            { data: 'acc_no', sClass: 'nk-tb-col' },
            { data: 'balance', sClass: 'text-right nk-tb-col' }
        ],
        "order": [
            [0, "desc"]
        ]
    })
});

NioApp.DataTable("#SoafeedrawdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/escrow/ajaxgetfeetransactions",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'name', sClass: 'nk-tb-col' },
        { data: 'particular', orderable: false, sClass: 'nk-tb-col' },
        { data: 'type', orderable: false, sClass: 'nk-tb-col' },
        { data: 'amount', sClass: 'text-right nk-tb-col' },
        { data: 'balance', orderable: false, sClass: 'text-right nk-tb-col' }
    ],
    "order": [
        [0, "asc"]
    ],
    "lengthMenu": [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
    ]
})

NioApp.DataTable("#SoafddrawdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/escrow/fd/transactions",
    columns: [
        { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'txn_id', sClass: 'nk-tb-col' },
        { data: 'name', sClass: 'nk-tb-col' },
        { data: 'particular', orderable: false, sClass: 'nk-tb-col' },
        { data: 'type', orderable: false, sClass: 'nk-tb-col' },
        { data: 'no_of_days', orderable: false, sClass: 'nk-tb-col' },
        { data: 'amount', sClass: 'text-right nk-tb-col' },
        { data: 'balance', orderable: false, sClass: 'text-right nk-tb-col' },
        { data: 'status', orderable: false, sClass: 'text-right nk-tb-col' },
        { data: 'action', orderable: false, sClass: 'text-right nk-tb-col' }
    ],
    "order": [
        [0, "asc"]
    ],
    "lengthMenu": [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
    ]
})

NioApp.DataTable("#ndaSitesdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/onboard/get/sites_nda",
    columns: [
        { data: 'created_at', sClass: 'nk-tb-col text-center' },
        { data: 'site_name', name: 'sites.site_name', sClass: 'nk-tb-col' },
        { data: 'doc_path', name: 'agreements.url', sClass: 'nk-tb-col' },
        { data: 'nda_status', name: 'sites.onboard_nda', orderable: false, sClass: 'nk-tb-col' },
    ],
    "order": [
        [0, "desc"]
    ],
    "lengthMenu": [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
    ]
})

NioApp.DataTable("#clientdataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/client/ajaxgetclients",
    columns: [
       { 'name': 'created_at', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'name', sClass: 'nk-tb-col' },
        { data: 'company_name', sClass: 'nk-tb-col' },
        { data: 'mobile', sClass: 'nk-tb-col' },
        { data: 'requirement', orderable: false, sClass: 'nk-tb-col' },
        { data: 'source', orderable: false, sClass: 'nk-tb-col' },
        { data: 'status', orderable: false, sClass: 'nk-tb-col' },
        { data: 'action', orderable: false, sClass: 'text-right nk-tb-col' }

    ],
    "order": [
        [0, "desc"]
    ]
})

NioApp.DataTable("#softwareDataTable", {
    processing: true,
    serverSide: true,
    ajax: domainUrl + "/admin/software/ajaxgetlist",
    columns: [
        { 'name': 'created_at', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
        { data: 'name', sClass: 'nk-tb-col' },
        { data: 'company_name', sClass: 'nk-tb-col' },
        { data: 'mobile', sClass: 'nk-tb-col' },
        { data: 'requirement', orderable: false, sClass: 'nk-tb-col' },
        { data: 'source', orderable: false, sClass: 'nk-tb-col' },
        { data: 'status', orderable: false, sClass: 'nk-tb-col' },
        { data: 'action', orderable: false, sClass: 'text-right nk-tb-col' }
    ],
    "order": [
        [0, "desc"]
    ]
})
