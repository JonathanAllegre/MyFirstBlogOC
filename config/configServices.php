<?php

return [
    AddPost::class => \DI\create()->constructor(
        \DI\get(Session::class),
        \DI\get(AppManager::class),
        \DI\get(Flash::class)
    ),
    UpdatePost::class => \DI\create()->constructor(
        \DI\get(Session::class),
        \DI\get(AppManager::class),
        \DI\get(Flash::class),
        \DI\get(AppFactory::class),
        \DI\get(FileUploader::class)
    ),
    Flash::class => \DI\create()->constructor(
        \DI\get(Session::class)
    ),
    CheckPermissions::class => \DI\create()->constructor(
        \DI\get(Session::class),
        \DI\get(Flash::class)
    ),
];
