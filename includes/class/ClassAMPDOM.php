<?php
/**
 * @package Glass
 * @subpackage AMPDOM
 * @author Kesc23 as MartinFields
 * @copyright 2021 Kesc23
 */
namespace MartinFields\Glass;
use \DOMDocument, \DOMNode, \DOMNodeList;

Class AMPDOM
{
    /**
     * The DOM manipulated by the class.
     *
     * @var DOMDocument $document.
     */
    private $document;

    /**
     * The HTML escaped version of the document.
     * 
     * @see htmlspecialchars_decode()
     * @see DOM::renderOuterHTML()
     *
     * @var string $outerHTML.
     */
    public $outerHTML;

    public $html;

    public $head;

    public $childNodes;

    private $amp;

    /**
     * The AMP JS library.
     *
     * @var DOMNode
     */
    private $ampJS;

    /**
     * The async attribute.
     *
     * @var DOMAttribute
     */
    private $async;

    private $ampcustom;

    /**
     * The amp-bolerplate attribute.
     *
     * @var DOMAttribute
     */
    private $ampBoilerplate;

    private $ampBoilerplateStyle;

    private $ampBoilerplateNoScript;

    private $ampViewport;

    private $ampCharset;

    private $ampCanonical;

    private $ampScript;

    private $ampscriptnode;

    public function __construct( $version = '', $charset = '', $doctype = null )
    {
        $this->document = new DOMDocument( $version, $charset );
    
        if( ! is_null( $doctype ) ):
            $this->setDoctype( $doctype );
        endif;
        
        $this->generateAMP();

        $this->outerHTML();
    }

    public function outerHTML()
    {
        $this->childNodes = $this->document->childNodes;
        $this->outerHTML = $this->document->saveHTML();
    }

    public function renderOuterHTML()
    {
        return $this->outerHTML; 
    }

    public function appendChild( $element )
    {
        $this->document->appendChild( $element );

        if ( $this->document->getElementsByTagName( 'html' ) )
        {
            $this->html = $this->document->getElementsByTagName( 'html' )->item( 0 );
        }

        if ( $this->document->getElementsByTagName( 'head' ) )
        {
            $this->head = $this->document->getElementsByTagName( 'head' )->item( 0 );
        }

        $this->outerHTML();
    }

    public function saveHTML( ?DOMNode $node = null )
    {
        $document = $this->document->saveHTML( $node );
        $this->outerHTML();
        return $document;
    }

    public function generateAMP()
    {
        /**
         * In This section, is defined some of the core elements to get ready to use AMP.
         * - @see AMPDOM::async                      The async property: you can useit if needed in other scripts.
         * - @see AMPDOM::ampcustom                  The amp-custom property. you can useit if needed in style tags.
         * - @see AMPDOM::amp                        The amp property. is used in the html tag.
         * - @see AMPDOM::ampBoilerPlate             The amp-boilerplate property: to use in AMP styles.
         * - @see AMPDOM::ampBoilerplateStyle        The Amp style tag to put in the head tag.
         * - @see AMPDOM::ampBoilerplateNoscript     The Amp No script to add in the head tag.
         * - @see AMPDOM::ampViewport                The Amp <meta> viewport to add in the head tag.
         * - @see AMPDOM::ampCharset                 The Amp <meta> charset to add in the head tag.
         * - @see AMPDOM::ampCanonical               The <link> rel="canonical" to add in the head tag.
         */
        $this->async = $this->document->createAttribute( 'async' );

        $this->ampcustom = $this->document->createAttribute( 'amp-custom' );

        $this->amp   = $this->document->createAttribute( 'amp' );

        $this->ampBoilerplate = $this->document->createAttribute( "amp-boilerplate" );

        $this->ampJS = $this->script( "https://cdn.ampproject.org/v0.js" );
        $this->ampJS->appendChild( $this->document->createAttribute( 'async' ) );

        $this->ampBoilerplateNoScript = $this->noScript();
        $style = $this->document->createElement( 
            'style',
            'body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}'
        );
        $style->appendChild( $this->document->createAttribute( "amp-boilerplate" ) );
        $this->ampBoilerplateNoScript->appendChild( $style );

        $this->ampBoilerplateStyle = $this->style(
            'body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}'
        );
        $this->ampBoilerplateStyle->appendChild( $this->document->createAttribute( "amp-boilerplate" ) );
        $this->ampViewport = $this->meta( 'viewport', 'width=device-width');

        $this->ampCharset  = $this->meta();
        $this->ampCharset->setAttribute( 'charset', empty( $charset )? 'utf-8':$charset );

        $this->ampCanonical = $this->link( 'canonical' );

        $this->ampScript = $this->document->createElement( 'script' );
        $this->ampScript->appendChild( $this->async );
        $this->ampScript->setAttribute( 'custom-element', 'amp-script' );
        $this->ampScript->setAttribute( 'src', "https://cdn.ampproject.org/v0/amp-script-0.1.js" );
        
        $this->ampscriptnode = $this->document->createElement( 'amp-script', '' );
    }

    public function ampBoilerplateStyle()
    {
        return $this->ampBoilerplateStyle;
    }

    public function ampBoilerplateNoScript()
    {
        return $this->ampBoilerplateNoScript;
    }

    public function ampJS()
    {
        return $this->ampJS;
    }

    public function ampViewport()
    {
        return $this->ampViewport;
    }
    
    public function ampCharset()
    {
        return $this->ampCharset;
    }

    public function ampCanonical()
    {
        return $this->ampCanonical;
    }

    public function setAmpCanonicalHREF( string $href )
    {
        $this->ampCanonical->setAttribute( 'href', $href );
    }

    public function ampBoilerplate()
    {
        return $this->ampBoilerplate;
    }

    public function async()
    {
        return $this->async;
    }

    public function loadHTML( string $html, bool $clean = false )
    {
        if( true === $clean ):
            @ $this->document->loadHTML( $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
        else:
            @ $this->document->loadHTML( $html );
        endif;

        if ( $this->document->getElementsByTagName( 'html' ) )
        {
            $this->html = $this->document->getElementsByTagName( 'html' )->item( 0 );
        }

        if ( $this->document->getElementsByTagName( 'head' ) )
        {
            $this->head = $this->document->getElementsByTagName( 'head' )->item( 0 );
        }

        $this->generateAMP();

        $this->outerHTML();
    }

    public function AMPHTML()
    {
        $tag = $this->document->createElement( 'html' );
        $tag->appendChild( $this->amp );
        $this->outerHTML();
        return $tag;
    }

    public function turnHTMLIntoAMP()
    {
        $domforBody = new DOM;
        $thisBody = $this->document->getElementsByTagName( 'body' )->item( 0 );
        $domforBody->appendChild( $domforBody->importNode( $thisBody, true ) );

        $this->html->appendChild( $this->amp );

        $headChilds = array();

        foreach( $this->head->childNodes as $node )
        {
            $headChilds[] = $node;
        }

        $htmlNode = $this->document->getElementsByTagName( 'html' )->item( 0 );

        $this->document->getElementsByTagName( 'html' )->item( 0 )->nodeValue = '';

        $index = 000;
        $toBody = array();
        $headtags = array();
        $beforeAMP = array();
        $afterAMP = array();
        $hashes = array();
        $CustomStyleContent = '';
        $canon = 0;

        global $nestlevel; $nestlevel = 0;

        foreach( $headChilds as $child )
        {
            $this->prepareChildNode( $child, $toBody, $headtags, $beforeAMP, $afterAMP, $hashes, $CustomStyleContent, $index, $headChilds, $canon, $this->document );
        }

        /**
         * HEAD SECTION
         */
        $head = $this->head();

        foreach( $beforeAMP as $before )
        {
            $head->appendChild( $before );
        }

        /**
         * The real body part starts here
         */
        $bodyTags    = $domforBody->document->getElementsByTagName( 'body' )->item( 0 )->childNodes;
        $bodyContent = $domforBody->document->getElementsByTagName( 'body' )->item( 0 )->nodeValue;

        /*
        foreach( $bodyTags as $child )
        {
            // $this->document->getElementsByTagName( 'body' )->item( 0 )->appendChild( $this->importNode( $tag, true ) );
            if( isset( $child->tagName ) ) {

                switch( $child->tagName )
                {
                    case 'script':
                        $this->defineScriptLocation( $child, $toBody, $hashes, $index );
                        break;

                    case 'style':
                        $child->nodeValue = str_replace( "\n", ' ', $child->nodeValue );
                        $CustomStyleContent .= $child->nodeValue;
                        break;
                    
                    case 'svg':
                        $children = $child->childNodes;

                        foreach( $children as $kid )
                        {
                            if( isset( $kid->tagName ) && $kid->tagName === 'style' )
                            {
                                $kid->nodeValue = str_replace( "\n", ' ', $kid->nodeValue );
                                $CustomStyleContent .= $kid->nodeValue;
                                $child->removeChild( $kid );
                            }
                        }
                        $toBody[] = $child;
                        break;
                    
                    default:
                        $toBody[] = $child;
                        break;
                }
            }
        }
        */

        $this->prepareNodesInAList( $bodyTags, $toBody, $headtags, $beforeAMP, $afterAMP, $hashes, $CustomStyleContent, $index, $canon, $domforBody->document );

        $CustomStyleContent = preg_replace( '/\!important/', '', $CustomStyleContent );
        $CustomStyleContent = preg_replace( "/;[\n\t\r ]+/", '; ', $CustomStyleContent );
        $CustomStyleContent = preg_replace( "/}[\n\t\r ]+/", '} ', $CustomStyleContent );
        $CustomStyleContent = preg_replace( '/\*\/'."[\n\t\r ]+/", '*/ ', $CustomStyleContent );
        $CustomStyleContent = preg_replace( "/[\n\t\r]+/", ' ', $CustomStyleContent );
        $styleampcustom = $this->style( $CustomStyleContent );
        $styleampcustom->appendChild( $this->ampcustom );

        $head->appendChild( $styleampcustom );
        
        $head->appendChild( $this->ampJS );
        $head->appendChild( $this->ampScript );
        $head->appendChild( $this->ampBoilerplateStyle );
        $head->appendChild( $this->ampBoilerplateNoScript );
        $head->appendChild( $this->meta( 'amp-script-src', implode( "\n" , $hashes ) ) );
        if( $canon === 0 ): $head->appendChild( $this->ampCanonical ); endif;

        /*
        $newbodydom = new DOMDocument();

        for( $i = 0; $i < max( array_keys( $toBody ) ); $i++ )
        {
            $nodo = $newbodydom->importNode( $toBody[$i], true );
            $newbodydom->appendChild( $nodo );
        }
        */
        
        foreach( $afterAMP as $after )
        {
            $head->appendChild( $after );
        }

        foreach( $headtags as $node )
        {
            if( null === $node || ! isset( $node->nodeType ) ): continue; endif;
            $head->appendChild( $this->importNode( $node, true ) );
        }

        $this->document->getElementsByTagName( 'html' )->item( 0 )->appendChild( $this->body() );

        $this->document->getElementsByTagName( 'body' )->item( 0 )->setAttribute( 'class', 'amp' );

        $this->document->getElementsByTagName( 'html' )->item( 0 )->insertBefore( 
            $head,
            $this->document->getElementsByTagName( 'body' )->item( 0 )
        );

        /**
         * BODY SECTION
         */
        foreach( $toBody as $node )
        {
            if( null == $node || ! isset( $node->nodeValue ) ): continue; endif;

            $this->document->getElementsByTagName( 'body' )->item( 0 )->appendChild( $this->importNode( $node, true ) );
        }

        $images = $this->document->getElementsByTagName( 'img' );
        $imagedom = new DOM;

        foreach( $images as $img )
        {          
            $ampimg = $this->ampIMG();
            $ampimg->setAttribute( 'layout', "intrinsic" );

            foreach( $img->attributes as $att )
            {
                $ampimg->setAttribute( $att->name, $att->value );
            }
            
            $imagedom->appendChild( $imagedom->importNode( $ampimg, true ) );
        }

        $ampimgindx = 0;        

        foreach( $imagedom->childNodes as $ampimg )
        {
            $img = $this->document->getElementsByTagName( 'img' )->item( $ampimgindx );

            $img->parentNode->insertBefore( 
                $this->importNode( $ampimg, true ),
                $img
            );

            $img->parentNode->removeChild( $img );
        }

        $this->head = $this->document->getElementsByTagName( 'head' )->item( 0 );

        $this->outerHTML();
        echo $this->outerHTML;
    }

    private function defineScriptLocation( &$child, &$bodyArray, &$hashArray, &$index )
    {
        global $nestlevel;
        $has_src = false; $src = ''; $id = '';
        foreach( $child->attributes as $atts )
        {
            switch( $atts->name )
            {
                case 'src':
                    $has_src = true;
                    $src = $atts->nodeValue;
                    break;

                case 'id':
                    $id = $atts->nodeValue;
                    break;

                default:
                    break;
            }
        }

        if( $has_src === false )
        {
            $ampscript = $this->document->createElement( 'amp-script' );
            $ampscript->setAttribute( 'layout', "responsive" );
            $ampscript->setAttribute( 'height', "0px" );
            $ampscript->setAttribute( 'width', "0px" );
            $ampscript->setAttribute( 'script', "the_script_{$index}" );
            $ampscript->setAttribute( 'sandbox', "allow-forms" );

            $hashArray[] = base64_encode( hash_hmac( 'sha384', $child->nodeValue, 'mumbojumbo' ) );

            $child->setAttribute( 'id', "the_script_{$index}" );
            $child->setAttribute( 'type', "text/plain" );
            $child->setAttribute( 'target', "amp-script" );

            if( $nestlevel === 0 ): $bodyArray[] = $child; endif;
            $index++;

        } else {                            
            $ampscript = $this->AMPScript( $src );
            $ampscript->setAttribute( 'layout', "responsive" );
            $ampscript->setAttribute( 'width', "0px" );
            $ampscript->setAttribute( 'height', "0px" );

            $domain = explode( '/', str_replace( 'https://', '', $src ) )[0];

            if( $domain != $_SERVER['HTTP_HOST'] ):
                $hashArray[] = base64_encode( hash_hmac( 'sha384', $src, 'mumbojumbo' ) );
            endif;

            $ampscript->setAttribute( 'id', $id );
        }
        if( $nestlevel === 0 ): $bodyArray[] = $ampscript; endif;
    }

    private function prepareChildNode( &$child, &$bodyArray, &$headArray, &$befAMPArray, &$aftAMPArray, &$hashArray, &$customStyle, &$index, &$parentArray, &$canon, $document )
    {
        global $nestlevel;
        if( isset( $child->tagName ) ) {

            switch( $child->tagName )
            {
                case 'script':
                    $this->defineScriptLocation( $child, $bodyArray, $hashArray, $index );
                    break;

                case 'style':
                    $child->nodeValue = str_replace( "\n", ' ', $child->nodeValue );
                    $customStyle .= $child->nodeValue;
                    break;

                case 'link':
                    $rel = $child->attributes->item( 0 );
                    if( $rel->name === 'rel' && $rel->nodeValue === 'canonical' ):
                        $canon = 1;
                        if( $nestlevel === 0 ): $befAMPArray[] = $child; endif;
                    elseif( $rel->name === 'rel' && $rel->nodeValue === 'stylesheet' ):
                        
                        $i = 0; $atts = array();

                        while( $i < $child->attributes->length )
                        {
                            switch( $child->attributes->item( $i )->name )
                            {
                                case 'href':
                                    $atts['href'] = $child->attributes->item( $i )->value;
                                    break;
                                
                                case 'id':
                                    $atts['id'] = $child->attributes->item( $i )->value;
                                    break;

                                default:
                                    break;
                            }
                            $i++;
                        }

                        $url = explode( '?', $atts['href'] )[0];

                        $pattern = '/https:\/\/(fast.fonts.net|fonts.googleapis.com|maxcdn.bootstrapcdn.com|use.fontawesome.com|use.typekit.net)/';

                        if( 1 === preg_match( $pattern, $url ) ):
                            if( $nestlevel === 0 ): $aftAMPArray[] = $child; endif; break;
                        endif;

                        $antiTheme = '\/betheme\/assets\/|\/betheme\/css\/|mfn-header-builder\/functions\/assets|elementor\/assets\/css\/frontend.min.css|betheme\/functions\/plugins\/elementor|elementor\/assets\/lib\/animations|uploads\/elementor\/css|elementor\/assets\/lib\/eicons\/css';

                        $pattern = "/fontawesome|fortawesome|block-library|$antiTheme/";

                        if( 1 === preg_match( $pattern, $url ) ): break; endif;

                        if( $url ):
                            $context = array(
                                "ssl" => array(
                                    "verify_peer" => false,
                                    "verify_peer_name" => false,
                                ),
                            );
                            $customStyle .= file_get_contents( $url, false, stream_context_create( $context ) );
                        else:
                            if( $nestlevel === 0 ): $aftAMPArray[] = $child; endif;
                        endif;
                    else:
                        if( $nestlevel === 0 ): $aftAMPArray[] = $child; endif;
                    endif;
                    break;

                case 'svg':
                    $children = $child->childNodes;

                    foreach( $children as $kid )
                    {
                        if( isset( $kid->tagName ) && $kid->tagName === 'defs' )
                        {
                            $kid->nodeValue = str_replace( "\n", ' ', $kid->nodeValue );
                            $customStyle .= $kid->nodeValue;
                            $child->removeChild( $kid );
                        }
                    }
                    if( $nestlevel === 0 ): $toBody[] = $child; endif;
                    break;
                            
                default:
                    if( $nestlevel === 0 ): $headArray[] = $child; endif;
                    break;
            }
        } elseif( isset( $child->wholeText ) ) {
        
            $regex  = array( "/ /", "/\t/", "/\n/", "/\r/" );
            $result = preg_replace( $regex, "", $child->wholeText );
            if( strlen( $result ) === 0 )
            {
                if( $parentArray instanceof DOMNodeList )
                {

                } else {
                    unset( $parentArray[ array_search( $child, $parentArray, true ) ] );
                }
            } else {
                if( $nestlevel === 0 ): $headArray[] = $child; endif;
            }
        } else {
            if( $nestlevel === 0 ): $headArray[] = $child; endif;
        }
    }

    private function prepareNodesInAList( &$nodelist, &$bodyArray, &$headArray, &$befAMPArray, &$aftAMPArray, &$hashArray, &$customStyle, &$index, &$canon, $document )
    {
        global $nestlevel; empty( $nestlevel ) ? $nestlevel = 0 : $nestlevel;
        foreach( $nodelist as $child )
        {
            if( isset( $child->childNodes ) && $child->childNodes->length >= 1 )
            {
                $nestlevel++;
                $this->prepareNodesInAList( $child->childNodes, $bodyArray, $headArray, $befAMPArray, $aftAMPArray, $hashArray, $customStyle, $index, $canon, $document );
            }

            $this->prepareChildNode( $child, $bodyArray, $headArray, $befAMPArray, $aftAMPArray, $hashArray, $customStyle, $index, $nodelist, $canon, $document );
        }
        --$nestlevel;
    }

    public function AMPIMG( $src = '', $id = '', $class = "" )
    {
        @ $tag = $this->document->createElement( 'amp-img' );
        if( ! empty( $src ) ): $tag->setAttribute( 'src', $src ); endif;
        $this->addAttributes( $tag, $id, $class );
        // $this->outerHTML();
        return $tag;
    }

    public function AMPScript( string $src = "", string $content = "" )
    {
        $tag = $this->ampscriptnode->cloneNode();
        if( ! empty( $src ) ): $tag->setAttribute( 'src', $src ); endif;
        if( ! empty( $content ) ): $tag->nodeValue = $content; endif;
        $this->outerHTML();
        return $tag;
    }

    public function importNode( $node, bool $deep = false )
    {
        $node = $this->document->importNode( $node, $deep );
        return $node;
    }

    public function setDoctype( $type = "html" )
    {
        $this->loadHTML( "<!DOCTYPE {$type}>", true );
        $this->outerHTML();
    }

    public function addAttributes( DOMNode|DOMDocument $node, $id, $class )
    {
        if( ! empty( $id ) ): $node->setAttribute( 'id', $id ); endif;
        if( ! empty( $class ) ): $node->setAttribute( 'class', $class ); endif;
    }

    public function html()
    {
        $tag = $this->document->createElement( 'html' );
        $this->outerHTML();
        return $tag;
    }

    public function head()
    {
        $tag = $this->document->createElement( 'head' );
        $this->outerHTML();
        return $tag;
    }

    public function meta( $name = '', $content = '' )
    {
        $tag = $this->document->createElement( 'meta' );
        if( ! empty( $name ) ): $tag->setAttribute( 'name', $name ); endif;
        if( ! empty( $content ) ): $tag->setAttribute( 'content', $content ); endif;
        $this->outerHTML();
        return $tag;
    }

    public function link( $rel = '', $href = '', $src = '' )
    {
        $tag = $this->document->createElement( 'link' );
        if( ! empty( $rel ) ): $tag->setAttribute( 'rel', $rel ); endif;
        if( ! empty( $href ) ): $tag->setAttribute( 'href', $href ); endif;
        if( ! empty( $src ) ): $tag->setAttribute( 'src', $src ); endif;
        $this->outerHTML();
        return $tag;
    }

    public function body( $id = '', $class = "" )
    {
        $tag = $this->document->createElement( 'body' );
        $this->addAttributes( $tag, $id, $class );
        $this->outerHTML();
        return $tag;
    }

    public function main( $content = '', $id = '', $class = "" )
    {
        @ $tag = $this->document->createElement( 'main' );
        $this->addAttributes( $tag, $id, $class );
        $this->outerHTML();
        return $tag;
    }

    public function div( $content = '', $id = '', $class = "" )
    {
        $tag = $this->document->createElement( 'div' );
        $this->addAttributes( $tag, $id, $class );
        $this->outerHTML();
        return $tag;
    }

    public function span( $content = '', $id = '', $class = "" )
    {
        $tag = $this->document->createElement( 'span', $content );
        $this->addAttributes( $tag, $id, $class );
        $this->outerHTML();
        return $tag;
    }

    public function img( $src = '', $id = '', $class = "" )
    {
        $tag = $this->document->createElement( 'img' );
        if( ! empty( $src ) ): $tag->setAttribute( 'src', $src ); endif;
        $this->addAttributes( $tag, $id, $class );
        $this->outerHTML();
        return $tag;
    }

    public function script( string $src = "", string $content = "" )
    {
        $tag = $this->document->createElement( 'script', $content );
        if( ! empty( $src ) ): $tag->setAttribute( 'src', $src ); endif;
        $this->outerHTML();
        return $tag;
    }

    public function noScript()
    {
        $tag = $this->document->createElement( 'noscript' );
        $this->outerHTML();
        return $tag;
    }

    public function style( string $content = "" )
    {
        $tag = $this->document->createElement( 'style', $content );
        $this->outerHTML();
        return $tag;
    }
}