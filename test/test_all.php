<?php

require 'test_login.php';
require 'test_register.php';
require 'test_show.php';
require 'test_update.php';
require 'test_logout.php';
require 'utils.php';

/**********************************************/
/* replace $baseurl with your local directory */
/* or with the address on the server          */
/* https://saw21.dibris.unige.it/~S.....      */
/*                                            */
/* DO NOT UPLOAD TEST FILES ON SAW21!         */
/**********************************************/
$baseurl = "http://127.0.0.1:8000";

echo "[+] Testing Registration - Login - Show Profile\n";

echo "[*] Generating random user\n";

echo "---\n";
$email = generate_random_email();
$pass = generate_random_password();
$first_name = generate_random_name();
$last_name = generate_random_name();
echo "Email: $email\n";
echo "Pass: $pass\n";
echo "First name: $first_name\n";
echo "Last name: $last_name\n";
echo "---\n";

echo "\n[-] Calling registration.php\n";

register($email, $pass, $first_name, $last_name, $baseurl);

echo "\n[-] Calling login.php\n";
login($email, $pass, $baseurl);


echo "\n[-] Calling show_profile.php\n";

echo check_correct_user($email, $first_name, $last_name, show_logged_user($baseurl))
    ? "\n[*] Success :)\n"
    : "\n[*] Failed\n";

echo "------------------------\n";

echo "\n[+] Testing Update - Show Profile\n";

echo "\n[*] Generating new random user\n";
$first_name = generate_random_name();
$last_name = generate_random_name();

echo "---\n";
echo "Email: $email\n";
echo "First name: $first_name\n";
echo "Last name: $last_name\n";
echo "---\n";

echo "\n[-] Calling update_profile.php\n";
update($email, $first_name, $last_name, $baseurl);

echo "\n[-] Calling show_profile.php\n";

echo check_correct_user($email, $first_name, $last_name, show_logged_user($baseurl))
    ? "\n[*] Success :)\n"
    : "\n[*] Failed\n";


echo "---\n";
echo "\n[+] Testing Logout - Show Profile\n";
echo "\n[-] Calling logout.php\n";
logout($baseurl);

echo "\n[-] Calling show_profile.php (it must fail after logout)\n";
echo check_correct_user($email, $first_name, $last_name, show_logged_user($baseurl))
    ? "\n[*] Success\n"
    : "\n[*] Failed :)\n";

echo "------------------------\n";
