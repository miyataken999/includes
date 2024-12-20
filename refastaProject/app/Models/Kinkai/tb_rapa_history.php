<?php
namespace App\Models\Kinkai;

use Illuminate\Database\Eloquent\Model;

class tb_rapa_history extends Model
{
  protected $connection = "refasta_kinkai";
  protected $table = 'tb_rapa_history';
  protected $primaryKey = 'id';
}
