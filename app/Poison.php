<?php

namespace App;

class Poison
{

    private static $viewsPath;

    private static $cachePath;

    private static $viewExtension = '.php';

    const ASSETS_PATH = __DIR__ . '/../public/assets/';

    /**
     * @var array Vars globally accessible for all views
     */
    private $globals = [];

    /**
     * Poison constructor.
     * @param string $path Path to the views directory
     */
    public function __construct(string $path, string $cache)
    {
        if (!is_null($path))
            self::$viewsPath = $path;

        if (!is_null($cache))
            self::$cachePath = $cache;
    }

    /**
     * Set views path
     * @param string $path Path to the views directory
     */
    public static function setViewsPath(string $path): void
    {
        if (!is_null($path))
            self::$viewsPath = $path;
    }

    /**
     * Change views extension
     * @param string $extension View extension in the format ".extension"
     */
    public static function setViewExtension(string $extension): void
    {
        if (!is_null($extension))
            self::$viewExtension = $extension;
    }

    /**
     * Clear the views cache folder
     */
    private function clearCache(): void
    {
        $files = scandir(self::$cachePath);

        if (count($files) >= 8) {
            foreach ($files as $file) {
                if ($file != '.' && $file != '..')
                    unlink(self::$cachePath . DIRECTORY_SEPARATOR . $file);
            }
        }
    }

    private function parseTag(string $tag): string
    {
        $real = $tag;

        if (startsWith($tag, '@include')) {
            $real = str_replace("@include", "<?= \$poison->include", $tag);
            $real = str_replace(")", "); ?>", $real);
        }

        if (startsWith($tag, '{{') && endsWith($tag, '}}')) {
            $real = str_replace("{{", "<?=", $tag);
            $real = str_replace("}}", ";?>", $real);
        }

        if (startsWith($tag, '@url')) {
            $real = str_replace("@url", "<?= \$router->url", $tag);
            $real = str_replace(")", "); ?>", $real);
        }

        if(startsWith($tag, '@foreach')) {
            $real = str_replace("@foreach", "<?php foreach", $tag);
            $real = $real . ": ?>";
        }

        if(startsWith($tag, '@endforeach')) {
            $real = str_replace("@endforeach", "<?php endforeach; ?>", $tag);
        }

        if(startsWith($tag, '@if')) {
            $real = str_replace("@if", "<?php if", $tag);
            $real = $real . ": ?>";
        }

        if(startsWith($tag, '@else')) {
            $real = str_replace("@else", "<?php else: ?>", $tag);
        }

        if(startsWith($tag, '@elseif')) {
            $real = str_replace("@elseif", "<?php elseif", $tag);
            $real = $real . ": ?>";
        }

        if(startsWith($tag, '@endif')) {
            $real = str_replace("@endif", "<?php endif; ?>", $tag);
        }

        if (startsWith($tag, '{?')) {
            $real = str_replace("{?", "<?php", $tag);
        }

        if (startsWith($tag, '?}')) {
            $real = str_replace("?}", "?>", $tag);
        }

        if (startsWith($tag, '@extend')) {
            $real = str_replace('@extend', '', $tag);
            $real = str_replace('(', '[', $real);
            $real = str_replace(')', ']', $real);
            $real = "<?php \$extension=" . $real;
            $real = $real . "; ?>";
            $real = str_replace("\"", "'", $real);
        }

        if (startsWith($tag, '@content')) {
            $real = str_replace("@content", "<?php ob_start(); ?>", $tag);
        }

        if (startsWith($tag, '@endcontent')) {
            $real = str_replace("@endcontent", "<?php \$extension[1]['content'] = ob_get_clean(); \$poison->render(\$extension[0], \$extension[1]); ?>", $tag);
        }

        /*
        if (startsWith($tag, '@style')) {
            $tag = str_replace('.', DIRECTORY_SEPARATOR, $tag);
            $link = str_replace('(', '', $tag);
            $link = str_replace(')', '', $link);
            $link = str_replace('@style', '', $link);
            $real = str_replace("@style(", "<link rel='stylesheet' href='" . self::ASSETS_PATH . $link, $tag);
            $real = str_replace("')", "'>", $real);
            dd($real);
        }
        */

        return $real;
    }

    /**
     * Create a formatted PHP file and store it to the cache folder
     * @param string $contents Unformatted PHP Contents
     * @param array $params Parameters to include in the view
     * @return string Formatted output for the web browser
     */
    private function parseTemplate(string $contents, array $params = []): string
    {

        preg_match_all("#(\{[@?!])(.*){1,}([@?!]\})|(\@\w+)([\(]*(.+)+?[\)]*)|(\{{2}(.*){1,}\}{2})|(\{[@?!]+)|([@?!]+\})#imxU", $contents, $matches);

        if (count($matches) > 0)
        {

            $tags = $matches[0];

            foreach ($tags as $tag):

                $real = $this->parseTag($tag);

                $contents = str_replace($tag, $real, $contents);

            endforeach;

            $cachedFile = self::$cachePath . DIRECTORY_SEPARATOR . uniqid("poison.") . '.php';

            file_put_contents($cachedFile, $contents);

            ob_start();

            extract($this->globals);
            extract($params);
            require($cachedFile);

            return ob_get_clean();

        } else {
            return $contents;
        }

    }

    /**
     * Render a view
     * @param string $view View to render
     * @param array $params Parameters to include in the view
     */
    public function render(string $view, array $params = []): void
    {
        $this->clearCache();

        $view = str_replace('.', DIRECTORY_SEPARATOR, $view);
        $path = self::$viewsPath . DIRECTORY_SEPARATOR . $view . self::$viewExtension;

        if (file_exists($path)) {
            Flash::progress();  // Check the flash message life

            $output = $this->parseTemplate(file_get_contents($path), $params);
            echo $output;
        } else {
            throw new \Exception("View [{$view}] not found.");
        }
    }

    /**
     * Add global variable
     * @param string $key Key of the variable to add
     * @param $value Value of the variable to add
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    /**
     * Include a file to the view to render
     * @param string $file File to include in the final ouput
     */
    public function include(string $file): void
    {
        $file = str_replace('.', DIRECTORY_SEPARATOR, $file);
        $path = self::$viewsPath . DIRECTORY_SEPARATOR . $file . self::$viewExtension;

        if (file_exists($path)) {
            $output = $this->parseTemplate(file_get_contents($path));
            echo $output;
        } else {
            throw new \Exception("File [{$file}] not found.");
        }
    }

}