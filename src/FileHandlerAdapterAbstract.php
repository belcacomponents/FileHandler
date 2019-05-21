<?php

namespace Belca\FileHandler;

use Belca\FileHandler\Contracts\FileHandlerAdapter;

/**
 * Адаптер обработки файла.
 *
 * Задачи адаптера:
 * - слияние переданных настроек указанным методом;
 * - вызов обработки файла по указанному сценарию (если используется).
 */
abstract class FileHandlerAdapterAbstract implements FileHandlerAdapter
{
    protected $config;

    /**
     * Название исполняемого скрита.
     *
     * @var string
     */
    protected $executableScript;

    /**
     * Сценарии обработки файлов.
     *
     * @var mixed
     */
    protected $scripts;

    /**
     * Относительный путь к обрабатываемому файлу.
     *
     * @var string
     */
    protected $filename;

    /**
     * Путь к директории.
     *
     * @var string
     */
    protected $directory;

    /**
     * Дополнительная информация для обработки файла.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Конечная информация для возврата результата обработки.
     *
     * Если обработчик выполнил генерацию файлов, то в качестве ключей
     * используются относительные имена файлов.
     *
     * @var mixed
     */
    protected $info;

    abstract public function __construct($rules = [], $scripts = []);

    /**
     * Объедиянет переданные правила обработчика указанным методом и
     * возвращает их.
     *
     * @param  string $method
     * @param  mixed  $rules
     * @return mixed
     */
    abstract public static function mergeRules($method, ...$rules);

    /**
     * Объединяет переданные скрипты указанным методом слияния и возвращает их.
     *
     * @param  string $method
     * @param  mixed  $scripts
     * @return mixed
     */
    abstract public static function mergeScripts($method, ...$scripts);

    /**
     * Возвращает тип обработчика: порождающий, извлекающий или модифицирующий.
     *
     * @var string
     */
    abstract public static function getHandlerType();

    /**
     * Устанавливает возможные сценарии обработки файла.
     *
     * @param mixed $scripts
     */
    public function setScripts($scripts)
    {
        $this->scripts = $scripts;
    }

    /**
     * Возвращает сценарии обработки файла.
     *
     * @return mixed
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * Устанавливает дополнительные данные для обработки.
     *
     * @param mixed $data
     */
    public function setAdditionalData($data = [])
    {
        $this->data = $data;
    }

    /**
     * Возвращает дополнительные данные для обработки.
     *
     * @return mixed
     */
    public function getAdditionalData()
    {
        return $this->data;
    }

    /**
     * Задает относительный путь к обрабатываемому файлу и директорию.
     *
     * @param string $filename
     * @param string $directory
     */
    public function setFile($filename, $directory = null)
    {
        $this->filename = $filename;

        if (isset($directory)) {
            $this->directory = $directory;
        }
    }

    /**
     * Возвращает относительный путь к файлу.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Устанавливает директорию обработки файла.
     *
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * Возвращает путь к директории.
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Возвращает абсолютное имя файла.
     *
     * @return string
     */
    public function getFile()
    {
        return ($this->directory ?? '') . $this->filename;
    }

    /**
     * Устанавливает имя исполняемого сценария.
     *
     * @param string $name
     */
    public function setExecutableScript($name)
    {
        $this->executableScript = $name;
    }

    /**
     * Возвращает имя исполняемого сценария.
     *
     * @return string
     */
    public function getExecutableScript()
    {
        return $this->executableScript;
    }

    /**
     * Запускает обработку файла по указанному сценарию. В случае ошибки,
     * при обработке файла, вернет false.
     *
     * @param  string $script
     * @return boolean
     */
    abstract public function handle($script = null);

    /**
     * Возвращает всю информацию о всех файлах, в т.ч. пути к файлам в виде
     * ключей массива.
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }
}
