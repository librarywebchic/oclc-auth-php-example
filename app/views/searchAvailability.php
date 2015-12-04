<?php

/**
 * This file presents the user with a form to enter an OCLC Number to searcj for holdings for
 */
?>
<html>
<head>
    <title>Search Availability</title>
    <style type="text/css">
        body {
            font-family: Helvetica, Verdana, sans-serif;
        }

        #container {
            width: 1300px;
            margin: auto;
            padding: 10px;
        }

        table {
            font-family: monospace;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            text-align: center;
            padding: 1px 4px;
            border: 1px solid lightgray
        }

        th {
            background-color: lightgray;
            border-color: gray
        }

        a {
            color: blue
        }

        a:hover {
            color: coral
        }

        .error {
            padding: 10px;
            border: 1px solid red;
            overflow: hidden;
            background-color: lightcoral;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div id="container">
    <form action="index.php" method="get">
    <label for="oclcNumber">OCLC Number: <input id="oclcNumber" name="oclcNumber" type="text"/></label>
    <input type="submit" value="Search Availablity"/>
    </form>
</div>
</body>
</html>
