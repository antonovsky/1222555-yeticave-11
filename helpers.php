<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Функция форматирования числа суммы, ставит пробел между категориями, добавляет знак ₽.
 * @param  int $input Сумма (число).
 * @return string Отформатированное число.
 */

function format_as_price_in_rub($input) {

    $cell_number = ceil($input);

    if ($cell_number < 1000) {
        $output = $cell_number;
    }
    else {
        $output = number_format($cell_number, 0, ".", " ");
    }
    return "$output ₽";
}

/**
 * Подcчитывает интервал времени в ЧЧ:ММ.
 * @param string $date_finish Дата окнчания аукциона.
 * @return array [ЧЧ,ММ]
 */
function timer($date_finish)
{
    $timestamp_finish = strtotime($date_finish); //Метка времени из текстового представления
    $timestamp_start = time();
    $interval = $timestamp_finish - $timestamp_start;
    $hours = floor($interval / 3600);
    $minuts = floor(($interval % 3600) / 60);
    if ($hours + $minuts < 0) {
        $hours = 0;
        $minuts = 0;
    }
    $hours_for_output = str_pad($hours, 2, "0", STR_PAD_LEFT);
    $minuts_for_output = str_pad($minuts, 2, "0", STR_PAD_LEFT);

    return $output = [$hours_for_output, $minuts_for_output];
}

/**
 * Проверка данных из формы.
 * @param $data
 * @return string $data
 */
function check($data)
{
    if (isset($data)) {
        $data = trim($data); // Удаляет пробелы
        $data = stripslashes($data); // Удаляет экранирование символов, двойные слеши
        $data = htmlspecialchars($data); // Преобразует специальные символы в HTML-сущности
        }
    return $data;
}


/**
 * Заполнение поля после отпавки формы.
 * @param $name
 * @return mixed|string
 */
function getPostVal($name) {
    htmlspecialchars($name);
    return $_POST[$name] ?? "";
}

/**
 * Проверка длины поля
 * @param $name string Имя поя.
 * @param $min int
 * @param $max int
 * @return string
 */
function isCorrectLength($name, $min, $max) {
    $len = strlen($_POST[$name]);
    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }
}

/**
 * Добавление новой записи
 *
 * @param $link
 * @param $sql
 * @param array $data
 * @return bool|int|string
 */
function db_insert_data($link, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $result = mysqli_insert_id($link);
    }
    return $result;
}

/**
 * Получение записей
 *
 * @param $link
 * @param $sql
 * @param array $data
 * @return array
 */
function db_fetch_data($link, $sql, $data = []) {
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return $result;
}
