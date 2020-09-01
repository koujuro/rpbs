<?php

class HTMLUtilities {

    public static function ImportLinks() {
        $args = func_get_args();

        echo "<link rel='stylesheet' href='../../css/common.css'>";
        foreach($args as $arg) {
            echo "<link rel=\"stylesheet\" href=\"{$arg}\">";
        }
    }

}









