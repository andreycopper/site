<?php
namespace Views;

use Traits\Magic;

/**
 * Class View
 * @package App\Views
 */
class View
{
    public $view; // содержимое страницы для вывода в шаблоне
    private ?string $template; // используемый шаблон
    public $user; // пользователь
    public $crypt; // пользователь

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->template = defined('TEMPLATE') ? TEMPLATE : 'main';
    }

    use Magic;

    /**
     * Возвращает строку - HTML-код шаблона
     * @param string $file - шаблон
     * @param array $vars - переданные переменные для рендера в шаблоне
     * @return string
     */
    public function render(string $file, array $vars = []): string
    {
        $fileArray = explode('.', $file);
        $file_name = $fileArray[0];
        $ext = $fileArray[1] ?? 'php';

        $filePath =
            DIR_TEMPLATES . DIRECTORY_SEPARATOR .
            $this->template .
            (mb_substr($file_name, 0, 1) === '/' || mb_substr($file_name, 0, 1) === '\\' ? '' : DIRECTORY_SEPARATOR) .
            $file_name . '.' .
            $ext;

        if (empty($file) || !is_file($filePath)) return '';

        ob_start();
        foreach ($this as $name => $value) {
            $$name = $value;
        }

        if (!empty($this->data) && is_array($this->data)) {
            foreach ($this->data as $k => $item) {
                $$k = $item;
            }
        }

        if (!empty($vars) && is_array($vars)) {
            foreach ($vars as $key => $val) {
                $$key = $val;
            }
        }

        include $filePath;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Отображает HTML-код шаблона
     * @param string $file
     */
    public function display(string $file)
    {
        $this->view = $this->render($file);
        echo $this->render('template');
        die;
    }

    /**
     * Отображает HTML-код файла
     * @param string $file
     */
    public function display_element(string $file)
    {
        echo $this->render($file);
        die;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Устанавливает директорию шаблона
     * @param string $template - имя директории шаблона
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }
}
