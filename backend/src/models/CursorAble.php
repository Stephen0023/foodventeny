<?php
interface CursorAble {
    public function fetchNext($startingAfter, $filters = []);
    public function fetchPrev($startingBefore, $filters = []);
    public function getPreviousLink();
    public function getNextLink();
}
?>