<?php

function loadpartial(string $fileName): void {
    require __DIR__ . "/partials/$fileName.php";
}

function loadview(string $fileName,array $data = []): void {
    $viewPath = getPath() . "/Framework/Views/$fileName.view.php";
    if (file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        echo "View '{$fileName}' not found!";
    }
}

function getPath(): string {
    $path = substr(__DIR__,0,-7);
    return $path;
}