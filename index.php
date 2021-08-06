<?php
include("vendor/autoload.php");
require("forms/form-contact-action.php");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hackers Poulette - Contact</title>
    <meta name="description" content="The company Hackers Poulette™ sells Raspberry Pi accessory kits to build your own">

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
</head>
<body>
<main class="contact-support">
    <section class="logo-section">
        <img class="logo" src="assets/img/logo.png" alt="Hackers poulette logo">
        <h1 class="page-title">Contact support</h1>
    </section>
    <section class="form-section">
        <form method="post" id="contactForm">
            <?php if (isset($error["global"])) { ?>
                <p class="error global-error"><?= $error["global"] ?></p>
            <?php } ?>
            <?php if (isset($success_message)) { ?>
                <p class="success"><?= $success_message ?></p>
            <?php } ?>
            <div class="input-group input-group-w-50">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Enter your name" value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>">
                <?php if (isset($error["name"])) { ?>
                    <p class="error"><?= $error["name"] ?></p>
                <?php } ?>
            </div>
            <div class="input-group input-group-w-50">
                <label for="lastname">Last name</label>
                <input type="text" name="lastname" id="lastname" placeholder="Enter your last name" value="<?= isset($_POST['lastname']) ? $_POST['lastname'] : '' ?>">
                <?php if (isset($error["lastname"])) { ?>
                    <p class="error"><?= $error["lastname"] ?></p>
                <?php } ?>
            </div>
            <div class="input-group input-group-w-50">
                <label for="gender">Gender</label>
                <select name="gender" id="gender">
                    <?php foreach ($genders as $gender) { ?>
                        <option value="<?= $gender ?>"><?= $gender ?></option>
                    <?php } ?>
                </select>
                <?php if (isset($error["gender"])) { ?>
                    <p class="error"><?= $error["gender"] ?></p>
                <?php } ?>
            </div>
            <div class="input-group input-group-w-50">
                <label for="email">Email adress</label>
                <input type="email" name="email" id="email" placeholder="Enter your email adress" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">
                <?php if (isset($error["email"])) { ?>
                    <p class="error"><?= $error["email"] ?></p>
                <?php } ?>
            </div>
            <div class="input-group input-group-w-50">
                <label for="country">Country</label>
                <select name="country" id="country">
                    <?php foreach ($countries as $country) { ?>
                    <option <?php if (isset($_POST['country']) && $_POST['country'] === $country) { ?>selected<?php } ?> value="<?= $country ?>"><?= $country ?></option>
                    <?php } ?>
                </select>
                <?php if (isset($error["country"])) { ?>
                    <p class="error"><?= $error["country"] ?></p>
                <?php } ?>
            </div>
            <div class="input-group input-group-w-50">
                <label for="subject">Subject</label>
                <select name="subject" id="subject">
                    <?php foreach ($subjects as $subject) { ?>
                        <option <?php if (isset($_POST['subject']) && $_POST['subject'] === $subject) { ?>selected<?php } ?> value="<?= $subject ?>"><?= $subject ?></option>
                    <?php } ?>
                </select>
                <?php if (isset($error["subject"])) { ?>
                    <p class="error"><?= $error["subject"] ?></p>
                <?php } ?>
            </div>
            <div class="input-group input-group-w-100">
                <label for="message">Message</label>
                <textarea name="message" id="message" cols="100" rows="15" placeholder="Enter your message"><?= isset($_POST['message']) ? $_POST['message'] : '' ?></textarea>
                <?php if (isset($error["message"])) { ?>
                    <p class="error"><?= $error["message"] ?></p>
                <?php } ?>
            </div>
            <input id="recaptchatoken" type="hidden" name="token">
            <div class="button-group-right">
                <button id="formButton" name="contact_submit" type="submit">send</button>
            </div>
        </form>
    </section>
</main>

<script src="https://www.google.com/recaptcha/api.js?render=6LfdHOIbAAAAAMemM-0xZfSBnKK_djbaLuGAvIi9"></script>
<script>
    let tokenInputElement = document.getElementById("recaptchatoken")
    let formElement = document.getElementById("contactForm")
    let formButton = document.getElementById("formButton")
    let submited = false

    grecaptcha.ready(function() {
        grecaptcha.execute('6LfdHOIbAAAAAMemM-0xZfSBnKK_djbaLuGAvIi9').then(function(token) {
            tokenInputElement.value = token
        });
    });

    formButton.addEventListener("click", (event) => {
        if (submited) {
            event.preventDefault()
        } else {
            submited = true
            formButton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>'
        }
    })
</script>
</body>
</html>
