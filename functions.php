<?php

$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];


function getPartsFromFullname(string $fullname): array {
    $parts = preg_split('/\s+/',  trim($fullname)) ?: [];
    return [
        'surname' => $parts[0] ?? '',
        'name' => $parts[1] ?? '',
        'patronymic' => $parts[2] ?? '',
    ];
}

function getFullnameFromParts(string $surname, string $name, string $patronymic): string {
    return trim("$surname $name $patronymic");
}

/**
 * Summary of getShortName
 * @param string $fullname
 * @return string
 */
function getShortName(string $fullname): string {
    $parts = getPartsFromFullname($fullname);
    $name = $parts['name'];
    $surname = $parts['surname'];
    $initial = mb_substr($surname, 0, 1);
    return "$name {$initial}.";
}

/**
 * Определение пола
 * Summary of getGenderFromName
 * @param string $fullname
 * @return int
 */
function getGenderFromName(string $fullname): int {
    $parts = getPartsFromFullname($fullname);
    $score = 0;

    $name        = mb_strtolower($parts['name']);
    $surname     = mb_strtolower($parts['surname']);
    $patronymic  = mb_strtolower($parts['patronymic']);

    if (mb_substr($patronymic, -3) === 'вна') $score--;
    if (mb_substr($name,       -1) === 'а') $score--;
    if (mb_substr($surname,    -2) === 'ва') $score--;

    if (mb_substr($patronymic, -2) === 'ич') $score++;
    $lastCharName = mb_substr($name, -1);
    if ($lastCharName === 'й' || $lastCharName === 'н') $score++;
    if (mb_substr($surname, -1) === 'в') $score++;

    if ($score > 0) return 1;
    if ($score < 0) return -1;
    return 0;

}

/**
 * Summary of getGenderDescription
 * @param array $persons
 * @return string
 */
function getGenderDescription(array $persons): string {
    $total = count($persons);
    $counts = ['male'=>0, 'female'=>0, 'unknown'=>0];

    foreach ($persons as $p) {
        $g = getGenderFromName($p['fullname']);
        if ($g > 0) $counts['male']++;
        elseif ($g < 0) $counts['female']++;
        else            $counts['unknown']++;
    }

    $m = round($counts['male'] / $total * 100, 1);
    $f = round($counts['female'] / $total * 100, precision: 1);
    $u = round($counts['unknown'] / $total * 100, 1);

    return "Гендерный состав аудитории:\n" .
           "---------------------------\n" .
           "Мужщчины - {$m}%\n" .
           "Женщины - {$f}%\n" .
           "Не удалось определить - {$u}%";
}


function getPerfectPartner(string $surname, string $name, string $patronymic, array $persons) : string {
    $surname    = mb_convert_case(mb_strtolower($surname), MB_CASE_TITLE, "UTF-8");
    $name       = mb_convert_case(mb_strtolower($name), MB_CASE_TITLE, "UTF-8");
    $patronymic = mb_convert_case(mb_strtolower($patronymic), MB_CASE_TITLE, "UTF-8");

    $full     = getFullnameFromParts($surname, $name, $patronymic);
    $myGender = getGenderFromName($full);

    do {
        $candidate  = $persons[array_rand($persons)]['fullname'];
        $candGender = getGenderFromName($candidate); 
    } while ($candGender === 0 || $candGender === $myGender);

    $me   = getShortName($full);
    $them = getShortName($candidate);
    $rate = number_format(mt_rand(5000, 10000) / 100, 2, '.', '');
    
    return "{$me} + {$them} = \n♡ Идеально на {$rate}% ♡ ";
}


// print_r(getPartsFromFullname('Иванов Иван Иванови'));
// echo "\n";
// echo getFullnameFromParts('Петров', 'Петр', patronymic: 'Иванович');
// echo "\n";
// echo getShortName('Степанова Наталья Степановна'), PHP_EOL;
// echo getGenderFromName('Иванов Иван Иванови'), PHP_EOL;
// echo getGenderDescription($example_persons_array);
echo getPerfectPartner(
    'иВаНов', 
    'иВАН', 
    'ИвановИЧ', 
    $example_persons_array
);

?>