<?php

class Hook 
{
    private $callbacks = array();
    private $isDoingAction = false;

    public function addHook( string $tag, callable $functionToAdd, $acceptedArgs = '', int $priority = 10)
    {
        $priorityExisted = isset( $this->callbacks[ $priority ] );

        $hookId = createIdForHook( $tag, $functionToAdd );
        $this->callbacks[ $priority ][ $hookId ] = array (
            'function' => $functionToAdd,
            'args'     => $acceptedArgs,
        );
        
        if ( ! $priorityExisted && count( $this->callbacks ) > 1 ) {
            ksort( $this->callbacks, SORT_NUMERIC );
        }

    }

    public function callHook( /* string $tag, $acceptedArgs = ''*/ )
    {
        if ( ! empty( $acceptedArgs ) )
        {
            $args = $acceptedArgs;
        }

        $priorities = array_keys($this->callbacks);

        foreach($priorities as $priority)
        {
            $functions = array_keys( ( $this->callbacks[ $priority ] ) );

            foreach ($functions as $function) {
                $this->isDoingAction = true;
                $theFunction = $this->callbacks[ $priority ][ $function ]['function'];
                $theArgs = $this->callbacks[ $priority ][ $function ]['args'];;

                call_user_func( $theFunction, $theArgs );
                $this->isDoingAction = false;
            }
        }
    }
}
