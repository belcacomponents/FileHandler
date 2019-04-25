<?php

namespace Belca\FileHandler;

/**
 * Адаптер обработки файла.
 *
 * Задачи адаптера:
 * - слияние переданных настроек указанным методом;
 * - вызов обработки файла по указанному сценарию (если используется).
 */
class FileHandlerAdapterAbstract
{
    const EXTRACTING = 'extracting';

    const GENERATING = 'generating';

    const MODIFYING = 'modifying';

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
    protected $file;

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

    public function __construct($config = [], $scripts = []);

    /**
     * Объедиянет переданные конфигурации обработчика указанным методом и
     * возвращает их.
     *
     * @param  string $mergeMethod Метод слияния
     * @param  mixed  $configs     Перечисление настроек обработчика
     * @return mixed
     */
    abstract public static function merge($mergeMethod, ...$configs);

    /**
     * Возвращает тип обработчика: порождающий, извлекающий или модифицирующий.
     *
     * @var string
     */
    abstract public static getHandlerType();

    /**
     * Устанавливает конфигурацию обработки файла.
     *
     * @param mixed $config
     */
    public function setConfig($config = [])
    {

    }

    /**
     * Возвращает конфигурацию обработчика.
     *
     * @return mixed
     */
    public function getConfig()
    {
        // TODO объединяет все данные
    }

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
     * @param string $file
     * @param string $directory
     */
    public function setFile($file, $directory = null)
    {
        $this->file = $file;

        if (isset($directory)) {
            $this->directory = $directory;
        }
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
     * Возвращает относительный путь к файлу.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
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
     * Возвращает всю информацию о всех файлах, в т.ч. пути к файлам.
     *
     * @return mixed
     */
    public function getInfo();
}
