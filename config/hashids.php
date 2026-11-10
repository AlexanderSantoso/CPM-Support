<?php

return [
    'salt'   => env('HASHIDS_SALT'),
    'length' => (int) env('HASHIDS_LENGTH', 20),
];