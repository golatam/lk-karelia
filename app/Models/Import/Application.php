<?php

namespace App\Models\Import;

use App\Models\DefaultModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Application extends DefaultModel
{
    protected $connection = 'import';

    protected $table = 'applications';

    /**
     * @var array
     */
    protected $fillable = [];

    public $with = [
        'extracts',
        'documentation',
        'protocols',
        'questionnaires',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function getContestNameAttribute()
    {
        return $this->contest ? $this->contest->contest_title : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAppGlavaAttribute()
    {
        return $this->user ? $this->user->app_glava : null;
    }

    public function getAppGlavaPhoneAttribute()
    {
        return $this->user ? $this->user->app_glava_phone : null;
    }

    public function getAppGlavaEmailAttribute()
    {
        return $this->user ? $this->user->app_glava_email : null;
    }

    public function getAppPostAddressAttribute()
    {
        return $this->user ? $this->user->app_post_address : null;
    }

    public function getAppExecutorAttribute()
    {
        return $this->user ? $this->user->app_executor : null;
    }

    public function getAppExecutorPhoneAttribute()
    {
        return $this->user ? $this->user->app_executor_phone : null;
    }

    public function getAppExecutorEmailAttribute()
    {
        return $this->user ? $this->user->app_executor_email : null;
    }

    public function municipalityThrough()
    {
        return $this->hasOneThrough(Municipality::class, User::class, 'id', 'id', 'user_id', 'municipality_id');
    }

    public function getMunicipalityUserNameAttribute()
    {
        return $this->municipalityThrough ? $this->municipalityThrough->name : null;
    }

    public function getMunicipalityNameAttribute()
    {
        return $this->municipality ? $this->municipality->name : null;
    }

    public function getMunicipalityParentNameAttribute()
    {
        return $this->municipality && $this->municipality->parent ? $this->municipality->parent->name : null;
    }

    // Выписки из реестра
    public function extracts()
    {
//        dd(str_replace('Import\\', '', self::getMorphClass()));
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','extracts')
        ;
    }

    public function getExtractsStringNameAttribute()
    {
        $result = $this
            ->extracts
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Проектная техническая документация
    public function documentation()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','documentation')
            ;
    }

    public function getDocumentationStringNameAttribute()
    {
        $result = $this
            ->documentation
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Протоколы собрания
    public function protocols()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group', 'protocols')
            ;
    }

    public function getProtocolsStringNameAttribute()
    {
        $result = $this
            ->protocols
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Опросные листы
    public function questionnaires()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','questionnaires')
            ;
    }

    public function getQuestionnairesStringNameAttribute()
    {
        $result = $this
            ->questionnaires
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Заверенные копии актов выполненных работ
    public function acts()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','acts')
            ;
    }

    // Заверенные копии документов подтверждающих оплату выполненных работ
    public function payment()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','payment')
            ;
    }

    // Заверенные копии публикаций в средствах массовой информации
    public function publications()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','publications')
            ;
    }

    // Участие СМИ
    public function mass_media()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','massMedia')
            ;
    }

    // Планируемые источники финансирования мероприятий проекта
    public function planned_sources_financing()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','plannedSourcesFinancing')
            ;
    }

    public function getMassMediaStringNameAttribute()
    {
        $result = $this
            ->massMedia
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    public function getGratuitousReceiptsAttribute($gratuitousReceipts)
    {
        return collect(json_decode($gratuitousReceipts, true))
            ->filter(function ($value) {

                if (!!$value['name'] && !!$value['value']) {

                    return $value;
                }
            })
            ->toArray()
            ;
    }

    public function getGratuitousReceiptsFirstAttribute()
    {
        return collect($this->gratuitous_receipts)->first();
    }

    public function getOperatingAndMaintenanceCostsAttribute($operatingAndMaintenanceCosts)
    {
        return collect(json_decode($operatingAndMaintenanceCosts, true))
            ->filter(function ($value) {

                if (!!$value['name'] && !!$value['value_one'] && !!$value['value_two'] && !!$value['value_three']) {

                    return $value;
                }
            })
            ->toArray()
            ;
    }

    public function getOperatingAndMaintenanceCostsFirstAttribute()
    {
        return collect($this->operating_and_maintenance_costs)->first();
    }

    public function get5Attribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

}
