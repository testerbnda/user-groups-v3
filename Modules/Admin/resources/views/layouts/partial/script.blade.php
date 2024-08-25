  <script src="{{ asset('assets/js/bundle.js') }}"></script>
  <script src="{{ asset('assets/js/scripts.js') }}"></script>

  <!-- New UI -->
  <script src="{{ asset('js/jquery.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.6/jquery.easypiechart.min.js"></script>
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>
  <script src="{{ asset('js/custom.js') }}"></script>

  <!-- New UI -->

  <!-- <script src="{{ asset('assets/js/charts/escrow-chart.js') }}"></script> -->
  @yield('script')
  <!------------------Start Custom code here-------------------------->

  <!-- Page level custom scripts -->
  <script src="{{ asset('js/prism.js') }}"></script>
  <script src="{{ asset('js/spectrum.min.js') }}"></script>
  @auth

      <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
      <script src="{{ asset('js/custom/custom.js') }}"></script>
  @endauth

  <!-- Daterange scripts -->
  <script type="text/javascript" src="{{ asset('js/moment.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/daterangepicker.min.js') }}"></script>

  <!-- Table2excel scripts -->
  <script src="{{ asset('js/jquery.table2excel.min.js') }}"></script>


  <!-- transfer this code from datatables.js to script.blade.php for modification -->
  <script type="text/javascript">
      var domainUrl = window.location.origin;

      var tblcol = [
          //   { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
          {
              data: 'created_at',
              name: 'users.created_at',
              sClass: 'nk-tb-col'
          },
          {
              data: 'name',
              name: 'users.name',
              sClass: 'nk-tb-col'
          },
          {
              data: 'email',
              name: 'users.email',
              searchable: true,
              visible: false
          },
          {
              data: 'mobile_no',
              name: 'users.mobile_no',
              sClass: 'nk-tb-col'
          },
          {
              data: 'status',
              name: 'users.status',
              orderable: false,
              serachable: false,
              sClass: 'text-center nk-tb-col'
          },
          {
              data: 'action',
              name: 'action',
              orderable: false,
              serachable: false,
              sClass: 'text-center nk-tb-col nk-tb-col-tools'
          },
      ];


      NioApp.DataTable("#userdataTable", {
          processing: true,
          serverSide: true,
          dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

          ajax: domainUrl + "/admin/user/ajaxgetusers",
          language: {
              paginate: {
                  next: '<i class="bx bx-chevron-right bx-18px"></i>',
                  previous: '<i class="bx bx-chevron-left bx-18px"></i>'
              }
          },
          columns: tblcol,
          "order": [
              [0, "desc"]
          ]
      })



      var tblcol2 = [
          //   { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
          {
              data: 'created_at',
              name: 'created_at',
              sClass: 'nk-tb-col'
          },
          {
              data: 'site_name',
              name: 'site_name',
              sClass: 'nk-tb-col'
          },
          {
              data: 'site_code',
              name: 'site_code',
              visible: true
          },
          {
              data: 'status',
              name: 'status',
              orderable: false,
              serachable: false,
              sClass: 'text-center nk-tb-col'
          },
          {
              data: 'action',
              name: 'action',
              orderable: false,
              serachable: false,
              sClass: 'text-center nk-tb-col nk-tb-col-tools'
          },
      ];


      NioApp.DataTable("#sitesdataTable", {
          processing: true,
          serverSide: true,
          dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

          ajax: domainUrl + "/admin/sites/ajaxgetsites",
          language: {
              paginate: {
                  next: '<i class="bx bx-chevron-right bx-18px"></i>',
                  previous: '<i class="bx bx-chevron-left bx-18px"></i>'
              }
          },
          columns: tblcol2,
          "order": [
              [0, "desc"]
          ]
      })

      var tblcol3 = [
          //   { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at' }, sClass: 'text-center nk-tb-col' },
          {
              data: 'created_at',
              name: 'created_at',
              sClass: 'nk-tb-col'
          },
          {
              data: 'name',
              name: 'name',
              sClass: 'nk-tb-col'
          },
          {
              data: 'users',
              name: 'users',
              orderable: true,
              serachable: true,
              sClass: 'text-center nk-tb-col nk-tb-col-users'
          },
          {
              data: 'status',
              name: 'status',
              orderable: false,
              serachable: false,
              sClass: 'text-center nk-tb-col'
          },
          
          {
              data: 'action',
              name: 'action',
              orderable: false,
              serachable: false,
              sClass: 'text-center nk-tb-col nk-tb-col-tools'
          },
          
      ];


      NioApp.DataTable("#groupsdataTable", {
          processing: true,
          serverSide: true,
          dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

          ajax: domainUrl + "/admin/groups/ajaxgetgroups",
          language: {
              paginate: {
                  next: '<i class="bx bx-chevron-right bx-18px"></i>',
                  previous: '<i class="bx bx-chevron-left bx-18px"></i>'
              }
          },
          columns: tblcol3,
          "order": [
              [0, "desc"]
          ]
      })
  </script>
