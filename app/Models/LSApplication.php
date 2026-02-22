<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LSApplication extends DefaultModel
{
    use SoftDeletes;

    protected $table = 'ls_applications';

    /**
     * @var array
     */
    protected $fillable = [
        'id',                                           // ID
        'user_id',                                      // Участник
        'contest_id',                                   // Конкурс
        'status',                                       // Статус заявки
        'contest_nomination',                           // Номинация конкурса
        'fio',                                          // Фамилия, имя, отчество (полностью)
        'date_birth',                                   // Число, месяц, год рождения
        'phone',                                        // Контактный телефон (желательно мобильный)
        'email',                                        // Адрес электронной почты
        'education',                                    // Название, год окончания учебного заведения, специальность
        'total_work_experience',                        // Общий трудовой стаж (в месяцах)
        'place_work',                                   // Место работы, занимаемая должность
        'organization_phone',                           // Телефон организации
        'organization_email',                           // Адрес электронной почты организации
        'working_hours_in_this_organization',           // Время работы в данной организации
        'working_hours_in_this_position',               // Время работы в данной должности
        'number_employees_division_total',              // Количество штатных сотрудников подразделения – всего
        'number_employees_division_under_your_command', // Количество штатных сотрудников подразделения находящихся в Вашем подчинении
        'job_responsibilities',                         // Должностные обязанности
        'consulting',                                   // Занятие консультационной деятельностью. Основные вопросы консультирования
        'awards',                                       // Наличие государственных и иных наград, премий, почетных званий
        'participation_in_projects',                    // Участие в проектах по проблемам местного самоуправления (да, нет, перечислите)
        'results_activity_in_current_year',             // Результаты деятельности в текущем  году (приведите краткое описание)
    ];

    protected $casts = [
        'date_birth' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        self::observe(Observers\LSApplicationObserver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class)
//            ->where('is_active', 1)
        ;
    }

    public function matrix(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity');
    }

    public function additional_education(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'additional_education');
    }

    public function professional_development(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'professional_development');
    }

    public function work_experience_in_government(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'work_experience_in_government');
    }

    // Фильтрация
    public function filtering()
    {
        $queryBuilder = $this;

        $sessionData = session("{$this->entity()}", []);
        unset($sessionData['filter']);
        $sessionData['filter'] = array_diff_key($sessionData, array_flip(['page', 'method', 'sort_column', 'sort_direction']));
        $sessionData['filter']['used'] = false;

        $fields = collect($sessionData['filter'])
            ->keys()
            ->toArray()
        ;

        if (in_array('id', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('id', session("{$this->entity()}.id"));
        }

        if (in_array('municipality_id', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('municipality_id', session("{$this->entity()}.municipality_id"));
        }

        if (in_array('status', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('status', session("{$this->entity()}.status"));
        }

        if (in_array('from_total_application_points', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('total_application_points', '>=', session("{$this->entity()}.from_total_application_points"));
        }

        if (in_array('to_total_application_points', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('total_application_points', '<=', session("{$this->entity()}.to_total_application_points"));
        }

        session()->put("{$this->entity()}", $sessionData);

        return $queryBuilder;
    }

    /**
     * --------------------------------
     * Поля которые выводятся в списке (Поля которые можно вывести)
     * --------------------------------
     *
     * @return array
     */
    public function fieldsSelected()
    {
        if (has_cookie("fields_{$this->entity()}")) {

            $fields = collect(get_cookie("fields_{$this->entity()}", []))->unique()->toArray();
        } else {

            $fields = collect(config("app.{$this->entity()}_applications.fields_selected_default", []))
                ->unique()
                ->toArray()
            ;
        }

        if(!auth()->user()->hasPermissions(['other.show_admin'])) {

            $fields = array_diff($fields, config("app.{$this->entity()}_applications.fields_private", []));
        }

        return $fields;
    }

    /**
     * ------------------
     * Название сущности
     * ------------------
     *
     * @return string
     */
    public function entity()
    {
        return (string) Str::of(str_replace('_applications', '', $this->getTable()));
    }

    /**
     * -----------------------------------------------------------------------
     * Поля для отображения в списке (Поля что выводятся изначально в списке)
     * -----------------------------------------------------------------------
     *
     * @return array
     */
    public function fieldsForShowing()
    {
        $fields = collect(config("app.{$this->entity()}_applications.fields_for_showing", []))
            ->unique()
            ->toArray()
        ;

        if(!auth()->user()->hasPermissions(['other.show_admin'])) {

            $fields = array_diff($fields, config("app.{$this->entity()}_applications.fields_private", []));
        }

        return $fields;
    }

    public function getDraftRulesValidation()
    {
        return [
            'user_id'                           => 'required',
            'contest_id'                        => 'required',
        ];
    }

    public function getPublishedRulesValidation()
    {
        return [
            'user_id'                                       => 'required',
            'contest_id'                                    => 'required',
        ];
    }

    public function getMessagesValidation()
    {
        return [];
    }

    public function getAttributesValidation()
    {
        return [
            'user_id' => 'Участник',
            'contest_id' => 'Конкурс',
        ];
    }

}
