<?php

/* 
 * file : app/config/constants.php
 * author : Dwikky Maradhiza
 */

return array(
    
    /**
     * This value is used to search tweets within this radius 
     * from the location searched.
     * 
     * Example : 50km (kilometers),1mi (miles)
     */
    'RADIUS' => '50km',
    
    
    /**
     * To set how much time that search histories 
     * have to keep the validity of data history in minutes
     * 
     * Example : 60 for 1 hour, etc.
     */
    'EXPIRED' => '60'
);

