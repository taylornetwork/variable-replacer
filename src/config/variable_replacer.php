<?php

return [
    /**
     * Use stages
     *
     * Stages are all user defined but allow you to replace variables at different times
     */
    'use-stage' => true,

    /**
     * The variable name open character
     */
    'open-char' => '{',

    /**
     * The variable name closing character
     */
    'close-char' => '}',

    /**
     * Character to prefix the stage name
     */
    'stage-prefix' => '@',

    /**
     * Character after the stage name
     */
    'stage-suffix' => null,

    /**
     * Quote the regex pattern with this character
     *
     * Usually '/' but can use a different one if it's used in anything above
     */
    'pattern-quote' => '/',
];