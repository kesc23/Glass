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

    private $nestingLevel;

    public function setHookName( string $hookName )
    {
        $this->hookName = $hookName;
    }

    public function getHookName()
    {
        return $this->hookName;
    }

    public function getCallbacks()
    {
        return $this->callbacks;
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
    public function addHook( string $tag, callable $functionToAdd, $acceptedArgs = '', int $priority = 10, int $existed = 0)
    {
        $this->setHookName( $tag );

        $priorityExisted = isset( $this->callbacks[ $priority ] );
        $hookId = createIdForHook( $tag, $functionToAdd );

        if ( $existed === 0 ):
            
            //if a function hasn't been added to a hook
            $this->callbacks[ $priority ][ $hookId ] = array (
                'function' => $functionToAdd,
                'args'     => $acceptedArgs,
            );

            $this->nestingLevel[ $hookId ] = 0;
        elseif ( $existed === 1 && $this->nestingLevel[ $hookId ] === 0 ) :

            /**
             * if a function is already added but we just got one instance
             * of the same function inside the same priority added to this hook.
             */
            $oldHookOrder = $this->callbacks[ $priority ][ $hookId ];
            $this->callbacks[ $priority ][ $hookId ]   = null;
            $this->callbacks[ $priority ][ $hookId ][] = $oldHookOrder;
            $this->callbacks[ $priority ][ $hookId ][] = array (
                'function' => $functionToAdd,
                'args'     => $acceptedArgs,
            );

            $this->nestingLevel[ $hookId ]  += 1;
        elseif ( $existed === 1 && $this->nestingLevel[ $hookId ] > 0 ):

            /**
             * if a function is already added and we got one or more instances
             * of the same function inside the same priority added to this hook.
             */
            $this->callbacks[ $priority ][ $hookId ][] = array (
                'function' => $functionToAdd,
                'args'     => $acceptedArgs,
            );

            $this->nestingLevel[$hookId]  += 1;
        endif; 

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

            $functions = array_keys( $this->callbacks[ $priority ] );
            foreach ($functions as $function)
            {
                $this->isDoingAction = true;

                if( true === GLASS_DEBUG['HOOK'] ) : $message .= "{$function}; "; endif;


                /**
                 * the at signs ignores errors that would occur in case of the functions being
                 * nested inside hooks executed by a paretal hook (in execution terms).
                 * 
                 * in the actual valid iteration, the functions will be inside the proper key
                 * in the hook object arrays for execution.
                 */ 
                @$theFunction = $this->callbacks[ $priority ][ $function ]['function'];
                @$theArgs = $this->callbacks[ $priority ][ $function ]['args'];

                if ( ! $theFunction && ! $theArgs ):
                    $innerFunctions = $this->callbacks[ $priority ][ $function ];
                    foreach ($innerFunctions as $innerFunction):

                        $theFunction = $innerFunction[ 'function' ];
                        $theArgs     = $innerFunction[ 'args' ];

                        global $actionNow;

                        $actionNow = $this->isDoingAction;

                        call_user_func( $theFunction, $theArgs );

                        $timeToExecute[] = microtime( true );
                    endforeach;
                else: 
                    global $actionNow;
                    $actionNow = $this->isDoingAction;

                    call_user_func( $theFunction, $theArgs );

                    $timeToExecute[] = microtime( true );
                endif;
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