<?php
$encryption_key  = hash('sha256', 'kljuczasifriranje');
$cipher = 'AES-256-CBC';
$iv_length = openssl_cipher_iv_length($cipher);
$options = 0; 
$encryption_iv = random_bytes($iv_length); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_FILES['file']['tmp_name'])) {

        $fileType = strtolower(pathinfo(basename($_FILES['file']['name']),PATHINFO_EXTENSION));
        if($fileType != "jpg" && $fileType != "png" && $fileType != "pdf" ) {
            echo "Dozvoljeni formati su: JPG, PNG i PDF!! ";
            exit();
        }

        //enkripcija

        $encrypted_file = 'kriptiranje/' . $_FILES['file']['name'] . '.enc';
        $temp_file = $_FILES['file']['tmp_name'];

        $input_file = fopen($temp_file, 'r');         //read
        $output_file = fopen($encrypted_file, 'w');   //write

        $plain = fread($input_file, filesize($temp_file));
        $encrypt = base64_encode(openssl_encrypt($plain, $cipher, $encryption_key, $options, $encryption_iv)); //OpenSSL u tekst
        fwrite($output_file, $encryption_iv);
        fwrite($output_file, $encrypt);

        fclose($input_file);
        fclose($output_file);

        echo 'Enkripcija uspješna<br>';
    } 

    //dekripcija

    $encrypted_files_dir = 'kriptiranje/';

    $encrypted_files = glob($encrypted_files_dir . '*.enc');

    foreach ($encrypted_files as $encrypted_file) {

        $decrypted_file = str_replace('.enc', '', $encrypted_file);

        $input_file = fopen($encrypted_file, 'r');
        $output_file = fopen($decrypted_file, 'w');

        $encryption_iv = fread($input_file, $iv_length);
        $encrypt = fread($input_file, filesize($encrypted_file));
        $decrypt = openssl_decrypt(base64_decode($encrypt), $cipher, $encryption_key, $options, $encryption_iv);
        fwrite($output_file, $decrypt);


        fclose($input_file);
        fclose($output_file);

        echo '<br><a href="'.$decrypted_file.'">Download '.basename($decrypted_file).'</a>';
    }
}
?>
