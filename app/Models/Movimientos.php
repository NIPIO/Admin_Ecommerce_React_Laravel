<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimientos extends Model
{
    use HasFactory;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'movimientos';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = ['tabla', 'tipo_movimiento', 'usuario', 'item_id', 'estado_viejo', 'estado_nuevo', 'diferencia', 'campo_modificado', 'created_at', 'updated_at'];

    
    protected $casts = [
        'created_at'  => 'datetime:d-m-Y H:i',
        'updated_at'  => 'datetime:d-m-Y H:i',
    ];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    public function usuario()
    {
        return $this->belongsTo(Vendedores::class, 'usuario', 'id');
    }
}
