<?php

namespace Belca\FileHandler\Contracts;

/**
 * Адаптер обработки файла.
 *
 * Задачи адаптера:
 * - слияние переданных настроек указанным методом;
 * - вызов обработки файла по указанному сценарию (если используется).
 */
class FileHandlerAdapter
{
    public function __construct($config = [], $scripts = []);

    /**
     * Объедиянет переданные конфигурации обработчика указанным методом и
     * возвращает их.
     *
     * @param  string $mergeMethod Метод слияния
     * @param  mixed  $configs     Перечисление настроек обработчика
     * @return mixed
     */
    public static function merge($mergeMethod, ...$configs);

    /**
     * Возвращает тип обработчика: порождающий, извлекающий или модифицирующий.
     *
     * @var string
     */
    public static getHandlerType();

    /**
     * Устанавливает конфигурацию обработки файла.
     *
     * @param mixed $config
     */
    public function setConfig($config = []);

    /**
     * Возвращает конфигурацию обработчика.
     *
     * @return mixed
     */
    public function getConfig();

    /**
     * Устанавливает возможные сценарии обработки файла.
     *
     * @param mixed $script
     */
    public function setScripts($script);

    /**
     * Возвращает сценарии обработки файла.
     *
     * @return mixed
     */
    public function getScripts();

    /**
     * Устанавливает дополнительные данные для обработки.
     *
     * @param mixed $data
     */
    public function setAdditionalData($data = []);

    /**
     * Возвращает дополнительные данные для обработки.
     *
     * @return mixed
     */
    public function getAdditionalData();

    /**
     * Задает относительный путь к обрабатываемому файлу и директорию.
     *
     * @param string $file
     * @param string $directory
     */
    public function setFile($file, $directory = null);

    /**
     * Устанавливает директорию обработки файла.
     *
     * @param string $directory
     */
    public function setDirectory($directory);

    /**
     * Возвращает относительный путь к файлу.
     *
     * @return string
     */
    public function getFile();

    /**
     * Возвращает путь к директории.
     *
     * @return string
     */
    public function getDirectory();

    /**
     * Устанавливает имя исполняемого сценария.
     *
     * @param string $name
     */
    public function setExecutableScript($name);

    /**
     * Возвращает имя исполняемого сценария.
     *
     * @return string
     */
    public function getExecutableScript();

    /**
     * Запускает обработку файла по указанному сценарию. В случае ошибки,
     * при обработке файла, вернет false.
     *
     * @param  string $script
     * @return boolean
     */
    public function handle($script = null);

    /**
     * Возвращает всю информацию о всех файлах, в т.ч. пути к файлам.
     *
     * @return mixed
     */
    public function getInfo();

    // TODO: Все основные данные должны быть возвращены или должны использоваться
    // какие-то общепринятые данные или методы возврата.
    //
    // Для первой обработки файла это могут быть путь, способ обработки,
    // размер файла и т.п.
    //
    // Для второй обработки файла (отдельного), часть информации может уже существовать
    // и соответственно она не нужна, если не будет изменена.
    //
    //  Поэтому вернуть нужно будет только новую информацию, а это может быть
    //  цвета файла. Для цветов не предназначена общее хранилище и поэтому они
    //  будут переданы в опциях.
    //
    //  Для третей обработке файла могут быть перегенерированы файлы и нужно
    //  будет вернуть всю информацию.
    //
    //  Можно объявить все методы, но они не будут реализовываться или возвращать
    //  пустоту.
    //
    //  Хотя главный файл может тоже возвращать эту информацию и может быть изменен.
    //
    //  Возвращаемая информация должна принадлежать какому-то файлу, а их может быть много.
    //  Соответственно должно быть правильное слияние данных


}
