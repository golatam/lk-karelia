<?php

namespace App\Models\Import;

use App\Models\DefaultModel;
use App\Models\User;
use App\Models\Contest;
use Illuminate\Database\Eloquent\Model;


class Municipality extends Model
{
    protected $connection = 'import';

    protected $table = 'municipalities';

  /**
   * @var array
   */
  protected $fillable = [
      'parent_id',  // Родитель
      'name',       // Наименование
  ];
}
