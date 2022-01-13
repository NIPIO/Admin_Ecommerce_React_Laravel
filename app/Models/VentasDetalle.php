<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentasDetalle extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ventas_detalle';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = ['venta_id', 'producto_id', 'precio', 'cantidad'];

    protected $casts = [
        'fecha_venta'  => 'datetime:d-m-Y',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public function venta()
    {
        return $this->belongsTo(Ventas::class, 'venta_id', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id', 'id');
    }

}

