<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
</head>

<body>
        <?php function contstructTime($hours, $minute)
        {
                return "$hours:$minute:00";
        }

        $changedValues = [];

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                echo "<p>This is a GET request.</p>\n";
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $beginHour = $_POST['begin_hh'];
                $beginMinute = $_POST['begin_mm'];

                $endHour = $_POST['end_hh'];
                $endMinute = $_POST['end_mm'];
                $beginAP = $_POST['begin_ap'];

                $endAP = $_POST['end_ap'];
                $beginTimechange = false;
                $endTimechange = false;
                //adds values to changedValuearray if the values are not defaults
                foreach ($_POST as $key => $vale) {

                        if ($vale != "default" && $vale != "") {
                                echo "<p>Changed Key: $key, Changed Value: $vale </p>";


                                $changedValues[$key] = $vale;
                        }
                }

                //if time has changed convert it to 24 hour formatted time string for get reuest
                if (array_key_exists("begin_hh", $changedValues)) {

                        $beginTimechange = true;


                        if ($changedValues["begin_ap"] == "pm") {
                                $hours = $changedValues["begin_hh"] + 12;


                                $beginFormatted = $hours . ":" . $changedValues["begin_mm"] . ":00";
                        } else {
                                $beginFormatted = $changedValues["begin_hh"] . ":" . $changedValues["begin_mm"] . ":00";
                        }

                        $changedValues["start"] = $beginFormatted;
                }
                if (array_key_exists("end_hh", $changedValues)) {
                        if ($changedValues["end_ap"] == "pm") {
                                $hours = $changedValues["end_hh"] + 12;


                                $endFormatted = $hours . ":" . $changedValues["end_mm"] . ":00";
                        } else {
                                $endFormatted = $changedValues["end_hh"] . ":" . $changedValues["end_mm"] . ":00";
                        }


                        $changedValues["end"] = $endFormatted;
                }
                //remove individual time fields
                $removeBegin = ['begin_hh', 'begin_mm', 'begin_ap'];

                foreach ($removeBegin as $key) {
                        unset($changedValues[$key]);
                }
                $removeEnd = ['end_hh', 'end_mm', 'end_ap'];


                foreach ($removeEnd as $key) {
                        unset($changedValues[$key]);
                }
                //if days is set to any remove days

                if ($changedValues['days'] == "any") {

                        unset($changedValues['days']);
                }
                //diagnostic print
                foreach ($changedValues as $key => $vale) {
                        echo "<p> $key ---------- $vale</p>";
                }



                $cURL = curl_init();

                $url = "http://ec2-3-143-211-101.us-east-2.compute.amazonaws.com/CS325_Project3/search";

                $url = $url . '?' . http_build_query($changedValues);

                curl_setopt($cURL, CURLOPT_URL, $url);
                // Set URL
                curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                // Return Transfer as String
                $output = curl_exec($cURL);
                // Execute Transfer (returns JSON)
                curl_close($cURL);
                // Close cURL Session
                echo "<p>";
                // Begin Output Paragraph
                $json = json_decode($output, true);
                // Decode JSON String
                foreach ($json as $key => $value) { // Print JSON Key/Value Pairs
                        echo "$key: " . var_export($value, true) . "<br />";
                }
                echo "</p>
                <p>";
                // Begin New Output Paragraph
                $jsonString = json_encode($json);
                // Encode JSON Array to a String
                echo $jsonString;
                // Print Encoded JSON String
                echo "</p>";

                /*
                echo "<p>url--------------$url";
                
                        curl_setopt($cURL, CURLOPT_URL, $url);
                        
                        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                        
                        $output = curl_exec($cURL);
                        
                        curl_close($cURL);
                        
                        echo "
                <p>";
                
                        $json = json_decode($output, true);
                        
                        foreach ($json as $key => $value) {
                        echo "$key: " . var_export($value, true) . "<br />";
                        
                        }
                        echo "</p>
                <p>";
                
                        $jsonString = json_encode($json);
                        
                        echo $jsonString;
                        
                        echo "</p>";
                        
                */
        }



        ?>
</body>

</html>