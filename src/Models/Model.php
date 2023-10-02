<?php



namespace XuiApi\Models;

interface Model{
    public function toArray():array;

    public function fromArray(array $data):static;
}