<?php

class Style
{
    private $style = array();

    private function setTag( string $tag )
    {
        $this->style = $tag;
    }
    
    public function addStyle( string $tag, string $path, $version )
    {
        $this->style[$tag.$version] = array(
            'name'    => $tag,
            'path'    => $path,
            'version' => $version,
        );
    }

    public function printStyles(){
        $tagName = array_keys($this->style);
        $actualStyle = array();

        foreach ($tagName as $tag) {
            $actualStyle[] = array(
                'id'      => $this->style[ $tag ][ 'name' ],
                'path'    => $this->style[ $tag ][ 'path' ],
                'version' => $this->style[ $tag ][ 'version' ],
            );
        }

        return $actualStyle;
    }
}