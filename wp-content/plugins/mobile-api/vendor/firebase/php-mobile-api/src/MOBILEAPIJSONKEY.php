<?php

namespace Firebase\MOBILEAPIAUT;

use DomainException;
use UnexpectedValueException;

class MOBILEAPIJSONKEY
{
    /**
     * Parse a set of MOBILEAPIJSONKEY keys
     *
     * @param array $mobilejson The JSON Web Key Set as an associative array
     *
     * @return array An associative array that represents the set of keys
     *
     * @throws InvalidArgumentException     Provided MOBILEAPIJSONKEY Set is empty
     * @throws UnexpectedValueException     Provided MOBILEAPIJSONKEY Set was invalid
     * @throws DomainException              OpenSSL failure
     *
     * @uses parseKey
     */
    public static function parseKeySet(array $mobilejsons)
    {
        $keys = array();

        if (!isset($mobilejsons['keys'])) {
            throw new UnexpectedValueException('"keys" member must exist in the MOBILEAPIJSONKEY Set');
        }
        if (empty($mobilejsons['keys'])) {
            throw new InvalidArgumentException('MOBILEAPIJSONKEY Set did not contain any keys');
        }

        foreach ($mobilejsons['keys'] as $k => $v) {
            $kid = isset($v['kid']) ? $v['kid'] : $k;
            if ($key = self::parseKey($v)) {
                $keys[$kid] = $key;
            }
        }

        if (0 === \count($keys)) {
            throw new UnexpectedValueException('No supported algorithms found in MOBILEAPIJSONKEY Set');
        }

        return $keys;
    }

    /**
     * Parse a MOBILEAPIJSONKEY key
     *
     * @param array $mobilejson An individual MOBILEAPIJSONKEY
     *
     * @return resource|array An associative array that represents the key
     *
     * @throws InvalidArgumentException     Provided MOBILEAPIJSONKEY is empty
     * @throws UnexpectedValueException     Provided MOBILEAPIJSONKEY was invalid
     * @throws DomainException              OpenSSL failure
     *
     * @uses createPemFromModulusAndExponent
     */
    private static function parseKey(array $mobilejson)
    {
        if (empty($mobilejson)) {
            throw new InvalidArgumentException('MOBILEAPIJSONKEY must not be empty');
        }
        if (!isset($mobilejson['kty'])) {
            throw new UnexpectedValueException('MOBILEAPIJSONKEY must contain a "kty" parameter');
        }

        switch ($mobilejson['kty']) {
            case 'RSA':
                if (\array_key_exists('d', $mobilejson)) {
                    throw new UnexpectedValueException('RSA private keys are not supported');
                }
                if (!isset($mobilejson['n']) || !isset($mobilejson['e'])) {
                    throw new UnexpectedValueException('RSA keys must contain values for both "n" and "e"');
                }

                $pem = self::createPemFromModulusAndExponent($mobilejson['n'], $mobilejson['e']);
                $publicKey = \openssl_pkey_get_public($pem);
                if (false === $publicKey) {
                    throw new DomainException(
                        'OpenSSL error: ' . \openssl_error_string()
                    );
                }
                return $publicKey;
            default:
                // Currently only RSA is supported
                break;
        }
    }

    /**
     * Create a public key represented in PEM format from RSA modulus and exponent information
     *
     * @param string $n The RSA modulus encoded in Base64
     * @param string $e The RSA exponent encoded in Base64
     *
     * @return string The RSA public key represented in PEM format
     *
     * @uses encodeLength
     */
    private static function createPemFromModulusAndExponent($n, $e)
    {
        $modulus = MOBILEAPIAUT::urlsafeB64Decode($n);
        $publicExponent = MOBILEAPIAUT::urlsafeB64Decode($e);

        $components = array(
            'modulus' => \pack('Ca*a*', 2, self::encodeLength(\strlen($modulus)), $modulus),
            'publicExponent' => \pack('Ca*a*', 2, self::encodeLength(\strlen($publicExponent)), $publicExponent)
        );

        $rsaPublicKey = \pack(
            'Ca*a*a*',
            48,
            self::encodeLength(\strlen($components['modulus']) + \strlen($components['publicExponent'])),
            $components['modulus'],
            $components['publicExponent']
        );

        // sequence(oid(1.2.840.113549.1.1.1), null)) = rsaEncryption.
        $rsaOID = \pack('H*', '300d06092a864886f70d0101010500'); // hex version of MA0GCSqGSIb3DQEBAQUA
        $rsaPublicKey = \chr(0) . $rsaPublicKey;
        $rsaPublicKey = \chr(3) . self::encodeLength(\strlen($rsaPublicKey)) . $rsaPublicKey;

        $rsaPublicKey = \pack(
            'Ca*a*',
            48,
            self::encodeLength(\strlen($rsaOID . $rsaPublicKey)),
            $rsaOID . $rsaPublicKey
        );

        $rsaPublicKey = "-----BEGIN PUBLIC KEY-----\r\n" .
            \chunk_split(\base64_encode($rsaPublicKey), 64) .
            '-----END PUBLIC KEY-----';

        return $rsaPublicKey;
    }

    /**
     * DER-encode the length
     *
     * DER supports lengths up to (2**8)**127, however, we'll only support lengths up to (2**8)**4.  See
     * {@link http://itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf#p=13 X.690 paragraph 8.1.3} for more information.
     *
     * @param int $length
     * @return string
     */
    private static function encodeLength($length)
    {
        if ($length <= 0x7F) {
            return \chr($length);
        }

        $temp = \ltrim(\pack('N', $length), \chr(0));

        return \pack('Ca*', 0x80 | \strlen($temp), $temp);
    }
}
