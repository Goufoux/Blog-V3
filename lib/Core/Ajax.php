<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

class Ajax extends Core
{
    public function run()
    {
        
    }

    public function r($controller, $view, $data = [])
    {
        if(!class_exists($controller)) {
            echo "Controller not found (" . $controller . ")";
            return;
        }

        $controller = new $controller($this);

        if(!method_exists($controller, $view)) {
            echo "View not found (" . $view . ")";
            return;
        }

        $methodArgs = new \ReflectionMethod($controller, $view);
        $methodArgs = $methodArgs->getParameters();
        
        $args = [];

        if(!empty($methodArgs)) {
            foreach($methodArgs as $val) {
                $args []= $val->getName();
            }
        }

        if(!empty($args) && empty($data)) {
            echo "Arguments error: " . count($args) . " argument(s) required for " . $view . "()";
            return;
        }
        $ac = 0;
        $val = array();
        $missing = array();
        for($i = 0; $i < count($args); $i++) {
            $temp = $args[$i];
            if(!empty($data[$temp])) {
                $val[$data[$temp]] = $data[$temp];
                $ac++;
            } else {
                $missing[] = '<strong>'.$temp.'</strong>';
            }
        }

        if($ac != count($args)) {
            $missing = implode(', ', $missing);
            echo 'Error missing this args : ' . $missing . ' for method ' . $view . '()';
            return;
        }

        $data = call_user_func_array(array($controller, $view), $val);
        return $data;
    }
}