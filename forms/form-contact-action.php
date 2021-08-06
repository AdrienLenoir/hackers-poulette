<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mailtrap_user = "MAILTROP_USER";
$mailtrap_password = "MAILTROP_PASSWORD";
$recaptacha_secret_key = "GOOGLE_RECAPTCHA_PRIVAE_KEY";

$error = [
    "global" => null,
    "name" => null,
    "lastname" => null,
    "gender" => null,
    "email" => null,
    "country" => null,
    "subject" => null,
    "message" => null
];
$success_message = null;

$required_input = ["name","lastname","gender","email","country","subject","message"];
$subjects = ["Order","Refound","Recruitment"];
$genders = ["Male","Female"];

$countries_request = file_get_contents("https://restcountries.eu/rest/v2/all");

if (!$countries_request) {
    $error["global"] = "An error occured with countries API (refresh this page)";
    return;
}

$countries = array_map(function ($value) { return $value->name;}, json_decode($countries_request));

if (!isset($_POST["contact_submit"])) return;

function checkCountryExist($country) {
    global $countries;
    return in_array($country,$countries);
}

function callRecaptchaAPI($token) {
    global $recaptacha_secret_key;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $recaptacha_secret_key, 'response' => $token)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

foreach ($required_input as $input_name) {
    if (!isset($_POST[$input_name]) || empty($_POST[$input_name])) {
        $error[$input_name] = "This field is required.";
    }
}

if (isset($_POST['name']) && !empty($_POST['name'])
    && isset($_POST['lastname']) && !empty($_POST['lastname'])
    && isset($_POST['gender']) && !empty($_POST['gender'])
    && isset($_POST['email']) && !empty($_POST['email'])
    && isset($_POST['country']) && !empty($_POST['country'])
    && isset($_POST['subject']) && !empty($_POST['subject'])
    && isset($_POST['message']) && !empty($_POST['message'])
    ) {
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $country = $_POST['country'];
    $message = nl2br($_POST['message']);
    $subject = $_POST['subject'];

    if (isset($_POST['token']) && !empty($_POST['token'])) {
        $token = $_POST['token'];
        $apiResponse = callRecaptchaAPI($token);

        if(!$apiResponse["success"] == '1' || $apiResponse["score"] < 0.5) {
            $error["global"] = "Recaptchat: robot detection";
            return;
        }
    }

    if (!ctype_alpha($name)) {
        $error["name"] = "Name is not a alphanumeric";
        return;
    }

    if (!ctype_alpha($lastname)) {
        $error["lastname"] = "Last name is not a alphanumeric";
        return;
    }

    if (!in_array($gender, $genders)) {
        $error["gender"] = "Not correct gender type";
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error["email"] = "Email is not correct";
        return;
    }

    if (!ctype_alpha($country)) {
        $error["country"] = "Country is not a alphanumeric";
        return;
    }

    if (!checkCountryExist($country)) {
        $error["country"] = "Country not exist";
        return;
    }

    if (!in_array($subject, $subjects)) {
        $error["subject"] = "Not correct gender type";
        return;
    }

    if (substr_count($_POST['message'], ' ') === strlen($_POST['message'])) {
        $error["message"] = "Not correct message";
        return;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = $mailtrap_user;
        $mail->Password = $mailtrap_password;

        $mail->setFrom('noreply@hackers-poulette.com', 'Hackers-Poulette');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Hackers Poulette - '.$subject;
        $mail->Body    = '<p>Your contact request has been sent successfully!</p><br><p>Your message :</p><p>"'.$message.'"</p>';

        $mail->send();

        $_POST = [];
        $success_message = "Your contact request has been sent successfully";
    } catch (Exception $e) {
        $error["global"] = "An error occured with mail";
    }
} else {
    $error["global"] = "Complete the entire form";
}
