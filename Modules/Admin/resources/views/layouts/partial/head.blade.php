<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{isset($siteinfo->site_name)?$siteinfo->site_name:''}} | Dashboard</title>
    @if(isset($siteinfo->sites_favicon))
        <link rel="shortcut icon" href="{{ asset($siteinfo->sites_favicon) }}">
    @else
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/css/dashlite.css')}}">

    <link href="{{ asset('css/spectrum.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/prism.css') }}" rel="stylesheet">
    <link href="{{ asset('css/msform.css') }}" rel="stylesheet"> 

    <!-- New UI -->
    <link rel="preload" as="font" type="font/woff" href="{{ asset('fonts/LabGrotesque-Bold.woff') }}" crossorigin="anonymous">
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/LabGrotesque-Bold.woff2') }}" crossorigin="anonymous">
    <link rel="preload" as="font" type="font/woff" href="{{ asset('fonts/LabGrotesque-Medium.woff') }}" crossorigin="anonymous">
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/LabGrotesque-Medium.woff2') }}" crossorigin="anonymous">
    <link rel="preload" as="font" type="font/woff" href="{{ asset('fonts/LabGrotesque-Regular.woff') }}" crossorigin="anonymous">
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/LabGrotesque-Regular.woff2') }}" crossorigin="anonymous">
    <!-- FontAwesome -->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- New UI -->
    
    <link id="skin-default" rel="stylesheet" href="{{ asset('assets/css/theme.css')}}">
    <link id="skin-escrow" rel="stylesheet" href="{{ asset('assets/css/skins/theme-escrow.css')}}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

   <style type="text/css">
       /* @php $theme = get_default_theme(); @endphp

        @if(isset($siteinfo->sites_sidebar_bg_color_code))
            .bg-gradient-primary{background-color: {{$siteinfo->sites_sidebar_bg_color_code}};}
        @else
            .bg-gradient-primary{background-color: {{ $theme->sidebar_bgcolor}}; }
        @endif

        @if(isset($siteinfo->sites_sidebar_menu_text_color_code))
        .sidebar-dark .nav-item .nav-link{color: {{$siteinfo->sites_sidebar_menu_text_color_code}};}
        .sidebar-dark .nav-item .nav-link i{color: {{$siteinfo->sites_sidebar_menu_text_color_code}};}
        .sidebar-heading{color: {{$siteinfo->sites_sidebar_menu_text_color_code}} !important;}
        .nk-menu .nk-menu-item .nk-menu-link, .nk-menu-icon{color: {{$siteinfo->sites_sidebar_menu_text_color_code}} !important;}
        .link-list a{color: {{$siteinfo->sites_sidebar_menu_text_color_code}} !important;}
        .nav-tabs .nav-link{color: {{$siteinfo->sites_sidebar_menu_text_color_code}} !important;}
        @else
            .sidebar-dark .nav-item .nav-link{color: {{$theme->menu_text}};}
            .sidebar-dark .nav-item .nav-link i{color: {{$theme->menu_text}};}
            .sidebar-heading{color: {{$theme->menu_text}} !important;}
            .nk-menu .nk-menu-item .nk-menu-link, .nk-menu-icon{color: {{$theme->menu_text}} !important;}
            .link-list a{color: {{$theme->menu_text}} !important;}
            .nav-tabs .nav-link{color: {{$theme->menu_text}} !important;}
        @endif

        @if(isset($siteinfo->sites_sidebar_menu_text_hover_color_code))
        .sidebar-dark .nav-item .nav-link:active, .sidebar-dark .nav-item .nav-link:focus, .sidebar-dark .nav-item .nav-link:hover{color: {{$siteinfo->sites_sidebar_menu_text_hover_color_code}};}
        .nk-menu-link:hover, .nk-menu-link:hover .nk-menu-icon,.is-dark .nk-menu-link:hover,.is-dark .nk-menu-item.active > .nk-menu-link .nk-menu-icon,.is-dark .active > .nk-menu-link{color: {{$siteinfo->sites_sidebar_menu_text_hover_color_code}} !important;}
        .link-list:hover,.link-list a:hover{color: {{$siteinfo->sites_sidebar_menu_text_hover_color_code}} !important;}
        .nav-tabs .nav-link:focus, .nav-tabs .nav-link.active, .nav-tabs .nav-item.active .nav-link{color: {{$siteinfo->sites_sidebar_menu_text_hover_color_code}} !important;}
        @else
            .sidebar-dark .nav-item .nav-link:active, .sidebar-dark .nav-item .nav-link:focus, .sidebar-dark .nav-item .nav-link:hover{color: {{$theme->menu_text_hover}};}
            .nk-menu-link:hover, .nk-menu-link:hover .nk-menu-icon,.is-dark .nk-menu-link:hover,.is-dark .nk-menu-item.active > .nk-menu-link .nk-menu-icon,.is-dark .active > .nk-menu-link{color: {{$theme->menu_text_hover}} !important;}
            .link-list:hover,.link-list a:hover{color: {{$theme->menu_text_hover}} !important;}
            .nav-tabs .nav-link:focus, .nav-tabs .nav-link.active, .nav-tabs .nav-item.active .nav-link{color: {{$theme->menu_text_hover}} !important;}
        @endif

        /* Sidebar sub menu */
        @if(isset($siteinfo->sites_sidebar_sub_menu_bg_color_code))
        .collapse-inner{background-color: {{$siteinfo->sites_sidebar_sub_menu_bg_color_code}} !important;}
        .nk-menu-sub .active > .nk-menu-link{background-color: {{$siteinfo->sites_sidebar_sub_menu_bg_color_code}} !important;}
        @else
            .collapse-inner{background-color: {{$theme->submenu_bgcolor}} !important;}
            .nk-menu-sub .active > .nk-menu-link{background-color: {{$theme->submenu_bgcolor}} !important;}
        @endif

        @if(isset($siteinfo->sites_sidebar_sub_menu_txt_color_code))
        .sidebar .nav-item .collapse .collapse-inner .collapse-item, .sidebar .nav-item .collapsing .collapse-inner .collapse-item{color: {{$siteinfo->sites_sidebar_sub_menu_txt_color_code}};}
        .collapse-header{color: {{$siteinfo->sites_sidebar_sub_menu_txt_color_code}} !important;}
        .nk-menu-sub .active > .nk-menu-link > .nk-menu-text{color: {{$siteinfo->sites_sidebar_sub_menu_txt_color_code}} !important;}
        @else
            .sidebar .nav-item .collapse .collapse-inner .collapse-item, .sidebar .nav-item .collapsing .collapse-inner .collapse-item{color: {{$theme->submenu_text}};}
            .collapse-header{color: {{$theme->submenu_text}} !important;}
            .nk-menu-sub .active > .nk-menu-link > .nk-menu-text{color: {{$theme->submenu_text}} !important;}
        @endif

        @if(isset($siteinfo->sites_sidebar_sub_menu_txt_hover_color_code))
        .sidebar .nav-item .collapse .collapse-inner .collapse-item:hover{background-color: {{$siteinfo->sites_sidebar_sub_menu_txt_hover_color_code}} !important;}
        .nk-menu-sub .active > .nk-menu-link:hover{background-color: {{$siteinfo->sites_sidebar_sub_menu_txt_hover_color_code}} !important;}
        @else
            .sidebar .nav-item .collapse .collapse-inner .collapse-item:hover{background-color: {{$theme->submenu_text_hover}} !important;}
            .nk-menu-sub .active > .nk-menu-link:hover{background-color: {{$theme->submenu_text_hover}} !important;}
        @endif*/

        /* Site buttons */
        /**Commented due to currently not-in use */
        /* @if(isset($siteinfo->sites_btn_bg_color_code))
        .btn-success, .btn-primary{background: {{$siteinfo->sites_btn_bg_color_code}} !important;border-color: {{$siteinfo->sites_btn_bg_color_code}};}
        .tab .nav-tabs > a.active{background: {{$siteinfo->sites_btn_bg_color_code}} !important;}
        .tab a:hover{background: {{$siteinfo->sites_btn_bg_color_code}} !important;}
        @else
            .btn-success, .btn-primary{background: {{$theme->btn_bgcolor}} !important;border-color: {{$theme->btn_bgcolor}};}
            .tab .nav-tabs > a.active{background: {{$theme->btn_bgcolor}} !important;}
            .tab a:hover{background: {{$theme->btn_bgcolor}} !important;}
        @endif

        @if(isset($siteinfo->sites_btn_txt_color_code))
        .btn-success, .btn-primary{color: {{$siteinfo->sites_btn_txt_color_code}} !important;}
        @else
            .btn-success, .btn-primary{color: {{ $theme->btn_text }} !important;}
        @endif
        */
        /* Sidebar background color */
        @if(isset($siteinfo->sites_sidebar_bg_color_code))
        .bgcstm_000{background-color: {{$siteinfo->sites_sidebar_bg_color_code}} !important;}color: {{$siteinfo->sites_sidebar_menu_text_color_code}};
        @else
            .bgcstm_000{background-color: {{ $theme->sidebar_bgcolor }}  !important; color: {{ $theme->menu_text }};}
        @endif

        .recent_txns{
            border-bottom: 2px dotted #aaa;
        }
        .recent_txns:last-child {
            border-bottom: 0px;
        }

</style>


<script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterangepicker.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css') }}" />
</head>
