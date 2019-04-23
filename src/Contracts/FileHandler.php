<?php

namespace Belca\FileHandler\Contracts;

/**
 * Интерфейс сохранения и обработки загруженного файла.
 *
 * Используется для сохранения загруженного файла из временной директории в
 * указанную и последующей обработки файла.
 *
 * Виды обработки файла:
 * - порождающая - создает новые файлы;
 * - модифицирующая - не создает новые файлы, но изменяет существующие (перезаписывает);
 * - извлекающая - извлекает данные из файла или генерирует информацию на основе
 * файла, но не создает новых файлов.
 *
 * Если обработчики файла создают новые или перезаписывают существующие файлы,
 * и в дополнении к этому возвращают дополнительную информацию о файле, то
 * это не извлекающий вид обработки файла, а порождающий или модифицирующий.
 *
 * Если обработчик файла при извлечении информации создает временные файлы, а
 * затем удаляет их (они не предназначены для дальнейшей манипуляции и
 * последующего хранения), то это ивзлекающий вид обработки файла.
 *
 * WARNING: Не предназначен для валидации файла при загрузке!
 */
interface FileHandler
{
    /**
     * Метод объединения массивов "merge" - объединяет значения нового и текущего
     * массива.
     *
     * @var string
     */
    const METHOD_MERGE = 'merge';

    /**
     * Метод объединения массиово "replace" - заменяет дублирующиеся значения.
     * Сравнение идет только по первичными ключам.
     *
     * @var string
     */
    const METHOD_REPLACE = 'replace';

    /**
     * Метод объединения массиово "reset" - заменяет значения предыдущего
     * массива.
     *
     * @var string
     */
    const METHOD_RESET = 'reset';

    /**
     * Метод объединения "break" - приостанавливается при совпадении ключей
     * массивов.
     *
     * @var string
     */
    const METHOD_BREAK = 'break';

    /**
     * Задает путь к файлу и дополнительные сведения о файле: оригинальное имя,
     * расширение файла.
     *
     * @param string $file      Абсолютный путь к файлу
     * @param array  $fileinfo  Базовая информация о файле
     * @return bool
     */
    public function setOriginalFile($file, $fileinfo = []);

    /**
     * Возвращает путь к оригинальному файлу.
     *
     * @return string
     */
    public function getOriginalFile();

    /**
     * Возвращает информацию об оригинальном файле.
     *
     * @return array
     */
    public function getFileInfoOriginalFile();

    /**
     * Устанавливает директорию для работы с файлом. Указанная директория
     * используется в качестве префикса пути к новым файлам.
     *
     * @param string $directory
     * @return bool
     */
    public function setDirectory($directory = '');

    /**
     * Возвращает путь к директории.
     *
     * @return string
     */
    public function getDirectory();

    /**
     * Задает обработчики файлов.
     *
     * @param array   $handlers    Обработчики файлов
     * @param string  $method      Метод слияния данных
     */
    public function setHandlers($handlers = [], $method = self::METHOD_MERGE);

    /**
     * Возвращает локальных обработчиков.
     *
     * @return array
     */
    public function getHandlers();

    /**
     * Возвращает глобальных обработчиков.
     *
     * @return array
     */
    public static function getGlobalHandlers();

    /**
     * Возвращает используемые обработчики - объединяет глобальных и локальных
     * обработчиков и возвращает эти данные.
     *
     * @return array
     */
    public function getUsedHandlers();

    /**
     * Добавляет нового глобального обработчика.
     *
     * @param string  $key          Ключ обработчика
     * @param string  $handlerClass Класс обработчика
     * @param boolean $replace      Замена существующего обработчика
     */
    public static function addGlobalHandler($key, $handlerClass, $replace = true);

    /**
     * Добавляет локального обработчика.
     *
     * @param string  $key          Ключ обработчика
     * @param string  $handlerClass Класс обработчика
     * @param boolean $replace      Замена существующего обработчика
     */
    public function addHandler($key, $handlerClass, $replace = true);

    /**
     * Возвращает имя класса обработчика по ключу.
     *
     * @param  string $key
     * @return string
     */
    public function getHandlerClassNameByKey($key);

    /**
     * Возвращает ключ обработчика по имени класса.
     *
     * @param  string $className
     * @return string
     */
    public function getHandlerKeyByClassName($className);

    /**
     * Проверяет существование обработчика по ключу.
     *
     * @param  string $key
     * @return boolean
     */
    public function handlerExistsByKey($key);

    /**
     * Проверяет существование обработчика по имени класса.
     *
     * @param  string $className
     * @return boolean
     */
    public function handlerExistsByClassName($className);

    /**
     * Проверяет существование глобального обработчика по имени класса.
     *
     * @param  string $className
     * @return boolean
     */
    public static function globalHandlerExistsByClassName($className);

    /**
     * Проверяет по ключу, глобальный ли обработчик.
     *
     * @param  string  $key
     * @return boolean
     */
    public function isGlobalHandler($key);

    /**
     * Проверяет по ключу, локальный ли обработчик.
     *
     * @param  string  $key
     * @return boolean
     */
    public function isLocalHandler($key);

    /**
     * Удаляет локального обработчика по ключу.
     *
     * @param  string $key
     * @return void
     */
    public function deleteHandlerByKey($key);

    /**
     * Удаляет глобального обработчика по ключу.
     *
     * @param  string $key
     * @return void
     */
    public function deleteGlobalHandlerByKey($key);

    /**
     * Устанавливает общую конфигурацию обработки файла.
     *
     * @param mixed $config Конфигурация обработки файла
     */
    public function setHandlerConfig($config);

    /**
     * Добавляет новую конфигурацию обработки файла.
     *
     * @param mixed   $config    Конфигурация обработки файла
     * @param string  $method    Метод слияния данных
     */
    public function addHandlerConfig($config, $method = self::METHOD_MERGE);

    /**
     * Добавляет новую глобальную конфигурацию обработки файла.
     *
     * @param string $key      Ключ обработчика
     * @param mixed  $config   Конфигурация обработки файла
     * @param string $method   Метод слияния данных
     */
    public static function addGlobalHandlerConfig($key, $config, $method = self::METHOD_REPLACE);

    /**
     * Возвращает настройки обработки файла.
     *
     * @return mixed
     */
    public function getHandlerConfig();

    /**
     * Возвращает глобальные настройки обработки файла.
     *
     * @return mixed
     */
    public function getGlobalHandlerConfig();

    /**
     * Возвращает конфигурацию обработчика файла по указанному ключу обработчика.
     *
     * @param  string $key Ключ обработчика
     * @return mixed
     */
    public function getHandlerConfigByKey($key);

    /**
     * Возвращает глобальную конфигурацию обработчика файла по указанному ключу.
     * @param  string $key Ключ обработчика
     * @return mixed
     */
    public static function getGlobalHandlerConfigByKey($key);

    /**
     * Возвращает используемую конфигурацию обработчиков объединяя значения
     * глобальной конфигурации с локальной.
     *
     * @return mixed
     */
    public function getUsedHandlerConfig();

    /**
     * Возвращает используемую конфигурацию указанного обработчика объединяя
     * значение глобальных настройками с локальными.
     *
     * @param  string $key Ключ обработчика
     * @return mixed
     */
    public function getUsedHandlerConfigByKey($key);

    /**
     * Возвращает ключи локальных обработчиков.
     *
     * @return array
     */
    public function getHandlerKeys();

    /**
     * Возвращает ключи глобальных обработчиков.
     *
     * @return array
     */
    public static function getGlobalHandlerKeys();

    /**
     * Возвращает глобальные и локальные ключи обработчиков.
     *
     * @return mixed
     */
    public function getAllHandlerKeys();

    /**
     * Задает сценарий обработки файла.
     *
     * Сценарий обработки - определенный набор действий, по которому будет
     * обрабатываться файл.
     *
     * Данное значение может не использоваться, тогда вся обработка будет
     * выполняться по текущим настройкам обработки файлов.
     *
     * @param string $handlingScript
     */
    public function setFileHandlingScript($handlingScript);

    /**
     * Возвращает сценарий обработки файла.
     *
     * @return string
     */
    public function getFileHandlingScript();

    // TODO задать карту обработки, в которой прописано в каком порядке делать обработку,
    // условия обработки, передачу данных и т.п.

    /**
     * Запускает обработку файла по заданным параметрам или параметрам
     * по умолчанию.
     *
     * @return void
     */
    public function handle($params = null);

    /**
     * Сохраняет файл со стандартным или указанным именем файла. При успешном
     * сохранении возвращает true.
     *
     * @param string   $filename Новое имя файла
     * @param bool     $replace  Заменяет существующий файл
     * @return boolean
     */
    public function save($filename = null, $replace = true);

    /**
     * Возвращает путь к файлу.
     *
     * @return string
     */
    public function getFilePath();

    /**
     * Возвращает пути к файлам.
     *
     * @return array
     */
    public function getFilePaths();

    /**
     * Возвращает информацию о файле.
     *
     * @return mixed
     */
    public function getFileInfo();

    /**
     * Возвращает всю информацию о всех файлах.
     *
     * @return mixed
     */
    public function getAllInfo();

    /**
     * Задает основные названия свойств файла.
     *
     * @param array $properties Название свойств
     */
    public function setBasicPropertyNames($properties);

    /**
     * Возвращает основные названия свойств файла.
     *
     * @return array
     */
    public function getBasicPropertyNames();

    /**
     * Возвращает основную информацию о файле в соответствии с основными полями.
     *
     * @return mixed
     */
    public function getBasicFileProperties();

    /**
     * Возвращает дополнительную информацию о файле в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    public function getAdditionalFileProperties();

    /**
     * Возвращает базовые свойства всех файлов.
     *
     * @return mixed
     */
    public function getBasicProperties();

    /**
     * Возвращает дополнительные свойства всех файлов в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    public function getAdditionalProperties();
}
