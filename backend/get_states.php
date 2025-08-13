<?php
$country = $_GET['country'];
$api_key = 'adminuser'; 

if ($country) {
    $url = "http://api.geonames.org/childrenJSON?geonameId=$country&username=$api_key";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['geonames'])) {
        echo '<option value="">Select State</option>';
        foreach ($data['geonames'] as $state) {
            echo '<option value="' . $state['geonameId'] . '">' . $state['name'] . '</option>';
        }
    }
}
?>
