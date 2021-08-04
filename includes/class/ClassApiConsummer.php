<?php

/**
 * Class ApiConsummer
 * 
 * Helping to make cURL requests to API's.
 * 
 * @since 0.6.2
 */
Class ApiConsummer
{
    public $url;
    public $query;
    public $headers = array();
    public $request_url;

    public function get_request( bool $ssl = true, bool $return = false, bool $json_decode = false )
    {
        return $this->request( $return, $ssl, $json_decode );
    }

    public function post_request( bool $ssl = true, bool $return = false, $json_decode = false, $post_fields = [] )
    {
        return $this->request( $return, $ssl, $json_decode, 'POST', $post_fields );
    }

    public function patch_request( bool $ssl = true, bool $return = false, $json_decode = false, $post_fields = [] )
    {
        return $this->request( $return, $ssl, $json_decode, 'PATCH', $post_fields );
    }

    public function get_api_url() : string
    {
        return $this->request_url;
    }

    public function request( bool $return = false, bool $ssl = true, bool $json_decode = false, string $method = 'GET', $post_fields = [] )
    {
        $ch = curl_init( $this->get_api_url() );

        $request_options = array(
            CURLOPT_HTTPHEADER     => $this->headers,
            CURLOPT_RETURNTRANSFER => $return,
            CURLOPT_SSL_VERIFYPEER => $ssl,
        );

        switch( $method ){

            case 'POST':
                $post_field = json_encode( (object) $post_fields );
    
                $request_options[ CURLOPT_POST ]       = true;
                $request_options[ CURLOPT_POSTFIELDS ] = $post_field;
            break;

            case 'PATCH':
                $post_field = json_encode( (object) $post_fields );
    
                $request_options[ CURLOPT_CUSTOMREQUEST ] = 'PATCH';
                $request_options[ CURLOPT_POSTFIELDS ]    = $post_field;
            break;

            case 'GET':
                $request_options[ CURLOPT_HTTPGET ] = true;
            break;

            default:
            break;
        }

        curl_setopt_array( $ch, $request_options );

        $response = curl_exec( $ch );

        curl_close( $ch );

        if( true == $return ){

            if( $json_decode == true ):
                return json_decode( $response, true );
            else:
                return $response;
            endif;

        }
    }

    public function get_url() : string
    {
        return $this->url;
    }

    public function set_url( string $url )
    {
        $this->url = $url;
    }

    public function set_headers( array $headers )
    {
        $this->headers = $headers;
    }

    public function get_query() : string
    {
        return $this->query;
    }

    public function set_query( string $query = '' )
    {
        $this->query = $query;
        $this->set_api_url();
    }

    public function set_api_url()
    {        
        $this->request_url = $this->get_url() . $this->get_query();
    }

    public function __construct( string $url, array $headers = array() )
    {
        $this->set_url( $url );
        $this->set_headers( $headers );
    }
}