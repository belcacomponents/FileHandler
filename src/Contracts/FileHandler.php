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
     * Метод объединения массивов "reset" - заменяет значения предыдущего
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
     * Тип ключа "last" - возвращает последний элемент ключа полученный
     * в виде строки объединенной через точку ('finfo.size' -> 'size').
     *
     * @var string
     */
    const KEY_TYPE_LAST = 'last';

    /**
     * Тип ключа "whole" - возвращает неизменный вид ключа ('finfo.size' -> 'finfo.size').
     *
     * @var string
     */
    const KEY_TYPE_WHOLE = 'whole';

    /**
     * Тип ключа "array" - возвращает значение ключа в указанной позиции массива.
     * Например, с ключом 'finfo.size' значение будет в массиве
     * [
     *    'finfo' => [
     *        'size' => 'value',
     *    ],
     * ]
     *
     * @var string
     */
    const KEY_TYPE_ARRAY = 'array';

    /**
     * Задает массив обработчиков, правила обработки обработчиков и сценарии
     * обработки файлов.
     *
     * @param array $handlers
     * @param mixed $rules
     * @param mixed $scripts
     */
    public function __construct($handlers, $rules = null, $scripts = null);

    /**
     * Задает путь к файлу и дополнительные сведения о файле: оригинальное имя,
     * расширение файла.
     *
     * @param  string $file      Абсолютный путь к файлу
     * @param  array  $fileinfo  Базовая информация о файле
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
     * @param  string $directory
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
     * Возвращает список обработчиков.
     *
     * @return array
     */
    public function getHandlers();

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
     * @param  string  $key
     * @return boolean
     */
    public function handlerExistsByKey($key);

    /**
     * Проверяет существование обработчика по имени класса.
     *
     * @param  string  $className
     * @return boolean
     */
    public function handlerExistsByClassName($className);

    /**
     * Возвращает ключи обработчиков.
     *
     * @return array
     */
    public function getHandlerKeys();

    /**
     * Задает правила к указанному обработчику файла. Если у обработчика уже
     * заданы правила, то выполняется слияние массивов указанным методом.
     *
     * @param string $handlerKey
     * @param mixed  $rules
     * @param string $method
     */
    public function setHandlerRules($handlerKey, $rules, $method = self::METHOD_MERGE);

    /**
     * Возвращает правила обработчика.
     *
     * @param  string $handlerKey
     * @return mixed
     */
    public function getHandlerRules($handlerKey);

    /**
     * Добавляет сценарий обработки файла с указанным именем сценария. Если
     * сценарий с указанным именем существует, то выполняется слияние массивов
     * сценария указанным методом.
     *
     * @param string $scriptName
     * @param mixed  $script
     * @param string $method
     */
    public function addScript($scriptName, $script, $method = self::METHOD_MERGE);

    /**
     * Возвращает сценарий обработки файла по названию сценария.
     *
     * @param  string $scriptName
     * @return mixed
     */
    public function getScriptByScriptName($scriptName);

    /**
     * Возвращает все заданные сценарии обработки файлов.
     *
     * @return mixed
     */
    public function getScripts();

    /**
     * Возвращает список обработчиков сценария по указанному имени сценария.
     *
     * @param  string $scriptName
     * @return array
     */
    public function getScriptHandlersByScriptName($scriptName);

    /**
     * Задает основные названия свойств файла. Если названия основных свойств
     * уже заданы, то дополняет их новыми свойствами, если иное это не определено
     * во втором параметре.
     *
     * Название свойств указываются через точку (например, 'finfo.color' или
     * 'finfo.color' => 'color'), где первое значение - имя обработчика,
     * второе - имя возвращаемого свойства обработчиком.
     *
     * @param array $properties
     */
    public function setBasicPropertyNames($properties, $method = self::METHOD_MERGE);

    /**
     * Возвращает основные названия свойств файла.
     *
     * @return mixed
     */
    public function getBasicPropertyNames();

    /**
     * Задает сценарий обработки файла.
     *
     * Сценарий обработки - определенный набор действий, по которому будет
     * обрабатываться файл.
     *
     * Данное значение может не использоваться, тогда вся обработка будет
     * выполняться по текущим настройкам обработки файлов.
     *
     * @param mixed $handlingScript
     */
    public function setHandlingScript($handlingScript);

    /**
     * Возвращает сценарий обработки файла.
     *
     * @return mixed
     */
    public function getHandlingScript();

    /**
     * Задает сценарий обработки файла по имени сценария.
     *
     * @param string $name
     */
    public function setHandlingScriptByScriptName($name);

    /**
     * Возвращает имя заданного сценария обработки файла.
     *
     * @return string
     */
    public function getHandlingScriptByScriptName();

    /**
     * Задает последовательность выполнения обработчиков.
     *
     * Последовательность обработчиков желательно должна соответствовать
     * правилу порядку выполнения обработчиков (модифицирующие,
     * генерирующие, извлекающие), иначе после обработки может быть получен
     * неподходящий результат выполнения обработки.
     * Если в последовательности не указаны обработчики, которые были заданы
     * в списке обработчиков, то они не будут выполняться.
     *
     * @param array $sequence Список обработчиков в порядке их выполнения
     */
    public function setHandlersSequence($sequence);

    /**
     * Возвращает последовательность выполнения обработчиков.
     *
     * Если последовательность обработчиков не была задана, то будет возвращена
     * последовательность по умолчанию: модифицирующие обработчики,
     * генерирующие обработчики, извлекающие обрабочики; при этом,
     * обработчики будут отсортированы в порядке добавления в список обработчиков.
     *
     * @return array
     */
    public function getHandlersSequence();

    /**
     * Сохраняет файл со стандартным или указанным именем файла. При успешном
     * сохранении возвращает true.
     *
     * @param  string   $filename Новое имя файла относительно рабочей директории
     * @param  bool     $replace  Заменяет существующий файл
     * @return boolean
     */
    public function save($filename = null, $replace = true);

    /**
     * Запускает обработку файла с возможность изменения сценария обработки
     * файла.
     *
     * Если не определен порядок выполнения обработчиков, то обработчики
     * выполняются в следующем порядке: модифицирующие, генерирующие,
     * извлекающие. Каждый из обработчиков определяет свои значения.
     * При использовании нескольких модифицирующих обработчиков для предсказуемой
     * работы необходимо использовать в сценарии обработки порядок выполнения
     * обработчиков.
     *
     * @param  mixed $scriptModification Измененные значения сценария обработки файла
     * @return void
     */
    public function handle($scriptModification = null);

    /**
     * Возвращает путь к исходному файлу (сохраненному или модифицированному).
     *
     * @return string
     */
    public function getFilePath();

    /**
     * Возвращает пути к файлам-модификациям.
     *
     * @return array
     */
    public function getFilePaths();

    /**
     * Возвращает всю информацию об оригинальном файле.
     *
     * Если группировка свойств по обработчикам отключена, то данные внутренних
     * массивов будут объединены. При этом, если будут одинаковые ключи,
     * они будут перезаписаны последними значениями.
     *
     * @param  boolean $handlerGroups Группировать свойства по обработчикам
     * @return mixed
     */
    public function getFileInfo($handlerGroups = true, $keyType = self::KEY_TYPE_WHOLE);

    /**
     * Возвращает всю информацию о всех файлах.
     *
     * Если группировка свойств по обработчикам отключена, то данные внутренних
     * массивов будут объединены. При этом, если будут одинаковые ключи,
     * они будут перезаписаны последними значениями.
     *
     * @param  boolean $handlerGroups Группировать свойства по обработчикам
     * @return mixed
     */
    public function getAllInfo($handlerGroups = true, $keyType = self::KEY_TYPE_WHOLE);

    /**
     * Возвращает основную информацию о файле в соответствии с основными полями.
     *
     * По умолчанию результат не объединяется в группы обработчиков, т.к.
     * чаще всего эти значения используются для массовой записи в основные
     * поля таблицы. При необходимости этот параметр можно изменить.
     *
     * @return mixed
     */
    public function getBasicFileProperties($handlerGroups = false, $keyType = self::KEY_TYPE_LAST);

    /**
     * Возвращает дополнительную информацию о файле в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * По умолчанию результат информации разделяется на группы обработчиков,
     * т.к. одинаковые названия полей могут присуствовать в разных обработчиках.
     * Кроме этого, при последующей обработки данных можно запрашивать данные
     * конкретных обработчиков.
     *
     * @return mixed
     */
    public function getAdditionalFileProperties($handlerGroups = true, $keyType = self::KEY_TYPE_ARRAY);

    /**
     * Возвращает базовые свойства всех файлов.
     *
     * @return mixed
     */
    public function getBasicProperties($handlerGroups = false, $keyType = self::KEY_TYPE_LAST);

    /**
     * Возвращает дополнительные свойства всех файлов в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    public function getAdditionalProperties($handlerGroups = true, $keyType = self::KEY_TYPE_ARRAY);
}
