<?php

namespace Belca\FileHandler\Contracts;

/**
 * Адаптер обработки файла.
 *
 * Задачи адаптера:
 * - слияние переданных правил и скриптов указанным методом;
 * - вызов обработки файла по указанному сценарию (если используется).
 */
interface FileHandlerAdapter
{
    const EXTRACTING = 'extracting';

    const GENERATING = 'generating';

    const MODIFYING = 'modifying';

    /**
     * Инициализирует обработчика файла, задает правила обработки и сценарий
     * обработки файла.
     *
     * @param array $rules
     * @param array $script
     */
    public function __construct($rules = [], $script = null);

    /**
     * Объедиянет переданные правила обработчика указанным методом и
     * возвращает их.
     *
     * @param  string $method
     * @param  mixed  $rules
     * @return mixed
     */
    public static function mergeRules($method, ...$rules);

    /**
     * Объединяет переданные скрипты указанным методом слияния и возвращает их.
     *
     * @param  string $method
     * @param  mixed  $scripts
     * @return mixed
     */
    public static function mergeScripts($method, ...$scripts);

    /**
     * Возвращает тип обработчика: порождающий, извлекающий или модифицирующий.
     *
     * @var string
     */
    public static function getHandlerType();

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
     * Возвращает относительный путь к файлу.
     *
     * @return string
     */
    public function getFilename();

    /**
     * Устанавливает директорию обработки файла.
     *
     * @param string $directory
     */
    public function setDirectory($directory);

    /**
     * Возвращает путь к директории.
     *
     * @return string
     */
    public function getDirectory();

    /**
     * Возвращает абсолютное имя файла.
     *
     * @return string
     */
    public function getFile();

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
     * @param  string  $scriptName
     * @return boolean
     */
    public function handle($scriptName = null);

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
