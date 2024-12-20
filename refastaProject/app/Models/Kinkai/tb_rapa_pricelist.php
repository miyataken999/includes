<?php
namespace App\Models\Kinkai;

use Illuminate\Database\Eloquent\Model;

class tb_rapa_pricelist extends Model
{
  protected $connection = "refasta_kinkai";
  protected $table = 'tb_rapa_pricelist';
  protected $primaryKey = 'id';
  public $timestamps = false;
}
