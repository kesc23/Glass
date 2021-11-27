<?php
namespace Glass;

/**
 * GlassJWT Class
 * 
 * It is intended to generate a JSON Web Token for use in Glass Updater Authorization to Github.
 * Using it makes possible to update Glass directly from Github.
 * 
 * This code is inspired in the Firebase\PHP-JWT @see https://github.com/firebase/php-jwt
 * 
 * @package Glass
 * @subpackage GlassUpdater
 * 
 * @since 0.7.0
 */
class GlassJWT
{
    public  static $token;       // The JWT Token to authorize Glass Updater on Github.
    private static $header;      // The token header.
    private static $payload;     // The token payload.
    private static $privateKey;  // The Glass Updater private key for this specific version.

    /**
     * This function sets the token header as:
     * 
     * alg: the crypto algorith for this token. in this case RS256
     * typ: the token type. A JSON Web Token.
     * 
     * @since 0.7.0
     * 
     * @return string  The token header Base64 Encoded.
     */
    private function setHeader() : string
    {
        self::$header = json_encode( array( 'alg' => 'RS256','typ' => 'JWT' ) );
        return $this->base64Encode( self::$header );
    }

    /**
     * This function sets the token payload as:
     * 
     * iat: the time it was issued. From now as past 60 seconds, so it will work asap.
     * exp: the expiration time. 10 minutes from now.
     * iss: The issuer. In this case Glass Updater App on Github.
     * 
     * @since 0.7.0
     * 
     * @return string  The token payload Base64 Encoded.
     */
    private function setPayload() : string
    {
        self::$payload = \json_encode( (object) array(
                'iat' => \strtotime('now') - 60,
                'exp' => \strtotime('now') + (10 * 60),
                'iss' => GLASS_UPDATER_ID,
            )
        );

        return $this->base64Encode( self::$payload );
    }

    /**
     * This function just stores the private key inside this attribute.
     * @since 0.7.0
     */
    private function setPrivateKey()
    {
        self::$privateKey = \file_get_contents( GLASS_PKPEM );
    }

    /**
     * This function safely encodes a string to Base64 format.
     * It also turns it compatible with JWT standards.
     * 
     * @since 0.7.0
     *
     * @param  string $input   The string to be Base64 encoded.
     * @return string          The string encoded string.
     */
    public function base64Encode( string $input ) : string
    {
        return \str_replace('=', '', \strtr(\base64_encode( $input ), '+/', '-_'));
    }

    public function __construct()
    {
        $token[]   = $this->setHeader();
        $token[]   = $this->setPayload();

        $this->setPrivateKey();

        $signing   = implode( '.', $token );
        $signature = '';

        \openssl_sign( $signing, $signature, self::$privateKey, 'SHA256' );

        $token[]     = $this->base64Encode( $signature );

        self::$token = \implode( '.', $token );
    }
}