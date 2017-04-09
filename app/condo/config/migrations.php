<?php

use Condo\Repository\Migration\CreateRepositoryTables;
use Pckg\Auth\Migration\CreateAuthTables;

return [
    CreateAuthTables::class,
    CreateRepositoryTables::class,
];