<?php

namespace Belca\FileHandler;

use Belca\FileHandler\Contracts\FileHandlerAdapter;
use Belca\Support\Str;
use Belca\Support\Arr;
use Illuminate\Support\Arr as IlluminateArray;

trait FileHandlerTrait
{
    // TODO реализовать все абстрактные методы FileHandler
    /**
     * Устанавливает параметры обработки файла.
     *
     * @param mixed $params Параметры обработки файла
     */
    public function setHandlerConfig($params)
    {
        // TODO этот метод нужно реализовать самому.
        // Его можно расширить. Было бы удобнее задавать настройки через СУС,
        // но пока можно тут.
        // TODO Шаблоны генерации имен и их опции - или передавать как имя файла
        // Передается в параметрах обработки сохраненного файла setHandlerParameters
        // // Задание имен сохранения файла через setHandlerParameters

    }

    /**
     * Сохраняет файл со стандартным или указанным именем файла. При успешном
     * сохранении возвращает путь к новому файлу.
     *
     * Если сохраняется загруженный файл через метод POST, то он будет перенесен
     * по указанному пути, остальные файлы копируются.
     *
     * @param string $filename Новое имя файла или полный путь к файлу
     * @param bool   $replace  Заменяет существующий файл
     * @return string
     */
    public function save($filename = null, $replace = true)
    {
        if ($filename) {
            // имя файла
        } elseif (isset($this->filename)) {
            $filename = $this->filename;
        }

        $filename = Str::removeDuplicateSymbols($filename, '/');

        $fullpath = Str::removeDuplicateSymbols($this->directory.'/'.$filename, '/');
        $path = pathinfo($fullpath, PATHINFO_DIRNAME);

        // Проверяем существование файла и проверяем, можно ли его перезаписать, если указано в параметрах
        if (! file_exists($fullpath) || (file_exists($fullpath) && $replace && is_writable($fullpath))) {

            // Создаем указанные директории, если они не существуют
            if (! file_exists($path) || (file_exists($path) && ! is_dir($path))) {
                if (! mkdir(pathinfo($fullpath, PATHINFO_DIRNAME), 0755, true)) { // TODO права на создания файла можно измениять
                    return false;
                }
            }

            // Сохраняем файл в по указанному пути и заменяем путь к оригинальному файлу
            if (move_uploaded_file($this->originalFile, $fullpath) || copy($this->originalFile, $fullpath)) {
                $this->originalName = $fullpath;
                $this->file = $filename;
            } else {
                return false;
            }
        }

        return $filename;
    }

    /**
     * Запускает модифицирующую и извлекающую обработку главного файла.
     *
     * @param  mixed $params Новые параметры обработки файла
     * @return void
     */
    public function handleOriginalFile($params = null)
    {

        // Если задан скрипт обработки, то объединяем заданные настройки,
        // если они есть
        if ($this->executableScriptOriginalFile) {
            // $this->originalFileHandlingScript
            // TODO объединяем переданную конфигурацию с начальными параметрами
            // Объединение происходит в классах-адаптерах обработчиках,
            // либо в какой-то функции по умолчанию
        }
        // Если не задан скрипт обработки, то используем только переданные
        // параметры.
        else {
            // TODO в качестве настроек для обработки используем только
            // заданные настройки, возможно с правилами конфигурации
        }

        // TODO на выходе получаем набор правил для обработки
        $script = [];

        // TODO если есть порядок выполнения скриптов, и он не изменен,
        // то выполняем обработку файла по этому порядку, т.о.
        // инициализация классов происходит в этом же порядке
        $handlers = [];
        $this->fileinfo = [];

        // Если указан порядок выполнения обработчиков, то берем этот список
        if (! empty($script['execution_order'])) {
            $handlerKeys = $script['execution_order'];
        }
        // Если порядок обработчиков не указан, то берем список ключей обработчиков
        elseif (! empty($script['handlers'])) {
            $handlerKeys = array_keys($script['handlers']);

            // TODO желательно отсортировать обработчиков: сначала порождающие,
            // затем извлекающие
        }

        // Инициализируем соответствующие обработчики и запускаем обработку
        if (isset($handlerKeys) && count($handlerKeys)) {
            foreach ($script['execution_order'] as $key) {
                if (isset($this->usedHandlers[$key]) && $this->usedHandlers[$key] instanceof FileHandlerAdapter) {
                    $adapters[$key] = new $this->usedHandlers[$key]($config[$key], $script);
                    $adapters[$key]->setFile($this->file, $this->directory);
                    $adapters[$key]->handle($this->executableScriptOriginalFile);
                    $this->fileinfo[$key] = $adapters[$key]->getInfo();
                }
            }
        }
    }

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
    public function handle($scriptModification = null)
    {
        // Если задан сценарий обработки, то сливаем данные с указанными модификациями
        if (! empty($this->script)) {

            // TODO объединяем данные скрипта с указанными $scriptModification
            // Если приняты параметры, то соединяем их с текущими настройками
            // Правила слияния конфигурации и какие данные могут передать/изменить?

            // TODO также можно запустить обработку файла без сценария, если задан порядок обработки файла

            $adapters = [];
            $results = [];

            $sequence = $this->getHandlersSequence();

            // Вызываем по очереди обработчиков и сохраняем значения в те или
            // другие переменные, в зависимости от типа обработчика
            foreach ($sequence as $handlerKey) {

                $className = $this->handlers[$handlerKey] ?? null;

                if ($className) {
                    $adapters[$handlerKey] = new $className($this->rules[$handlerKey], $this->script[$handlerKey]);
                    $adapters[$handlerKey]->setFile($this->file, $this->directory);
                    $adapters[$handlerKey]->handle($this->scriptName);

                    // Сохраняем информацию извлекающего обработчика: только информация
                    if ($className::getHandlerType() == FileHandlerAdapter::EXTRACTING) {
                        $this->fileinfo[$handlerKey] = $adapters[$handlerKey]->getInfo();
                    }
                    // Сохраняем информацию модифицирующего обработчика: информация и путь к файлу
                    elseif ($className::getHandlerType() == FileHandlerAdapter::MODIFYING) {
                        $this->fileinfo[$handlerKey] = $adapters[$handlerKey]->getInfo();
                        $this->file = $adapters[$handlerKey]->getFilename();
                    }
                    // Сохраняем информацию генерирующего обработчика: информация и список файлов
                    else {
                        $this->files = array_merge_recursive($this->files, $adapters[$handlerKey]->getInfo());
                        // WARNING: Как вернуть информацию при извлечении информации из списка обрабатываемых файлов?
                    }
                }
            }
        }

        // Если вообще никакого скрипта не задано, то не происходит обработки



        // 1. Обработка одного файла без перегенерации, только получение значений
        // 2. Обработка файла с генерацей новых файлов и получением значений
        // 3. Обработка файлов без перегенерации, но получить все значения
        // 4. Переобработка файлов с измененными конфигами

        // За одну обработку файла были сгенерированы файлы от 2-х обработчиков
        // и получены сведения от 3-х обработчиков.
        // Как вариант запускать все обработчики по порядку, а не все сразу





        // TODO в зависимости от указанного скрипта выполняются те или иные действия
        // для разных адаптеров обработчиков

        // Обработка файла может выполняться, а может и не выполняться, т.к.
        // может быть загружена модификация файла, которую не трубется модифицировать

        // ИЛИ
        // инициализируется необходимый (необходимые) обработчики и передаются в них
        // данные

        // TODO этот метод нужно реализовать самому.
        // Его можно расширить. Было бы удобнее задавать настройки через СУС,
        // но пока можно тут.
        // TODO Шаблоны генерации имен и их опции - или передавать как имя файла
        // Передается в параметрах обработки сохраненного файла setHandlerParameters
        // // Задание имен сохранения файла через setHandlerParameters

    }


    /**
     * Возвращает путь к файлу.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->file;
    }

    /**
     * Возвращает пути к файлам.
     *
     * @return array
     */
    public function getFilePaths()
    {
        return array_keys($this->files ?? []);
    }

    /**
     * Возвращает информацию о файле.
     *
     * @return mixed
     */
    public function getFileInfo($handlerGroups = true, $keyType = self::KEY_TYPE_WHOLE)
    {
        return $this->fileinfo;
    }

    /**
     * Возвращает всю информацию о всех файлах.
     *
     * @return mixed
     */
    public function getAllInfo($handlerGroups = true, $keyType = self::KEY_TYPE_WHOLE)
    {
        return $this->files;
    }

    /**
     * Возвращает информацию указанных свойств из указанного массива.
     *
     * @param  mixed  $source     Источник информации (массив)
     * @param  array  $properties Свойства указанные через точку
     * @param  string $keyType    Формат возвращаемых клюей
     * @return mixed
     */
    protected function getPropertyValuesFromSource($source, $properties = [], $keyType = self::KEY_TYPE_LAST)
    {
        $values = [];

        if (! empty($properties) && is_array($properties)) {

            foreach ($properties as $key => $value) {

                // Если в значении указано новое имя свойства (синоним),
                // то текущий ключ будет оригинальным свойством.
                // Иначе ключом будет значение.
                $property = is_string($key) ? $key : $value;

                // Поля указываются через точку с учетом имени обработчика - finfo.mime
                $propertyValue = Arr::pullThroughSeparator($source, $property);

                if ($propertyValue) {
                    // Если ключ - строковое значение, то в значении новое имя ключа.
                    $propertyKey = is_string($key) ? $value : Str::lastElementChain($property);
                    $values[$propertyKey] = $propertyValue;
                }
            }
        }

        return $values;
    }

    /**
     * Возвращает оставшиеся значения свойств из указанного источника (массива),
     * кроме исключенных (массив свойств указанных через точку).
     *
     * @param  mixed  $source     Источник
     * @param  array  $exceptions Исключен
     * @param  string $keyType    Формат возвращаемых данных
     * @return mixed
     */
    protected function getPropertyValuesFromSourceExpect($source, $exceptions = [], $keyType = self::KEY_TYPE_ARRAY)
    {
        $result = $source;

        while ($expretion = current($exceptions)) {

            // Исключаем значение из массива
            IlluminateArray::pull($result, $expretion);

            next($exceptions);
        }

        return $result;
    }

    /**
     * Возвращает основную информацию о файле в соответствии с основными полями.
     *
     * @param  boolean $handlerGroups Группировка значений по обработчикам
     * @return mixed
     */
    public function getBasicFileProperties($handlerGroups = false, $keyType = self::KEY_TYPE_LAST)
    {
        return $this->getPropertyValuesFromSource($this->fileinfo, $this->basicProperties, $keyType);
    }

    /**
     * Возвращает дополнительную информацию о файле в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    public function getAdditionalFileProperties($handlerGroups = true, $keyType = self::KEY_TYPE_ARRAY)
    {
        // WARNING: В зависимости от типа будут вызываться разные функции и передаваться разные ключи
        return $this->getPropertyValuesFromSourceExpect($this->fileinfo, Arr::originalKeys($this->basicProperties), $keyType);
    }

    /**
     * Возвращает базовые свойства всех файлов.
     *
     * @return mixed
     */
    public function getBasicProperties($handlerGroups = false, $keyType = self::KEY_TYPE_LAST)
    {
        $result = [];

        if (! empty($this->files)) {
            foreach ($this->files as $filename => $fileinfo) {
                $result[$filename] = $this->getPropertyValuesFromSource($fileinfo, $this->basicProperties, $keyType);
            }
        }

        return $result;
    }

    /**
     * Возвращает дополнительные свойства всех файлов в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    public function getAdditionalProperties($handlerGroups = true, $keyType = self::KEY_TYPE_ARRAY)
    {
        $result = [];

        if (! empty($this->files)) {
            foreach ($this->files as $filename => $fileinfo) {
                $result[$filename] = $this->getPropertyValuesFromSource($fileinfo, Arr::originalKeys($this->basicProperties), $keyType);
            }
        }

        return $result;
    }
}
