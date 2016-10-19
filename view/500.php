<?php

class InternalServerError {

    public function render500ErrorPage() {
        echo "<h1>Oops, we messed something up here. Try again later and we'll see if it is fixed yet</h1>";
    }

}