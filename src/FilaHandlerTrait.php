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

            foreach ($sequence as $handlerKey) {
                // вызываем по очереди обработчиков
                // в зависимости от типа обработчика сохраняем те или иные значения

                $className = $this->handlers[$handlerKey] ?? null;

                if ($className) {
                    $adapters[$handlerKey] = new $className($this->rules[$handlerKey], $this->script[$handlerKey]);
                    $adapters[$handlerKey]->setFile($this->file, $this->directory);
                    $adapters[$handlerKey]->handle($this->scriptName);

                    if ($className::getHandlerType() == FileHandlerAdapter::EXTRACTING) {
                        $this->fileinfo[$handlerKey] = $adapters[$handlerKey]->getInfo();
                    } elseif ($className::getHandlerType() == FileHandlerAdapter::MODIFYING) {
                        // Вернуть информацию об изменениях и путь к файлу
                        $this->fileinfo[$handlerKey] = $adapters[$handlerKey]->getInfo();
                        $this->file = $adapters[$handlerKey]->getFilename();
                    } else {
                        // Вернуть информацию о файлах (пути) и информацию
                        $this->fileinfo[$handlerKey] = $adapters[$handlerKey]->getInfo();
                        $this->files = $adapters[$handlerKey]->getFilename();
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
     * Возвращает основную информацию о файле в соответствии с основными полями.
     *
     * @param  boolean $handlerGroups Группировка значений по обработчикам
     * @return mixed
     */
    public function getBasicFileProperties($handlerGroups = false, $keyType = self::KEY_TYPE_LAST)
    {
        $props = [];

        if (! empty($this->basicProperties) && is_array($this->basicProperties)) {
            foreach ($this->basicProperties as $property) {

                // Поля указываются через точку с учетом имени обработчика
                $value = Arr::pullThroughSeparator($this->fileinfo, $property);

                if ($value) {
                    $props[Str::lastElementChain($property)] = $value;
                }
            }
        }

        return $props;
    }

    /**
     * Возвращает дополнительную информацию о файле в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    public function getAdditionalFileProperties($handlerGroups = true, $keyType = self::KEY_TYPE_ARRAY)
    {
        $result = [];

        // TODO проходим по всему массиву, исключая из значений указанные ключи
        // т.е. указанные ключи по умолчанию должны быть переделаны в массив,
        // чтобы сверять их с текущим значением массива ИЛИ НЕТ

        // IlluminateArray
        // IlluminateArray::set($result, 'products.desk.price', 200);
        return [];
    }

    /**
     * Возвращает базовые свойства всех файлов.
     *
     * @return mixed
     */
    public function getBasicProperties($handlerGroups = false, $keyType = self::KEY_TYPE_LAST)
    {
        return [];
    }

    /**
     * Возвращает дополнительные свойства всех файлов в соответствии с основными
     * полями (возвращает все свойства, кроме заданных).
     *
     * @return mixed
     */
    public function getAdditionalProperties($handlerGroups = true, $keyType = self::KEY_TYPE_ARRAY)
    {
        return [];
    }
}
