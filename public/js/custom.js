jQuery(document).ready(function (){

    jQuery("div.lang-switcher a").click(function (){
        jQuery("div.lang-switcher a").removeClass("lang-active");
        jQuery(this).addClass("lang-active");
    });

    jQuery(".toggler-icon img").click(function (){
        jQuery(".sidebar-main").toggleClass("hide-sidebar");
        jQuery(".dashboard-main").toggleClass("dashboard-full");
        jQuery(".dashboard-topbar").toggleClass("mobile-bar");
    });
    jQuery(".add-note-td a").click(function(){
        jQuery(this).closest(".add-note-td").find(".add-your-note-main").slideToggle();
    });
    jQuery(".mobile-toggle-btn").click(function (){
        jQuery(".sidebar-main").removeClass("hide-sidebar");
    });

    var allPanels = jQuery('.custom-dropdown .notification-dropdown');
    jQuery(".custom-dropdown").click(function (){
        allPanels.slideUp();
        jQuery(this).closest(".custom-dropdown").find(" > .notification-dropdown").stop().slideToggle();
    });

    

    jQuery(".toggled-tbl tbody tr td:first-child").click(function (){
        jQuery(this).closest("tr").find(".inner-tbl").slideToggle(300);
        jQuery(this).closest("tr").find(".inner-table-main").attr('colspan',function (index,attr){
            return attr == '3' ? null : '3';
        });
    });

    jQuery(".detail-head").click(function (){
        jQuery(".detail-content").slideToggle();
        jQuery(this).toggleClass("dtl-head")
    });

    jQuery('body').on('click', '.remove-detail', function() {
        jQuery(".new-content").remove();
    });

    jQuery(".data-table-main table tr td:last-child span").click(function (){
        jQuery(this).toggleClass("toggled-open");
        jQuery(".tax_a-table-main").toggleClass("left-side-compress");
        jQuery(".tax_toggle-form").toggle("slide");
    });
    // jQuery(".language-datatable table").DataTable({
    //     "targets": 'no-sort',
    //     "bSort": false,
    //     "filter":false,
    //     "info" : false,
    //     "scrollX": true,
    //     columnDefs: [{
    //         orderable: false,
    //         className: 'select-checkbox',
    //         targets: 0
    //     }],
    //     select: {
    //         style: 'os',
    //         selector: 'td:first-child'
    //     },
    //     order: [
    //         [1, 'asc']
    //     ]
    // });
	// jQuery(".currency-list-table table").DataTable({
    //     "targets": 'no-sort',
    //     "bSort": false,
    //     "filter":false,
    //     "info" : false,
    //     "scrollX": true,
    // });
    // jQuery('.data-table-main table').DataTable({
    //     "filter":false,
    //     "info" : false,
    //     "scrollX": true,
    //     "autoWidth":false,
    // });
	
    jQuery('.filter-dropdown').select2({
        placeholder:"Select Options",
    });
    jQuery('.filter-dropdown').select2("destroy");
    jQuery('.assigned-dropdown').select2({
        placeholder:"Select Options",
    });
    jQuery('.company-dropdown').select2({
        placeholder:"Select Company",
    });
    jQuery('.assigned-dropdown').select2("destroy");
    jQuery('.company-dropdown').select2("destroy");

});
jQuery(document).on("click", function(event){
    var $trigger = jQuery(".custom-dropdown");
    if($trigger !== event.target && !$trigger.has(event.target).length){
        jQuery(".notification-dropdown").slideUp("fast");
    }
});

jQuery(".js-example-basic-multiple").select2({
    tags: true,
    tokenSeparators: [',', ' '],
    placeholder: "Search",
});
jQuery(".chart-search").select2({
    tags: true,
    tokenSeparators: [',', ' '],
    placeholder: "Search",
});

jQuery('.countrycode').select2({
    placeholder:"Select Options",
    dropdownParent: $('#myModal'),
    searchable:true
});



