<?php
$state = $_GET['state'];
$api_key = 'adminuser'; // Your Geonames API key

if ($state) {
    $url = "http://api.geonames.org/searchJSON?adminCode1=$state&country=PK&maxRows=100&username=$api_key";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['geonames'])) {
        echo '<option value="">Select City</option>';
        foreach ($data['geonames'] as $city) {
            echo '<option value="' . $city['geonameId'] . '">' . $city['name'] . '</option>';
        }
    }
}
?>
