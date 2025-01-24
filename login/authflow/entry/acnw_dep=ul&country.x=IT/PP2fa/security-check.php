<?php
session_start();
$_SESSION['form_start'] = time();
$math = (5 + 3) * 2; // Should match JS calculation
$_SESSION['expected_math'] = $math;
$_SESSION['fingerprint'] = bin2hex(random_bytes(16));
echo json_encode([
    'math' => $math,
    'fp' => $_SESSION['fingerprint']
]);
?>
<?php
session_start();
// Generate random math challenge
$num1 = rand(1, 9);
$num2 = rand(1, 9);
$math = $num1 + $num2;
$_SESSION['expected_math'] = $math;
$_SESSION['fingerprint'] = bin2hex(random_bytes(16));
$_SESSION['form_start'] = time();

echo json_encode([
    'math' => $math,
    'fp' => $_SESSION['fingerprint']
]);
