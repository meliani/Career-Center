<html lang="fr">

<head>
    <title>Internship Agreement</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        h1 {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            margin-top: 10px;

        }

        .section-content {
            margin-left: 20px;
            text-align: justify;
        }

        p {
            text-align: justify;
        }

        table {
            border-collapse: collapse;
        }

        table p {
            margin: 0;
            text-align: center;
        }

        table,
        th,
        td {
            border: 1px solid gray;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        .header_logo {
            width: 130px;
            /* height: 100px; */
            margin-bottom: 20px;
        }

        .footer {
            width: 100%;
            text-align: left;
            /* position: absolute; */
            bottom: 0;
            left: 0;
            padding: 10px;
        }

        .footer p {
            margin: 0;
        }
    </style>
</head>

<body>
    {{-- platform logo left aligned --}}
    <img src="{{ asset('svg/logo-colors.svg') }}" alt="INPT" class="header_logo">


    <h1>CONVENTION DE STAGE</h1>

    {{-- page foorter --}}
    <hr>
    <div class="footer">
        <p>Av. Allal El Fassi,</p>
        <p>Madinat Al Irfane,</p>
        <p>Rabat - Maroc</p>
        <p>Tél. : + 212 5 37 77 30 77</p>
        <p>Fax : + 212 5 37 77 30 44</p>
        <p>https://www.inpt.ac.ma</p>
    </div>

</body>

</html>