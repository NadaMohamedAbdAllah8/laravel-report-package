<?php

arch()
    ->expect('App')
    ->not->toUse(['die', 'dd', 'dump']);

arch()
    ->expect('App\\Traits')
    ->toBeTraits();

arch()->preset()->php();
arch()->preset()->security()->ignoring('md5');
