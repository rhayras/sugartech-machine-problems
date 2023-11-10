<?php


function partSums($array){
	$sums = array();

	$arrayLength = count($array);

	for($x = 0; $x <= $arrayLength; $x++){
		if($x == 0){
			$sums[] = array_sum($array);
		}else{
			$sum = 0;
			foreach($array as $key => $val){
				if($key >= $x){
					$sum += $val;
				}
			}
			$sums[] = $sum;
		}
	}


	return json_encode($sums);
}

echo partSums([1, 2, 3, 4, 5, 6]);
