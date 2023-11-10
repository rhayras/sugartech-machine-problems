<?php

function calculateEarnings($dailyExpense,$expeditions){

    $errors = array();
    //convert data from textarea into array
    $expeditions = explode("\n", str_replace("\r", "", $expeditions));
    $dailyEarnings = array();

    foreach($expeditions as $expedition){
        $hasError = false;

        //explode each expedition to get the price, path and the price
        $expeditionData = explode(" ",$expedition);
        if(count($expeditionData) != 3){
            $errors[] = "Wrong Format for : ".$expedition;
        }else{
            $lineError = "";
            $hours = intval($expeditionData[0]);
            $path = $expeditionData[1];
            $price = $expeditionData[2];

            //check if the hour is integer
            if(!is_int($hours)){
                $lineError .= "Invalid hour format<br/>";
                $hasError = true;
            }

            //check if the path is invalid
            if (preg_match('~[0-9]+~', $path)) {
                $lineError .= 'Invalid path format<br/>';
                $hasError = true;
            }
            //check if the price is invalid
            if(!is_numeric($price)){
                $lineError .= 'Invalid price format<br/>';
                $hasError = true;
            }

            if($lineError != ""){
                $lineError .= " for : <b>".$expedition."</b>";
                $errors[] = $lineError;
            }

            if(!$hasError){
                $partitionValues = array();
                $hoursBasedOnPath = strlen($path);

                $loops = intdiv($hours, $hoursBasedOnPath);
                for($x = 0; $x < $loops; $x++){
                    $partitionValues[] = $hoursBasedOnPath;
                }

                $remainder = $hours - ($hoursBasedOnPath * $loops);
                if($remainder > 0){
                    $partitionValues[] = $remainder;
                }

                $bottles = 0;
                foreach($partitionValues as $partition){
                    $word = substr($path, 0, $partition);
                    $bottles = $bottles + substr_count($word,"B");
                }
                $dailyEarnings[] = $bottles * $price;
            }
        }
    }

    if(!empty($errors)){
        $errorDisplay = "";
        $errorDisplay .= "<ul>";
        foreach($errors as $error){
            $errorDisplay .= "<li>".$error."</li>";
        }
        $errorDisplay .= "</ul>";

        return $errorDisplay;
    }else{

        $totalEarnings = array_sum($dailyEarnings);
        $averageEarnings = array_sum($dailyEarnings) / count($dailyEarnings);

        if($averageEarnings > $dailyExpense){
            //good
            $extraMoney = $averageEarnings - $dailyExpense;
            $extraMoney = (is_float($extraMoney)) ? $extraMoney : number_format($extraMoney);
            return "Good earnings. Extra money per day: ".$extraMoney;
        }else{
            //hard time
            $neededMoney = $totalEarnings - $dailyExpense;
            $neededMoney = (is_float($neededMoney)) ? $neededMoney : number_format($neededMoney);
            return "Hard times. Money needed: ".$neededMoney;
        }

        // return $averageEarnings;
        // return json_encode($dailyEarnings);
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Machine Problem 2</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-lg-5">
            <h1>Bottle Collector</h1>
            <?php
                if(isset($_POST['dailyExpense'])){
                    echo "<b>Result: </b>". calculateEarnings($_POST['dailyExpense'],$_POST['expeditions']);
                }
            ?>
                <form method="POST" id="calculator">
                    <div class="form-group">
                        <label>Daily Expense</label>
                        <input type="number" name="dailyExpense" id="dailyExpense" class="form-control" required value="<?= isset($_POST['dailyExpense']) ? $_POST['dailyExpense'] : '' ?>" />
                    </div>
                    <div class="form-group mt-2">
                        <label>Expedition</label>
                        <textarea name="expeditions" id="expeditions" rows="5" class="form-control" required placeholder="{Hours} {Path} {Price}"><?= isset($_POST['dailyExpense']) ? $_POST['expeditions'] : '' ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Calculate</button>
                </form>
            </div>
        </div>
    </div>    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>