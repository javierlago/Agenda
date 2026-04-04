<?php

namespace App\Utils;

class View
{
    private static string $viewsPath  = __DIR__ . '/../../views/';
    private static string $layoutPath = __DIR__ . '/../../views/layout/';

    /**
     * Renders a view template wrapped in the shared header/footer layout.
     *
     * @param string $template  Relative path to the view, e.g. 'contacts/index'
     * @param array  $data      Variables to extract and make available in the view
     */
    public static function render(string $template, array $data = []): void
    {
        extract($data);
        include self::$layoutPath . 'header.php';
        include self::$viewsPath  . $template . '.php';
        include self::$layoutPath . 'footer.php';
    }
}
