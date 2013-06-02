<?php
//original data must be parsed from getwork Bitcoin protocol!!!
$original_data              = "00000002417c7e4e57cb6ee4b543beb1289f13a6cfa414e5d7decec90000000100000000951e9b988792a1bc19af68b83f52d6668fe91c828023e8355c76367123eb097051aaf6491a01616400000000000000800000000000000000000000000000000000000000000000000000000000000000000000000000000080020000";

$original_data_chopped      = substr($original_data, 0, 152); // 160 - 8(nonce)
$original_data_chopped_part = substr($original_data, 160); // everything after nonce
$target                     = "0000000000000000000000000000000000000000000000646101000000000000";
$reversed_target            = strrev($target); //reversed characters
$fixed_target               = 0x00000000; //hardcoded THIS MUTST BE CHANGED!!!
//$reversed_target          = invert($target); //little to big endian
$data                       = invert($original_data); //little to big endian

//debug
echo "Original data:\n".substr($original_data, 0, 64);
echo "\n".substr($original_data, 64, 64);
echo "\n".substr($original_data, 128, 64);
echo "\n".substr($original_data, 192, 64);
echo "\n\n";
echo "Original target:\n".$target;
echo "\n\nReversed target:\n".$reversed_target;

echo "\n\nLittle endian data:\n".substr($data, 0, 64);
echo "\n".substr($data, 64, 64);
echo "\n".substr($data, 128, 64);
echo "\n".substr($data, 192, 64);
echo "\n\n";

$data_chopped = substr($data, 0, 152);
//$nonce = '0xffffffff';
$nonce = 0x00000000;
$max = 0xffffffff;
$counter = 0;
$full_counter = 0;
$start_time = time();
for($i = +($nonce); $i<=$max; $i++) {
  //$nonce = dechex($i);
  $nonce = str_pad(dechex($i), 8, '0', STR_PAD_LEFT);
  $hash = hash("sha256",hash("sha256",$data_chopped.$nonce));

  //verify hash
  /*
  if($try <= $reversed_target)
    die("\n\nFOUND!\n\nnonce: ".$nonce."\n\n".$original_data_chopped.$nonce);
  */
  $try = substr($hash, 0, 8); //hardcoded THIS MUTST BE CHANGED!!!
  eval("\$htry = 0x$try;"); //string to hex
  //if(hexdec($try) <= hexdec($fixed_target)){  // decimal verification
  if($htry <= $fixed_target){
    die("\n\nFOUND!\n\nnonce: ".$nonce."\n\nPROOF OF WORK:\n".$original_data_chopped.$nonce.$original_data_chopped_part);
  }
  
  if($counter > 999999){
    $hashes_per_second = $full_counter/(time()-$start_time);
    echo "\n$nonce: ".$try." <= 00000000 ($full_counter hashes so far [".intval($hashes_per_second)." hashes/s])";
    $counter = 0;
  }
  $counter++;
  $full_counter++;
}
die("\n\nWTF!?");

// little to big endian
function invert($data){
  for($i=0; $i<32; $i++){
    $s1 = substr($data, $i*8, 8);
    $b = null;
    for($j=0; $j<4; $j++){
      $s2 = substr($s1, $j*2, 2);
      $b[] = $s2;
    }
    $c[] = array_reverse($b);
  }
  $w= null;
  foreach($c as $cc){
    $w .= implode("",$cc);
  }
  return $w;
}