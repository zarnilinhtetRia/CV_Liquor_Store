<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liquar POS</title>

    <!-- Google Font: Source Sans Pro -->

    {{-- <link rel="stylesheet" href="{{ asset('locallink/css/google_font_source_sans_pro.css') }}"> --}}
    <!-- Font Awesome -->

    {{-- <link rel="shortcut icon" href="{{ asset('img/logo.jpg') }}" type="image/x-icon"> --}}

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <!-- Theme style -->

    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>


<style>
    .dt-buttons {
        background-color: #007BFF;

        color: #fff;

    }



    .form-control {
        font-size: 15px;
        font-family: "Times New Roman", serif;

    }

    button {
        font-family: 'Times New Roman', serif;
    }


    label {
        font-family: "Times New Roman", serif;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-family: "Times New Roman", serif;
        font-weight: bold;
    }

    li {
        font-family: 'Times New Roman', serif;
        font-weight: bold;
    }

    span {
        font-family: 'Times New Roman', serif;
        font-weight: bold;
    }

    a {
        font-family: 'Times New Roman', serif;
        font-size: 16px;
        /* Adjust size as needed */
        font-weight: bold;

        color: #007bff;
        /* Default link color (blue) */
        text-decoration: none;
        /* Removes underline */
    }

    a:hover {
        color: #0056b3;
        /* Darker shade on hover */
        text-decoration: underline;
        /* Adds underline on hover */
    }

    .dataTables_wrapper .dataTables_paginate {
        font-family: 'Times New Roman', serif;
        font-size: 14px;
        /* Adjust size as needed */
        font-weight: bold;
        /* Optional: Makes text bold */
    }

    .dataTables_wrapper .dataTables_info {
        font-family: 'Times New Roman', serif;
        font-size: 15px;
        /* Adjust as needed */
        font-weight: bold;
        /* Optional */
        color: #333;
        /* Optional: Adjust color */
    }

    .alert {
        font-family: 'Times New Roman', serif;
        font-size: 16px;

        font-weight: bold;

        padding: 10px 15px;
        border-radius: 5px;

    }

    th {
        font-size: 14px;
        font-family: "Times New Roman", serif;
        font-weight: bold;
        /* text-transform: uppercase; */
    }

    td {
        font-size: 14px;
        font-family: "Times New Roman", serif;
    }

    .main-header {
        /* background: radial-gradient(circle, rgb(255, 100, 100), rgb(52, 52, 52)); */
        background: #092366;

    }

    /*
    .modal-header,

    .modal-body,


    .card-color {
        background-color: #092366 !important;
    } */
</style>
