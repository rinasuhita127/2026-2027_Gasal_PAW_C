<?php
// 6.1
// array_push()
$a = array("A");
echo 'Array awal: ("A")<br>';
array_push($a, "B");  
echo "Hasil array_push: " . implode(" ", $a) . "<br><br>";

// array_merge()
$a1 = array("A", "B");
$a2 = array("C");
echo 'Array awal: ("A", "B") digabung dengan ("C")<br>';
$gabung = array_merge($a1, $a2); 
echo "Hasil array_merge: " . implode(" ", $gabung) . "<br><br>";

// array_values()
$b = array("x" => 1, "y" => 2);
echo 'Array awal: ("x" => 1, "y" => 2)<br>';
$nilai = array_values($b);   
echo "Hasil array_values: " . implode(" ", $nilai) . "<br><br>";

// array_search()
$c = array("A", "B", "C");
echo 'Mencari "B" pada array: ("A", "B", "C")<br>';
$cari = array_search("B", $c);   
echo "Hasil array_search: " . $cari . "<br><br>";

// array_filter()
$d = array(0, 1, false, 2, "", 3, "array");
echo 'Array awal: (0, 1, false, 2, "", 3, "array")<br>';
$filter = array_filter($d);  
echo "Hasil array_filter: " . implode(" ", $filter) . "<br><br>";

// sort() dan rsort() (array terindeks)
$e = array(3, 1, 2);
echo "Array awal: (3, 1, 2)<br>";
$e1 = $e;
sort($e1);
echo "Hasil sort: " . implode(" ", $e1) . "<br>";
$e2 = $e;
rsort($e2); 
echo "Hasil rsort: " . implode(" ", $e2) . "<br><br>";

// asort(), ksort(), arsort(), krsort() (array asosiatif)
$age = array("Peter" => 35, "Ben" => 37, "Joe" => 43);
echo 'Array awal: ("Peter"=>35, "Ben"=>37, "Joe"=>43)<br>';

$f = $age;
asort($f);    // urut naik berdasarkan value
echo "Hasil asort: ";
foreach ($f as $k => $v) { echo $k . "=> " . $v . ", "; }
echo "<br>";

$f = $age;
ksort($f);    // urut naik berdasarkan key
echo "Hasil ksort: ";
foreach ($f as $k => $v) { echo $k . "=> " . $v . ", "; }
echo "<br>";

$f = $age;
arsort($f);   // urut turun berdasarkan value
echo "Hasil arsort: ";
foreach ($f as $k => $v) { echo $k . "=> " . $v . ", "; }
echo "<br>";

$f = $age;
krsort($f);   // urut turun berdasarkan key
echo "Hasil krsort: ";
foreach ($f as $k => $v) { echo $k . "=> " . $v . ", "; }
echo "<br>";
?>