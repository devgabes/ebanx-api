<?php

class Store {
    private static string $file = __DIR__ . '/../data.json';

    public static function load(): array {
        if (!file_exists(self::$file)) return [];
        return json_decode(file_get_contents(self::$file), true);
    }

    public static function save(array $data): void {
        file_put_contents(self::$file, json_encode($data));
    }

    public static function reset(): void {
        self::save([]);
    }
}