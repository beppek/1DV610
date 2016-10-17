<?php

/**
 * Abstract class to be inherited from in other HTML Form based views
 */
abstract class FormView {
    //TODO: See if I can move code into this one that will be inherited
    abstract public function response();
}