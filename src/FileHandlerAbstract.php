<?php

namespace Belca\FileHandler;

use Belca\FileHandler\Contracts\FileHandler as FileHandlerInterface;
use Belca\FileHandler\Contracts\FileHandlerAdapter;
use Belca\Support\Str;

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
     * Правила обработки файла.
     *
     * @var mixed
     */
    protected $rules = [];

    /**
     * Сценарии обработки файла.
     *
     * @var mixed
     */
    protected $scripts = [];

    /**
     * Сценарий обработки файла.
     *
     * @var mixed
     */
    protected $script;

    /**
     * Имя исполняемого сценария обработки файла.
     *
     * @var string
     */
    protected $scriptName;

    /**
     * Последовательность выполнения обработчиков.
     *
     * @var array
     */
    protected $sequence;

    /**
     * Названия основных свойств файла.
     *
     * @var array
     */
    protected $basicProperties = [];

    /**
     * Путь к обрабатываемому файлу. Может совпадать с оригинальным файлом.
     *
     * @var string
     */
    protected $file;

    /**
     * Относительные пути к сохраненными файлам и информация о файлах.
     *
     * @var mixed
     */
    protected $files;

    public function __construct($handlers, $rules = null, $scripts = null)
    {
        $this->setHandlers($handlers);
        $this->setRules($rules);
        $this->setScripts($scripts);

        if (isset($rules) && is_array($rules)) {
            $this->rules = $rules;
        }

        if (isset($scripts) && is_array($scripts)) {
            $this->scripts = $scripts;
        }
    }

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
        // TODO устанавливает директорию для записи и проверяет возможность записи файлов
        $this->directory = Str::removeDuplicateSymbols($directory, '/');
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
     * Все обработчики файлов должны иметь интерфейс Belca\FileHandler\FileHandlerAdapter
     *
     * @param array   $handlers
     */
    protected function setHandlers($handlers = [])
    {
        if (is_array($handlers)) {
            foreach ($handlers as $handlerKey => $handlerClass) {
                if (is_subclass_of($handlerClass, FileHandlerAdapter::class)) {
                    $this->handlers[$handlerKey] = $handlerClass;
                }
            }
        }
    }

    /**
     * Возвращает список обработчиков.
     *
     * @return array
     */
    public function getHandlers()
    {
        return $this->handlers;
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
        return array_key_exists($key, $this->handlers);
    }

    /**
     * Проверяет существование обработчика по имени класса.
     *
     * @param  string $className
     * @return boolean
     */
    public function handlerExistsByClassName($className)
    {
        return in_array($className, $this->handlers);
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
     * Задает указанные правила обработки файл.
     *
     * @param mixed $rules
     */
    protected function setRules($rules)
    {
        if (! empty($rules) && is_array($rules)) {
            foreach ($rules as $handlerKey => $config) {
                $this->setHandlerRules($handlerKey, $config);
            }
        }
    }

    /**
     * Задает правила к указанному обработчику файла. Если у обработчика уже
     * заданы правила, то выполняется слияние массивов указанным методом.
     *
     * @param string $handlerKey
     * @param mixed $rules
     * @param string $method
     */
    public function setHandlerRules($handlerKey, $rules, $method = self::METHOD_MERGE)
    {
        if (isset($this->handlers[$handlerKey]) && class_exists($this->handlers[$handlerKey])) {
            $class = $this->handlers[$handlerKey];
        } else {
            $class = FileHandlerUtility::class;
        }

        $this->rules[$handlerKey] = $class::mergeRules($method, $this->rules[$handlerKey] ?? [], $rules);
    }

    /**
     * Возвращает правила обработчика.
     *
     * @param  string $handlerKey
     * @return mixed
     */
    public function getHandlerRules($handlerKey)
    {
        return $this->rules[$handlerKey] ?? [];
    }

    /**
     * Задает сценарии обработки файла, порядок выполнения обработчиков
     * и основные возвращаемые свойства файла.
     *
     * @param mixed $scripts
     */
    protected function setScripts($scripts)
    {
        if (! empty($scripts) && is_array($scripts)) {
            foreach ($scripts as $scriptName => $config) {
                $this->addScript($scriptName, $config);
            }
        }
    }

    /**
     * Задает сценарий обработки файла для указанного обработчика. Если у
     * обработчика уже заданы сценарии, то выполняется слияние массивов
     * указанным методом.
     *
     * @param string $scriptName
     * @param mixed  $script
     * @param string $method
     */
    public function addScript($scriptName, $script, $method = self::METHOD_MERGE)
    {
        if (isset($this->handlers[$scriptName]) && class_exists($this->handlers[$scriptName])) {
            $class = $this->handlers[$scriptName];
        } else {
            $class = FileHandlerUtility::class;
        }

        $this->scripts[$scriptName] = $class::mergeScripts($method, $this->scripts[$scriptName] ?? [], $script);
    }

    /**
     * Возвращает сценарии обработки файла указанного обработчика.
     *
     * @param  string $handlerKey
     * @return mixed
     */
    public function getScriptByScriptName($handlerKey)
    {
        return $this->scripts[$handlerKey] ?? [];
    }

    /**
     * Возвращает все заданные сценарии обработки файлов.
     *
     * @return mixed
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * Возвращает список обработчиков сценария по указанному имени сценария.
     *
     * @param  string $scriptName
     * @return array
     */
    public function getScriptHandlersByScriptName($scriptName)
    {
        return $this->scripts[$scriptName]['handlers'] ?? [];
    }

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
    public function setBasicPropertyNames($properties, $method = self::METHOD_MERGE)
    {
        $this->basicProperties = FileHandlerUtility::mergePropertyNames($method, $this->basicProperties, $properties);
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
    public function setHandlingScript($handlingScript)
    {
        $this->script = $handlingScript; // TODO должен сливаться по указанному методу
    }

    /**
     * Возвращает сценарий обработки файла.
     *
     * @return mixed
     */
    public function getHandlingScript()
    {
        return $this->script;
    }

    /**
     * Задает сценарий обработки файла по имени сценария.
     *
     * @param string $name
     */
    public function setHandlingScriptByScriptName($name)
    {
        $this->scriptName = $name;

        if (isset($this->scripts[$name])) {

            if (! $this->script && isset($this->scripts[$name]['handlers'])) {
                $this->script = $this->scripts[$name]['handlers'];
            }

            if (! $this->basicProperties && isset($this->scripts[$name]['properties'])) {
                $this->basicProperties = $this->scripts[$name]['properties'];
            }

            if (! $this->sequence && isset($this->scripts[$name]['sequence'])) {
                $this->sequence = $this->scripts[$name]['sequence'];
            }
        }
    }

    /**
     * Возвращает имя заданного сценария обработки файла.
     *
     * @return string
     */
    public function getHandlingScriptByScriptName()
    {
        return $this->scriptName;
    }

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
    public function setHandlersSequence($sequence)
    {
        $this->sequence = $sequence;
    }

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
    public function getHandlersSequence()
    {
        if (empty($this->sequence)) {
            // Отсортировать обработчики в порядке модификаторы, генераторы, извлекаторы
        }

        return $this->sequence;
    }

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
    abstract public function getFileInfo($handlerGroups = true, $keyType = self::KEY_TYPE_WHOLE);

    /**
     * Возвращает всю информацию о всех файлах.
     *
     * @return mixed
     */
    abstract public function getAllInfo($handlerGroups = true, $keyType = self::KEY_TYPE_WHOLE);

    /**
     * Возвращает основную информацию о файле в соответствии с основными полями.
     *
     * @return mixed
     */
    abstract public function getBasicFileProperties($handlerGroups = false, $keyType = self::KEY_TYPE_LAST);

    /**
     * Возвращает дополнительную информацию о файле в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    abstract public function getAdditionalFileProperties($handlerGroups = true, $keyType = self::KEY_TYPE_ARRAY);

    /**
     * Возвращает базовые свойства всех файлов.
     *
     * @return mixed
     */
    abstract public function getBasicProperties($handlerGroups = false, $keyType = self::KEY_TYPE_LAST);

    /**
     * Возвращает дополнительные свойства всех файлов в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    abstract public function getAdditionalProperties($handlerGroups = true, $keyType = self::KEY_TYPE_ARRAY);
}
