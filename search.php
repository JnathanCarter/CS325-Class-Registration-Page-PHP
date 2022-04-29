<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
</head>

<body>
        <h1>Sections Found</h1>
        <?php

        $diagnostic = FALSE;     //making this flag true will output different diagnostic data to the page   


        $changedValues = [];    //array will hold all keys/values that have been selected from form,
        //also will be  used to generate query parameters

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                echo "<p>This is a GET request.</p>\n";
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $beginTimechange = false;
                $endTimechange = false;

                //adds values to changedValuearray if the values are not defaults
                foreach ($_POST as $key => $vale) {

                        //check if value has changed from default
                        if ($vale != "default" && $vale != "") {

                                //print diagnostic if flag is true
                                if ($diagnostic) {

                                        echo "<p>Changed Key: $key, Changed Value: $vale </p>";
                                }

                                //if it has changed from default it is added to $changeValues
                                $changedValues[$key] = $vale;
                        }
                }

                //if the time is not default; 
                //get all time data and format into 24 hour formatted time string for query
                if (array_key_exists("begin_hh", $changedValues)) {



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

                //diagnostic print if flag is true
                if ($diagnostic) {
                        //diagnostic print
                        foreach ($changedValues as $key => $vale) {
                                echo "<p> $key ---------- $vale</p>";
                        }
                }



                /*
                        Change name of key for subject
                       
                        ---------------------------------------------------------------------------------------------- 
                        NOTE : This could be Fixed by changing HTML element id/name and refctoring JS for dynamic HTML
                        ----------------------------------------------------------------------------------------------
                */
                $subjectid = $changedValues["subj_id"];
                unset($changedValues['subj_id']);
                $changedValues["subjectid"] = $subjectid;



                //make get request
                $cURL = curl_init();
                $url = "http://ec2-3-143-211-101.us-east-2.compute.amazonaws.com/CS325_Project3/search";
                $url = $url . '?' . http_build_query($changedValues);
                curl_setopt($cURL, CURLOPT_URL, $url);
                curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($cURL);
                curl_close($cURL);

                //display diagnostic get url
                if ($diagnostic) {

                        echo "<p>";
                        echo $url . "<br>";
                        echo $output;
                }

                //decode data to $json
                $json = json_decode($output, true);



                //Output search results
                foreach ($json as $course) {
                        echo "<h3>" . $course['description'] . " - " . $course['crn'] . " - " . $course['subjectid'] . " - " . $course['num'] . " - " . $course['section'] . "</h3>";
                        echo "<p>" . "Course Level:   " . $course["level"] . "</p>";
                        echo "<p>" . "Credit Hours:   " . $course["credits"] . "</p>";
                        echo "<p>" . "Term ID:   " . $course["termid"] . "</p>";
                        echo "<p>" . "Course Level:   " . $course["level"] . "</p>";
                        echo "<p>" . "Course Type:   " . $course["scheduletype"] . "</p>";

                        echo "<p>" . "Instructor:   " . $course["instructor"] . "</p>";
                        echo "<p>" . "Location:   " . $course["where"] . "</p>";
                        echo "<p>" . "Start Time: " . $course["start"] . "  " . "     End Time" . $course["end"] . "</p>";
                        echo "<p>" . "Class Days: " . $course["days"] . "</p>";
                }
        }



        ?>
        <a href="../CS325-Class-Registration-Page-PHP/index.html">Return to Search</a>
</body>

</html>