<?php
/**
 * @package Glass
 * @subpackage Hooks
 * @since 0.1.0
 * @author Kesc23
 */


/**
 * Hook class
 * 
 * This Class adds the main functionalities of Hooks inside the program.
 * There are functions in the main Glass Component that serves to set up the
 * actual functionalities when this is required in the configs.
 * 
 * @see config.php
 * @see functions.php
 */
class Hook
{
    private $hookName;

    /**
     * @var array $Callbacks        is where the hooks are located each one of them
     *                              ordered by priority
     * @since 0.1.0
     */
    private $callbacks = array();
    
    /**
     * @var boolean $isDoingAction  serves the purpose of stating when we're doing
     *                              an action or not.
     *                              it's useful to track if a particular function is being
     *                              called outside the proper hooks.
     * @since 0.1.0
     */
    private $isDoingAction = false;

    public function setHookName( string $hookName )
    {
        $this->hookName = $hookName;
    }

    public function getHookName()
    {
        return $this->hookName;
    }

    /**
     * @method addHook()
     *
     * @param string    $tag                is the name that is given to the proper hook
     *                                      or its used to track an existing hook to apply a
     *                                      function to it.
     * 
     * @param callable  $functionToAdd      its the function that will be added in the hook.
     * 
     * @param mixed     $acceptedArgs       
     * @param integer   $priority
     */
    public function addHook( string $tag, callable $functionToAdd, $acceptedArgs = '', int $priority = 10)
    {
        $this->setHookName( $tag );

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

        $hookInAction = $this->getHookName();
        $totalExecution = null;
        $message = null;

        foreach($priorities as $priority)
        {
            if( true === GLASS_DEBUG['HOOK'] ) : $message = "Now Executed {$hookInAction}->"; endif;

            $timeToExecute[] = microtime( true );

            $functions = array_keys( ( $this->callbacks[ $priority ] ) );
            foreach ($functions as $function)
            {
                $this->isDoingAction = true;

                if( true === GLASS_DEBUG['HOOK'] ) : $message .= "{$function}; "; endif;

                $theFunction = $this->callbacks[ $priority ][ $function ]['function'];
                $theArgs = $this->callbacks[ $priority ][ $function ]['args'];;

                global $actionNow;
                $actionNow = $this->isDoingAction;

                call_user_func( $theFunction, $theArgs );

                $timeToExecute[] = microtime( true );
            }
            $timeToExecute = max($timeToExecute) - min($timeToExecute);
            $totalExecution += $timeToExecute;
            $message .= 'in ' . number_format($timeToExecute, 5) . ' seconds';

            if( true === GLASS_DEBUG['HOOK'] ) : prePrint_r( $message ); endif;

            $timeToExecute = null;
            $message = null;
        }
        $this->isDoingAction = false;
        
        global $actionNow;
        $actionNow = $this->isDoingAction;

        if( true === GLASS_DEBUG['HOOK'] ) : prePrint_r( 'Total Execution aprox. ' . number_format($totalExecution, 5) . ' seconds.'); endif;
    }
}