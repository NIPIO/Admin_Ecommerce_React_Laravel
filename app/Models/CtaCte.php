<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CtaCte extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cuentas_corrientes';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = ['proveedor_id', 'cliente_id', 'saldo', 'tipo_cuenta', 'activo', 'created_at', 'updated_at'];

    
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

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'proveedor_id', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id', 'id');
    }
}
