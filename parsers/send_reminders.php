<?php
    include("../includes/db.php");
    include("../includes/conf.php");

    function sendSMSReminder($api_key, $contacts, $sender_id, $message) {
        $url = 'https://bulksms.zamtel.co.zm/api/v2.1/action/send/api_key/'.$api_key.'/contacts/'.$contacts.'/senderId/'.$sender_id.'/message/'.urlencode($message);
        // Use cURL to send the SMS
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    $api_key = API; // Replace with your API key
    $sender_id = SENDER; // Replace with your sender ID

    try {
        $currentDate = date('Y-m-d');
        $fiveDaysAhead = date('Y-m-d', strtotime('+5 days'));
        $twoDaysAhead = date('Y-m-d', strtotime('+2 days'));

        // Fetch birthdays
        $stmt = $connect->prepare("SELECT * FROM birthdays WHERE date BETWEEN ? AND ?");
        $stmt->execute([$currentDate, $fiveDaysAhead]);
        $birthdays = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch reminder contacts
        $contactStmt = $connect->query("SELECT * FROM reminder_contacts");
        $contacts = $contactStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($birthdays as $birthday) {
            $birthdayDate = $birthday['date'];
            $daysAhead = ($birthdayDate === $fiveDaysAhead) ? 5 : (($birthdayDate === $twoDaysAhead) ? 2 : 0);

            if ($daysAhead > 0 || $birthdayDate === $currentDate) {
                $message = "Reminder: {$birthday['names']}'s birthday is in {$daysAhead} days.";

                // Send SMS to all contacts
                foreach ($contacts as $contact) {
                    sendSMSReminder($api_key, $contact['contact_phone'], $sender_id, $message);
                }
            }
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
?>
