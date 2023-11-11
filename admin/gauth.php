<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

class PHPGangsta_GoogleAuthenticator
{
    protected $_codeLength = 6;
    public function createSecret($secretLength = 16)
    {
        $validChars = $this->_getBase32LookupTable();
        if ($secretLength < 16 || 128 < $secretLength) {
            throw new Exception("Bad secret length");
        }
        $secret = "";
        $rnd = false;
        if (function_exists("random_bytes")) {
            $rnd = random_bytes($secretLength);
        } else {
            if (function_exists("mcrypt_create_iv")) {
                $rnd = mcrypt_create_iv($secretLength, MCRYPT_DEV_URANDOM);
            } else {
                if (function_exists("openssl_random_pseudo_bytes")) {
                    $rnd = openssl_random_pseudo_bytes($secretLength, $cryptoStrong);
                    if (!$cryptoStrong) {
                        $rnd = false;
                    }
                }
            }
        }
        if ($rnd !== false) {
            for ($i = 0; $i < $secretLength; $i++) {
                $secret .= $validChars[ord($rnd[$i]) & 31];
            }
            return $secret;
        }
        throw new Exception("No source of secure random");
    }
    public function getCode($secret, $timeSlice = NULL)
    {
        if ($timeSlice === NULL) {
            $timeSlice = floor(time() / 30);
        }
        $secretkey = $this->_base32Decode($secret);
        $time = chr(0) . chr(0) . chr(0) . chr(0) . pack("N*", $timeSlice);
        $hm = hash_hmac("SHA1", $time, $secretkey, true);
        $offset = ord(substr($hm, -1)) & 15;
        $hashpart = substr($hm, $offset, 4);
        $value = unpack("N", $hashpart);
        $value = $value[1];
        $value = $value & 2147483647;
        $modulo = pow(10, $this->_codeLength);
        return str_pad($value % $modulo, $this->_codeLength, "0", STR_PAD_LEFT);
    }
    public function getQRCodeGoogleUrl($name, $secret, $title = NULL, $params = [])
    {
        $width = !empty($params["width"]) && 0 < (int) $params["width"] ? (int) $params["width"] : 200;
        $height = !empty($params["height"]) && 0 < (int) $params["height"] ? (int) $params["height"] : 200;
        $level = !empty($params["level"]) && array_search($params["level"], ["L", "M", "Q", "H"]) !== false ? $params["level"] : "M";
        $urlencoded = urlencode("otpauth://totp/" . $name . "?secret=" . $secret . "");
        if (isset($title)) {
            $urlencoded .= urlencode("&issuer=" . urlencode($title));
        }
        return "https://api.qrserver.com/v1/create-qr-code/?data=" . $urlencoded . "&size=" . $width . "x" . $height . "&ecc=" . $level;
    }
    public function verifyCode($secret, $code, $discrepancy = 1, $currentTimeSlice = NULL)
    {
        if ($currentTimeSlice === NULL) {
            $currentTimeSlice = floor(time() / 30);
        }
        if (strlen($code) != 6) {
            return false;
        }
        for ($i = -1 * $discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);
            if ($this->timingSafeEquals($calculatedCode, $code)) {
                return true;
            }
        }
        return false;
    }
    public function setCodeLength($length)
    {
        $this->_codeLength = $length;
        return $this;
    }
    protected function _base32Decode($secret)
    {
        if (empty($secret)) {
            return "";
        }
        $base32chars = $this->_getBase32LookupTable();
        $base32charsFlipped = array_flip($base32chars);
        $paddingCharCount = substr_count($secret, $base32chars[32]);
        $allowedValues = [6, 4, 3, 1, 0];
        if (!in_array($paddingCharCount, $allowedValues)) {
            return false;
        }
        for ($i = 0; $i < 4; $i++) {
            if ($paddingCharCount == $allowedValues[$i] && substr($secret, -1 * $allowedValues[$i]) != str_repeat($base32chars[32], $allowedValues[$i])) {
                return false;
            }
        }
        $secret = str_replace("=", "", $secret);
        $secret = str_split($secret);
        $binaryString = "";
        $i = 0;
        while ($i < count($secret)) {
            $x = "";
            if (!in_array($secret[$i], $base32chars)) {
                return false;
            }
            for ($j = 0; $j < 8; $j++) {
                $x .= str_pad(base_convert($base32charsFlipped[$secret[$i + $j]], 10, 2), 5, "0", STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for ($z = 0; $z < count($eightBits); $z++) {
                $binaryString .= ($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48 ? $y : "";
            }
            $i = $i + 8;
        }
        return $binaryString;
    }
    protected function _getBase32LookupTable()
    {
        return ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "2", "3", "4", "5", "6", "7", "="];
    }
    private function timingSafeEquals($safeString, $userString)
    {
        if (function_exists("hash_equals")) {
            return hash_equals($safeString, $userString);
        }
        $safeLen = strlen($safeString);
        $userLen = strlen($userString);
        if ($userLen != $safeLen) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < $userLen; $i++) {
            $result |= ord($safeString[$i]) ^ ord($userString[$i]);
        }
        return $result === 0;
    }
}

?>