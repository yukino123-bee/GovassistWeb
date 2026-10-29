<?php

return [

    /*
    | Hours a conversation may wait for the resident to answer a staff reply
    | before it is deleted automatically. Never lower than 8 hours.
    */
    'reply_timeout_hours' => max(8, (int) env('INQUIRY_REPLY_TIMEOUT_HOURS', 8)),

];
