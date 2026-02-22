# Система скоринга заявок

## Обзор

Каждая заявка PPMI, LTOS, LPTOS, SZPTOS получает автоматический балл при публикации.
Расчёт зависит от **типа муниципалитета** (5 вариантов) и включает **9 взвешенных компонентов**.

Скоринг реализован в PHP-трейтах, которые **не должны модифицироваться при миграции**.

---

## Трейты скоринга

| Файл | Строк | Используется в |
|------|-------|---------------|
| `app/Traits/CalculationPPMIApplicationTrait.php` | 864 | PPMIApplicationController |
| `app/Traits/CalculationLTOSApplicationTrait.php` | ~450 | LTOSApplicationController |
| `app/Traits/CalculationLPTOSApplicationTrait.php` | ~370 | LPTOSApplicationController |
| `app/Traits/CalculationSZPTOSApplicationTrait.php` | ~400 | SZPTOSApplicationController |

---

## Скоринг PPMI (самый сложный)

### Типы муниципалитетов и методы расчёта

| Тип | Название | Метод |
|-----|---------|-------|
| MO | Муниципальный округ | `getMOCalculation()` |
| MR | Муниципальный район | `getMRCalculation()` |
| GO | Городской округ | `getGOCalculation()` |
| GP | Городское поселение | `getGPCalculation()` |
| SP | Сельское поселение | `getSPCalculation()` |
| NP | Не определено | `getNPCalculation()` |

### 9 компонентов скоринга PPMI

| # | Компонент | Вес (ratio) | Логика |
|---|----------|-------------|--------|
| 1 | Уровень софинансирования | 0.20 | % софинансирования от общей стоимости → пороговые значения → баллы |
| 2 | Софинансирование физ./юрлиц | 0.30 | Наличие/отсутствие + безвозмездный труд |
| 3 | Доля населения-благополучателей | 0.05 | % населения → тирыбаллов |
| 4 | Участие граждан в принятии решений | 0.10 | Зависит от населения (>4000 / ≤4000) |
| 5 | Участие населения в реализации | 0.10 | Количество участников → тирыбаллов |
| 6 | Финансирование эксплуатации | 0.10 | Количество источников финансирования |
| 7 | Участие в эксплуатации | 0.05 | Количество участников |
| 8 | Анкетирование (опросы) | 0.05 | Наличие анкет/опросов |
| 9 | Участие в СМИ | 0.05 | Наличие публикаций в СМИ |

### Пример расчёта компонента 1 (уровень софинансирования)

```php
// Для типа MO (Муниципальный округ):
$ratio = 0.2;
$coFinancingPercent = ($totalCost - $fundsMunicipal) / $totalCost * 100;

if ($coFinancingPercent <= 5)  return 3 * $ratio;  // 0.6
if ($coFinancingPercent <= 10) return 5 * $ratio;  // 1.0
if ($coFinancingPercent <= 30) return 7 * $ratio;  // 1.4
if ($coFinancingPercent <= 50) return 8 * $ratio;  // 1.6
if ($coFinancingPercent > 50)  return 10 * $ratio; // 2.0

// Для типа SP (Сельское поселение) пороги другие:
if ($coFinancingPercent <= 5)  return 5 * $ratio;
if ($coFinancingPercent <= 10) return 7 * $ratio;
// ...
```

### Итоговый балл

```
total_application_points = Σ (component_score × ratio)
final_points = total_application_points + points_from_administrator
```

`points_from_administrator` — ручная корректировка администратором с комментарием.

---

## Валидация софинансирования

Дополнительно к скорингу, система проверяет допустимый уровень софинансирования по типу МО:

```php
// validateExceedingLevelCoFinancing()
$maxPercent = match($municipalityType) {
    'MO' => $isDistrictPlusGP ? 5.0 : 10.0,
    'MR' => 20.0,
    'GO' => 20.0,
    'GP' => 20.0,
    'SP' => 20.0,
};

if ($coFinancingPercent > $maxPercent) {
    // Ошибка валидации: превышен уровень софинансирования
}
```

---

## Пересчёт скоринга

Администратор может запустить массовый пересчёт через:
- `POST /applications/ppmi/reCalculation` — пересчитывает баллы всех опубликованных заявок текущего конкурса
- `POST /applications/szptos/reCalculation` — аналогично для SZPTOS

---

## Тестовое покрытие (требуется перед миграцией)

Для каждого типа муниципалитета нужен тест с известными входными данными и ожидаемыми баллами:

```php
// Пример теста:
public function test_ppmi_mo_scoring()
{
    $application = PPMIApplication::factory()->create([
        'municipality_id' => $moMunicipality->id,
        'cost_repair_work' => '100000',
        'funds_municipal' => '50000',
        'population_benefiting' => '500',
        // ...
    ]);

    $controller = new PPMIApplicationController();
    $controller->calculation($application);

    $this->assertEquals(5.4, $application->total_application_points);
}
```

**Покрытие**: 5 типов МО × 4 типа заявок × 3 сценария (мин, средний, макс) = ~60 тестов
