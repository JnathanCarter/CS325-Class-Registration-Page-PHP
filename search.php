<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
</head>

<body>
        <?php
        error_reporting(E_ALL);
        ?>
        <?php
        ini_set('error_reporting', E_ALL);
        ?>
        <?php
        function contstructTime($hours, $minute)
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



                //adds values to changedValue array if the values are not defaults
                foreach ($_POST as $key => $vale) {

                        if ($vale != "default" && $vale != "") {
                                echo "<p>Changed Key: $key, Changed Value: $vale </p>";

                                $changedValues[$key] =  $vale;
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

                        $changedValues["formattedBegin"] = $beginFormatted;
                }
                if (array_key_exists("end_hh", $changedValues)) {
                        if ($changedValues["end_ap"] == "pm") {
                                $hours = $changedValues["end_hh"] + 12;

                                $endFormatted = $hours . ":" . $changedValues["end_mm"] . ":00";
                        } else {
                                $endFormatted = $changedValues["end_hh"] . ":" . $changedValues["end_mm"] . ":00";
                        }


                        $changedValues["formattedEnd"] = $endFormatted;
                }




                echo "<p>begin chagne ------$beginTimechange";
                echo "<p>end change ------------- $endTimechange";
                //diagnostic print

                foreach ($changedValues as $key => $vale) {
                        echo "<p> $key ---------- $vale</p>";
                }

                function removeBeginEndHHMM()
                {
                }

                function convertToFormatted24HHTime($hours, $minutes, $meridian)
                {
                        $result = "";
                        if ($meridian == "pm") {
                                $hours = $hours + 12;
                                $minutes = $minutes + 12;
                                $result = $hours + ":" + $minutes + ":00";
                        } else {
                                $result = $hours + ":" + $minutes + ":00";
                        }
                        return $result;
                }
        }



        ?>
</body>

</html>