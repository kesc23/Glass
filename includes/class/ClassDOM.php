<?php
/**
 * @package Glass
 * @subpackage DOM
 * @author Kesc23 as MartinFields
 * @copyright 2021 Kesc23
 */
namespace MartinFields\Glass;
use \DOMDocument;
use \DOMNode;

Class DOM
{
    /**
     * The DOM manipulated by the class.
     *
     * @var DOMDocument $document.
     */
    public $document;

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

    public function __construct( $version = '', $charset = '', $doctype = null )
    {
        $this->document = new DOMDocument( $version, $charset );

        if( ! is_null( $doctype ) ):
            $this->setDoctype( $doctype );
        endif;
        
        $this->outerHTML();
    }

    public function outerHTML()
    {
        $this->childNodes = $this->document->childNodes;
        $this->outerHTML = preg_replace( "/\>[\n\t\r]+\</", '><', $this->document->saveHTML() );
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

        $this->outerHTML();
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