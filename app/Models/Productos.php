<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'productos';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = ['nombre', 'marca', 'precio', 'costo', 'stock', 'stock_reservado', 'en_transito', 'en_transito_reservado', 'created_at', 'updated_at', 'activo'];

    
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

    public function marcas()
    {
        return $this->belongsTo(Marcas::class, 'marca', 'id');
    }


}
