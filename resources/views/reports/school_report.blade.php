<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>School Report</title>

    <style>

        body{
            font-family: DejaVu Sans;
            font-size:12px;
        }

        .title{
            text-align:center;
            margin-bottom:20px;
        }

        .section{
            margin-top:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        table th,
        table td{
            border:1px solid #ddd;
            padding:8px;
        }

        .summary{
            margin-top:15px;
        }

    </style>

</head>

<body>

@include('reports.partials.header')

@include('reports.partials.attendance')

@include('reports.partials.academic')

@include('reports.partials.financial')

@include('reports.partials.salary')

@include('reports.partials.footer')

</body>
</html>
