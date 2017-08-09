<?php

use Condo\Migration\CreateCondoTables;
use Condo\Repository\Migration\CreateRepositoryTables;
use Pckg\Auth\Migration\CreateAuthTables;

return [
    CreateAuthTables::class,
    CreateRepositoryTables::class,
    CreateCondoTables::class,
];