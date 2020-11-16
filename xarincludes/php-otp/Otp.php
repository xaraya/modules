<?php
/* Xaraya
namespace TomasMrozek\PhpOtp;

use Exception;
Xaraya */

/**
 * PHP One-Time Passwords
 * (a PHP implementation of the one-time password system as specified in RFC 2289)
 *
 * @package   php-otp
 * @version   2.0.0
 * @author    Tomas Mrozek <mail@cascaval.com>
 * @copyright Copyright (C) 2009 Tomas Mrozek
 * @license   LGPLv3 (lgpl.txt)
 * @link      http://sourceforge.net/projects/php-otp/
 * @tutorial  readme.txt
 * @todo      modify isDecOtp() the same way as isWordsOtp()
 */
class Otp
{
    /**
     * @var array array of hash algorithms available
     */
    protected $availableAlgorithms = array();

    /**
     * @var string default hash algorithm to be used
     */
    protected $defaultAlgorithm = 'md5';

    /**
     * @var integer default number of sequences to be produced during generation of one-time passwords
     */
    protected $defaultSequenceCount = 100;

    /**
     * @var array dictionary to be used for creating of six-word representation of one-time passwords
     */
    private $dictionary = array();


    /**
     * Constructor method
     */
    public function __construct()
    {
        // Set available algorithms
        $this->availableAlgorithms = $this->getAvailableAlgorithms();

        // Set default dictionary
        $this->dictionary = $this->getDefaultDictionary();
    }


    /**
     * Intializes one-time password sequence with the OTP provided by user
     *
     * @param string  $userInput     a six-word or hexadecimal representation of an OTP
     * @param string  $seed          seed that the user used for generation of the OTP
     * @param integer $sequenceCount sequence number
     * @param string  $algorithm     hash algorithm
     * @return array an array containing the one-time password forms and other data:
     *                               'next_sequence' => next sequence number to be used for authentication,
     *                               'seed' => seed of the one-time password,
     *                               'algorithm' => algorithm used (it's just a backward reference),
     *                               'previous_hex_otp' => hexadecimal form of the previous sequence (= $sequenceCount) one-time password
     * @throws Exception
     */
    public function initializeOtp($userInput, $seed, $sequenceCount, $algorithm)
    {
        $output = null;
        $sequenceCount = intval($sequenceCount);

        // Check values
        if ($this->isValidSeed($seed) !== true) {
            throw new Exception('A valid seed is required', 101);
        }
        if ($this->isValidSequence($sequenceCount) !== true || $sequenceCount < 1) {
            throw new Exception('A sequence count > 0 is required', 102);
        }
        if ($this->isAvailableAlgorithm($algorithm) !== true) {
            throw new Exception('Chosen hash algorithm cannot be used', 103);
        }

        // Attempt to convert the OTP provided by user
        if ($this->isWordsOtp($userInput)) {
            $otp = $this->convertOtp($userInput, 'words', 'hex');
        } elseif ($this->isHexOtp($userInput)) {
            $otp = $userInput;
        } else {
            throw new Exception('Could not recognize provided one-time password', 104);
        }

        // Create a result array
        $output = array(
            'next_sequence' => ($sequenceCount - 1),
            'seed' => strtolower($seed),
            'algorithm' => $algorithm,
            'previous_hex_otp' => $this->reformatHexOtp($otp),
        );

        return $output;
    }


    /**
     * Reintializes one-time password sequence with the OTP provided by user
     * (just a wrapping method for initializeOtp() method)
     *
     * @param string  $userInput     a six-word or hexadecimal representation of an OTP
     * @param string  $newSeed       new seed that the user used for generation of the OTP
     * @param mixed   $oldSeeds      a list of old seeds that the user has used before
     * @param integer $sequenceCount sequence number
     * @param string  $algorithm     hash algorithm
     * @return array containing the one-time password forms and other data:
     *                               'next_sequence' => next sequence number to be used for authentication,
     *                               'seed' => seed of the one-time password,
     *                               'algorithm' => algorithm used (it's just a backward reference),
     *                               'previous_hex_otp' => hexadecimal form of the previous sequence (= $sequenceCount) one-time password
     * @throws Exception
     */
    public function reinitializeOtp($userInput, $newSeed, $oldSeeds, $sequenceCount, $algorithm)
    {
        if (is_string($oldSeeds)) {
            $oldSeeds = array($oldSeeds);
        }
        if (!is_array($oldSeeds)) {
            throw new Exception('An array or a string is expected as the "old seeds" argument', 201);
        }

        // Check that the new seed is valid and hasn't been used before
        if ($this->isValidSeed($newSeed) !== true) {
            throw new Exception('A valid seed is required', 202);
        }
        if (in_array($newSeed, $oldSeeds)) {
            throw new Exception('The supplied seed has been used before', 203);
        }

        $newSeed = strtolower($newSeed);

        return $this->initializeOtp($userInput, $newSeed, $sequenceCount, $algorithm);
    }


    /**
     * Authenticates with user input against current or previous
     * hexadeciaml representation of one-time password
     *
     * @param string  $userInput        a six-word or hexadecimal representation of an OTP
     * @param string  $masterHexOtp     a hexadecimal representation of an OTP against which user input will be compared
     * @param string  $masterHexOtpType 'previous' = comparison against an OTP of previous sequence, 'current' = comparison against an OTP of current sequence
     * @param integer $sequence         sequence number
     * @param string  $algorithm        hash algorithm
     * @return array an array containing the following data:
     *                                  'result => TRUE if authentication succeeded, otherwise FALSE,
     *                                  'otp' => FALSE if authentication failed, otherwise an array containing:
     *                                  'next_sequence' => sequence number for the next authentication,
     *                                  'algorithm' => an echo of the hash algorithm that was used,
     *                                  'previous_hex_otp' => hexadecimal value of the OTP which is supposed to be stored and used for comparison next time
     * @throws Exception
     */
    public function authAgainstHexOtp($userInput, $masterHexOtp, $masterHexOtpType, $sequence, $algorithm)
    {
        $output = array('result' => false, 'otp' => false);
        $sequence = intval($sequence);

        // Check values
        if ($userInput === false || !is_string($userInput) || empty($userInput)) {
            return $output;
        }
        if ($this->isHexOtp($masterHexOtp) !== true) {
            return $output;
        }
        if ($masterHexOtpType != 'previous' && $masterHexOtpType != 'current') {
            return $output;
        }
        if ($this->isValidSequence($sequence) !== true) {
            return $output;
        }
        if ($this->isAvailableAlgorithm($algorithm) !== true) {
            return $output;
        }

        // Make sure that one-time password used for comparison has standard form
        try {
            $masterHexOtp = $this->reformatHexOtp($masterHexOtp);
        } catch (Exception $e) {
            throw new Exception('A hexadecimal string expected for comparison.', 1601, $e);
        }

        // Test the user input as a six-word one-time password
        if ($output['result'] === false && $this->isWordsOtp($userInput) === true) {
            $otp = $this->convertOtp($userInput, 'words', 'bin');
            if ($masterHexOtpType == 'previous') {
                $otp = $this->binaryOtp($otp, $algorithm);
            }
            $hexOtp = $this->convertOtp($otp, 'bin', 'hex');

            // Alter the result array if the one-time password matches
            if (strcasecmp($hexOtp, $masterHexOtp) === 0) {
                $output['result'] = true;
                $output['otp'] = array(
                    'next_sequence' => ($sequence > 0 ? ($sequence - 1) : false),
                    'algorithm' => $algorithm,
                    'previous_hex_otp' => $this->convertOtp($userInput, 'words', 'hex'),
                );
            }
        }

        // Test the user input as a hexadecimal one-time password
        if ($output['result'] === false && $this->isHexOtp($userInput) === true) {
            $otp = $this->convertOtp($userInput, 'hex', 'bin');
            if ($masterHexOtpType == 'previous') {
                $otp = $this->binaryOtp($otp, $algorithm);
            }
            $hexOtp = $this->convertOtp($otp, 'bin', 'hex');

            // Alter the result array if the one-time password matches
            if (strcasecmp($hexOtp, $masterHexOtp) === 0) {
                $output['result'] = true;
                $output['otp'] = array(
                    'next_sequence' => ($sequence > 0 ? ($sequence - 1) : false),
                    'algorithm' => $algorithm,
                    'previous_hex_otp' => $this->reformatHexOtp($userInput),
                );
            }
        }

        return $output;
    }


    /**
     * Generates an one-time password
     *
     * @param string  $passPhrase secret pass-phrase provided by user
     * @param string  $seed       seed that will be concatenated with the secret pass-phrase
     * @param integer $sequence   sequence number
     * @param string  $algorithm  hash algorithm for the generated password - if false, default hash will be used
     * @return array an array containing the one-time password in various forms and other data:
     *                            'sequence' => sequence number,
     *                            'seed' => seed of the one-time password,
     *                            'algorithm' => algorithm used,
     *                            'hex_otp' => hexadecimal form of the one-time password,
     *                            'words_otp' => six-word form of the one-time password,
     *                            'previous_hex_otp' => hexadecimal form of the previous (= $sequence+1) one-time password,
     *                            'previous_words_otp' => six-word form of the previous (= $sequence+1) one-time password,
     *                            'next_hex_otp' => hexadecimal form of the next (= $sequence-1) one-time password,
     *                            'next_words_otp' => six-word form of the next (= $sequence-1) one-time password
     * @throws Exception
     */
    public function generateOtp($passPhrase, $seed, $sequence, $algorithm)
    {
        // Set default algorithm if not provided
        if ($algorithm === false || is_null($algorithm)) {
            $algorithm = $this->defaultAlgorithm;
        }

        // Check values
        if ($this->isValidPassPhrase($passPhrase) !== true) {
            throw new Exception('A valid passphrase is required', 401);
        }
        if ($this->isValidSeed($seed) !== true) {
            throw new Exception('A valid seed is required', 402);
        }
        if ($this->isValidSequence($sequence) !== true) {
            throw new Exception('A valid sequence number is required', 403);
        }
        if ($this->isAvailableAlgorithm($algorithm) !== true) {
            throw new Exception('A valid algorithm is required', 404);
        }

        // Generate one-time password
        $seed = strtolower($seed);
        $otp = $seed . $passPhrase;
        $nextOtp = false;
        for ($x = 0; $x <= $sequence; $x++) {
            if ($x != 0) {
                $nextOtp = $otp;
            }
            $otp = $this->binaryOtp($otp, $algorithm);
        }

        // Create result array
        $output = array(
            'sequence' => $sequence,
            'seed' => $seed,
            'algorithm' => $algorithm,
            'hex_otp' => $this->convertOtp($otp, 'bin', 'hex'),
            'words_otp' => $this->convertOtp($otp, 'bin', 'words'),
            'previous_hex_otp' => $this->convertOtp($this->binaryOtp($otp, $algorithm), 'bin', 'hex'),
            'previous_words_otp' => $this->convertOtp($this->binaryOtp($otp, $algorithm), 'bin', 'words'),
            'next_hex_otp' => ($nextOtp ? $this->convertOtp($nextOtp, 'bin', 'hex') : ''),
            'next_words_otp' => ($nextOtp ? $this->convertOtp($nextOtp, 'bin', 'words') : ''),
        );

        return $output;
    }


    /**
     * Generates a list of one-time passwords
     *
     * @param string  $passPhrase       secret pass-phrase provided by user
     * @param string  $seed             seed - if false, a random seed will be generated
     * @param mixed   $excludedSeeds    list of seeds that must not be used
     * @param integer $sequenceCount    number of one-time passwords to be generated - if false, a default number will be used
     * @param string  $algorithm        hash algorithm for the generated password - if false, default hash will be used
     * @return array an array of passwords, each represented as an array containing:
     *                                  'sequence' => sequence number of the one-time password,
     *                                  'seed' => seed of the one-time password,
     *                                  'algorithm' => algorithm used (it's just a backward reference),
     *                                  'hex_otp' => hexadecimal form of the one-time password,
     *                                  'words_otp' => six-word form of the one-time password
     * @throws Exception
     */
    public function generateOtpList($passPhrase, $seed = null, $excludedSeeds = array(), $sequenceCount = null, $algorithm = null)
    {
        $sequenceCount = intval($sequenceCount);
        if (is_string($excludedSeeds)) {
            $excludedSeeds = array($excludedSeeds);
        }
        if (!is_array($excludedSeeds)) {
            throw new Exception('An array or a string is expected as the "old seeds" argument', 501);
        }

        // Set default values if not provided
        if (is_null($seed)) {
            $seed = $this->generateSeed(false, false, $excludedSeeds);
        }
        if (is_null($sequenceCount)) {
            $sequenceCount = $this->defaultSequenceCount;
        }
        if (is_null($algorithm)) {
            $algorithm = $this->defaultAlgorithm;
        }

        // Check values
        if ($this->isValidPassPhrase($passPhrase) !== true) {
            throw new Exception('A valid passphrase is required', 502);
        }
        if ($this->isValidSeed($seed) !== true) {
            throw new Exception('A valid seed is required', 503);
        }
        if (in_array($seed, $excludedSeeds)) {
            throw new Exception('The supplied seed has been used before', 504);
        }
        if ($this->isValidSequence($sequenceCount) !== true || $sequenceCount < 1) {
            throw new Exception('A valid sequence number is required', 505);
        }
        if ($this->isAvailableAlgorithm($algorithm) !== true) {
            throw new Exception('A valid algorithm is required', 506);
        }

        // Generate list of one-time passwords
        $output = array();
        $seed = strtolower($seed);
        $otp = $seed . $passPhrase;
        for ($x = 0; $x <= $sequenceCount; $x++) {
            $otp = $this->binaryOtp($otp, $algorithm);
            $output[] = array(
                'sequence' => $x,
                'seed' => $seed,
                'algorithm' => $algorithm,
                'hex_otp' => $this->convertOtp($otp, 'bin', 'hex'),
                'words_otp' => $this->convertOtp($otp, 'bin', 'words'),
            );
        }

        // Sort the array so that the last computed one-time password comes first
        // as this would fit normal usage
        $output = array_reverse($output);

        return $output;
    }


    /**
     * Creates a hashed binary string for the given pass-phrase
     *
     * @param mixed   $passString string if it is the initial step or binary string for other steps
     * @param  string $algorithm  algorithm to be used - must be 'md4', 'md5' or 'sha1'
     * @return mixed binary string resulting from the hash function
     * @throws Exception
     */
    protected function binaryOtp($passString, $algorithm)
    {
        if (is_null($passString) || empty($passString)) {
            throw new Exception('A non-empty string is expected as an input', 601);
        }
        if ($this->isAvailableAlgorithm($algorithm) !== true) {
            throw new Exception('A valid hash algorithm is required', 602);
        }

        $algorithm = strtolower($algorithm);
        switch ($algorithm) {
            case 'md4':
                // Get binary value of the hash
                $hashString = $this->md4Hash($passString);

                // Fold the hash
                $hash = substr($hashString, 0, 8) ^ substr($hashString, 8, 8);
                break;

            case 'md5':
                // Get binary value of the hash
                $hashString = $this->md5Hash($passString);

                // Fold the hash
                $hash = substr($hashString, 0, 8) ^ substr($hashString, 8, 8);
                break;

            case 'sha1':
                // Get binary value of the hash
                $hashString = $this->sha1Hash($passString);

                // Fold the hash
                $temp = substr($hashString, 0, 4) ^ substr($hashString, 8, 4);
                $temp = $temp ^ substr($hashString, 16, 4);
                $temp .= substr($hashString, 4, 4) ^ substr($hashString, 12, 4);
                $hash = strrev(substr($temp, 0, 4)) . strrev(substr($temp, 4, 4));
                break;

            default:
                $hash = null;
                break;
        }

        return $hash;
    }


    /**
     * Converts an one-time password from one format to another
     *
     * @param mixed  $inputOtp an one-time password
     * @param string $fromType input format of the one-time password: 'bin', 'hex', 'word', 'bit', 'dec'
     * @param string $toType   output format of the one-time password: 'bin', 'hex', 'word', 'bit', 'dec'
     * @return mixed an one-time password of the output format
     * @throws Exception
     */
    public function convertOtp($inputOtp, $fromType, $toType)
    {
        if (!is_string($inputOtp) || empty($inputOtp)) {
            throw new Exception('A non-empty string is expected as an input', 701);
        }
        if (!is_string($fromType)) {
            throw new Exception('The input type of the OTP must be specified', 702);
        }
        if (!is_string($toType)) {
            throw new Exception('The output type of the OTP must be specified', 703);
        }

        // Convert internally $inputOtp to binary format
        switch ($fromType) {
            case 'bin':
            case 'binary':
                $otp = $inputOtp;
                break;

            case 'hex':
            case 'hexadecimal':
                if ($this->isHexOtp($inputOtp) !== true) {
                    throw new Exception('The supplied input OTP is not hexadecimal', 704);
                }

                $inputOtp = $this->reformatHexOtp($inputOtp);
                $otp = pack('H*', $inputOtp);
                break;

            case 'word':
            case 'words':
                if ($this->isWordsOtp($inputOtp) !== true) {
                    throw new Exception('The supplied input OTP is not a string of words', 705);
                }

                // Reformat the string and split it into an array
                try {
                    $words = $this->reformatWordsOtp($inputOtp);
                    $words = explode(' ', $words);
                } catch (Exception $e) {
                    throw new Exception('Failed to process the string of words', 706, $e);
                }

                // Create a string of bits
                $bitString = '';
                foreach ($words as $word) {
                    if (!in_array($word, $this->dictionary)) {
                        throw new Exception('One of the words has not been found in the dictionary', 707);
                    }

                    $key = array_search($word, $this->dictionary);
                    $dec = decbin($key);
                    $bitString .= str_pad($dec, 11, '0', STR_PAD_LEFT);
                }

                // Test the string of bits
                try {
                    if ($this->validateBitString($bitString) !== true) {
                        throw new Exception('Failed to process a word-based OTP', 708);
                    }
                } catch (Exception $e) {
                    throw new Exception('Failed to process a word-based OTP', 709, $e);
                }

                // Remove the last two checksum bits
                $bitString = substr($bitString, 0, 64);

                // Convert the string of bits to the binary one-time password
                try {
                    $otp = $this->convertOtp($bitString, 'bit', 'bin');
                } catch (Exception $e) {
                    throw new Exception('Failed to process a word-based OTP', 710, $e);
                }
                break;

            case 'bit':
            case 'bits':
                if ($this->isBitOtp($inputOtp) !== true) {
                    throw new Exception('The supplied input OTP is not a string of bits', 711);
                }

                // Create a hexadecimal string out of the string of bits
                $hexString = '';
                for ($x = 0; $x < 16; $x++) {
                    $partBit = substr($inputOtp, ($x * 4), 4);
                    $partHex = base_convert($partBit, 2, 16);
                    $hexString .= $partHex;
                }

                // Convert a hexadecimal string to the binary one-time password
                $otp = pack('H*', $hexString);
                break;

            case 'dec':
            case 'decimal':
                if ($this->isDecOtp($inputOtp) !== true) {
                    throw new Exception('The supplied input OTP is not decimal', 712);
                }
                $numbers = explode(' ', $inputOtp);

                $bitString = '';
                foreach ($numbers as $number) {
                    if (!array_key_exists($number, $this->dictionary)) {
                        throw new Exception('One of the numbers has not been found in the dictionary', 713);
                    }
                    $dec = decbin($number);
                    $bitString .= str_pad($dec, 11, '0', STR_PAD_LEFT);
                }

                // Test the string of bits
                try {
                    if ($this->validateBitString($bitString) !== true) {
                        throw new Exception('Failed to process a decimal OTP', 714);
                    }
                } catch (Exception $e) {
                    throw new Exception('Failed to process a decimal OTP', 715, $e);
                }

                // Remove the last two checksum bits
                $bitString = substr($bitString, 0, 64);

                // Convert the string of bits to the binary one-time password
                try {
                    $otp = $this->convertOtp($bitString, 'bit', 'bin');
                } catch (Exception $e) {
                    throw new Exception('Failed to process a decimal OTP', 716, $e);
                }
                break;

            default:
                throw new Exception('Unknown input OTP format', 717);
                break;
        }

        // Check the internal format
        if ($otp === false || empty($otp)) {
            throw new Exception('An unknowk error occurred during conversion', 718);
        }

        // Convert to the output format
        $outputOtp = '';
        switch ($toType) {
            case 'bin':
            case 'binary':
                $outputOtp = $otp;
                break;

            case 'hex':
            case 'hexadecimal':
                $outputOtp = bin2hex($otp);
                break;

            case 'word':
            case 'words':
                // Convert binary one-time password to a string of bits and add checksum
                $bitString = $this->convertOtp($otp, 'bin', 'bit');
                $bitString .= $this->calculateChecksum($bitString);

                // Split the string of bits and convert it to dictionary words
                for ($x = 0; $x < 6; $x++) {
                    $wordNumber = bindec(substr($bitString, ($x * 11), 11));
                    $outputOtp .= (empty($outputOtp) ? '' : ' ') . $this->dictionary[$wordNumber];
                }
                break;

            case 'bit':
            case 'bits':
                $hexString = bin2hex($otp);

                for ($x = 0; $x < strlen($hexString); $x++) {
                    $partHex = substr($hexString, $x, 1);
                    $partBin = base_convert($partHex, 16, 2);
                    $partBit = str_pad($partBin, 4, '0', STR_PAD_LEFT);
                    $outputOtp .= $partBit;
                }
                break;

            case 'dec':
            case 'decimal':
                // Convert binary one-time password to a string of bits and add checksum
                $bitString = $this->convertOtp($otp, 'bin', 'bit');
                $bitString .= $this->calculateChecksum($bitString);

                // Split the string of bits and convert it to numbers
                for ($x = 0; $x < 6; $x++) {
                    $wordNumber = bindec(substr($bitString, ($x * 11), 11));
                    $outputOtp .= (empty($outputOtp) ? '' : ' ') . $wordNumber;
                }
                break;

            default:
                throw new Exception('Unknown output OTP format', 719);
                break;
        }

        return $outputOtp;
    }


    /**
     * Test whether the string of bits is valid
     *
     * @param string $bitString a string of 66 bits (which means including the checksum bits)
     * @return boolean TRUE if the bit string is correct, otherwise FALSE
     * @throws Exception
     */
    protected function validateBitString($bitString)
    {
        if (!is_string($bitString)) {
            throw new Exception('A string was expected as an input', 801);
        }
        if (!preg_match('/^[01]{66}$/', $bitString)) {
            throw new Exception('The input string must be 66 characters long', 802);
        }

        // Split the checksum bits from the string
        $mainBits = substr($bitString, 0, 64);
        $lastBits = substr($bitString, 64, 2);

        // Get checksum
        $checksum = $this->calculateChecksum($mainBits);

        if (strcasecmp($checksum, $lastBits) === 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Creates a challenge string that conforms to RFC 2289
     *
     * @param string $seed      seed
     * @param mixed  $sequence  integer or numeric string representing sequence number
     * @param string $algorithm algorithm used - must be 'md4', 'md5' or 'sha1'
     * @return string challenge string including a space at the end
     * @throws Exception
     */
    public function createChallenge($seed, $sequence, $algorithm)
    {
        // Check seed
        if ($this->isValidSeed($seed) !== true) {
            throw new Exception('A valid seed was expected', 901);
        }

        // Check sequence number
        if ($this->isValidSequence($sequence) !== true) {
            throw new Exception('A valid sequence number was expected', 902);
        }

        // Check algorithm
        if ($this->isValidAlgorithm($algorithm) !== true) {
            throw new Exception('An available algorithm was expected', 903);
        }

        // Create a challenge string
        $challenge = 'otp-' . strtolower($algorithm) . ' ' . $sequence . ' ' . $seed . ' ';

        return $challenge;
    }


    /**
     * Parses a challenge string
     *
     * @param string $challenge challenge string
     * @return array containing 3 fields:
     *                          'algorithm' - 'md4', 'md5' or 'sha1'
     *                          'sequence'  - sequence number
     *                          'seed'      - challenge seed
     * @throws Exception
     */
    public function parseChallenge($challenge)
    {
        if (!is_string($challenge)) {
            throw new Exception('A string is expected as an OTP challenge', 1001);
        }

        // Test the challenge
        if ($this->isValidChallenge($challenge) !== true) {
            throw new Exception('The supplied string is not a valid OTP challenge', 1002);
        } else {
            $challenge = trim($challenge);
        }

        // Parse the challenge
        $challenge = preg_replace('/\s+/', ' ', $challenge);
        $challenge = trim($challenge);
        $parsedString = explode(' ', $challenge);
        $output = array(
            'algorithm' => substr($parsedString[0], 4),
            'sequence' => $parsedString[1],
            'seed' => $parsedString[2]
        );

        return $output;
    }


    /**
     * Tests whether the string might be a hexadecimal one-time password
     *
     * @param string $hexOtp a string which is supposed to be hexadecimal
     * @return boolean TRUE if the string is a hexadecimal one-time password, otherwise FALSE
     */
    public function isHexOtp($hexOtp)
    {
        if (!is_string($hexOtp)) {
            return false;
        }

        // Remove all spaces for easier check with a regular expression
        $hexOtp = preg_replace('/\s+/', '', $hexOtp);

        if (preg_match('/^[0-9a-fA-F]{16}$/', $hexOtp)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Tests whether the string is a valid six-word one-time password
     *
     * @param string $wordsOtp a string which is supposed to be a six words password
     * @return boolean TRUE if the string is a six-word password, otherwise FALSE
     */
    public function isWordsOtp($wordsOtp)
    {
        if (!is_string($wordsOtp) || !preg_match('/^\s*[a-zA-Z]{1,4}\s+[a-zA-Z]{1,4}\s+[a-zA-Z]{1,4}\s+[a-zA-Z]{1,4}\s+[a-zA-Z]{1,4}\s+[a-zA-Z]{1,4}\s*$/', $wordsOtp)) {
            return false;
        }

        // Reformat the string and split it into an array
        $words = $this->reformatWordsOtp($wordsOtp);
        $words = explode(' ', $words);

        // Create a string of bits
        $bitString = '';
        foreach ($words as $word) {
            if (!in_array($word, $this->dictionary)) {
                return false;
            }

            $key = array_search($word, $this->dictionary);
            $dec = decbin($key);
            $bitString .= str_pad($dec, 11, '0', STR_PAD_LEFT);
        }

        // Test the parity and return the result accordingly
        if ($this->validateBitString($bitString) !== true) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * Tests whether the string might be a string of bits of an one-time password
     *
     * @param string $bitOtp a string which is supposed to contain 64 bits
     * @return boolean TRUE if the string is a string of bits, otherwise FALSE
     */
    public function isBitOtp($bitOtp)
    {
        if (is_string($bitOtp) && preg_match('/^\s*[01]{64}\s*$/', $bitOtp)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Tests whether the string might be a six-number one-time password
     *
     * @param string $decOtp a string which is supposed to contain six numbers
     * @return boolean TRUE if the string is a string of six numbers, otherwise FALSE
     */
    public function isDecOtp($decOtp)
    {
        if (is_string($decOtp) && preg_match('/^\s*[0-9]{1,4}\s+[0-9]{1,4}\s+[0-9]{1,4}\s+[0-9]{1,4}\s+[0-9]{1,4}\s+[0-9]{1,4}\s*$/', $decOtp)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Tests whether the supplied algorithm can be used
     *
     * @param string $algorithm algorithm
     * @return boolean TRUE if the algorithm can be used, otherwise FALSE
     */
    public function isAvailableAlgorithm($algorithm)
    {
        if (!is_string($algorithm)) {
            return false;
        } else {
            $algorithm = strtolower($algorithm);
        }

        if (in_array($algorithm, $this->availableAlgorithms)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Tests whether the challenge string adheres to the specification
     *
     * @param string $challenge challenge string
     * @return boolean TRUE if the challenge adheres to the RFC 2289, otherwise FALSE
     */
    public function isValidChallenge($challenge)
    {
        if (is_string($challenge) && preg_match('/^\s*otp-(md4|md5|sha1)\s+[0-9]+\s+[0-9a-z]+\s+$/', $challenge)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Tests whether the supplied pass-phrase conforms to RFC 2289
     *
     * @param string $passPhrase passphrase supplied by the user
     * @return boolean TRUE if the pass-phrase is valid, otherwise FALSE
     */
    public function isValidPassPhrase($passPhrase)
    {
        if (is_string($passPhrase) && strlen($passPhrase) >= 10) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Tests whether the supplied seed conforms to RFC 2289
     *
     * @param string $seed seed
     * @return boolean TRUE if the seed is valid, otherwise FALSE
     */
    public function isValidSeed($seed)
    {
        if (is_string($seed) && preg_match('/^[0-9a-zA-Z]{1,16}$/', $seed)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Tests whether the supplied algorithm conforms to RFC 2289
     *
     * @param string $algorithm algorithm
     * @return boolean TRUE if the algorithm is valid, otherwise FALSE
     */
    public function isValidAlgorithm($algorithm)
    {
        if (strcasecmp($algorithm, 'md4') == 0) {
            return true;
        } elseif (strcasecmp($algorithm, 'md5') == 0) {
            return true;
        } elseif (strcasecmp($algorithm, 'sha1') == 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Test whether the supplied dictionary conforms to RFC 2289
     *
     * @param array $dictionary an array of 2048 words that contain 1 to 4 uppercase letters
     * @return boolean TRUE if the dictionary adheres to the specification, otherwise FALSE
     */
    public function isValidDictionary($dictionary)
    {
        if (!is_array($dictionary) || count($dictionary) != 2048) {
            return false;
        }

        // Load default RFC 2289 dictionary
        $standardDictionary = $this->getDefaultDictionary();

        // Check all the word in the dictionary
        foreach ($dictionary as $word) {
            // Every word must contain only 1 to 4 letters
            if (!preg_match('/^[a-zA-Z]{1,4}$/', $word)) {
                return false;
            }

            // The word must not be in the standard dictionary
            if (in_array(strtoupper($word), $standardDictionary)) {
                return false;
            }
        }

        return true;
    }


    /**
     * Tests whether the supplied sequence number could be considered valid
     *
     * @param integer $sequence sequence number
     * @return boolean TRUE if the sequence is valid, otherwise FALSE
     */
    public function isValidSequence($sequence)
    {
        if (is_numeric($sequence) && (preg_match('/^0$/', $sequence) || preg_match('/^[1-9][0-9]*$/', $sequence))) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Creates a seed
     *
     * @param integer $minLength     required minimal length of the seed - must be of 1-16 characters
     * @param integer $maxLength     required maximal length of the seed - must be of 1-16 characters
     * @param array   $excludedSeeds previously used seeds that should be avoided
     * @return string seed containing digits and lowercase a-z characters
     * @throws Exception
     */
    public function generateSeed($minLength = null, $maxLength = null, $excludedSeeds = array())
    {
        $seed = '';

        // If no min and max length was provided, set defaul values
        if (is_null($minLength)) {
            $minLength = 1;
        }
        if (is_null($maxLength)) {
            $maxLength = 16;
        }
        if (!is_array($excludedSeeds)) {
            $excludedSeeds = array();
        }

        // Non-numeric values cannot be accepted
        if (is_numeric($minLength)) {
            $minLength = intval($minLength);
        } else {
            throw new Exception('A numeric value is expected as the minimum length', 1101);
        }

        if (is_numeric($maxLength)) {
            $maxLength = intval($maxLength);
        } else {
            throw new Exception('A numeric value is expected as the maximum length', 1102);
        }

        // Check that length is correct
        if ($minLength < 1 || $minLength > 16) {
            throw new Exception('The minimum length cannot be less then 1 or more than 16', 1103);
        }
        if ($maxLength < 1 || $maxLength > 16) {
            throw new Exception('The maximum length cannot be less then 1 or more than 16', 1104);
        }
        if ($minLength > $maxLength) {
            throw new Exception('The minimum lenght cannot be more than the maximum length', 1105);
        }

        // Get final length
        $length = round(mt_rand($minLength, $maxLength));

        // Create a seed - keep on creating until you get a seed that is not
        // in the list of excluded seeds
        $x = 0;
        while ($seed == '' || in_array($seed, $excludedSeeds)) {
            $seedCharPool = $this->getSeedCharPool();

            $seed = '';
            for ($y = 0; $y < $length; $y++) {
                $poolKey = array_rand($seedCharPool, 1);
                $seed .= $seedCharPool[$poolKey];
            }

            $x++;

            // For short seeds it's quite possible that a not-excluded seed is not found in 1000 tries
            if ($x > 1000) {
                throw new Exception('Could not create a seed that would not be in the excluded seeds', 1106);
            }
        }

        return $seed;
    }


    /**
     * Creates a pool of characters for generation of seed
     *
     * @return array an array containing randomly ordered digits and lowercase a-z characters
     */
    protected function getSeedCharPool()
    {
        $pool = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        shuffle($pool);

        return $pool;
    }


    /**
     * Calculates a checksum of the bit string version of an one-time password
     *
     * @param string $bitString a string of bits
     * @return string two chcksum bits
     * @throws Exception
     */
    protected function calculateChecksum($bitString)
    {
        if (!is_string($bitString)) {
            throw new Exception('A string was expected as the only parameter.', 1201);
        }

        // Create an array out of the string of bits
        $bitArray = array();
        for ($x = 0; $x < strlen($bitString); $x++) {
            $bitArray[$x] = substr($bitString, $x, 1);
        }

        // Calculate checksum
        $checksum = 0;
        for ($x = 0; $x < 64; $x += 2) {
            $checksum += $bitArray[$x + 1] + (2 * $bitArray[$x]);
        }

        // Create a string of 2 checksum bits
        $checksumBits = fmod(floor($checksum / 2), 2) . '' . fmod($checksum, 2);

        return $checksumBits;
    }


    /**
     * Checks which hash algorithms are available
     *
     * @return array list of available algorithms
     */
    public function getAvailableAlgorithms()
    {
        $algorithms = array();

        // Check for MD4
        if (function_exists('hash') || function_exists('mhash')) {
            $algorithms[] = 'md4';
        }

        // Check for MD5
        if (function_exists('hash') || function_exists('mhash') || function_exists('md5')) {
            $algorithms[] = 'md5';
        }

        // Check for SHA1
        if (function_exists('hash') || function_exists('mhash') || function_exists('sha1')) {
            $algorithms[] = 'sha1';
        }

        return $algorithms;
    }


    /**
     * Calculates MD4 hash
     *
     * @param mixed $data data to be encoded
     * @return mixed md4 digest in raw binary format
     * @throws Exception
     */
    protected function md4Hash($data)
    {
        if (function_exists('hash')) {
            return hash('md4', $data, true);
        } elseif (function_exists('mhash')) {
            return mhash(MHASH_MD4, $data);
        } else {
            throw new Exception('The MD4 hash algorithm cannot be used', 1301);
        }
    }


    /**
     * Calculates MD5 hash
     *
     * @param mixed $data data to be encoded
     * @return mixed md5 digest in raw binary format
     * @throws Exception
     */
    protected function md5Hash($data)
    {
        if (function_exists('md5')) {
            return pack('H*', md5($data));
        } elseif (function_exists('hash')) {
            return hash('md5', $data, true);
        } elseif (function_exists('mhash')) {
            return mhash(MHASH_MD5, $data);
        } else {
            throw new Exception('The MD5 hash algorithm cannot be used', 1401);
        }
    }


    /**
     * Calculates SHA1 hash
     *
     * @param mixed $data data to be encoded
     * @return mixed sha1 digest in raw binary format
     * @throws Exception
     */
    protected function sha1Hash($data)
    {
        if (function_exists('sha1')) {
            return pack('H*', sha1($data));
        } elseif (function_exists('hash')) {
            return hash('sha1', $data, true);
        } elseif (function_exists('mhash')) {
            return mhash(MHASH_SHA1, $data);
        } else {
            throw new Exception('The SHA1 hash algorithm cannot be used', 1501);
        }
    }


    /**
     * Reformats a hexadecimal one-time password string
     *
     * @param string $hexOtp     hexadecimal one-time password
     * @param string $letterCase case of letters - should be 'uppercase' or 'lowercase' (default)
     * @param string $format     format - should be '1' (default), '2', '4' or '8'
     * @return string formatted string
     * @throws Exception
     */
    public function reformatHexOtp($hexOtp, $letterCase = null, $format = null)
    {
        if ($this->isHexOtp($hexOtp) !== true) {
            throw new Exception('A hexadecimal string expected as an input', 1601);
        }

        $output = '';
        $hexOtp = preg_replace('/\s+/', '', $hexOtp);

        // Set required case of letters
        switch ($letterCase) {
            case 'uppercase':
            case 'upper':
                $hexOtp = strtoupper($hexOtp);
                break;

            case 'lowercase':
            case 'lower':
            default:
                $hexOtp = strtolower($hexOtp);
                break;
        }

        // Set required form
        switch ($format) {
            case '8':
                for ($x = 0; $x < 16; $x += 2) {
                    $output .= (empty($output) ? '' : ' ') . substr($hexOtp, $x, 2);
                }
                break;

            case '4':
                for ($x = 0; $x < 16; $x += 4) {
                    $output .= (empty($output) ? '' : ' ') . substr($hexOtp, $x, 4);
                }
                break;

            case '2':
                $output = substr($hexOtp, 0, 8) . ' ' . substr($hexOtp, 8, 8);
                break;

            case '1':
            default:
                $output = $hexOtp;
                break;
        }

        return $output;
    }


    /**
     * Reformats a six-word one-time password string
     *
     * @param string $wordsOtp six-word one-time password
     * @return string trimmed with all letters converted to uppercase
     * @throws Exception
     */
    public function reformatWordsOtp($wordsOtp)
    {
        if (!is_string($wordsOtp) || !preg_match('/^\s*[a-zA-Z]{1,4}\s+[a-zA-Z]{1,4}\s+[a-zA-Z]{1,4}\s+[a-zA-Z]{1,4}\s+[a-zA-Z]{1,4}\s+[a-zA-Z]{1,4}\s*$/', $wordsOtp)) {
            throw new Exception('A string of words expected as an input', 1701);
        }

        $wordsOtp = preg_replace('/\s+/', ' ', $wordsOtp);
        $wordsOtp = trim($wordsOtp);
        $wordsOtp = strtoupper($wordsOtp);

        return $wordsOtp;
    }


    /**
     * Set dictionary to an alternative one
     *
     * @param array $dictionary an array of dictionary words
     * @return boolean returns TRUE if the dictionary was set
     * @throws Exception
     */
    public function setAlternativeDictionary($dictionary)
    {
        if ($this->isValidDictionary($dictionary) === false) {
            throw new Exception('An array of words meeting RFC requirements is expected', 1801);
        }

        $this->dictionary = $dictionary;

        return true;
    }


    /**
     * Returns the default dictionary as specified in RFC 2289 and RFC 1760
     *
     * @return array dictionary words
     */
    public function getDefaultDictionary()
    {
        $dictionary = array(
            'A', 'ABE', 'ACE', 'ACT', 'AD', 'ADA', 'ADD',
            'AGO', 'AID', 'AIM', 'AIR', 'ALL', 'ALP', 'AM', 'AMY',
            'AN', 'ANA', 'AND', 'ANN', 'ANT', 'ANY', 'APE', 'APS',
            'APT', 'ARC', 'ARE', 'ARK', 'ARM', 'ART', 'AS', 'ASH',
            'ASK', 'AT', 'ATE', 'AUG', 'AUK', 'AVE', 'AWE', 'AWK',
            'AWL', 'AWN', 'AX', 'AYE', 'BAD', 'BAG', 'BAH', 'BAM',
            'BAN', 'BAR', 'BAT', 'BAY', 'BE', 'BED', 'BEE', 'BEG',
            'BEN', 'BET', 'BEY', 'BIB', 'BID', 'BIG', 'BIN', 'BIT',
            'BOB', 'BOG', 'BON', 'BOO', 'BOP', 'BOW', 'BOY', 'BUB',
            'BUD', 'BUG', 'BUM', 'BUN', 'BUS', 'BUT', 'BUY', 'BY',
            'BYE', 'CAB', 'CAL', 'CAM', 'CAN', 'CAP', 'CAR', 'CAT',
            'CAW', 'COD', 'COG', 'COL', 'CON', 'COO', 'COP', 'COT',
            'COW', 'COY', 'CRY', 'CUB', 'CUE', 'CUP', 'CUR', 'CUT',
            'DAB', 'DAD', 'DAM', 'DAN', 'DAR', 'DAY', 'DEE', 'DEL',
            'DEN', 'DES', 'DEW', 'DID', 'DIE', 'DIG', 'DIN', 'DIP',
            'DO', 'DOE', 'DOG', 'DON', 'DOT', 'DOW', 'DRY', 'DUB',
            'DUD', 'DUE', 'DUG', 'DUN', 'EAR', 'EAT', 'ED', 'EEL',
            'EGG', 'EGO', 'ELI', 'ELK', 'ELM', 'ELY', 'EM', 'END',
            'EST', 'ETC', 'EVA', 'EVE', 'EWE', 'EYE', 'FAD', 'FAN',
            'FAR', 'FAT', 'FAY', 'FED', 'FEE', 'FEW', 'FIB', 'FIG',
            'FIN', 'FIR', 'FIT', 'FLO', 'FLY', 'FOE', 'FOG', 'FOR',
            'FRY', 'FUM', 'FUN', 'FUR', 'GAB', 'GAD', 'GAG', 'GAL',
            'GAM', 'GAP', 'GAS', 'GAY', 'GEE', 'GEL', 'GEM', 'GET',
            'GIG', 'GIL', 'GIN', 'GO', 'GOT', 'GUM', 'GUN', 'GUS',
            'GUT', 'GUY', 'GYM', 'GYP', 'HA', 'HAD', 'HAL', 'HAM',
            'HAN', 'HAP', 'HAS', 'HAT', 'HAW', 'HAY', 'HE', 'HEM',
            'HEN', 'HER', 'HEW', 'HEY', 'HI', 'HID', 'HIM', 'HIP',
            'HIS', 'HIT', 'HO', 'HOB', 'HOC', 'HOE', 'HOG', 'HOP',
            'HOT', 'HOW', 'HUB', 'HUE', 'HUG', 'HUH', 'HUM', 'HUT',
            'I', 'ICY', 'IDA', 'IF', 'IKE', 'ILL', 'INK', 'INN',
            'IO', 'ION', 'IQ', 'IRA', 'IRE', 'IRK', 'IS', 'IT',
            'ITS', 'IVY', 'JAB', 'JAG', 'JAM', 'JAN', 'JAR', 'JAW',
            'JAY', 'JET', 'JIG', 'JIM', 'JO', 'JOB', 'JOE', 'JOG',
            'JOT', 'JOY', 'JUG', 'JUT', 'KAY', 'KEG', 'KEN', 'KEY',
            'KID', 'KIM', 'KIN', 'KIT', 'LA', 'LAB', 'LAC', 'LAD',
            'LAG', 'LAM', 'LAP', 'LAW', 'LAY', 'LEA', 'LED', 'LEE',
            'LEG', 'LEN', 'LEO', 'LET', 'LEW', 'LID', 'LIE', 'LIN',
            'LIP', 'LIT', 'LO', 'LOB', 'LOG', 'LOP', 'LOS', 'LOT',
            'LOU', 'LOW', 'LOY', 'LUG', 'LYE', 'MA', 'MAC', 'MAD',
            'MAE', 'MAN', 'MAO', 'MAP', 'MAT', 'MAW', 'MAY', 'ME',
            'MEG', 'MEL', 'MEN', 'MET', 'MEW', 'MID', 'MIN', 'MIT',
            'MOB', 'MOD', 'MOE', 'MOO', 'MOP', 'MOS', 'MOT', 'MOW',
            'MUD', 'MUG', 'MUM', 'MY', 'NAB', 'NAG', 'NAN', 'NAP',
            'NAT', 'NAY', 'NE', 'NED', 'NEE', 'NET', 'NEW', 'NIB',
            'NIL', 'NIP', 'NIT', 'NO', 'NOB', 'NOD', 'NON', 'NOR',
            'NOT', 'NOV', 'NOW', 'NU', 'NUN', 'NUT', 'O', 'OAF',
            'OAK', 'OAR', 'OAT', 'ODD', 'ODE', 'OF', 'OFF', 'OFT',
            'OH', 'OIL', 'OK', 'OLD', 'ON', 'ONE', 'OR', 'ORB',
            'ORE', 'ORR', 'OS', 'OTT', 'OUR', 'OUT', 'OVA', 'OW',
            'OWE', 'OWL', 'OWN', 'OX', 'PA', 'PAD', 'PAL', 'PAM',
            'PAN', 'PAP', 'PAR', 'PAT', 'PAW', 'PAY', 'PEA', 'PEG',
            'PEN', 'PEP', 'PER', 'PET', 'PEW', 'PHI', 'PI', 'PIE',
            'PIN', 'PIT', 'PLY', 'PO', 'POD', 'POE', 'POP', 'POT',
            'POW', 'PRO', 'PRY', 'PUB', 'PUG', 'PUN', 'PUP', 'PUT',
            'QUO', 'RAG', 'RAM', 'RAN', 'RAP', 'RAT', 'RAW', 'RAY',
            'REB', 'RED', 'REP', 'RET', 'RIB', 'RID', 'RIG', 'RIM',
            'RIO', 'RIP', 'ROB', 'ROD', 'ROE', 'RON', 'ROT', 'ROW',
            'ROY', 'RUB', 'RUE', 'RUG', 'RUM', 'RUN', 'RYE', 'SAC',
            'SAD', 'SAG', 'SAL', 'SAM', 'SAN', 'SAP', 'SAT', 'SAW',
            'SAY', 'SEA', 'SEC', 'SEE', 'SEN', 'SET', 'SEW', 'SHE',
            'SHY', 'SIN', 'SIP', 'SIR', 'SIS', 'SIT', 'SKI', 'SKY',
            'SLY', 'SO', 'SOB', 'SOD', 'SON', 'SOP', 'SOW', 'SOY',
            'SPA', 'SPY', 'SUB', 'SUD', 'SUE', 'SUM', 'SUN', 'SUP',
            'TAB', 'TAD', 'TAG', 'TAN', 'TAP', 'TAR', 'TEA', 'TED',
            'TEE', 'TEN', 'THE', 'THY', 'TIC', 'TIE', 'TIM', 'TIN',
            'TIP', 'TO', 'TOE', 'TOG', 'TOM', 'TON', 'TOO', 'TOP',
            'TOW', 'TOY', 'TRY', 'TUB', 'TUG', 'TUM', 'TUN', 'TWO',
            'UN', 'UP', 'US', 'USE', 'VAN', 'VAT', 'VET', 'VIE',
            'WAD', 'WAG', 'WAR', 'WAS', 'WAY', 'WE', 'WEB', 'WED',
            'WEE', 'WET', 'WHO', 'WHY', 'WIN', 'WIT', 'WOK', 'WON',
            'WOO', 'WOW', 'WRY', 'WU', 'YAM', 'YAP', 'YAW', 'YE',
            'YEA', 'YES', 'YET', 'YOU', 'ABED', 'ABEL', 'ABET', 'ABLE',
            'ABUT', 'ACHE', 'ACID', 'ACME', 'ACRE', 'ACTA', 'ACTS', 'ADAM',
            'ADDS', 'ADEN', 'AFAR', 'AFRO', 'AGEE', 'AHEM', 'AHOY', 'AIDA',
            'AIDE', 'AIDS', 'AIRY', 'AJAR', 'AKIN', 'ALAN', 'ALEC', 'ALGA',
            'ALIA', 'ALLY', 'ALMA', 'ALOE', 'ALSO', 'ALTO', 'ALUM', 'ALVA',
            'AMEN', 'AMES', 'AMID', 'AMMO', 'AMOK', 'AMOS', 'AMRA', 'ANDY',
            'ANEW', 'ANNA', 'ANNE', 'ANTE', 'ANTI', 'AQUA', 'ARAB', 'ARCH',
            'AREA', 'ARGO', 'ARID', 'ARMY', 'ARTS', 'ARTY', 'ASIA', 'ASKS',
            'ATOM', 'AUNT', 'AURA', 'AUTO', 'AVER', 'AVID', 'AVIS', 'AVON',
            'AVOW', 'AWAY', 'AWRY', 'BABE', 'BABY', 'BACH', 'BACK', 'BADE',
            'BAIL', 'BAIT', 'BAKE', 'BALD', 'BALE', 'BALI', 'BALK', 'BALL',
            'BALM', 'BAND', 'BANE', 'BANG', 'BANK', 'BARB', 'BARD', 'BARE',
            'BARK', 'BARN', 'BARR', 'BASE', 'BASH', 'BASK', 'BASS', 'BATE',
            'BATH', 'BAWD', 'BAWL', 'BEAD', 'BEAK', 'BEAM', 'BEAN', 'BEAR',
            'BEAT', 'BEAU', 'BECK', 'BEEF', 'BEEN', 'BEER', 'BEET', 'BELA',
            'BELL', 'BELT', 'BEND', 'BENT', 'BERG', 'BERN', 'BERT', 'BESS',
            'BEST', 'BETA', 'BETH', 'BHOY', 'BIAS', 'BIDE', 'BIEN', 'BILE',
            'BILK', 'BILL', 'BIND', 'BING', 'BIRD', 'BITE', 'BITS', 'BLAB',
            'BLAT', 'BLED', 'BLEW', 'BLOB', 'BLOC', 'BLOT', 'BLOW', 'BLUE',
            'BLUM', 'BLUR', 'BOAR', 'BOAT', 'BOCA', 'BOCK', 'BODE', 'BODY',
            'BOGY', 'BOHR', 'BOIL', 'BOLD', 'BOLO', 'BOLT', 'BOMB', 'BONA',
            'BOND', 'BONE', 'BONG', 'BONN', 'BONY', 'BOOK', 'BOOM', 'BOON',
            'BOOT', 'BORE', 'BORG', 'BORN', 'BOSE', 'BOSS', 'BOTH', 'BOUT',
            'BOWL', 'BOYD', 'BRAD', 'BRAE', 'BRAG', 'BRAN', 'BRAY', 'BRED',
            'BREW', 'BRIG', 'BRIM', 'BROW', 'BUCK', 'BUDD', 'BUFF', 'BULB',
            'BULK', 'BULL', 'BUNK', 'BUNT', 'BUOY', 'BURG', 'BURL', 'BURN',
            'BURR', 'BURT', 'BURY', 'BUSH', 'BUSS', 'BUST', 'BUSY', 'BYTE',
            'CADY', 'CAFE', 'CAGE', 'CAIN', 'CAKE', 'CALF', 'CALL', 'CALM',
            'CAME', 'CANE', 'CANT', 'CARD', 'CARE', 'CARL', 'CARR', 'CART',
            'CASE', 'CASH', 'CASK', 'CAST', 'CAVE', 'CEIL', 'CELL', 'CENT',
            'CERN', 'CHAD', 'CHAR', 'CHAT', 'CHAW', 'CHEF', 'CHEN', 'CHEW',
            'CHIC', 'CHIN', 'CHOU', 'CHOW', 'CHUB', 'CHUG', 'CHUM', 'CITE',
            'CITY', 'CLAD', 'CLAM', 'CLAN', 'CLAW', 'CLAY', 'CLOD', 'CLOG',
            'CLOT', 'CLUB', 'CLUE', 'COAL', 'COAT', 'COCA', 'COCK', 'COCO',
            'CODA', 'CODE', 'CODY', 'COED', 'COIL', 'COIN', 'COKE', 'COLA',
            'COLD', 'COLT', 'COMA', 'COMB', 'COME', 'COOK', 'COOL', 'COON',
            'COOT', 'CORD', 'CORE', 'CORK', 'CORN', 'COST', 'COVE', 'COWL',
            'CRAB', 'CRAG', 'CRAM', 'CRAY', 'CREW', 'CRIB', 'CROW', 'CRUD',
            'CUBA', 'CUBE', 'CUFF', 'CULL', 'CULT', 'CUNY', 'CURB', 'CURD',
            'CURE', 'CURL', 'CURT', 'CUTS', 'DADE', 'DALE', 'DAME', 'DANA',
            'DANE', 'DANG', 'DANK', 'DARE', 'DARK', 'DARN', 'DART', 'DASH',
            'DATA', 'DATE', 'DAVE', 'DAVY', 'DAWN', 'DAYS', 'DEAD', 'DEAF',
            'DEAL', 'DEAN', 'DEAR', 'DEBT', 'DECK', 'DEED', 'DEEM', 'DEER',
            'DEFT', 'DEFY', 'DELL', 'DENT', 'DENY', 'DESK', 'DIAL', 'DICE',
            'DIED', 'DIET', 'DIME', 'DINE', 'DING', 'DINT', 'DIRE', 'DIRT',
            'DISC', 'DISH', 'DISK', 'DIVE', 'DOCK', 'DOES', 'DOLE', 'DOLL',
            'DOLT', 'DOME', 'DONE', 'DOOM', 'DOOR', 'DORA', 'DOSE', 'DOTE',
            'DOUG', 'DOUR', 'DOVE', 'DOWN', 'DRAB', 'DRAG', 'DRAM', 'DRAW',
            'DREW', 'DRUB', 'DRUG', 'DRUM', 'DUAL', 'DUCK', 'DUCT', 'DUEL',
            'DUET', 'DUKE', 'DULL', 'DUMB', 'DUNE', 'DUNK', 'DUSK', 'DUST',
            'DUTY', 'EACH', 'EARL', 'EARN', 'EASE', 'EAST', 'EASY', 'EBEN',
            'ECHO', 'EDDY', 'EDEN', 'EDGE', 'EDGY', 'EDIT', 'EDNA', 'EGAN',
            'ELAN', 'ELBA', 'ELLA', 'ELSE', 'EMIL', 'EMIT', 'EMMA', 'ENDS',
            'ERIC', 'EROS', 'EVEN', 'EVER', 'EVIL', 'EYED', 'FACE', 'FACT',
            'FADE', 'FAIL', 'FAIN', 'FAIR', 'FAKE', 'FALL', 'FAME', 'FANG',
            'FARM', 'FAST', 'FATE', 'FAWN', 'FEAR', 'FEAT', 'FEED', 'FEEL',
            'FEET', 'FELL', 'FELT', 'FEND', 'FERN', 'FEST', 'FEUD', 'FIEF',
            'FIGS', 'FILE', 'FILL', 'FILM', 'FIND', 'FINE', 'FINK', 'FIRE',
            'FIRM', 'FISH', 'FISK', 'FIST', 'FITS', 'FIVE', 'FLAG', 'FLAK',
            'FLAM', 'FLAT', 'FLAW', 'FLEA', 'FLED', 'FLEW', 'FLIT', 'FLOC',
            'FLOG', 'FLOW', 'FLUB', 'FLUE', 'FOAL', 'FOAM', 'FOGY', 'FOIL',
            'FOLD', 'FOLK', 'FOND', 'FONT', 'FOOD', 'FOOL', 'FOOT', 'FORD',
            'FORE', 'FORK', 'FORM', 'FORT', 'FOSS', 'FOUL', 'FOUR', 'FOWL',
            'FRAU', 'FRAY', 'FRED', 'FREE', 'FRET', 'FREY', 'FROG', 'FROM',
            'FUEL', 'FULL', 'FUME', 'FUND', 'FUNK', 'FURY', 'FUSE', 'FUSS',
            'GAFF', 'GAGE', 'GAIL', 'GAIN', 'GAIT', 'GALA', 'GALE', 'GALL',
            'GALT', 'GAME', 'GANG', 'GARB', 'GARY', 'GASH', 'GATE', 'GAUL',
            'GAUR', 'GAVE', 'GAWK', 'GEAR', 'GELD', 'GENE', 'GENT', 'GERM',
            'GETS', 'GIBE', 'GIFT', 'GILD', 'GILL', 'GILT', 'GINA', 'GIRD',
            'GIRL', 'GIST', 'GIVE', 'GLAD', 'GLEE', 'GLEN', 'GLIB', 'GLOB',
            'GLOM', 'GLOW', 'GLUE', 'GLUM', 'GLUT', 'GOAD', 'GOAL', 'GOAT',
            'GOER', 'GOES', 'GOLD', 'GOLF', 'GONE', 'GONG', 'GOOD', 'GOOF',
            'GORE', 'GORY', 'GOSH', 'GOUT', 'GOWN', 'GRAB', 'GRAD', 'GRAY',
            'GREG', 'GREW', 'GREY', 'GRID', 'GRIM', 'GRIN', 'GRIT', 'GROW',
            'GRUB', 'GULF', 'GULL', 'GUNK', 'GURU', 'GUSH', 'GUST', 'GWEN',
            'GWYN', 'HAAG', 'HAAS', 'HACK', 'HAIL', 'HAIR', 'HALE', 'HALF',
            'HALL', 'HALO', 'HALT', 'HAND', 'HANG', 'HANK', 'HANS', 'HARD',
            'HARK', 'HARM', 'HART', 'HASH', 'HAST', 'HATE', 'HATH', 'HAUL',
            'HAVE', 'HAWK', 'HAYS', 'HEAD', 'HEAL', 'HEAR', 'HEAT', 'HEBE',
            'HECK', 'HEED', 'HEEL', 'HEFT', 'HELD', 'HELL', 'HELM', 'HERB',
            'HERD', 'HERE', 'HERO', 'HERS', 'HESS', 'HEWN', 'HICK', 'HIDE',
            'HIGH', 'HIKE', 'HILL', 'HILT', 'HIND', 'HINT', 'HIRE', 'HISS',
            'HIVE', 'HOBO', 'HOCK', 'HOFF', 'HOLD', 'HOLE', 'HOLM', 'HOLT',
            'HOME', 'HONE', 'HONK', 'HOOD', 'HOOF', 'HOOK', 'HOOT', 'HORN',
            'HOSE', 'HOST', 'HOUR', 'HOVE', 'HOWE', 'HOWL', 'HOYT', 'HUCK',
            'HUED', 'HUFF', 'HUGE', 'HUGH', 'HUGO', 'HULK', 'HULL', 'HUNK',
            'HUNT', 'HURD', 'HURL', 'HURT', 'HUSH', 'HYDE', 'HYMN', 'IBIS',
            'ICON', 'IDEA', 'IDLE', 'IFFY', 'INCA', 'INCH', 'INTO', 'IONS',
            'IOTA', 'IOWA', 'IRIS', 'IRMA', 'IRON', 'ISLE', 'ITCH', 'ITEM',
            'IVAN', 'JACK', 'JADE', 'JAIL', 'JAKE', 'JANE', 'JAVA', 'JEAN',
            'JEFF', 'JERK', 'JESS', 'JEST', 'JIBE', 'JILL', 'JILT', 'JIVE',
            'JOAN', 'JOBS', 'JOCK', 'JOEL', 'JOEY', 'JOHN', 'JOIN', 'JOKE',
            'JOLT', 'JOVE', 'JUDD', 'JUDE', 'JUDO', 'JUDY', 'JUJU', 'JUKE',
            'JULY', 'JUNE', 'JUNK', 'JUNO', 'JURY', 'JUST', 'JUTE', 'KAHN',
            'KALE', 'KANE', 'KANT', 'KARL', 'KATE', 'KEEL', 'KEEN', 'KENO',
            'KENT', 'KERN', 'KERR', 'KEYS', 'KICK', 'KILL', 'KIND', 'KING',
            'KIRK', 'KISS', 'KITE', 'KLAN', 'KNEE', 'KNEW', 'KNIT', 'KNOB',
            'KNOT', 'KNOW', 'KOCH', 'KONG', 'KUDO', 'KURD', 'KURT', 'KYLE',
            'LACE', 'LACK', 'LACY', 'LADY', 'LAID', 'LAIN', 'LAIR', 'LAKE',
            'LAMB', 'LAME', 'LAND', 'LANE', 'LANG', 'LARD', 'LARK', 'LASS',
            'LAST', 'LATE', 'LAUD', 'LAVA', 'LAWN', 'LAWS', 'LAYS', 'LEAD',
            'LEAF', 'LEAK', 'LEAN', 'LEAR', 'LEEK', 'LEER', 'LEFT', 'LEND',
            'LENS', 'LENT', 'LEON', 'LESK', 'LESS', 'LEST', 'LETS', 'LIAR',
            'LICE', 'LICK', 'LIED', 'LIEN', 'LIES', 'LIEU', 'LIFE', 'LIFT',
            'LIKE', 'LILA', 'LILT', 'LILY', 'LIMA', 'LIMB', 'LIME', 'LIND',
            'LINE', 'LINK', 'LINT', 'LION', 'LISA', 'LIST', 'LIVE', 'LOAD',
            'LOAF', 'LOAM', 'LOAN', 'LOCK', 'LOFT', 'LOGE', 'LOIS', 'LOLA',
            'LONE', 'LONG', 'LOOK', 'LOON', 'LOOT', 'LORD', 'LORE', 'LOSE',
            'LOSS', 'LOST', 'LOUD', 'LOVE', 'LOWE', 'LUCK', 'LUCY', 'LUGE',
            'LUKE', 'LULU', 'LUND', 'LUNG', 'LURA', 'LURE', 'LURK', 'LUSH',
            'LUST', 'LYLE', 'LYNN', 'LYON', 'LYRA', 'MACE', 'MADE', 'MAGI',
            'MAID', 'MAIL', 'MAIN', 'MAKE', 'MALE', 'MALI', 'MALL', 'MALT',
            'MANA', 'MANN', 'MANY', 'MARC', 'MARE', 'MARK', 'MARS', 'MART',
            'MARY', 'MASH', 'MASK', 'MASS', 'MAST', 'MATE', 'MATH', 'MAUL',
            'MAYO', 'MEAD', 'MEAL', 'MEAN', 'MEAT', 'MEEK', 'MEET', 'MELD',
            'MELT', 'MEMO', 'MEND', 'MENU', 'MERT', 'MESH', 'MESS', 'MICE',
            'MIKE', 'MILD', 'MILE', 'MILK', 'MILL', 'MILT', 'MIMI', 'MIND',
            'MINE', 'MINI', 'MINK', 'MINT', 'MIRE', 'MISS', 'MIST', 'MITE',
            'MITT', 'MOAN', 'MOAT', 'MOCK', 'MODE', 'MOLD', 'MOLE', 'MOLL',
            'MOLT', 'MONA', 'MONK', 'MONT', 'MOOD', 'MOON', 'MOOR', 'MOOT',
            'MORE', 'MORN', 'MORT', 'MOSS', 'MOST', 'MOTH', 'MOVE', 'MUCH',
            'MUCK', 'MUDD', 'MUFF', 'MULE', 'MULL', 'MURK', 'MUSH', 'MUST',
            'MUTE', 'MUTT', 'MYRA', 'MYTH', 'NAGY', 'NAIL', 'NAIR', 'NAME',
            'NARY', 'NASH', 'NAVE', 'NAVY', 'NEAL', 'NEAR', 'NEAT', 'NECK',
            'NEED', 'NEIL', 'NELL', 'NEON', 'NERO', 'NESS', 'NEST', 'NEWS',
            'NEWT', 'NIBS', 'NICE', 'NICK', 'NILE', 'NINA', 'NINE', 'NOAH',
            'NODE', 'NOEL', 'NOLL', 'NONE', 'NOOK', 'NOON', 'NORM', 'NOSE',
            'NOTE', 'NOUN', 'NOVA', 'NUDE', 'NULL', 'NUMB', 'OATH', 'OBEY',
            'OBOE', 'ODIN', 'OHIO', 'OILY', 'OINT', 'OKAY', 'OLAF', 'OLDY',
            'OLGA', 'OLIN', 'OMAN', 'OMEN', 'OMIT', 'ONCE', 'ONES', 'ONLY',
            'ONTO', 'ONUS', 'ORAL', 'ORGY', 'OSLO', 'OTIS', 'OTTO', 'OUCH',
            'OUST', 'OUTS', 'OVAL', 'OVEN', 'OVER', 'OWLY', 'OWNS', 'QUAD',
            'QUIT', 'QUOD', 'RACE', 'RACK', 'RACY', 'RAFT', 'RAGE', 'RAID',
            'RAIL', 'RAIN', 'RAKE', 'RANK', 'RANT', 'RARE', 'RASH', 'RATE',
            'RAVE', 'RAYS', 'READ', 'REAL', 'REAM', 'REAR', 'RECK', 'REED',
            'REEF', 'REEK', 'REEL', 'REID', 'REIN', 'RENA', 'REND', 'RENT',
            'REST', 'RICE', 'RICH', 'RICK', 'RIDE', 'RIFT', 'RILL', 'RIME',
            'RING', 'RINK', 'RISE', 'RISK', 'RITE', 'ROAD', 'ROAM', 'ROAR',
            'ROBE', 'ROCK', 'RODE', 'ROIL', 'ROLL', 'ROME', 'ROOD', 'ROOF',
            'ROOK', 'ROOM', 'ROOT', 'ROSA', 'ROSE', 'ROSS', 'ROSY', 'ROTH',
            'ROUT', 'ROVE', 'ROWE', 'ROWS', 'RUBE', 'RUBY', 'RUDE', 'RUDY',
            'RUIN', 'RULE', 'RUNG', 'RUNS', 'RUNT', 'RUSE', 'RUSH', 'RUSK',
            'RUSS', 'RUST', 'RUTH', 'SACK', 'SAFE', 'SAGE', 'SAID', 'SAIL',
            'SALE', 'SALK', 'SALT', 'SAME', 'SAND', 'SANE', 'SANG', 'SANK',
            'SARA', 'SAUL', 'SAVE', 'SAYS', 'SCAN', 'SCAR', 'SCAT', 'SCOT',
            'SEAL', 'SEAM', 'SEAR', 'SEAT', 'SEED', 'SEEK', 'SEEM', 'SEEN',
            'SEES', 'SELF', 'SELL', 'SEND', 'SENT', 'SETS', 'SEWN', 'SHAG',
            'SHAM', 'SHAW', 'SHAY', 'SHED', 'SHIM', 'SHIN', 'SHOD', 'SHOE',
            'SHOT', 'SHOW', 'SHUN', 'SHUT', 'SICK', 'SIDE', 'SIFT', 'SIGH',
            'SIGN', 'SILK', 'SILL', 'SILO', 'SILT', 'SINE', 'SING', 'SINK',
            'SIRE', 'SITE', 'SITS', 'SITU', 'SKAT', 'SKEW', 'SKID', 'SKIM',
            'SKIN', 'SKIT', 'SLAB', 'SLAM', 'SLAT', 'SLAY', 'SLED', 'SLEW',
            'SLID', 'SLIM', 'SLIT', 'SLOB', 'SLOG', 'SLOT', 'SLOW', 'SLUG',
            'SLUM', 'SLUR', 'SMOG', 'SMUG', 'SNAG', 'SNOB', 'SNOW', 'SNUB',
            'SNUG', 'SOAK', 'SOAR', 'SOCK', 'SODA', 'SOFA', 'SOFT', 'SOIL',
            'SOLD', 'SOME', 'SONG', 'SOON', 'SOOT', 'SORE', 'SORT', 'SOUL',
            'SOUR', 'SOWN', 'STAB', 'STAG', 'STAN', 'STAR', 'STAY', 'STEM',
            'STEW', 'STIR', 'STOW', 'STUB', 'STUN', 'SUCH', 'SUDS', 'SUIT',
            'SULK', 'SUMS', 'SUNG', 'SUNK', 'SURE', 'SURF', 'SWAB', 'SWAG',
            'SWAM', 'SWAN', 'SWAT', 'SWAY', 'SWIM', 'SWUM', 'TACK', 'TACT',
            'TAIL', 'TAKE', 'TALE', 'TALK', 'TALL', 'TANK', 'TASK', 'TATE',
            'TAUT', 'TEAL', 'TEAM', 'TEAR', 'TECH', 'TEEM', 'TEEN', 'TEET',
            'TELL', 'TEND', 'TENT', 'TERM', 'TERN', 'TESS', 'TEST', 'THAN',
            'THAT', 'THEE', 'THEM', 'THEN', 'THEY', 'THIN', 'THIS', 'THUD',
            'THUG', 'TICK', 'TIDE', 'TIDY', 'TIED', 'TIER', 'TILE', 'TILL',
            'TILT', 'TIME', 'TINA', 'TINE', 'TINT', 'TINY', 'TIRE', 'TOAD',
            'TOGO', 'TOIL', 'TOLD', 'TOLL', 'TONE', 'TONG', 'TONY', 'TOOK',
            'TOOL', 'TOOT', 'TORE', 'TORN', 'TOTE', 'TOUR', 'TOUT', 'TOWN',
            'TRAG', 'TRAM', 'TRAY', 'TREE', 'TREK', 'TRIG', 'TRIM', 'TRIO',
            'TROD', 'TROT', 'TROY', 'TRUE', 'TUBA', 'TUBE', 'TUCK', 'TUFT',
            'TUNA', 'TUNE', 'TUNG', 'TURF', 'TURN', 'TUSK', 'TWIG', 'TWIN',
            'TWIT', 'ULAN', 'UNIT', 'URGE', 'USED', 'USER', 'USES', 'UTAH',
            'VAIL', 'VAIN', 'VALE', 'VARY', 'VASE', 'VAST', 'VEAL', 'VEDA',
            'VEIL', 'VEIN', 'VEND', 'VENT', 'VERB', 'VERY', 'VETO', 'VICE',
            'VIEW', 'VINE', 'VISE', 'VOID', 'VOLT', 'VOTE', 'WACK', 'WADE',
            'WAGE', 'WAIL', 'WAIT', 'WAKE', 'WALE', 'WALK', 'WALL', 'WALT',
            'WAND', 'WANE', 'WANG', 'WANT', 'WARD', 'WARM', 'WARN', 'WART',
            'WASH', 'WAST', 'WATS', 'WATT', 'WAVE', 'WAVY', 'WAYS', 'WEAK',
            'WEAL', 'WEAN', 'WEAR', 'WEED', 'WEEK', 'WEIR', 'WELD', 'WELL',
            'WELT', 'WENT', 'WERE', 'WERT', 'WEST', 'WHAM', 'WHAT', 'WHEE',
            'WHEN', 'WHET', 'WHOA', 'WHOM', 'WICK', 'WIFE', 'WILD', 'WILL',
            'WIND', 'WINE', 'WING', 'WINK', 'WINO', 'WIRE', 'WISE', 'WISH',
            'WITH', 'WOLF', 'WONT', 'WOOD', 'WOOL', 'WORD', 'WORE', 'WORK',
            'WORM', 'WORN', 'WOVE', 'WRIT', 'WYNN', 'YALE', 'YANG', 'YANK',
            'YARD', 'YARN', 'YAWL', 'YAWN', 'YEAH', 'YEAR', 'YELL', 'YOGA',
            'YOKE');

        return $dictionary;
    }
}
