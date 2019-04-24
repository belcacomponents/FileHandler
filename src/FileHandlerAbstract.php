<?php

namespace Belca\FileHandler;

use Belca\FileHandler\Contracts\FileHandler as FileHandlerInterface;

abstract class FileHandlerAbstract implements FileHandlerInterface
{
    /**
     * Путь к оригинальному файлу.
     *
     * @var string
     */
    protected $originalFile;

    /**
     * Информация об оригинальном файле.
     *
     * @var array
     */
    protected $originalFileinfo;

    /**
     * Обработчики файла.
     *
     * Ассоциативный массив в виде ключа обработчика и имени класса.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * Глобальные обработчики файла.
     *
     * ассоциативный массив в виде ключа обработчика и имени класса.
     *
     * @var array
     */
    protected static $globalHandlers = [];

    /**
     * Используемые обработчики.
     *
     * Ассоциативный массив в виде ключа обработчика и имени класса.
     *
     * Данное значение создается при инициализации класса и наполняется по
     * мере добавления и удаления обработчиков.
     *
     * @var mixed
     */
    protected $usedHandlers;

    /**
     * Настройки обработчиков.
     *
     * Ассоциативный массив в виде ключа обработчика и конфигурации.
     *
     * @var mixed
     */
    protected $config = [];

    /**
     * Глобальные настройки обработчиков.
     *
     * Ассоциативный массив в виде ключа обработчика и конфигурации.
     *
     * @var mixed
     */
    protected $globalConfig = [];

    /**
     * Используемые настройки обработчиков.
     *
     * Ассоциативный массив в виде ключа обработчика и конфигурации.
     *
     * @var mixed
     */
    protected $generatedConfig;

    /**
     * Сценарий обработки файла.
     *
     * @var string
     */
    protected $script;

    /**
     * Названия основных свойств файла.
     *
     * @var array
     */
    protected $properties;

    // TODO
    protected $file;

    /**
     * Относительные пути к сохраненными файлам и информация о файлах.
     *
     * @var mixed
     */
    protected $files;

    /**
     * Задает путь к файлу и дополнительные сведения о файле: оригинальное имя,
     * расширение файла.
     *
     * @param string $file      Абсолютный путь к файлу
     * @param array  $fileinfo  Базовая информация о файле
     * @return bool
     */
    public function setOriginalFile($file, $fileinfo = [])
    {
        if (file_exists($file)) {
            $this->originalFile = $file;

            if (isset($fileinfo) && is_array($fileinfo)) {
                $this->originalFileinfo = $fileinfo;
            } else {
                // TODO определить тип и расширение файла
                // TODO сгенерировать имя файла с учетом расширения файла
                $this->originalFileinfo['filename'] = [];
            }

            return true;
        }

        return false;
    }

    /**
     * Возвращает путь к оригинальному файлу.
     *
     * @return string
     */
    public function getOriginalFile()
    {
        return $this->originalFile;
    }

    /**
     * Возвращает информацию об оригинальном файле.
     *
     * @return array
     */
    public function getFileInfoOriginalFile()
    {
        return $this->originalFileinfo;
    }

    /**
     * Устанавливает директорию для работы с файлом. Указанная директория
     * используется в качестве префикса пути к новым файлам.
     *
     * @param string $directory
     * @return bool
     */
    public function setDirectory($directory = '')
    {
        $this->directory = $directory;
        // TODO удалить лишние слеши
        // TODO устанавливает директорию для записи и проверяет возможность записи файлов
    }

    /**
     * Возвращает путь к директории.
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory ?? '';
    }

    /**
     * Задает обработчики файлов.
     *
     * @param array   $handlers    Обработчики файлов
     * @param string  $method      Метод слияния данных
     */
    public function setHandlers($handlers = [], $method = self::METHOD_MERGE)
    {
        // TODO задает обработчиков в зависимости от указанного метода
        // и сливает данные
        // Можно абстрактным
    }

    /**
     * Возвращает локальных обработчиков.
     *
     * @return array
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * Возвращает глобальных обработчиков.
     *
     * @return array
     */
    public static function getGlobalHandlers()
    {
        return self::$globalHandlers;
    }

    /**
     * Возвращает используемые обработчики - объединяет глобальных и локальных
     * обработчиков и возвращает эти данные.
     *
     * @return array
     */
    public function getUsedHandlers()
    {
        // TODO сливает глобальные данные с локальными (локальные главн)
        // и возвращает
    }

    /**
     * Добавляет нового глобального обработчика.
     *
     * @param string  $key          Ключ обработчика
     * @param string  $handlerClass Класс обработчика
     * @param boolean $replace      Замена существующего обработчика
     */
    public static function addGlobalHandler($key, $handlerClass, $replace = true)
    {
        // todo добавляет новый обработчик
    }

    /**
     * Добавляет локального обработчика.
     *
     * @param string  $key          Ключ обработчика
     * @param string  $handlerClass Класс обработчика
     * @param boolean $replace      Замена существующего обработчика
     */
    public function addHandler($key, $handlerClass, $replace = true)
    {
        // tdo
    }

    /**
     * Возвращает имя класса обработчика по ключу.
     *
     * @param  string $key
     * @return string
     */
    public function getHandlerClassNameByKey($key)
    {
        return $this->handlers[$key] ?? null;
    }

    /**
     * Возвращает ключ обработчика по имени класса.
     *
     * @param  string $className
     * @return string
     */
    public function getHandlerKeyByClassName($className)
    {
        return array_search($className, $this->handlers);
    }

    /**
     * Проверяет существование обработчика по ключу.
     *
     * @param  string $key
     * @return boolean
     */
    public function handlerExistsByKey($key)
    {
        return array_key_exists($key, $this->handlers) || array_key_exists($key, self::$globalHandlers);
    }

    /**
     * Проверяет существование обработчика по имени класса.
     *
     * @param  string $className
     * @return boolean
     */
    public function handlerExistsByClassName($className)
    {
        return in_array($className, $this->handlers) || in_array($className, self::$globalHandlers);
    }

    /**
     * Проверяет существование глобального обработчика по имени класса.
     *
     * @param  string $className
     * @return boolean
     */
    public static function globalHandlerExistsByClassName($className)
    {
        return in_array($className, self::$globalHandlers);
    }

    /**
     * Проверяет по ключу, глобальный ли обработчик.
     *
     * @param  string  $key
     * @return boolean
     */
    public function isGlobalHandler($key)
    {
        return array_key_exists($key, self::$globalHandlers);
    }

    /**
     * Проверяет по ключу, локальный ли обработчик.
     *
     * @param  string  $key
     * @return boolean
     */
    public function isLocalHandler($key)
    {
        array_key_exists($key, $this->handlers);
    }

    /**
     * Удаляет локального обработчика по ключу.
     *
     * @param  string $key
     * @return void
     */
    public function deleteHandlerByKey($key)
    {
        unset($this->handlers[$key]);
    }

    /**
     * Удаляет глобального обработчика по ключу.
     *
     * @param  string $key
     * @return void
     */
    public function deleteGlobalHandlerByKey($key)
    {
        unset(self::$globalHandlers[$key]);
    }

    /**
     * Устанавливает общую конфигурацию обработки файла.
     *
     * @param mixed $config Конфигурация обработки файла
     */
    public function setHandlerConfig($config)
    {
        if (isset($config) && is_array($config))
        {
            $this->config = $config;
        }
    }

    /**
     * Добавляет новую конфигурацию обработки файла.
     *
     * @param mixed   $config    Конфигурация обработки файла
     * @param string  $method    Метод слияния данных
     */
    public function addHandlerConfig($config, $method = self::METHOD_MERGE)
    {
        // TODO в зависимости от метода и класса происходит добавление (если он существует)
    }

    /**
     * Добавляет новую глобальную конфигурацию обработки файла.
     *
     * @param string $key      Ключ обработчика
     * @param mixed  $config   Конфигурация обработки файла
     * @param string $method   Метод слияния данных
     */
    public static function addGlobalHandlerConfig($key, $config, $method = self::METHOD_REPLACE)
    {
        // тот же при
    }

    /**
     * Возвращает настройки обработки файла.
     *
     * @return mixed
     */
    public function getHandlerConfig()
    {
        return $this->confg;
    }

    /**
     * Возвращает глобальные настройки обработки файла.
     *
     * @return mixed
     */
    public function getGlobalHandlerConfig()
    {
        return self::$globalConfig;
    }

    /**
     * Возвращает конфигурацию обработчика файла по указанному ключу обработчика.
     *
     * @param  string $key Ключ обработчика
     * @return mixed
     */
    public function getHandlerConfigByKey($key)
    {
        return $this->config[$key] ?? [];
    }

    /**
     * Возвращает глобальную конфигурацию обработчика файла по указанному ключу.
     * @param  string $key Ключ обработчика
     * @return mixed
     */
    public static function getGlobalHandlerConfigByKey($key)
    {
        return self::$config[$key] ?? [];
    }

    /**
     * Возвращает используемую конфигурацию обработчиков объединяя значения
     * глобальной конфигурации с локальной.
     *
     * @return mixed
     */
    public function getUsedHandlerConfig()
    {
        return $this->generatedConfig;
    }

    /**
     * Возвращает используемую конфигурацию указанного обработчика объединяя
     * значение глобальных настройками с локальными.
     *
     * @param  string $key Ключ обработчика
     * @return mixed
     */
    public function getUsedHandlerConfigByKey($key)
    {
        return $this->generatedConfig[$key] ?? [];
    }

    /**
     * Возвращает ключи локальных обработчиков.
     *
     * @return array
     */
    public function getHandlerKeys()
    {
        return array_keys($this->handlers);
    }

    /**
     * Возвращает ключи глобальных обработчиков.
     *
     * @return array
     */
    public static function getGlobalHandlerKeys()
    {
        return array_keys(self::$globalHandlers);
    }

    /**
     * Возвращает глобальные и локальные ключи обработчиков.
     *
     * @return mixed
     */
    public function getAllHandlerKeys()
    {
        return array_merge(array_keys(self::$globalHandlers), array_keys($this->handlers));
    }

    /**
     * Задает сценарий обработки оригинального файла.
     *
     * @param mixed $handlingScript
     */
    public function setOriginalFileHandlingScript($handlingScript)
    {
        $this->originalFileHandlingScript = $handlingScript;
    }

    /**
     * Возвращает сценарий обработки оригинального файла.
     *
     * @return mixed
     */
    public function getOriginalFileHandlingScript()
    {
        return $this->originalFileHandlingScript;
    }

    /**
     * Задает имя исполняемого сценария обработки оригинального файла.
     *
     * @param string $name
     */
    public function setExecutableScriptHandlingOriginalFile($name)
    {
        $this->executableScriptOriginalFile = $name;
    }

    /**
     * Возвращает имя исполняемого сценария обработки оригинального файла.
     *
     * @return string
     */
    public function getExecutableScriptHandlingOriginalFile()
    {
        return $this->executableScriptOriginalFile;
    }

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
    public function setFileHandlingScript($handlingScript)
    {
        $this->script = $handlingScript;
    }

    /**
     * Возвращает сценарий обработки файла.
     *
     * @return string
     */
    public function getFileHandlingScript()
    {
        return $this->script;
    }

    /**
     * Задает имя исполняемого сценария обработки файла.
     *
     * @param string $name
     */
    public function setExecutableScriptHandlingFile($name)
    {
        $this->executableScript = $name;
    }

    /**
     * Возвращает имя исполняемого сценария обработки файла.
     *
     * @return string
     */
    public function getExecutableScriptHandlingFile()
    {
        return $this->executableScript;
    }

    /**
     * Запускает модифицирующую и извлекающую обработку главного файла.
     *
     * @param  mixed $params Новые параметры обработки файла
     * @return void
     */
    abstract public function handleOriginalFile($params = null);

    /**
     * Запускает обработку файла по заданным параметрам или параметрам
     * по умолчанию.
     *
     * @return bool
     */
    abstract public function handle($params = null);

    /**
     * Сохраняет файл со стандартным или указанным именем файла. При успешном
     * сохранении возвращает true.
     *
     * @param string   $filename Новое имя файла
     * @param bool     $replace  Заменяет существующий файл
     * @return boolean
     */
    abstract public function save($filename = null, $replace = true);

    /**
     * Возвращает путь к файлу.
     *
     * @return string
     */
    abstract public function getFilePath();

    /**
     * Возвращает пути к файлам.
     *
     * @return array
     */
    abstract public function getFilePaths();

    /**
     * Возвращает информацию о файле.
     *
     * @return mixed
     */
    abstract public function getFileInfo($handlerGroups = true);

    /**
     * Возвращает всю информацию о всех файлах.
     *
     * @return mixed
     */
    abstract public function getAllInfo($handlerGroups = true);

    /**
     * Задает основные названия свойств файла.
     *
     * @param array $properties Название свойств
     */
    public function setBasicPropertyNames($properties)
    {
        $this->basicProperties = $properties;
    }

    /**
     * Возвращает основные названия свойств файла.
     *
     * @return array
     */
    public function getBasicPropertyNames()
    {
        return $this->basicProperties;
    }

    /**
     * Возвращает основную информацию о файле в соответствии с основными полями.
     *
     * @return mixed
     */
    abstract public function getBasicFileProperties();

    /**
     * Возвращает дополнительную информацию о файле в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    abstract public function getAdditionalFileProperties();

    /**
     * Возвращает базовые свойства всех файлов.
     *
     * @return mixed
     */
    abstract public function getBasicProperties();

    /**
     * Возвращает дополнительные свойства всех файлов в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    abstract public function getAdditionalProperties();
}
