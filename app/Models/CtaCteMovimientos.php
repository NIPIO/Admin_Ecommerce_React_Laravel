<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CtaCteMovimientos extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cuentas_corrientes_movimientos';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = ['cuenta_corriente', 'tipo_movimiento', 'saldo_movimiento', 'created_at', 'updated_at'];

    
    protected $casts = [
        'created_at'  => 'datetime:d-m-Y',
        'updated_at'  => 'datetime:d-m-Y',
    ];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function cuenta()
    {
        return $this->belongsTo(CtaCte::class, 'cuenta_corriente', 'id');
    }
}
