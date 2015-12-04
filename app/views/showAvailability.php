<?php
use Guzzle\Http\Client;

$oclcNumber = $_GET['oclcNumber'];

$url = "https://worldcat.org/circ/availability/sru/service?x-registryId=" . $_SESSION['institution'] . "&query=no:ocm" . $oclcNumber;

$client = new Client($guzzleOptions);

$headers = array('Authorization' => 'Bearer ' . $accessToken->getValue());

// Set the guzzle headers
$guzzleOptions['headers'] = $headers;

// And execute the request (in this case a simple GET with no request body)
try {
    $response = \Guzzle::get($url, $guzzleOptions);

    // Define the namespaces for parsing

    $availabilityResponse = simplexml_load_string($response->getBody(true));
    $acquisitionsAtom->registerXPathNamespace("atom", "http://www.w3.org/2005/Atom");
    $holdings = $availabilityResponse->xpath('//holdings/holding');

} catch (\Guzzle\Http\Exception\BadResponseException $error) {
    // Or display the error, if one occurs
    echo '<div class="error">';
    echo $error->getResponse()->getStatusCode();
    echo $error->getRequest();
    echo '</div>';
}
?>
<html>
<head>
    <title>Availability Detail Screen for <?php echo $oclcNumber ?></title>
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

        td {
            padding: 1px 4px;
            border: 1px solid lightgray
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
    <button style='font-size:1.5em;' onclick="window.location='index.php'">Back</button>
    <h1>Availability Detail Screen for <?php echo $oclcNumber ?></h1>
    <table>
        <tr>
            <td>Branch</td>
            <td>Location</td>
            <td>Call Number</td>
            <td>Availablity</td>
        </tr>
        <?php
        foreach ($holding as $holdings){
            $row = '<tr>';
            $row .= '<td>' . $holding->localLocation . '</td>';
            $row .= '<td>' . $holding->shelvingLocation . '</td>';
            $row .= '<td>' . $holding->callNumber . '</td>';
            $row .= '<td>' . $holding->circulations->circulation->availableNow['value'] . '</td>';
            $row .= '</tr>';
            echo $row;
        }?>
    </table>
</div>
</body>
</html>