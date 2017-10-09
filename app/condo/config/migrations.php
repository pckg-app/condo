<?php

use Condo\Migration\CreateCondoTables;
use Condo\Repository\Migration\CreateRepositoryTables;
use Pckg\Auth\Migration\CreateAuthTables;
use Pckg\Queue\Migration\Queue;

return [
    CreateAuthTables::class,
    CreateRepositoryTables::class,
    CreateCondoTables::class,
    Queue::class,
];