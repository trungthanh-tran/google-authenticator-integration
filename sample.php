<?php
require_once  __DIR__."/vendor/autoload.php";
require_once('GoogleAuthenticator.php');
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Response\QrCodeResponse;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\NotoSans;

$issuer = "VGP";
$username = "useraccount";
$qr_path = __DIR__.'/qrcode.png';
$google_auth = new GoogleAuthenticator();

function store_png($username, $url, $saved_path) {
    $result = Builder::create()
    ->writer(new PngWriter())
    ->writerOptions([])
    ->data($url)
    ->encoding(new Encoding('UTF-8'))
    ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
    ->size(300)
    ->margin(10)
    ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
    ->labelFont(new NotoSans(20))
    ->labelAlignment(new LabelAlignmentCenter())
    ->validateResult(false)
    ->labelText('VGP GA for user: '.$username)
    ->build();
    $result->saveToFile($saved_path);
}


# Generate keys. Save username and associated key in database and
$key = $google_auth->createSecret(32);

echo nl2br("Secret key for user: " . $username . " is ". $key . ".\n");

# Generate QR text
$qr_url = $google_auth->getQRCodeUrl($username, $key, $issuer);

# Save to image
store_png($username, $qr_url, $qr_path);

# Scan qr in google authenticator

# Verify code

while(true) {
    $code = readline('Enter a code  (Ctrl^C to exit): ');
    if($google_auth->verifyCode($key, $code)) {
        echo "Verified\n";
    } else {
        echo("Not verfied!!\n");
    }
}




?>
