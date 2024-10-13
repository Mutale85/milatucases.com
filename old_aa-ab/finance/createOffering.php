<?php
require '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['confirmation']) || $_POST['confirmation'] !== 'on') {
        echo 'Please confirm that the offering is true.';
        exit;
    }

    $memberId = filter_input(INPUT_POST, 'member_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $churchId = filter_input(INPUT_POST, 'church_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_SPECIAL_CHARS);
    $donationDate = filter_input(INPUT_POST, 'donation_date', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        $stmt = $connect->prepare("INSERT INTO church_offering (churchId, memberId, amount, donationDate) VALUES (?, ?, ?, ?)");
        $stmt->execute([$churchId, $memberId, $amount, $donationDate]);
        echo 'Offering added successfully';
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>
