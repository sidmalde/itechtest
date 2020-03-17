<?php

namespace itechTest\Components\Views;

use itechTest\Components\Contracts\CanUseApplication;

/**
 * Class ViewHandler
 *
 * @package itechTest\Components\Views
 */
class ViewHandler extends CanUseApplication
{

    private const VIEW_PATH_DELIMITER = '.';

    /**
     * @var string
     */
    private $themePath;

    /**
     * @var string|null
     */
    private $theme;
    /**
     * @var string|null
     */
    private $themeLayout;

    /**
     * @var array
     */
    private $headCss = [];

    /**
     *
     * @throws \Exception
     */
    private function loadTheme(): void
    {
        $defaultTheme = config('themes.default_theme');
        $defaultThemeLayout = config('themes.default_layout');

        /*
         * set the layout
         */
        $themeName = $this->theme ?? $defaultTheme;
        $themeLayout = $this->themeLayout ?? $defaultThemeLayout;
        $themeLayout .= '.php';

        $themePath = $this->getApplication()->getThemePath();
        $themePath = \joinPaths([$themePath, $themeName, 'layouts', $themeLayout]);
        if (file_exists($themePath)) {

            $this->themePath = normalizePath($themePath);
        } else {
            throw new \Exception("Invalid Theme [$themeName] at [$themePath]");
        }
    }

    /**
     * @param string $theme
     *
     * @return ViewHandler
     */
    public function setTheme(string $theme): ViewHandler
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    private function renderIntoTheme(string $content): string
    {
        $this->loadTheme();

        $data = compact('content');
        return $this->loadFileIntoVariable($this->themePath, $data);
    }

    /**
     * @param string $viewPath
     * @param array  $data
     *
     * @throws \Exception
     */
    public function render(string $viewPath, array $data = []): void
    {
        $filePath = $this->parseViewPathToRealPath($viewPath);

        if (is_readable($filePath)) {
            $output = $this->loadFileIntoVariable($filePath, $data);
        } else {
            throw new \Exception("$filePath not found");
        }

        echo $this->renderIntoTheme($output);
    }

    /**
     * @param string $viewPath
     *
     * @return string
     */
    private function parseViewPathToRealPath(string $viewPath): string
    {
        $viewPath = rtrim($viewPath, '.php');
        $paths = explode(self::VIEW_PATH_DELIMITER, $viewPath);
        $filename = end($paths) . '.php';
        array_pop($paths);
        $paths[] = $filename;
        array_unshift($paths, $this->getApplication()->getViewPath());

        return implode(DIRECTORY_SEPARATOR, $paths);
    }

    /**
     * This method will load an evaluated file into a variable so that it can be later injected into the theme
     *
     * @param string $path
     * @param array  $data
     *
     * @return string
     */
    private function loadFileIntoVariable(string $path, array $data = []): string
    {
        /**
         * add the head css
         */
        $data['_headCss'] = $this->getHeadCss();

        $output = function () use ($path, $data) {
            extract($data, EXTR_SKIP);
            ob_start();

            include $path;
            $result = ob_get_contents();
            ob_end_clean();
            return $result;
        };

        return $output();
    }

    /**
     * @param string $themeLayout
     *
     * @return ViewHandler
     */
    public function setThemeLayout(string $themeLayout): ViewHandler
    {
        $themeLayout = rtrim($themeLayout, '.php');
        $this->themeLayout = $themeLayout;
        return $this;
    }

    /**
     * @param string $css
     *
     * @return ViewHandler
     */
    public function addHeadCss(string $css): ViewHandler
    {
        $this->headCss[] = $css;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeadCss(): array
    {
        return $this->headCss;
    }
}